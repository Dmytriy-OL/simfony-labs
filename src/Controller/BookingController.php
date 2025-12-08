<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Guest;
use App\Entity\Room;
use App\Repository\BookingRepository;
use App\Repository\GuestRepository;
use App\Repository\RoomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/booking')]
class BookingController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
        private BookingRepository $bookingRepo,
        private GuestRepository $guestRepo,
        private RoomRepository $roomRepo
    ) {}

    // GET ALL BOOKINGS
    #[Route('/', name: 'booking_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $bookings = $this->bookingRepo->findAll();

        $data = [];
        foreach ($bookings as $b) {
            $data[] = [
                'id' => $b->getId(),
                'guest' => $b->getGuest()->getFullName(),
                'room' => $b->getRoom()->getRoomNumber(),
                'check_in' => $b->getCheckIn()->format('Y-m-d'),
                'check_out' => $b->getCheckOut()->format('Y-m-d'),
                'status' => $b->getStatus(),
            ];
        }

        return new JsonResponse($data);
    }

    // GET ONE BOOKING
    #[Route('/{id}', name: 'booking_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $booking = $this->bookingRepo->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        return new JsonResponse([
            'id' => $booking->getId(),
            'guest' => $booking->getGuest()->getFullName(),
            'room' => $booking->getRoom()->getRoomNumber(),
            'check_in' => $booking->getCheckIn()->format('Y-m-d'),
            'check_out' => $booking->getCheckOut()->format('Y-m-d'),
            'total_price' => $booking->getTotalPrice(),
            'status' => $booking->getStatus(),
        ]);
    }

    // CREATE BOOKING
    #[Route('/create', name: 'booking_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $guest = $this->guestRepo->find($data['guest_id'] ?? 0);
        $room = $this->roomRepo->find($data['room_id'] ?? 0);

        if (!$guest || !$room) {
            return new JsonResponse(['error' => 'Invalid guest or room'], 400);
        }

        $booking = new Booking();
        $booking->setGuest($guest);
        $booking->setRoom($room);
        $booking->setCheckIn(new \DateTime($data['check_in']));
        $booking->setCheckOut(new \DateTime($data['check_out']));
        $booking->setTotalPrice($data['total_price'] ?? 0);
        $booking->setStatus('pending');

        $this->em->persist($booking);
        $this->em->flush();

        return new JsonResponse(['message' => 'Booking created', 'id' => $booking->getId()], 201);
    }

    // UPDATE BOOKING
    #[Route('/update/{id}', name: 'booking_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $booking = $this->bookingRepo->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['status'])) {
            $booking->setStatus($data['status']);
        }

        if (isset($data['total_price'])) {
            $booking->setTotalPrice($data['total_price']);
        }

        $this->em->flush();

        return new JsonResponse(['message' => 'Booking updated']);
    }

    // DELETE BOOKING
    #[Route('/delete/{id}', name: 'booking_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $booking = $this->bookingRepo->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $this->em->remove($booking);
        $this->em->flush();

        return new JsonResponse(['message' => 'Booking deleted']);
    }
}

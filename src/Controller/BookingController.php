<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Service\BookingManager;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class BookingController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;
    const REQUIRED_FIELDS = ['guest_id', 'room_id', 'check_in', 'check_out'];

    private EntityManagerInterface $em;
    private BookingManager $bookingManager;
    private RequestCheckerService $checker;

    public function __construct(
        EntityManagerInterface $em,
        BookingManager $bookingManager,
        RequestCheckerService $checker
    ) {
        $this->em = $em;
        $this->bookingManager = $bookingManager;
        $this->checker = $checker;
    }

    /**
     * GET collection (pagination + filters)
     */
    #[Route('/bookings', name: 'booking_collection', methods: ['GET'])]
    public function getCollection(Request $request): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage']) ? (int)$query['itemsPerPage'] : self::ITEMS_PER_PAGE;
        $page = isset($query['page']) ? (int)$query['page'] : 1;

        $result = $this->em
            ->getRepository(Booking::class)
            ->getAllBookingsByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }

    /**
     * GET /bookings/{id}
     */
    #[Route('/bookings/{id}', name: 'booking_one', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        $booking = $this->em->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        return new JsonResponse($booking);
    }

    /**
     * CREATE booking
     */
    #[Route('/bookings', name: 'booking_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Перевірка обов'язкових полів
        $this->checker->check($data, self::REQUIRED_FIELDS);

        // Валідація + створення через Manager
        $booking = $this->bookingManager->createBooking($data);

        $this->em->flush();

        return new JsonResponse($booking, Response::HTTP_CREATED);
    }

    /**
     * UPDATE booking
     */
    #[Route('/bookings/{id}', name: 'booking_update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $booking = $this->em->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $this->bookingManager->updateBooking($booking, $data);
        $this->em->flush();

        return new JsonResponse($booking);
    }

    /**
     * DELETE booking
     */
    #[Route('/bookings/{id}', name: 'booking_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $booking = $this->em->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return new JsonResponse(['error' => 'Booking not found'], 404);
        }

        $this->em->remove($booking);
        $this->em->flush();

        return new JsonResponse(['message' => 'Booking deleted']);
    }
}

<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Guest;
use App\Entity\Room;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
    }

    /**
     * Створення бронювання
     */
    public function createBooking(
        Guest $guest,
        Room $room,
        string $checkIn,
        string $checkOut,
        float $totalPrice,
        string $status
    ): Booking {
        $booking = $this->createBookingObject(
            $guest,
            $room,
            $checkIn,
            $checkOut,
            $totalPrice,
            $status
        );

        // Валідація згідно з Constraint у Booking
        $this->requestCheckerService->validateRequestDataByConstraints($booking);

        $this->entityManager->persist($booking);

        return $booking;
    }

    /**
     * Приватний метод створення об'єкта Booking
     */
    private function createBookingObject(
        Guest $guest,
        Room $room,
        string $checkIn,
        string $checkOut,
        float $totalPrice,
        string $status
    ): Booking {
        $booking = new Booking();
        $booking
            ->setGuest($guest)
            ->setRoom($room)
            ->setCheckIn(new \DateTime($checkIn))
            ->setCheckOut(new \DateTime($checkOut))
            ->setTotalPrice($totalPrice)
            ->setStatus($status);

        return $booking;
    }

    /**
     * Оновлення бронювання
     */
    public function updateBooking(Booking $booking, array $data): void
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (!method_exists($booking, $method)) {
                continue;
            }

            $booking->$method($value);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($booking);
    }
}

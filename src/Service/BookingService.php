<?php

namespace App\Service;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;

class BookingService
{
    private $em;
    private $validator;

    public function __construct(EntityManagerInterface $em, ValidationService $validator)
    {
        $this->em = $em;
        $this->validator = $validator;
    }

    public function createBooking(Guest $guest, Room $room, string $checkIn, string $checkOut)
    {
        // Валідація дат
        $this->validator->validateDateRange($checkIn, $checkOut);

        // Розрахунок ціни
        $days = (strtotime($checkOut) - strtotime($checkIn)) / 86400;
        $price = $room->getRoomType()->getPricePerNight() * $days;

        // Створення об'єкта
        $booking = new Booking();
        $booking->setGuest($guest);
        $booking->setRoom($room);
        $booking->setCheckIn(new \DateTime($checkIn));
        $booking->setCheckOut(new \DateTime($checkOut));
        $booking->setTotalPrice($price);

        $this->em->persist($booking);
        $this->em->flush();

        return $booking;
    }
}

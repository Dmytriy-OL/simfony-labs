<?php

namespace App\Service;

use App\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;

class RoomManager
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Створення номера
     */
    public function createRoom(array $data): Room
    {
        $room = new Room();

        $room->setRoomNumber($data['room_number']);
        $room->setHotelId($data['hotel_id']);
        $room->setRoomTypeId($data['room_type_id']);
        $room->setCapacity($data['capacity']);

        $this->em->persist($room);

        return $room;
    }

    /**
     * Оновлення номера
     */
    public function updateRoom(Room $room, array $data): Room
    {
        if (isset($data['room_number'])) {
            $room->setRoomNumber($data['room_number']);
        }
        if (isset($data['hotel_id'])) {
            $room->setHotelId($data['hotel_id']);
        }
        if (isset($data['room_type_id'])) {
            $room->setRoomTypeId($data['room_type_id']);
        }
        if (isset($data['capacity'])) {
            $room->setCapacity($data['capacity']);
        }

        return $room;
    }
}

<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\Column(length: 10)]
    private string $roomNumber;

    #[ORM\ManyToOne(targetEntity: Hotel::class)]
    private Hotel $hotel;

    #[ORM\ManyToOne(targetEntity: RoomType::class)]
    private RoomType $roomType;

    #[ORM\Column(type: 'integer')]
    private int $capacity;

    #[ORM\Column(enumType: 'string')]
    private string $status = 'available';
}

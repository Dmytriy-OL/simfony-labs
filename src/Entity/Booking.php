<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id;

    #[ORM\ManyToOne(targetEntity: Guest::class)]
    private Guest $guest;

    #[ORM\ManyToOne(targetEntity: Room::class)]
    private Room $room;

    #[ORM\Column(type: 'date')]
    private \DateTime $checkIn;

    #[ORM\Column(type: 'date')]
    private \DateTime $checkOut;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $totalPrice;

    #[ORM\Column(length: 20)]
    private string $status = 'pending';
}

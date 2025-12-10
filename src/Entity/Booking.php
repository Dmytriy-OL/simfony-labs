<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use App\State\BookingCreateProcessor;
use App\State\BookingUpdateProcessor;
use App\State\BookingCollectionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [

        // GET /bookings (фільтрація + пагінація)
        new GetCollection(
            provider: BookingCollectionProvider::class
        ),

        // GET /bookings/{id}
        new Get(),

        // POST /bookings
        new Post(
            processor: BookingCreateProcessor::class
        ),

        // PATCH /bookings/{id}
        new Patch(
            processor: BookingUpdateProcessor::class
        ),

        // DELETE /bookings/{id}
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'guest' => 'exact',
    'room' => 'exact',
])]
#[ApiFilter(DateFilter::class, properties: ['checkIn', 'checkOut'])]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private \DateTime $checkIn;

    #[ORM\Column(type: "datetime")]
    private \DateTime $checkOut;

    #[ORM\ManyToOne(targetEntity: Guest::class)]
    private $guest;

    #[ORM\ManyToOne(targetEntity: Room::class)]
    private $room;

    // ========== ГЕТЕРИ / СЕТЕРИ ==========
    public function getId(): ?int { return $this->id; }

    public function getCheckIn(): \DateTime { return $this->checkIn; }
    public function setCheckIn(\DateTime $date): self { $this->checkIn = $date; return $this; }

    public function getCheckOut(): \DateTime { return $this->checkOut; }
    public function setCheckOut(\DateTime $date): self { $this->checkOut = $date; return $this; }

    public function getGuest() { return $this->guest; }
    public function setGuest($guest): self { $this->guest = $guest; return $this; }

    public function getRoom() { return $this->room; }
    public function setRoom($room): self { $this->room = $room; return $this; }
}

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
use App\State\RoomCreateProcessor;
use App\State\RoomUpdateProcessor;
use App\State\RoomCollectionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [

        // GET /rooms (фільтри + пагінація)
        new GetCollection(
            provider: RoomCollectionProvider::class
        ),

        // GET /rooms/{id}
        new Get(),

        // POST /rooms
        new Post(
            processor: RoomCreateProcessor::class
        ),

        // PATCH /rooms/{id}
        new Patch(
            processor: RoomUpdateProcessor::class
        ),

        // DELETE /rooms/{id}
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'roomNumber' => 'partial',
    'hotel' => 'exact',
    'type' => 'exact',
])]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column]
    private int $roomNumber;

    #[ORM\Column]
    private int $capacity;

    #[ORM\ManyToOne(targetEntity: Hotel::class)]
    private $hotel;

    #[ORM\ManyToOne(targetEntity: RoomType::class)]
    private $type;

    // ---------------- GET/SET ----------------

    public function getId(): ?int { return $this->id; }

    public function getRoomNumber(): int { return $this->roomNumber; }
    public function setRoomNumber(int $num): self { $this->roomNumber = $num; return $this; }

    public function getCapacity(): int { return $this->capacity; }
    public function setCapacity(int $cap): self { $this->capacity = $cap; return $this; }

    public function getHotel() { return $this->hotel; }
    public function setHotel($hotel): self { $this->hotel = $hotel; return $this; }

    public function getType() { return $this->type; }
    public function setType($type): self { $this->type = $type; return $this; }
}

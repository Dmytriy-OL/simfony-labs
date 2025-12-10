<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\State\RoomTypeCollectionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        // GET /room-types (фільтри + пагінація)
        new GetCollection(
            provider: RoomTypeCollectionProvider::class
        ),

        // GET /room-types/{id}
        new Get(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial'
])]
class RoomType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private float $price;


    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): self { $this->description = $desc; return $this; }

    public function getPrice(): float { return $this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }
}

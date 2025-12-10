<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use App\State\HotelCollectionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        // GET /hotels (фільтри + пагінація)
        new GetCollection(
            provider: HotelCollectionProvider::class
        ),

        // GET /hotels/{id}
        new Get(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'city' => 'partial'
])]
class Hotel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $city;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    // ------------ GETTERS / SETTERS ----------
    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }

    public function getCity(): string { return $this->city; }
    public function setCity(string $city): self { $this->city = $city; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $desc): self { $this->description = $desc; return $this; }
}

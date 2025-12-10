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
use App\State\GuestCreateProcessor;
use App\State\GuestUpdateProcessor;
use App\State\GuestCollectionProvider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource(
    operations: [
        // GET /guests (пагінація + фільтри)
        new GetCollection(
            provider: GuestCollectionProvider::class
        ),

        // GET /guests/{id}
        new Get(),

        // POST /guests
        new Post(
            processor: GuestCreateProcessor::class
        ),

        // PATCH /guests/{id}
        new Patch(
            processor: GuestUpdateProcessor::class
        ),

        // DELETE /guests/{id}
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'fullName' => 'partial',
    'email' => 'partial'
])]
class Guest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $fullName;

    #[ORM\Column(length: 255, unique: true)]
    private string $email;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    // GETTERS / SETTERS

    public function getId(): ?int { return $this->id; }

    public function getFullName(): string { return $this->fullName; }
    public function setFullName(string $name): self { $this->fullName = $name; return $this; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): self { $this->phone = $phone; return $this; }
}

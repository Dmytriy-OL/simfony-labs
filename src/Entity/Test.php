<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV4;

#[ORM\Entity]
#[ApiResource(
    operations: [
        // ---- COLLECTION OPERATIONS ----
        new GetCollection(
            normalizationContext: ['groups' => ['get:collection:test']]
        ),
        new Post(
            denormalizationContext: ['groups' => ['post:collection:test']],
            normalizationContext: ['groups' => ['get:item:test']]
        ),

        // ---- ITEM OPERATIONS ----
        new Get(
            normalizationContext: ['groups' => ['get:item:test']]
        ),
        new Patch(
            denormalizationContext: ['groups' => ['patch:item:test']],
            normalizationContext: ['groups' => ['get:item:test']]
        ),
        new Delete()
    ]
)]
class Test
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['get:collection:test', 'get:item:test'])]
    private Uuid $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Groups([
        'get:collection:test',
        'get:item:test',
        'post:collection:test',
        'patch:item:test'
    ])]
    private string $name;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Groups([
        'get:collection:test',
        'get:item:test',
        'post:collection:test',
        'patch:item:test'
    ])]
    private string $price;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }
    public function setPrice(string $price): self
    {
        $this->price = $price;
        return $this;
    }
}

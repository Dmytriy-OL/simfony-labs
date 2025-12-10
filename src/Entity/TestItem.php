<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\State\TestCollectionProvider;
use App\State\TestCreateProcessor;
use App\State\TestDeleteProcessor;

#[ApiResource(
    operations: [
        new GetCollection(provider: TestCollectionProvider::class),
        new Get(provider: TestCollectionProvider::class),
        new Post(processor: TestCreateProcessor::class),
        new Delete(processor: TestDeleteProcessor::class),
    ]
)]
class TestItem
{
    public ?int $id = null;
    public ?string $name = null;
}

<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;

class TestCollectionProvider implements ProviderInterface
{
    private array $items = [
        1 => ['id' => 1, 'name' => 'Item 1'],
        2 => ['id' => 2, 'name' => 'Item 2'],
    ];

    public function provide(Operation $operation, array $context = [])
    {
        // GET collection
        if ($operation instanceof \ApiPlatform\Metadata\GetCollection) {
            return array_values($this->items);
        }

        // GET one
        $id = $context['uri_variables']['id'] ?? null;

        if ($id && isset($this->items[$id])) {
            return $this->items[$id];
        }

        return null;
    }
}

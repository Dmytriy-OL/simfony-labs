<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;

class TestDeleteProcessor implements ProcessorInterface
{
    public function process($data, Operation $operation, array $context = [])
    {
        $id = $context['uri_variables']['id'] ?? null;

        return [
            'message' => "Item with ID $id deleted"
        ];
    }
}

<?php

namespace App\State;

use ApiPlatform\State\ProcessorInterface;
use ApiPlatform\Metadata\Operation;

class TestCreateProcessor implements ProcessorInterface
{
    public function process($data, Operation $operation, array $context = [])
    {
        $payload = json_decode($context['request']->getContent(), true);

        return [
            'message' => 'Created successfully',
            'data' => $payload
        ];
    }
}

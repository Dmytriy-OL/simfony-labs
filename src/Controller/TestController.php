<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/get', name: 'test_get_all', methods: ['GET'])]
    public function getAll(Request $request): JsonResponse
    {
        $items = [
            ['id' => 1, 'name' => 'Item 1'],
            ['id' => 2, 'name' => 'Item 2'],
        ];

        return new JsonResponse($items);
    }

    #[Route('/post', name: 'test_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        return new JsonResponse([
            'message' => 'Created successfully',
            'data' => $data
        ]);
    }

    #[Route('/get/{id}', name: 'test_get_one', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        return new JsonResponse([
            'id' => $id,
            'name' => "Item with ID $id"
        ]);
    }

    #[Route('/delete/{id}', name: 'test_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        return new JsonResponse([
            'message' => "Item with ID $id deleted"
        ]);
    }
}

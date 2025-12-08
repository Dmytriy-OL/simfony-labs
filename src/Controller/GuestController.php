<?php

namespace App\Controller;

use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/guest')]
class GuestController
{
    public function __construct(private EntityManagerInterface $em) {}

    #[Route('/create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $guest = new Guest();
        $guest->setFullName($data['full_name']);
        $guest->setEmail($data['email']);
        $guest->setPhone($data['phone'] ?? null);

        $this->em->persist($guest);
        $this->em->flush();

        return new JsonResponse(['status' => 'guest created']);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function read(int $id): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], 404);
        }

        return new JsonResponse([
            'id' => $guest->getId(),
            'full_name' => $guest->getFullName(),
            'email' => $guest->getEmail(),
            'phone' => $guest->getPhone(),
        ]);
    }

    #[Route('/update/{id}', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $guest->setFullName($data['full_name'] ?? $guest->getFullName());
        $guest->setEmail($data['email'] ?? $guest->getEmail());
        $guest->setPhone($data['phone'] ?? $guest->getPhone());

        $this->em->flush();

        return new JsonResponse(['status' => 'guest updated']);
    }

    #[Route('/delete/{id}', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], 404);
        }

        $this->em->remove($guest);
        $this->em->flush();

        return new JsonResponse(['status' => 'guest deleted']);
    }
}

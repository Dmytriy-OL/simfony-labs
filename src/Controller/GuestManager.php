<?php

namespace App\Service;

use App\Entity\Guest;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

class GuestManager
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestChecker;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestChecker
    ) {
        $this->entityManager = $entityManager;
        $this->requestChecker = $requestChecker;
    }

    /**
     * CREATE Guest
     */
    public function createGuest(array $data): Guest
    {
        $guest = new Guest();

        $guest->setFullName($data['full_name']);
        $guest->setEmail($data['email']);
        $guest->setPhone($data['phone'] ?? null);

        // Валідація Entity через Symfony Constraints
        $this->requestChecker->validateRequestDataByConstraints($guest);

        $this->entityManager->persist($guest);
        $this->entityManager->flush();

        return $guest;
    }

    /**
     * UPDATE Guest
     */
    public function updateGuest(Guest $guest, array $data): Guest
    {
        foreach ($data as $key => $value) {

            // перетворення full_name → setFullName
            $method = 'set' . str_replace('_', '', ucwords($key, '_'));

            if (method_exists($guest, $method)) {
                $guest->$method($value);
            }
        }

        $this->requestChecker->validateRequestDataByConstraints($guest);

        $this->entityManager->flush();

        return $guest;
    }

    /**
     * DELETE Guest
     */
    public function deleteGuest(Guest $guest): void
    {
        $this->entityManager->remove($guest);
        $this->entityManager->flush();
    }
}

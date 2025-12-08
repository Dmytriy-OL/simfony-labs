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
        // Перевірка обов’язкових полів
        $this->requestChecker->check($data, ['full_name', 'email']);

        $guest = new Guest();
        $guest->setFullName($data['full_name']);
        $guest->setEmail($data['email']);
        $guest->setPhone($data['phone'] ?? null);

        // Валідація через Constraints (ЛР №3)
        $this->requestChecker->validateRequestDataByConstraints($guest);

        $this->entityManager->persist($guest);

        return $guest;
    }

    /**
     * UPDATE Guest
     */
    public function updateGuest(Guest $guest, array $data): Guest
    {
        foreach ($data as $key => $value) {

            // Формуємо назву сеттера: full_name → setFullName
            $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

            if (method_exists($guest, $method)) {
                $guest->$method($value);
            }
        }

        // Валідація оновленого об’єкта
        $this->requestChecker->validateRequestDataByConstraints($guest);

        return $guest;
    }

    /**
     * DELETE Guest
     */
    public function deleteGuest(Guest $guest): void
    {
        $this->entityManager->remove($guest);
    }
}

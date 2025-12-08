<?php
namespace App\Service;

use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;

class GuestService
{
    public function __construct(private EntityManagerInterface $em) {}

    public function createGuest(string $name, string $email, ?string $phone): Guest
    {
        $guest = new Guest();
        $guest->setFullName($name);
        $guest->setEmail($email);
        $guest->setPhone($phone);

        $this->em->persist($guest);
        $this->em->flush();

        return $guest;
    }
}

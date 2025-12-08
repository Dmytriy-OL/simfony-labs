<?php

namespace App\Controller;

use App\Entity\Hotel;
use App\Repository\HotelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/hotels')]
class HotelController extends AbstractController
{
    #[Route('/', name: 'hotel_list')]
    public function index(HotelRepository $repo)
    {
        return $this->json($repo->findAll());
    }

    #[Route('/create', name: 'hotel_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em)
    {
        $hotel = new Hotel();
        $hotel->setName($request->get('name'));
        $hotel->setAddress($request->get('address'));
        $hotel->setCity($request->get('city'));

        $em->persist($hotel);
        $em->flush();

        return $this->json(['message' => 'Hotel created']);
    }

    #[Route('/{id}/delete', name: 'hotel_delete', methods: ['DELETE'])]
    public function delete(Hotel $hotel, EntityManagerInterface $em)
    {
        $em->remove($hotel);
        $em->flush();

        return $this->json(['message' => 'Hotel deleted']);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/{nickname}', name: 'profile')]
    public function view(int|string $nickname, ManagerRegistry $doctrine): Response
    {
       $user = $doctrine->getRepository(User::class)->findOneByNickname($nickname);

        return $this->render('profile/view.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user
        ]);
    }

    #[Route('/profile/edit', name: 'edit_profile')]
    public function edit(): Response
    {
        return $this->render('profile/edit.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}

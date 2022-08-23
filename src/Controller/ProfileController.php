<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Subscription;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController
{
    #[Route('/profile/{nickname}', name: 'profile')]
    public function view(int|string $nickname, ManagerRegistry $doctrine, Subscription $subscription): Response
    {
        $user = $doctrine->getRepository(User::class)->findOneByNickname($nickname);
        $userSubscriptions = $subscription->getUserSubscriptions($user);
        $userFollowers = $subscription->getUserFollowers($user);

        return $this->render('profile/view.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'userSubscriptions' => $userSubscriptions,
            'userFollowers' => $userFollowers,
        ]);
    }

//    #[Route('/profile/1/edit', name: 'edit_profile')]
//    public function edit(): Response
//    {
//        return $this->render('profile/edit.html.twig', [
//            'controller_name' => 'ProfileController',
//        ]);
//    }

    #[Route('/profile/subscribe/{user}', name: 'subscribe')]
    public function subscribe(User $user , Subscription $subscription): Response
    {
        $subscription->followUser($this->getUser(), $user);

        return $this->redirectToRoute('profile', [
           'nickname'=> $user->getNickname()
        ]);
    }



    #[Route('/profile/unsubscribe/{user}', name: 'unsubscribe')]
    public function unsubscribe(User $user , Subscription $subscription): Response
    {
        $subscription->unfollowUser($this->getUser(), $user);

        return $this->redirectToRoute('profile', [
            'nickname'=> $user->getNickname()
        ]);
    }
}

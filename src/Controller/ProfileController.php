<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Service\Like;
use App\Service\Uploaders\AvatarPictureUploader;
use App\Service\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'my_profile')]
    public function myProfile(): Response
    {
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        return $this->redirectToRoute('profile', [
            'nickname' => $currentUser->getNickname()
        ]);
    }


    #[Route('/profile/{nickname}', name: 'profile')]
    public function view(int|string $nickname, ManagerRegistry $doctrine, Subscription $subscription, Like $like): Response
    {
        //Просматривать профили может только авторизированный пользователь
        $currentUser = $this->getUser();
        if (!$currentUser) {
            return $this->redirectToRoute('app_login');
        }

        $user = $doctrine->getRepository(User::class)->findOneByNickname($nickname);
        $userSubscriptionsIds = $subscription->getUserSubscriptionsIds($user);
        $userSubscriptions = $doctrine->getRepository(User::class)->findBy(['id' => $userSubscriptionsIds]);
        $userFollowersIds = $subscription->getUserFollowersIds($user);
        $userFollowers = $doctrine->getRepository(User::class)->findBy(['id' => $userFollowersIds]);
        $numberUserFollowers = $subscription->countUserFollowers($user);
        $numberUserSubscriptions = $subscription->countUserSubscriptions($user);
        $mutualSubscriptionsIds = $subscription->getMutualSubscriptionsIds($user);
        $mutualSubscriptions = $doctrine->getRepository(User::class)->findBy(['id' => $mutualSubscriptionsIds]);
        $userPosts = $doctrine->getRepository(Post::class)->findBy(['user' => $user], ['created_at'=>"DESC"]);
        $userLikes = $like->getUserLikesIds($this->getUser());

        return $this->render('profile/view.html.twig', [
            'user' => $user,
            'userSubscriptions' => $userSubscriptions,
            'userFollowers' => $userFollowers,
            'numberUserFollowers' => $numberUserFollowers,
            'numberUserSubscriptions' => $numberUserSubscriptions,
            'mutualSubscriptions' => $mutualSubscriptions,
            'currentUser' => $currentUser,
            'userPosts' => $userPosts,
            'userLikes' => $userLikes,
            'like' => $like
        ]);
    }

    #[Route('/profile/{user}/edit', name: 'edit_profile')]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, AvatarPictureUploader $avatarUploader): Response
    {
        //Пользователь может редактировать только свой профил
        if ($this->getUser() !== $user) {
            return $this->redirectToRoute('profile', [
                'nickname' => $user->getNickname()
            ]);
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pictureFile */
            $pictureFile = $form->get('picture')->getData();
            if ($pictureFile) {
                $pictureFileName = $avatarUploader->upload($pictureFile);
                // Remove previous picture from storage
                if ($user->getPicture()) {
                    $avatarUploader->delete($user->getPicture());
                }
                $user->setPicture($pictureFileName);
            }
            $user->setUpdatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('profile', [
                'nickname' => $user->getNickname()
            ]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/profile/subscribe/{user}', name: 'subscribe')]
    public function subscribe(User $user, Subscription $subscription): Response
    {
        if ($this->getUser() === $user) {
            return $this->redirectToRoute('profile', [
                'nickname' => $user->getNickname()
            ]);
        }
        $subscription->followUser($user);

        return $this->redirectToRoute('profile', [
            'nickname' => $user->getNickname()
        ]);
    }

    #[Route('/profile/unsubscribe/{user}', name: 'unsubscribe')]
    public function unsubscribe(User $user, Subscription $subscription): Response
    {
        if ($this->getUser() === $user) {
            return $this->redirectToRoute('profile', [
                'nickname' => $user->getNickname()
            ]);
        }
        $subscription->unfollowUser($user);

        return $this->redirectToRoute('profile', [
            'nickname' => $user->getNickname()
        ]);
    }
}

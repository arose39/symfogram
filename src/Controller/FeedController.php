<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\PostRepository;
use App\Service\Like;
use App\Service\PostCommentsCounter;
use App\Service\Subscription;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index( Like $like, PostRepository $postRepository, Subscription $subscription ): Response
    {
        $currentUser = $this->getUser();
        $userLikes = $like->getUserLikesIds($currentUser);
        $userSubscriptions = $subscription->getUserSubscriptionsIds($currentUser);
        //Пользователь в ленте видит так же и свои посты
        $userSubscriptions[] = $currentUser->getId();
        $feedPosts = $postRepository->findBy(['user' => $userSubscriptions], ['created_at'=>"DESC"]);

        return $this->render('feed/index.html.twig', [
            'feedPosts' => $feedPosts,
            'userLikes' => $userLikes,
            'like' => $like,
        ]);
    }
}

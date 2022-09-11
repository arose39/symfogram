<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\FeedRepository;
use App\Service\Like;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FeedController extends AbstractController
{
    #[Route('/feed', name: 'app_feed')]
    public function index(FeedRepository $feedRepository, Like $like): Response
    {
        $userLikes = $like->getUserLikesIds($this->getUser());
        $feedPosts = $feedRepository->findBy(['user_id' => $this->getUser()]);

        return $this->render('feed/index.html.twig', [
            'feedPosts' => $feedPosts,
            'userLikes' => $userLikes,
            'like'=> $like
        ]);
    }
}

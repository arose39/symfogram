<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Feed;
use App\Events\PostCreatedEvent;
use App\Events\PostDeletedEvent;
use App\Events\PostUpdatedEvent;
use App\Repository\FeedRepository;
use Doctrine\ORM\EntityManagerInterface;

class FeedService
{
    private EntityManagerInterface $entityManager;
    private FeedRepository $feedRepository;

    public function __construct(EntityManagerInterface $entityManager, FeedRepository $feedRepository)
    {
        $this->entityManager = $entityManager;
        $this->feedRepository = $feedRepository;
    }

    public function addToFeeds(PostCreatedEvent $event): void
    {
        $user = $event->getUser();
        $post = $event->getPost();
        $userFollowersIds = $event->getUserFollowersIds();
        foreach ($userFollowersIds as $followerId) {
            $feedItem = new Feed();

            $feedItem->setUserId((int) $followerId);
            $feedItem->setAuthorId($user->getId());
            $feedItem->setAuthorName($user->getFirstName() . " " . $user->getLastName());
            $feedItem->setAuthorNickname($user->getNickname());
            $feedItem->setAuthorPicture($user->getPicture());
            $feedItem->setPostId($post->getId());
            $feedItem->setPostFilename($post->getFilename());
            $feedItem->setPostDescription($post->getDescription());
            $feedItem->setPostCreatedAt($post->getCreatedAt());
            $this->entityManager->persist($feedItem);
            $this->entityManager->flush();
        }
    }

    public function updateOnFeeds(PostUpdatedEvent $event): void
    {
        $post = $event->getPost();
        $userFollowersIds = $event->getUserFollowersIds();
        foreach ($userFollowersIds as $followerId) {
            $feedItem = $this->feedRepository->findOneByPostAndUserIds($post->getId(), $followerId);
            $feedItem->setPostFilename($post->getFilename());
            $feedItem->setPostDescription($post->getDescription());
            $this->entityManager->persist($feedItem);
            $this->entityManager->flush();
        }
    }

    public function deleteFromFeeds(PostDeletedEvent $event): void
    {
        $post = $event->getPost();
        $this->feedRepository->deleteFeedItemsByPostId($post->getId());
    }
}
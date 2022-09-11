<?php

namespace App\Service;

use App\Entity\Feed;
use App\Events\PostCreatedEvent;
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
            $feedItem->setUserId($followerId);
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

    public function updateOnFeeds(PostUpdatedEvent $event)
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
}
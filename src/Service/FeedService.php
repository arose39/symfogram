<?php

namespace App\Service;

use App\Entity\Feed;
use App\Events\PostCreatedEvent;
use Doctrine\ORM\EntityManagerInterface;

class FeedService
{

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToFeeds(PostCreatedEvent $event)
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
}
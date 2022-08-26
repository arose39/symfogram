<?php

namespace App\Listeners;

use App\Events\PostCreatedEvent;
use App\Service\FeedService;

class PostCreatedListener
{
    private $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function onPostCreated(PostCreatedEvent $event)
    {
        $this->feedService->addToFeeds($event);
    }
}
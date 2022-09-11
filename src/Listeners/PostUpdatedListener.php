<?php

namespace App\Listeners;

use App\Events\PostCreatedEvent;
use App\Events\PostUpdatedEvent;
use App\Service\FeedService;

class PostUpdatedListener
{
    private $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function onPostUpdated(PostUpdatedEvent $event)
    {
        $this->feedService->updateOnFeeds($event);
    }
}
<?php declare(strict_types=1);

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

    public function onPostCreated(PostCreatedEvent $event): void
    {
        $this->feedService->addToFeeds($event);
    }
}
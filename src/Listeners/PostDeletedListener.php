<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\PostDeletedEvent;
use App\Service\FeedService;

class PostDeletedListener
{
    private FeedService $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function onPostDeleted(PostDeletedEvent $event): void
    {
        $this->feedService->deleteFromFeeds($event);
    }
}
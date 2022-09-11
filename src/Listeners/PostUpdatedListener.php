<?php declare(strict_types=1);

namespace App\Listeners;

use App\Events\PostUpdatedEvent;
use App\Service\FeedService;

class PostUpdatedListener
{
    private $feedService;

    public function __construct(FeedService $feedService)
    {
        $this->feedService = $feedService;
    }

    public function onPostUpdated(PostUpdatedEvent $event): void
    {
        $this->feedService->updateOnFeeds($event);
    }
}
<?php

namespace App\Events;

use App\Entity\Post;
use App\Entity\User;
use phpDocumentor\Reflection\Types\Collection;
use Symfony\Contracts\EventDispatcher\Event;

class PostUpdatedEvent extends Event
{
    private User $user;
    private Post $post;
    private array $userFollowersIds;


    public function __construct( Post $post, array $userFollowersIds)
    {
        $this->post = $post;
        $this->userFollowersIds = $userFollowersIds;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPost(): Post
    {
        return $this->post;
    }

    public function getUserFollowersIds(): array
    {
        return $this->userFollowersIds;
    }
}

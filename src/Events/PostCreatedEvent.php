<?php declare(strict_types=1);

namespace App\Events;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class PostCreatedEvent extends Event
{
    private User $user;
    private Post $post;
    private array $userFollowersIds;


    public function __construct(User $user, Post $post, array $userFollowersIds)
    {
        $this->user = $user;
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

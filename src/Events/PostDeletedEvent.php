<?php declare(strict_types=1);

namespace App\Events;

use App\Entity\Post;
use Symfony\Contracts\EventDispatcher\Event;

class PostDeletedEvent extends Event
{
    private Post $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}

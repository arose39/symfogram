<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Post;
use App\Entity\User;
use Predis\Client;
use Symfony\Component\Security\Core\Security;

class Like
{
    public function __construct(private Client $redis, private Security $security)
    {
    }

    public function likePost(Post $post): void
    {
        $this->redis->sadd("post:" . $post->getId() . ":likes", $this->security->getUser()->getId());
        $this->redis->sadd("user:" . $this->security->getUser()->getId() . ":likes", $post->getId());
    }

    public function unlikePost(Post $post): void
    {
        $this->redis->srem("post:" . $post->getId() . ":likes", $this->security->getUser()->getId());
        $this->redis->srem("user:" . $this->security->getUser()->getId() . ":likes", $post->getId());
    }


    public function getUserLikesIds(User $user): array
    {
        $key = "user:" . $user->getId() . ":likes";

        return $this->redis->smembers($key);
    }

    public function getPostLikersIds(Post $post): array
    {
        $key = "post:" . $post->getId() . ":likes";

        return $this->redis->smembers($key);
    }

    public function countPostLikes(Post $post): int
    {
        $key = "post:" . $post->getId() . ":likes";

        return $this->redis->scard($key);
    }

    public function countPostLikesById(int $postId): int
    {
        $key = "post:" . $postId . ":likes";

        return $this->redis->scard($key);
    }
}

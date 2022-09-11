<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Predis\Client;
use Symfony\Component\Security\Core\Security;

class Subscription
{
    public function __construct(private Client $redis, private Security $security)
    {
    }

    public function followUser(User $user): void
    {
        $this->redis->sadd("user:" . $this->security->getUser()->getId() . ":subscriptions", $user->getId());
        $this->redis->sadd("user:" . $user->getId() . ":followers", $this->security->getUser()->getId());
    }

    public function unfollowUser(User $user): void
    {
        $this->redis->srem("user:" . $this->security->getUser()->getId() . ":subscriptions", $user->getId());
        $this->redis->srem("user:" . $user->getId() . ":followers", $this->security->getUser()->getId());
    }

    public function getUserSubscriptionsIds(User $user): array
    {
        $key = "user:" . $user->getId() . ":subscriptions";

        return $this->redis->smembers($key);
    }

    public function getUserFollowersIds(User $user): array
    {
        $key = "user:" . $user->getId() . ":followers";

        return $this->redis->smembers($key);
    }

    public function countUserSubscriptions(User $user): int
    {
        $key = "user:" . $user->getId() . ":subscriptions";

        return $this->redis->scard($key);
    }

    public function countUserFollowers(User $user): int
    {
        $key = "user:" . $user->getId() . ":followers";

        return $this->redis->scard($key);
    }

    public function getMutualSubscriptionsIds(User $user): array
    {
        $key1 = "user:" . $this->security->getUser()->getId() . ":subscriptions";
        $key2 = "user:" . $user->getId() . ":followers";

        return $this->redis->sinter([$key1, $key2]);
    }
}
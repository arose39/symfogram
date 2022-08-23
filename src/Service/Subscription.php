<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Predis\Client;

class Subscription
{
    private Client $redis;

    public function __construct()
    {
        $this->redis = new Client($_ENV['REDIS_URL']);
    }

    public function followUser(User $subscriber, User $followedUser): void
    {
        $this->redis->sadd("user:" . $subscriber->getId() . ":subscriptions", $followedUser->getId());
        $this->redis->sadd("user:" . $followedUser->getId() . ":followers", $subscriber->getId());
    }

    public function unfollowUser(User $subscriber, User $followedUser): void
    {
        $this->redis->srem("user:" . $subscriber->getId() . ":subscriptions", $followedUser->getId());
        $this->redis->srem("user:" . $followedUser->getId() . ":followers", $subscriber->getId());
    }


    public function getUserSubscriptions(User $user): array
    {
        $key = "user:" . $user->getId() . ":subscriptions";

        return $this->redis->smembers($key);
    }

    public function getUserFollowers(User $user): array
    {
        $key = "user:" . $user->getId() . ":followers";

        return $this->redis->smembers($key);
    }
}
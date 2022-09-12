<?php declare(strict_types=1);

namespace App\Service;

use App\Repository\CommentRepository;

class PostCommentsCounter
{
    public function __construct(private CommentRepository $commentRepository)
    {
    }

    public function count($postId): int
    {
        return $this->commentRepository->count(['post' => $postId]);
    }
}
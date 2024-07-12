<?php

namespace App\Services;

use App\Models\Comment;
use App\Repositories\CommentRepositoryInterface;

class CommentService
{
    private CommentRepositoryInterface $commentRepository;

    public function __construct(
        CommentRepositoryInterface $commentRepository
    )
    {
        $this->commentRepository = $commentRepository;
    }

    public function getCommentsByArticleId(string $articleId): array
    {
        return $this->commentRepository->getCommentsByArticleId($articleId);
    }

    public function likeComment(string $id): void
    {
        $this->commentRepository->incrementCommentLikes($id);
    }

    public function getArticleIdByCommentId(string $commentId): string
    {
        return $this->commentRepository->getArticleIdByCommentId($commentId);
    }

    public function insertComment(Comment $comment): void
    {
        $this->commentRepository->insertComment($comment);
    }

    public function deleteComment(string $id): void
    {
        $this->commentRepository->deleteComment($id);
    }
}
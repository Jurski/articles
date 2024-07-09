<?php

namespace App\Repositories;

use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function getCommentsByArticleId(string $articleId): array;

    public function incrementCommentLikes(string $id): void;

    public function getArticleIdByCommentId(string $commentId): string;

    public function insertComment(Comment $comment): void;

    public function deleteComment(string $id): void;
}
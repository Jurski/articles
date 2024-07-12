<?php

namespace App\Controllers;

use App\Models\Comment;
use App\RedirectResponse;
use App\Services\CommentService;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class CommentController
{
    private CommentService $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    public function likeComment(string $id): RedirectResponse
    {
        $this->commentService->likeComment($id);

        $articleId = $this->commentService->getArticleIdByCommentId($id);

        return new RedirectResponse(
            '/articles/' . $articleId . '#' . $id,
            'Comment liked!',
            'success'
        );
    }

    public function storeComment(string $articleId): RedirectResponse
    {
        $authorValidator = v::alnum(' ')->length(1, 80)->notEmpty();
        $contentValidator = v::length(1, 500)->notEmpty();

        $author = $_POST['author'];
        $content = $_POST['content'];

        if (
            $authorValidator->validate($author) &&
            $contentValidator->validate($content)
        ) {

            $comment = new Comment(
                Uuid::uuid4()->toString(),
                $articleId,
                $_POST['author'],
                $_POST['content'],
                Carbon::now('UTC')
            );

            $this->commentService->insertComment($comment);

            return new RedirectResponse(
                '/articles/' . $articleId,
                'Comment added!',
                'success'
            );
        } else {
            return new RedirectResponse(
                '/articles/' . $articleId . '#form',
                'Validation error!',
                'danger',
                [
                    'author' => $author,
                    'content' => $content,
                ]
            );
        }
    }

    public function deleteComment(string $id): RedirectResponse
    {
        $articleId = $this->commentService->getArticleIdByCommentId($id);

        $this->commentService->deleteComment($id);

        return new RedirectResponse(
            '/articles/' . $articleId,
            'Comment deleted!',
            'success'
        );
    }
}
<?php

namespace App\Repositories;

use App\Models\Comment;
use Carbon\Carbon;
use DateTimeInterface;
use Medoo\Medoo;
use Psr\Log\LoggerInterface;


class SqliteCommentRepository implements CommentRepositoryInterface
{
    private Medoo $database;
    private LoggerInterface $logger;
    private LikeRepositoryInterface $likeRepository;

    public function __construct(
        Medoo                   $database,
        LoggerInterface         $logger,
        LikeRepositoryInterface $likeRepository
    )
    {
        $this->database = $database;
        $this->logger = $logger;
        $this->likeRepository = $likeRepository;
    }

    public function getCommentsByArticleId(string $articleId): array
    {
        $response = $this->database->select('comments', [
            'id',
            'article_id',
            'author',
            'content',
            'created_at',
            'likes'
        ], ['article_id' => $articleId]);

        if (!$response) {
            return [];
        }

        $comments = [];
        foreach ($response as $comment) {
            $createdAt = Carbon::parse($comment["created_at"], 'UTC');
            $comments[] = new Comment(
                $comment['id'],
                $comment['article_id'],
                $comment['author'],
                $comment['content'],
                $createdAt,
                $comment['likes']
            );
        }
        return $comments;
    }

    public function incrementCommentLikes(string $id): void
    {
        $response = $this->database->get('comments', ['likes'], ['id' => $id]);

        $newLikesCount = $response['likes'] + 1;

        $this->likeRepository->insertLike($id, 'comment');

        $this->database->update('comments', ['likes' => $newLikesCount], ['id' => $id]);
    }

    public function getArticleIdByCommentId(string $commentId): string
    {
        $response = $this->database->get('comments', ['article_id'], ['id' => $commentId]);
        return $response['article_id'];
    }

    public function insertComment(Comment $comment): void
    {
        $response = $this->database->insert('comments', [
            'id' => $comment->getId(),
            'article_id' => $comment->getArticleId(),
            'author' => $comment->getAuthor(),
            'content' => $comment->getContent(),
            'created_at' => $comment->getCreatedAt()->format(DateTimeInterface::ATOM),
            'likes' => $comment->getLikes()
        ]);

        if ($response) {
            $this->logger->info('Inserted a comment for article ' . $comment->getArticleId() . '!');
        } else {
            $this->logger->error('Couldnt insert a comment for article ' . $comment->getArticleId() . '!');
        }
    }

    public function deleteComment(string $id): void
    {
        $response = $this->database->delete('comments', ['id' => $id]);

        if ($response) {
            $this->logger->info('Deleted comment ' . $id);
        } else {
            $this->logger->error('Failed to delete comment ' . $id);
        }
    }
}
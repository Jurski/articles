<?php

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;
use DateTimeInterface;
use Medoo\Medoo;
use Psr\Log\LoggerInterface;


class SqliteArticleRepository implements ArticleRepositoryInterface
{
    private Medoo $database; //TODO: Coupling here
    private LoggerInterface $logger;

    public function __construct(Medoo $database, LoggerInterface $logger)
    {
        $this->database = $database;
        $this->logger = $logger;
    }


    public function getAll(): array
    {
        $response = $this->database->select('articles', [
            'id',
            'title',
            'content',
            'author',
            'created_at',
            'updated_at'
        ]);

        if (!$response) {
            return [];
        }

        $articles = [];
        foreach ($response as $article) {

            $articles[] = new Article(
                $article['id'],
                $article['title'],
                $article['content'],
                $article['author'] ?? 'Anonymous',
                Carbon::parse($article['created_at']),
                $article['updated_at'] ? Carbon::parse($article['updated_at'], 'UTC') : null
            );
        }
        return $articles;
    }

    public function getById(string $id): Article
    {
        $response = $this->database->get('articles', ['id', 'title', 'content', 'author', 'created_at', 'updated_at'], ['id' => $id]);

        if (!$response) {
            $this->logger->error('Article' . $id . ' not found');
        }

        return new Article(
            $response['id'],
            $response['title'],
            $response['content'],
            $response['author'] ?? 'Anonymous',
            Carbon::parse($response['created_at'], 'UTC'),
            $response['updated_at'] ? Carbon::parse($response['updated_at'], 'UTC') : null
        );
    }

    public function insert(Article $article): void
    {
        $author = $article->getAuthor() ?: 'Anonymous';
        $response = $this->database->insert('articles', [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'author' => $author,
            'created_at' => $article->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => null
        ]);

        if ($response) {
            $this->logger->info('Inserted a record!');
        } else {
            $this->logger->error('Failed to insert record!');
        }
    }

    public function update(Article $article): void
    {
        $id = $article->getId();
        $author = $article->getAuthor() ?: 'Anonymous';

        $response = $this->database->update('articles', [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'updated_at' => $article->getUpdatedAt()->format(DateTimeInterface::ATOM),
            'author' => $author
        ], ['id' => $id]);

        if ($response->rowCount() > 0) {
            $this->logger->info('Updated a record!');
        } else {
            $this->logger->error('Failed to update record!');
        }
    }

    public function delete(string $id): void
    {
        $response = $this->database->delete('articles', ['id' => $id]);

        if ($response) {
            $this->logger->info('Deleted a record!');
        } else {
            $this->logger->error('Failed to delete record!');
        }
    }
}
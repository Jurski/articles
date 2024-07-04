<?php

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;
use DateTimeInterface;
use Medoo\Medoo;


class SqliteArticleRepository implements ArticleRepositoryInterface {
    private Medoo $database;

    public function __construct(Medoo $database) {
        $this->database = $database;
    }


    public function getAll(): array
    {
        //TODO: Exception, maybe will need id
        return $this->database->select('articles',['title', 'content','author', 'created_at', 'updated_at']);
    }

    public function getById(string $id): Article
    {

        // TODO:EXCEPTION
        $response = $this->database->get('articles',['id','title', 'content','author', 'created_at', 'updated_at'], ['id' => $id]);
        return new Article(
            $response['id'],
            $response['title'],
            $response['content'],
            $response['author'],
            Carbon::parse($response['created_at'], 'UTC'),
            $response['updated_at'] ? Carbon::parse($response['updated_at'], 'UTC') : null
        );
    }

    public function insert(Article $article): void
    {
        // TODO:EXCEPTIONS
        $this->database->insert('articles', [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'author' => $article->getAuthor(),
            'created_at' => $article->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => null
        ]);
    }

    public function update(Article $article): void
    {
        // TODO: Exception
        $id = $article->getId();
        $this->database->update('articles', [
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'updated_at' => $article->getUpdatedAt()->format(DateTimeInterface::ATOM)
        ], ['id' => $id]);
    }

    public function delete(string $id): void
    {
        // TODO: Exception
        $this->database->delete('articles', ['id' => $id]);
    }
}
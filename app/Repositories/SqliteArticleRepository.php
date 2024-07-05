<?php

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;
use DateTimeInterface;
use Medoo\Medoo;


class SqliteArticleRepository implements ArticleRepositoryInterface {
    private Medoo $database;

    public function __construct() {
        $this->database = new Medoo([
            'type' => 'sqlite',
            'database' => 'storage/database.sqlite'
        ]);
    }


    public function getAll(): array
    {
        //TODO: Exception, maybe will need id
        $response = $this->database->select('articles',[
            'id',
            'title',
            'content',
            'author',
            'created_at',
            'updated_at'
        ]);

        $articles = [];
        foreach($response as $article){

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

        // TODO:EXCEPTION
        $response = $this->database->get('articles',['id','title', 'content','author', 'created_at', 'updated_at'], ['id' => $id]);
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
        // TODO:EXCEPTIONS
        $author = $article->getAuthor() ?: 'Anonymous';
        $this->database->insert('articles', [
            'id' => $article->getId(),
            'title' => $article->getTitle(),
            'content' => $article->getContent(),
            'author' => $author,
            'created_at' => $article->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updated_at' => null
        ]);
    }

    public function update(Article $article): void
    {
        try {
            $id = $article->getId();
            if ($id === null) {
                throw new \InvalidArgumentException('Article ID is required for update.');
            }

            $author = $article->getAuthor() ?: 'Anonymous';

            $this->database->update('articles', [
                'title' => $article->getTitle(),
                'content' => $article->getContent(),
                'updated_at' => $article->getUpdatedAt()->format(DateTimeInterface::ATOM),
                'author' => $article->getAuthor()
            ], ['id' => $id]);
        } catch (\Exception $e) {
            // Log the error or handle it as needed
            throw new \RuntimeException('Failed to update the article.', 0, $e);
        }
    }

    public function delete(string $id): void
    {
        // TODO: Exception
        $this->database->delete('articles', ['id' => $id]);
    }
}
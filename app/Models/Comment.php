<?php

namespace App\Models;

use Carbon\Carbon;

class Comment
{
    private string $id;
    private string $articleId;
    private string $author;
    private string $content;
    private Carbon $createdAt;
    private int $likes;


    public function __construct(
        string $id,
        string $articleId,
        string $author,
        string $content,
        Carbon $createdAt,
        int    $likes = 0
    )
    {
        $this->id = $id;
        $this->articleId = $articleId;
        $this->author = $author;
        $this->content = $content;
        $this->createdAt = $createdAt;
        $this->likes = $likes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getArticleId(): string
    {
        return $this->articleId;
    }


    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }
}
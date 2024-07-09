<?php

namespace App\Models;

use Carbon\Carbon;

class Article
{
    private string $id;
    private string $title;
    private string $content;
    private string $author;
    private Carbon $createdAt;
    private ?Carbon $updatedAt;
    private int $likes;


    public function __construct(
        string $id,
        string $title,
        string $content,
        string $author,
        Carbon $createdAt,
        Carbon $updatedAt = null,
        int    $likes = 0
    )
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->likes = $likes;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }


    public function setAuthor(string $author): void
    {
        $this->author = $author;
    }

    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }

    public function setUpdatedAt(Carbon $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
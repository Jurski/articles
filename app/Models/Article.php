<?php

namespace App\Models;

use Carbon\Carbon;

class Article {
    private string $id;
    private string $title;
    private string $content;
    private string $author;
    private Carbon $createdAt;
    private ?Carbon $updatedAt;


    public function __construct(
        string $id,
        string $title,
        string $content,
        string $author,
        Carbon $createdAt,
        Carbon $updatedAt = null

    ) {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->author = $author;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getAuthor(): string {
        return $this->author;
    }

    public function getCreatedAt(): Carbon {
        return $this->createdAt;
    }

    public function getUpdatedAt(): Carbon {
        return $this->updatedAt;
    }

    public function setTitle(string $title): void {
        $this->title = $title;
    }

    public function setContent(string $content): void {
        $this->content = $content;
    }

    public function setUpdatedAt(Carbon $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }
}
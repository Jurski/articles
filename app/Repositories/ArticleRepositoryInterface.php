<?php

namespace App\Repositories;

use App\Models\Article;

interface ArticleRepositoryInterface {
    public function getAll(): array;
    public function getById(string $id): Article;
    public function insert(Article $article): void;
    public function update(Article $article): void;
    public function delete(string $id): void;
}
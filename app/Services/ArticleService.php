<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Comment;
use App\Repositories\ArticleRepositoryInterface;

class ArticleService
{
    private ArticleRepositoryInterface $articleRepository;

    public function __construct(ArticleRepositoryInterface $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function show(string $id): Article
    {
        return $this->articleRepository->getById($id);
    }

    public function index(): array
    {
        return $this->articleRepository->getAll();
    }

    public function insert(Article $article): void
    {
        $this->articleRepository->insert($article);
    }

    public function update(Article $article): void
    {
        $this->articleRepository->update($article);
    }

    public function delete(string $id): void
    {
        $this->articleRepository->delete($id);
    }

    public function find(string $id): Article
    {
        return $this->articleRepository->getById($id);
    }

    public function likeArticle(string $id):void {
        $this->articleRepository->incrementArticleLikes($id);
    }

    public function getCommentsByArticleId(string $articleId): array {
        return $this->articleRepository->getCommentsByArticleId($articleId);
    }

    public function likeComment(string $id):void {
        $this->articleRepository->incrementCommentLikes($id);
    }

    public function getArticleIdByCommentId(string $commentId): string {
        return $this->articleRepository->getArticleIdByCommentId($commentId);
    }

    public function insertComment(Comment $comment): void {
        $this->articleRepository->insertComment($comment);
    }

    public function deleteComment(string $id): void {
        $this->articleRepository->deleteComment($id);
    }
}
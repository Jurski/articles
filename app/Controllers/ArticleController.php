<?php

namespace App\Controllers;

use App\Models\Article;
use App\Models\Comment;
use App\RedirectResponse;
use App\Response;
use App\Services\ArticleService;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;

session_start();

class ArticleController
{
    private ArticleService $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(): Response
    {
        $articles = $this->articleService->index();
        return new Response(
            'index.twig',
            ['articles' => $articles]
        );
    }

    public function show(string $id): Response
    {
        $article = $this->articleService->show($id);
        $comments = $this->articleService->getCommentsByArticleId($id);
        return new Response(
            'show.twig',
            [
                'article' => $article,
                'comments' => $comments
            ]
        );
    }

    public function createArticle(): Response
    {
        return new Response(
            'create-article.twig',
        );
    }

    public function storeArticle(): RedirectResponse
    {
        $article = new Article(
            Uuid::uuid4()->toString(),
            $_POST['title'],
            $_POST['content'],
            $_POST['author'],
            Carbon::now('UTC')
        );
        $this->articleService->insert($article);

        return new RedirectResponse(
            '/index',
            'Article added!',
            'success'
        );
    }

    public function editArticle(string $id): Response
    {
        $article = $this->articleService->find($id);
        return new Response(
            'edit-article.twig',
            ['article' => $article]
        );
    }

    public function storeEditArticle(string $id): RedirectResponse
    {

        $existingArticle = $this->articleService->find($id);

        $existingArticle->setTitle($_POST['title']);
        $existingArticle->setContent($_POST['content']);
        $existingArticle->setAuthor($_POST['author']);
        $existingArticle->setUpdatedAt(Carbon::now('UTC'));

        $this->articleService->update($existingArticle);

        return new RedirectResponse(
            '/index',
            'Article updated!',
            'success'
        );
    }

    public function deleteArticle(string $id): RedirectResponse
    {
        $this->articleService->delete($id);

        return new RedirectResponse(
            '/index',
            'Article deleted!',
            'success'
        );
    }

    public function likeArticle(string $id): RedirectResponse
    {
        $this->articleService->likeArticle($id);

        return new RedirectResponse(
            '/articles/' . $id,
            'Article liked!',
            'success'
        );
    }

    public function likeComment(string $id): RedirectResponse
    {
        $this->articleService->likeComment($id);

        $articleId = $this->articleService->getArticleIdByCommentId($id);

        return new RedirectResponse(
            '/articles/' . $articleId . '#' . $id,
            'Comment liked!',
            'success'
        );
    }

    public function storeComment(string $articleId): RedirectResponse
    {
        $comment = new Comment(
            Uuid::uuid4()->toString(),
            $articleId,
            $_POST['author'],
            $_POST['content'],
            Carbon::now('UTC')
        );

        $this->articleService->insertComment($comment);

        return new RedirectResponse(
            '/articles/' . $articleId,
            'Comment added!',
            'success'
        );
    }

    public function deleteComment(string $id): RedirectResponse
    {
        $articleId = $this->articleService->getArticleIdByCommentId($id);

        $this->articleService->deleteComment($id);

        return new RedirectResponse(
            '/articles/' . $articleId,
            'Comment deleted!',
            'success'
        );
    }
}
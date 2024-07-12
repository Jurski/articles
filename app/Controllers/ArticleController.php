<?php

namespace App\Controllers;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use App\RedirectResponse;
use App\Response;
use App\Services\ArticleService;
use App\Services\CommentService;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Respect\Validation\Validator as v;

class ArticleController
{
    private ArticleService $articleService;
    private CommentService $commentService;

    public function __construct(
        ArticleService $articleService,
        CommentService $commentService
    )
    {
        $this->articleService = $articleService;
        $this->commentService = $commentService;
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
        try {
            $article = $this->articleService->show($id);
            $comments = $this->commentService->getCommentsByArticleId($id);
            return new Response(
                'show.twig',
                [
                    'article' => $article,
                    'comments' => $comments
                ]
            );
        } catch (ArticleNotFoundException $e) {
            return new Response(
                'error.twig',
                ['error' => $e->getMessage()]
            );
        }
    }

    public function createArticle(): Response
    {
        return new Response(
            'create-article.twig',
        );
    }

    public function storeArticle(): RedirectResponse
    {
        $authorValidator = v::alnum(' ');
        $titleValidator = v::alnum(' ')->length(1, 250)->notEmpty();
        $contentValidator = v::length(1, 1000)->notEmpty();

        $author = strlen($_POST['author']) === 0 ? 'Anonymous' : $_POST['author'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        if (
            $authorValidator->validate($author) &&
            $titleValidator->validate($title) &&
            $contentValidator->validate($content)
        ) {
            $article = new Article(
                Uuid::uuid4()->toString(),
                $title,
                $content,
                $author,
                Carbon::now('UTC')
            );
            $this->articleService->insert($article);

            return new RedirectResponse(
                '/index',
                'Article added!',
                'success'
            );
        } else {
            return new RedirectResponse(
                '/articles/new',
                'Validation error!',
                'danger',
                [
                    'author' => $author,
                    'title' => $title,
                    'content' => $content,
                ]
            );
        }
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
        $authorValidator = v::alnum(' ');
        $titleValidator = v::alnum(' ')->length(1, 250)->notEmpty();
        $contentValidator = v::length(1, 1000)->notEmpty();

        $author = strlen($_POST['author']) === 0 ? 'Anonymous' : $_POST['author'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        if (
            $authorValidator->validate($author) &&
            $titleValidator->validate($title) &&
            $contentValidator->validate($content)
        ) {
            $existingArticle = $this->articleService->find($id);

            $existingArticle->setTitle($title);
            $existingArticle->setContent($content);
            $existingArticle->setAuthor($author);
            $existingArticle->setUpdatedAt(Carbon::now('UTC'));

            $this->articleService->update($existingArticle);

            return new RedirectResponse(
                '/index',
                'Article updated!',
                'success'
            );
        } else {
            return new RedirectResponse(
                '/articles/' . $id . '/edit',
                'Validation error!',
                'danger',
                [
                    'author' => $author,
                    'title' => $title,
                    'content' => $content,
                ]
            );
        }
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
}
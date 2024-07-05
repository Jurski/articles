<?php

namespace App\Controllers;

use App\Models\Article;
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
        return new Response(
            'show.twig',
            ['article' => $article]
        );
    }

    public function createArticle(): Response
    {
        return new Response(
            'create-article.twig',
        );
    }

    public function storeArticle(): void
    {

        $article = new Article(
            Uuid::uuid4()->toString(),
            $_POST['title'],
            $_POST['content'],
            $_POST['author'],
            Carbon::now('UTC')
        );
        $this->articleService->insert($article);
        $_SESSION['flash_message'] = 'Article added!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /index');
    }

    public function editArticle(string $id): Response
    {
        $article = $this->articleService->find($id);
        return new Response(
            'edit-article.twig',
            ['article' => $article]
        );
    }

    public function storeEditArticle(string $id): void
    {

        $existingArticle = $this->articleService->find($id);
//        if ($existingArticle === null) {
//            // Handle the case where the article does not exist
//            throw new \Exception('Article not found.');
//        }

        // Update the article with new data from the POST request
        $existingArticle->setTitle($_POST['title']);
        $existingArticle->setContent($_POST['content']);
        $existingArticle->setAuthor($_POST['author']);
        $existingArticle->setUpdatedAt(Carbon::now('UTC'));

        $this->articleService->update($existingArticle);

        $_SESSION['flash_message'] = 'Article updated!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /index');
    }

    public function deleteArticle(string $id): void {
        $this->articleService->delete($id);
        $_SESSION['flash_message'] = 'Article deleted!';
        $_SESSION['flash_type'] = 'success';
        header('Location: /index');
    }
}
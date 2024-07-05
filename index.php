<?php

require "vendor/autoload.php";

use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\SqliteArticleRepository;
use Medoo\Medoo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

$flashMessage = isset($_SESSION["flash_message"]) ? $_SESSION["flash_message"] : null;
$flashType = isset($_SESSION["flash_type"]) ? $_SESSION["flash_type"] : null;

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler('storage/app.log', Logger::DEBUG));

$database = new Medoo([
    'type' => 'sqlite',
    'database' => 'storage/database.sqlite'
]);

$container = new DI\Container();
$container->set(
    ArticleRepositoryInterface::class,
    new SqliteArticleRepository($database, $logger)
);

if ($flashMessage !== null) {
    unset($_SESSION["flash_message"]);
    unset($_SESSION["flash_type"]);
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/index', ['App\Controllers\ArticleController', 'index']);
    $r->addRoute('GET', '/articles/new', ['App\Controllers\ArticleController', 'createArticle']);
    $r->addRoute('POST', '/articles', ['App\Controllers\ArticleController', 'storeArticle']);
    $r->addRoute('POST', '/articles/{id}', ['App\Controllers\ArticleController', 'storeEditArticle']);
    $r->addRoute('POST', '/articles/{id}/delete', ['App\Controllers\ArticleController', 'deleteArticle']);
    $r->addRoute('GET', '/articles/{id}', ['App\Controllers\ArticleController', 'show']);
    $r->addRoute('GET', '/articles/{id}/edit', ['App\Controllers\ArticleController', 'editArticle']);
    $r->addRoute('GET', '/404', ['App\Controllers\ErrorController', 'show']);
    $r->addRoute('GET', '/405', ['App\Controllers\ErrorController', 'show']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $_SESSION["flash_message"] = "Route not found";
        $_SESSION["flash_type"] = "danger";
        header("Location: /404");
        exit();
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $_SESSION["flash_message"] = "Method on this route not allowed!";
        $_SESSION["flash_type"] = "danger";
        header("Location: /405");
        exit();
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = $handler;

        $response = ($container->get($controller))->{$method}(...array_values($vars));

        $loader = new FilesystemLoader('templates');

        $twig = new Environment($loader);

        try {
            echo $twig->render($response->getTemplate(), [
                "data" => $response->getData(),
                "session" => [
                    "flash_message" => $flashMessage,
                    "flash_type" => $flashType,
                ]
            ]);
        } catch (\Twig\Error\LoaderError|\Twig\Error\SyntaxError|\Twig\Error\RuntimeError $e) {
            echo "Error occured while loading template: " . $e->getMessage();
            $logger->error($e->getMessage());
        }

        break;
}
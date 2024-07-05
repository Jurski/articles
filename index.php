<?php

require "vendor/autoload.php";

use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\SqliteArticleRepository;
use Carbon\Carbon;
use DI\ContainerBuilder;
use Medoo\Medoo;
use App\Models\Article;
use Ramsey\Uuid\Uuid;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use function DI\create;

session_start();

$flashMessage = isset($_SESSION["flash_message"]) ? $_SESSION["flash_message"] : null;
$flashType = isset($_SESSION["flash_type"]) ? $_SESSION["flash_type"] : null;

$builder = new ContainerBuilder();
$builder->addDefinitions(
    [
        ArticleRepositoryInterface::class => create(SqliteArticleRepository::class),
    ]
);

$container = $builder->build();

if ($flashMessage !== null) {
    unset($_SESSION["flash_message"]);
    unset($_SESSION["flash_type"]);
}

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
//    $r->addRoute('GET', '/', ['App\Controllers\CryptocurrencyController', 'index']);
//    $r->addRoute('GET', '/index', ['App\Controllers\CryptocurrencyController', 'index']);
//    $r->addRoute('GET', '/cryptocurrencies/{symbol}', ['App\Controllers\CryptocurrencyController', 'show']);
//    $r->addRoute('POST', '/cryptocurrencies/{symbol}/buy', ['App\Controllers\CryptocurrencyController', 'buy']);
//    $r->addRoute('POST', '/cryptocurrencies/{symbol}/sell', ['App\Controllers\CryptocurrencyController', 'sell']);
//    $r->addRoute('GET', '/transactions', ['App\Controllers\TransactionController', 'index']);
//    $r->addRoute('GET', '/wallet', ['App\Controllers\WalletController', 'show']);
//    $r->addRoute('GET', '/error', ['App\Controllers\ErrorController', 'show']);
    $r->addRoute('GET', '/index', ['App\Controllers\ArticleController', 'index']);
    $r->addRoute('GET', '/articles/new', ['App\Controllers\ArticleController', 'createArticle']);
    $r->addRoute('POST', '/articles', ['App\Controllers\ArticleController', 'storeArticle']);
    $r->addRoute('POST', '/articles/{id}', ['App\Controllers\ArticleController', 'storeEditArticle']);
    $r->addRoute('POST', '/articles/{id}/delete', ['App\Controllers\ArticleController', 'deleteArticle']);
    $r->addRoute('GET', '/articles/{id}', ['App\Controllers\ArticleController', 'show']);
    $r->addRoute('GET', '/articles/{id}/edit', ['App\Controllers\ArticleController', 'editArticle']);

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
        header("Location: /error");
        exit();
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        $_SESSION["flash_message"] = "Method on this route not allowed!";
        $_SESSION["flash_type"] = "danger";
        header("Location: /error");
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
        }

        break;
}

//$id = Uuid::uuid4()->toString();

//$repo = new SqliteArticleRepository($database);
//$repo->insert(new Article($id,'author now', 'Now wit hauthor','Test', Carbon::now('UTC')));


//var_dump($repo->getById('e409cca2-202e-44da-8dc1-840695854252'));

//var_dump($repo->getAll());
<?php

require "vendor/autoload.php";

use App\RedirectResponse;
use App\Repositories\ArticleRepositoryInterface;
use App\Repositories\SqliteArticleRepository;
use Medoo\Medoo;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

session_start();

$flashMessage = $_SESSION["flash_message"] ?? null;
$flashType = $_SESSION["flash_type"] ?? null;
if ($flashMessage !== null) {
    unset($_SESSION["flash_message"]);
    unset($_SESSION["flash_type"]);
}

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

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    $routes = include __DIR__ . "/routes.php";
    foreach ($routes as $route) {
        $r->addRoute($route[0], $route[1], $route[2]);
    }
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

        if ($response instanceof RedirectResponse) {
            $_SESSION["flash_message"] = $response->getFlashMessage();
            $_SESSION["flash_type"] = $response->getFlashType();
            header("Location: " . $response->getLocation());
            exit();
        } else {
            try {
                echo $twig->render($response->getTemplate(), [
                    "data" => $response->getData(),
                    "session" => [
                        "flash_message" => $flashMessage,
                        "flash_type" => $flashType,
                    ]
                ]);
            } catch (\Twig\Error\LoaderError|\Twig\Error\SyntaxError|\Twig\Error\RuntimeError $e) {
                $logger->error($e->getMessage());
                $_SESSION["flash_message"] = 'Failed to render page';
                $_SESSION["flash_type"] = "danger";
                header("Location: /error");
            }
        }
        break;
}
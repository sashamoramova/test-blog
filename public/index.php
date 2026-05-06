<?php

declare(strict_types=1);

use App\Controller\ArticleController;
use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Core\Database;
use App\Core\Router;
use App\Core\View;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;

$config = require dirname(__DIR__) . '/src/bootstrap.php';

$pdo = Database::connection($config['db']);

$view = new View(
    dirname(__DIR__) . '/templates',
    dirname(__DIR__) . '/templates_c',
    $config['app']['env'] !== 'production'
);

$categoryRepo = new CategoryRepository($pdo);
$articleRepo  = new ArticleRepository($pdo);

$router = new Router();

$router->get('/', function () use ($view, $categoryRepo, $articleRepo) {
    (new HomeController($view, $categoryRepo, $articleRepo))->index();
});

$router->get('/category/(\d+)', function (string $id) use ($view, $categoryRepo, $articleRepo, $config) {
    (new CategoryController($view, $categoryRepo, $articleRepo, $config['app']))->show((int) $id);
});

$router->get('/article/(\d+)', function (string $id) use ($view, $categoryRepo, $articleRepo, $config) {
    (new ArticleController($view, $categoryRepo, $articleRepo, $config['app']))->show((int) $id);
});

$router->notFound(function () use ($view) {
    http_response_code(404);
    $view->assign('title', '404 — страница не найдена')->display('404.tpl');
});

$router->dispatch($_SERVER['REQUEST_URI'] ?? '/');

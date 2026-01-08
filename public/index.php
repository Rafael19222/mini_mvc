<?php
require dirname(__DIR__) . '/vendor/autoload.php';
use Mini\Core\Router;
session_start();

$routes = [
    ['GET', '/', [Mini\Controllers\HomeController::class, 'index']],
    ['GET', '/users', [Mini\Controllers\HomeController::class, 'users']],
    ['GET', '/products', [Mini\Controllers\ProductController::class, 'index']],
    ['GET', '/product/{id}', [Mini\Controllers\ProductController::class, 'show']],
    ['GET', '/login', [Mini\Controllers\AuthController::class, 'login']],
    ['POST', '/login', [Mini\Controllers\AuthController::class, 'login']],
    ['GET', '/register', [Mini\Controllers\AuthController::class, 'register']],
    ['POST', '/register', [Mini\Controllers\AuthController::class, 'register']],
    ['GET', '/logout', [Mini\Controllers\AuthController::class, 'logout']],
    ['GET', '/cart', [Mini\Controllers\CartController::class, 'index']],
    ['POST', '/cart/add', [Mini\Controllers\CartController::class, 'add']],
    ['POST', '/cart/remove', [Mini\Controllers\CartController::class, 'remove']],
    ['POST', '/cart/clear', [Mini\Controllers\CartController::class, 'clear']],
];

$router = new Router($routes);
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);



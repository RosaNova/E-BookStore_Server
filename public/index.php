<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use App\Controllers\BookController;
use App\Routes\Router;

// Load .env
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$router = new Router();
$router->get('/books', [new BookController(),'index']);
$router->get('/books/{id}', [new BookController(), 'show']);
$router->post('/books', [new BookController(), 'save']);
$router->put('/books/{id}', [new BookController(), 'update']);
$router->delete('/books/{id}', [new BookController(), 'delete']);
$router->dispatch();
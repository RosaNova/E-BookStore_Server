<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use App\Controllers\BookController;
use App\Controllers\CustomerController;
use App\Controllers\AdminController;
use App\Controllers\BookCategoriesController;
use App\Routes\Router;

// Load .env
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Update this to your frontend URL
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

$router = new Router();

// Book API End-point
$router->get('/books', [new BookController(),'index']);
$router->get('/books/{id}', [new BookController(), 'show']);
$router->post('/books', [new BookController(), 'save']);
$router->put('/books/{id}', [new BookController(), 'update']);
$router->delete('/books/{id}', [new BookController(), 'delete']);
$router->get('/bookprice',[new BookController(), 'getBookPrice']);
$router->get('/books/countbook', [new BookController(), 'countBooks']);

// Customer API end-point : 
$router->get('/customers', [new CustomerController(), 'index']);
$router->get('/customers/{id}', [new CustomerController(), 'show']);
$router->post('/customers/register', [new CustomerController(), 'store']);
$router->post('/customers/login', [new CustomerController(), 'login']);
$router->put('/customers/{id}', [new CustomerController(), 'update']);
$router->delete('/customers/{id}', [new CustomerController(), 'delete']);
$router->post('/customers/forgot-password', [new CustomerController(), 'forgotPassword']);
$router->post('/customers/reset-password', [new CustomerController(), 'resetPassword']);
$router->put('/customers/logout', [new CustomerController(), 'logout']);
$router->get('/customers/count', [new CustomerController(), 'countCustomers']);

// Admin API end-point
// $router->get('/admin', [new AdminController(), 'index']);
// $router->get('/admin/{id}', [new AdminController(), 'show']);
$router->post('/admin', [new AdminController(), 'store']);
$router->get('/admin',[new AdminController(), 'login']);
// $router->put('/admin/{id}', [new AdminController(), 'update']);
// $router->delete('/admin/{id}', [new AdminController(), 'delete']);

$router->get("/bookcategory", [new BookCategoriesController(), 'index']);
$router->dispatch();

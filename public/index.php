<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\BookController;
use App\Middlewares\RequestMiddleware;

// --- Headers ---
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json; charset=UTF-8");



$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$requestMethod = $_SERVER['REQUEST_METHOD'];

// Map request to controller action
$controller = new BookController();


switch ($requestMethod) {
case 'POST':
    $middleware = new RequestMiddleware('POST');
    // Read JSON input from Postman
    $json = file_get_contents('php://input');
    $requestData = json_decode($json, true);
    // Required fields
    $required = ['title','author_id','category_id','price','stock','description','published_date'];

    if ($middleware->handle($requestData, $required)) {
        $success = $controller->create($requestData);

        if ($success) {
            http_response_code(201); 
            echo json_encode([
                'status' => 'success',
                'message' => 'Book created successfully ✅',
                'data' => $requestData
            ]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create book ❌'
            ]);
        }
    }
    break;

    case 'GET':
        $middleware = new RequestMiddleware('GET');
        if ($middleware->handle()) {
            // $controller->list(); // Example: list all books
        }
        break;

    case 'PUT':
        parse_str(file_get_contents('php://input'), $putData); // PHP PUT input
        $middleware = new RequestMiddleware('PUT');
        $required = ['id']; // require book ID to update
        if ($middleware->handle($putData, $required)) {
            // $controller->update($putData);
        }
        break;

    case 'DELETE':
        parse_str(file_get_contents('php://input'), $deleteData);
        $middleware = new RequestMiddleware('DELETE');
        $required = ['id']; // require book ID to delete
        if ($middleware->handle($deleteData, $required)) {
            // $controller->delete($deleteData['id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}



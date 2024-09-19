<?php
require __DIR__ . '/../vendor/autoload.php';


use App\Database;
use App\CORS;
use App\UserController;
use App\FoodController;
use App\ResidentController;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

CORS::setHeaders();

$database = new Database();
$pdo = $database->getConnection();

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestUri) {
    case '/login':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new UserController($pdo);
            $controller->login($data['username'] ?? '', $data['password'] ?? '');
        }
        break;
    case '/add-food':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new FoodController($pdo);
            $controller->addFood($data['name'] ?? '', $data['category'] ?? '', $data['iddsiLevel'] ?? '');
        }
        break;
    case '/add-resident':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new ResidentController($pdo);
            $controller->addResident($data['name'] ?? '', $data['iddsiLevel'] ?? '');
        }
        break;
	case '/assign-foods':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new UserController($pdo);
            $controller->assignFoodToResident($data['resident'] ?? '', $data['foods'] ?? '');
        }
        break;	
	case '/get-assign-foods':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new UserController($pdo);
            $controller->getAssignedFoods($data['resident'] ?? '');
        }
        break;
	case '/import-csv-foods':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $controller = new FoodController($pdo);
            $controller->importCSVFoods($data['csvData'] ?? '');
        }
        break;	
    case '/food-items':
        if ($requestMethod === 'GET') {
            $controller = new FoodController($pdo);
            $controller->getFoodItems();
        }
        break;
    case '/residents':
        if ($requestMethod === 'GET') {
            $controller = new ResidentController($pdo);
            $controller->getResidents();
        }
        break;
	case '/categories':
        if ($requestMethod === 'GET') {
            $controller = new FoodController($pdo);
            $controller->getFoodCategories();
        }
        break;	
	case '/levels':
        if ($requestMethod === 'GET') {
            $controller = new FoodController($pdo);
            $controller->getLevels();
        }
        break;	
    default:
        \App\Response::json(['error' => 'Endpoint not found'], 404);
        break;
}
?>
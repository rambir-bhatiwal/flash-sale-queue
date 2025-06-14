<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Scenario1/CartController.php';
require_once __DIR__ . '/../src/Scenario1/QueueProcessor.php';
require_once __DIR__ . '/../src/Scenario1/redis.php';

use Scenario1\CartController;
use Scenario1\QueueProcessor;

use Dotenv\Dotenv;

// Load .env variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new PDO("mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Simulated user session
$session = ['user_id' => rand(1000, 9999)];

// Create cart controller
$controller = new CartController($db, $session);

// Add to cart
$response = $controller->addToCart(1, 1); // product ID = 1
print_r($response);

// Process the queue (optional)
$processor = new QueueProcessor($db);
$processor->process(1);

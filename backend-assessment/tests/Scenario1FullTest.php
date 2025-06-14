<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Scenario1/CartController.php';
require_once __DIR__ . '/../src/Scenario1/QueueProcessor.php';
require_once __DIR__ . '/../src/Scenario1/redis.php';

use Dotenv\Dotenv;
use Scenario1\CartController;
use Scenario1\QueueProcessor;
// use PDO;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Setup PDO
$pdo = new PDO("mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}", $_ENV['DB_USER'], $_ENV['DB_PASS']);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Setup Redis
$redis = redis();
$stockKey = "flash_sale_stock:1";
// $redis->set($stockKey, 5);
// $redis->set($stockKey, 5, 3600); // Set stock with TTL of 1 hour
// $redis->set($stockKey, 5, ['nx', 'ex' => 3600]); // Set stock with TTL of 1 hour if not exists
// $redis->del($stockKey); // Clear any existing key
echo "✅ Redis connected.\n";
echo "✅ Redis cache stock: " . $redis->get($stockKey) . "\n";

// Step 1: Insert product if not exists
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = 1");
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    $pdo->exec("INSERT INTO products (id, name, stock) VALUES (1, 'Scenario1 Test Product', 5)");
    echo "✅ Test product created.\n";
} else {
    echo "✅ Test product exists.\n";
}

// Step 2: Add to cart
$userId = rand(1000, 9999);
$controller = new CartController($pdo, ['user_id' => $userId]);
$response = $controller->addToCart(1, 1);

if ($response['success']) {
    echo "✅ Cart added to queue: User ID $userId, Queue Position: {$response['queue_position']}\n";
} else {
    echo "❌ Failed to add to cart: {$response['message']}\n";
}

// Step 3: Confirm Redis cart entry
$redisKey = "cart_item:$userId:1";
if ($redis->exists($redisKey)) {
    echo "✅ Redis cart TTL set: $redisKey\n";
} else {
    echo "❌ Redis cart TTL missing for $redisKey\n";
}

// Step 4: Process queue
$processor = new QueueProcessor($pdo);
$processor->process(1);

// Step 5: Check if item stored in cart_items
$stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ? AND product_id = ?");
$stmt->execute([$userId, 1]);
$cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

if ($cartItem) {
    echo "✅ Cart item successfully saved to database.\n";
} else {
    echo "❌ Cart item not saved.\n";
}

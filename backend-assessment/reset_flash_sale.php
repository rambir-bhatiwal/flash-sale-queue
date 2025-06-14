<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Scenario1/redis.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Reset stock in MySQL
try {
    $pdo = new PDO(
        "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_NAME']}",
        $_ENV['DB_USER'],
        $_ENV['DB_PASS']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Reset product stock to 5
    $pdo->exec("UPDATE products SET stock = 5 WHERE id = 1");
    echo "✅ Product stock reset to 5.\n";
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
}

// Connect to Redis
$redis = redis();

echo "🔁 Resetting Redis keys...\n";

// Delete Redis stock cache
$redis->del('flash_sale_stock:1');

// Delete Redis queue
$redis->del('flash_sale_queue:1');

// Delete all cart items for product ID 1
$cartKeys = $redis->keys('cart_item:*:1');
foreach ($cartKeys as $key) {
    $redis->del($key);
}

echo "✅ Redis flash sale keys cleared.\n";

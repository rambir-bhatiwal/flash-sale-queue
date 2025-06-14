<?php
namespace Scenario1;

require_once 'redis.php';
use PDO;
use Scenario1\redis;

class CartController
{
    protected $db;
    protected $redis;
    protected $session;

    public function __construct($db, $session)
    {
        $this->db = $db;
        $this->session = $session;
        $this->redis = redis(); // from redis.php
    }

    public function addToCart($productId, $quantity)
    {
        $userId = $this->session['user_id'];
        $queueKey = "flash_sale_queue:$productId";
        $stockKey = "flash_sale_stock:$productId";

        // Initialize stock in Redis if not present
        if (!$this->redis->exists($stockKey)) {
            // $product = $this->db->query("SELECT stock FROM products WHERE id = ?", [$productId])->fetch();
            $stmt = $this->db->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$product) {
                return ['success' => false, 'message' => 'Product not found'];
            }
            $this->redis->set($stockKey, $product['stock']);
        }

        // Join queue
        $position = $this->redis->rPush($queueKey, $userId);

        if ($position > $this->redis->get($stockKey)) {
            $this->redis->lRem($queueKey, $userId, 0);
            return ['success' => false, 'message' => 'Out of stock'];
        }

        // Set cart temp with 5-min expiry
        $this->redis->setex("cart_item:$userId:$productId", 300, $quantity);

        return [
            'success' => true,
            'queue_position' => $position,
            'message' => 'In queue. Complete checkout in 5 minutes.'
        ];
    }
}

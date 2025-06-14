<?php
namespace Scenario1;

require_once 'redis.php';

class QueueProcessor
{
    protected $db;
    protected $redis;

    public function __construct($db)
    {
        $this->db = $db;
        $this->redis = redis(); // from redis.php
    }

    public function process($productId)
    {
        $queueKey = "flash_sale_queue:$productId";
        $stockKey = "flash_sale_stock:$productId";

        while ($this->redis->lLen($queueKey) > 0 && $this->redis->get($stockKey) > 0) {
            $userId = $this->redis->lPop($queueKey);
            $quantity = $this->redis->get("cart_item:$userId:$productId");

            if (!$quantity) continue;

            // Safe DB transaction
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            $stmt->execute([$quantity, $productId, $quantity]);

            $insert = $this->db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $insert->execute([$userId, $productId, $quantity]);

            $this->db->commit();

            $this->redis->decrBy($stockKey, $quantity);
        }
    }
}

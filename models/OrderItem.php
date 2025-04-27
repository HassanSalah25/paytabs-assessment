<?php
require_once __DIR__ . '/../config/database.php';

class OrderItem
{
    public static function add($orderId, $productId, $quantity = 1)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $productId, $quantity]);
    }

    public static function getItemsByOrder($orderId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT oi.*, p.name, p.price FROM order_items oi 
                               JOIN products p ON oi.product_id = p.id
                               WHERE oi.order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }
}

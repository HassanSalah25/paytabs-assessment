<?php
require_once __DIR__ . '/../config/database.php';

class Order
{
    public static function create($customerId, $totalAmount, $shippingType)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO orders (customer_id, total_amount, shipping_type) VALUES (?, ?, ?)");
        $stmt->execute([$customerId, $totalAmount, $shippingType]);
        return $pdo->lastInsertId();
    }

    public static function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function all()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}

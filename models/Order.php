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

    // update order
    public function update($id, $data)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE orders SET customer_id = ?, total_amount = ?, shipping_type = ? WHERE id = ?");
        $stmt->execute([$data['customer_id'], $data['total_amount'], $data['shipping_type'], $id]);

        return $stmt->rowCount() > 0;
    }

    // update status
    public static function updateStatus($id, $status)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function getByCustomerId($customerId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

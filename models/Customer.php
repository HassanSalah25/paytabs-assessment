<?php
require_once __DIR__ . '/../config/database.php';

class Customer
{
    public static function create($name, $email, $phone)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone) VALUES (?, ?, ?)");
        $stmt->execute([$name, $email, $phone]);
        return $pdo->lastInsertId();
    }

    public static function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

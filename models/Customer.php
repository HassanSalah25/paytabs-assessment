<?php
require_once __DIR__ . '/../config/database.php';

class Customer
{
    public static function create($name, $email, $phone, $street1, $city, $state, $country, $zip)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO customers (name, email, phone,street1, city, state, country, zip) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $street1, $city, $state, $country, $zip]);
        return $pdo->lastInsertId();
    }

    public static function find($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function findByEmail($email)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

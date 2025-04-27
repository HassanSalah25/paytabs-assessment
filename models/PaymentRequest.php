<?php
require_once __DIR__ . '/../config/database.php';

class PaymentRequest
{
    public static function create($orderId, $requestPayload, $responsePayload)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO payment_requests (order_id, request_payload, response_payload) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $requestPayload, $responsePayload]);
    }

    public static function getByOrder($orderId)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM payment_requests WHERE order_id = ?");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

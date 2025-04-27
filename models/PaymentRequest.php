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
}

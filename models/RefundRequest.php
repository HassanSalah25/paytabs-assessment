<?php
require_once __DIR__ . '/../config/database.php';

class RefundRequest
{
    public static function create($orderId, $requestPayload, $responsePayload)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO refund_requests (order_id, request_payload, response_payload) VALUES (?, ?, ?)");
        $stmt->execute([$orderId, $requestPayload, $responsePayload]);
    }
}

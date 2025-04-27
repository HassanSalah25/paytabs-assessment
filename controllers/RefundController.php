<?php
require_once __DIR__ . '/../models/RefundRequest.php'; // Load the model first

class RefundController
{
    public function createRefund()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "405 - Method Not Allowed";
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;

        if (!$orderId) {
            http_response_code(400);
            echo "400 - Bad Request - Missing Order ID";
            exit;
        }

        $order = Order::find($orderId);

        if (!$order || $order['status'] !== 'paid') {
            echo "Cannot refund this order.";
            exit;
        }

        // Step 1: Create Real Refund to PayTabs
        $paymentController = new PaymentController();
        $refundResponse = $paymentController->refundPayment($order['id'], $order['total_amount']);

        // Step 2: Save refund record
        RefundRequest::create(
            $orderId,
            json_encode([
                'order_id' => $order['id'],
                'amount' => $order['total_amount'],
                'currency' => 'EGP'
            ]),
            json_encode($refundResponse)
        );

        // Step 3: Update Order Status to "refunded"
        Order::updateStatus($orderId, 'refunded');

        // Step 4: Redirect
        header("Location: /simple_store/order-details?id=" . urlencode($orderId));
        exit;
    }


    public function showRefundsByOrder()
    {
        $orderId = $_GET['order_id'] ?? null;

        if (!$orderId) {
            http_response_code(400);
            echo "400 - Bad Request - Missing order_id";
            exit;
        }

        $refunds = RefundRequest::getByOrder($orderId);

        header('Content-Type: application/json');
        echo json_encode($refunds);
    }
}

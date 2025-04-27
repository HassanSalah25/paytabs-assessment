<?php
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/PaymentRequest.php';
require_once __DIR__ . '/../models/RefundRequest.php';

// Protection: if no id, go back to orders
if (!isset($_GET['id'])) {
    header('Location: /orders');
    exit();
}

$orderId = (int) $_GET['id'];

$orderController = new OrderController();
$orderData = $orderController->getOrderDetails($orderId);

if (!$orderData['order']) {
    echo "<h3>Order not found!</h3>";
    exit();
}

$customer = Customer::find($orderData['order']['customer_id']);
$paymentRequests = PaymentRequest::getByOrder($orderId);
$refundRequests = RefundRequest::getByOrder($orderId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Order #<?= $orderId ?> Details 📦</h2>

    <div class="card mb-4">
        <div class="card-body">
            <h5>Customer Info</h5>
            <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
            <hr>
            <h5>Order Info</h5>
            <p><strong>Status:</strong> <?= ucfirst($orderData['order']['status']) ?></p>
            <p><strong>Shipping Type:</strong> <?= ucfirst($orderData['order']['shipping_type']) ?></p>
            <p><strong>Total Amount:</strong> EGP <?= number_format($orderData['order']['total_amount'], 2) ?></p>
            <p><strong>Created At:</strong> <?= date('Y-m-d H:i', strtotime($orderData['order']['created_at'])) ?></p>
        </div>
    </div>

    <h4>Ordered Products 🛒</h4>
    <?php if (count($orderData['items']) > 0): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Product</th>
                <th>Unit Price (EGP)</th>
                <th>Quantity</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orderData['items'] as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No products found for this order. 🤔</p>
    <?php endif; ?>

    <h4 class="mt-5">Payment Requests History 💳</h4>
    <?php if (count($paymentRequests) > 0): ?>
        <?php foreach ($paymentRequests as $payment): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Request Payload:</h6>
                    <pre><?= htmlspecialchars($payment['request_payload']) ?></pre>

                    <h6>Response Payload:</h6>
                    <pre><?= htmlspecialchars($payment['response_payload']) ?></pre>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No payment history found. 🧹</p>
    <?php endif; ?>

    <h4 class="mt-5">Refund Requests History 💸</h4>
    <?php if (count($refundRequests) > 0): ?>
        <?php foreach ($refundRequests as $refund): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6>Request Payload:</h6>
                    <pre><?= htmlspecialchars($refund['request_payload']) ?></pre>

                    <h6>Response Payload:</h6>
                    <pre><?= htmlspecialchars($refund['response_payload']) ?></pre>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No refunds made for this order. 🎯</p>
    <?php endif; ?>

</div>

</body>
</html>

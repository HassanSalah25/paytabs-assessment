<?php
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
require_once __DIR__ . '/../models/Product.php';

// Protection: if direct visit without POST, redirect to home
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /');
    exit();
}

// Fetch products from database
$products = Product::all();

// 1. Capture form data
$customerData = [
    'name' => $_POST['name'],
    'email' => $_POST['email'],
    'phone' => $_POST['phone'],
];

$shippingType = $_POST['shipping_type'] ?? 'shipping';
$productsSelected = [];

foreach ($_POST['products'] as $productId => $quantity) {
    if ((int)$quantity > 0) {
        $productsSelected[$productId] = (int)$quantity;
    }
}

// 2. Create Order + Customer
$orderController = new OrderController();
$paymentController = new PaymentController();

$orderId = $orderController->createOrder($customerData, $productsSelected, $shippingType);

// 3. Get Total Amount
$orderDetails = $orderController->getOrderDetails($orderId);
$amount = $orderDetails['order']['total_amount'];

// 4. Create Payment Page with PayTabs
$response = $paymentController->createPaymentPage($orderId, $customerData, $amount, "http://localhost/payment-callback");

$iframeUrl = $response['redirect_url'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <h2 class="mb-4">Checkout - Complete Payment 💳</h2>

    <?php if ($iframeUrl): ?>
        <iframe src="<?= htmlspecialchars($iframeUrl) ?>" width="100%" height="650px" frameborder="0"></iframe>
    <?php else: ?>
        <div class="alert alert-danger">
            Payment failed to initiate. Please try again.
        </div>
    <?php endif; ?>

</div>

</body>
</html>

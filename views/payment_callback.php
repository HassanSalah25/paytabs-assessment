<?php
require_once __DIR__ . '/../controllers/PaymentController.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This means PayTabs sent a POST callback
    $paymentController = new PaymentController();
    $paymentController->handleCallback($_POST);

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // This means PayTabs redirected the user after payment
    if (!isset($_GET['cart_id']) || !isset($_GET['payment_result'])) {
        header('Location: /error');
        exit();
    }

    $paymentController = new PaymentController();
    $paymentController->handleCallback($_GET);

} else {
    echo "<h3>Invalid Request Method 👀</h3>";
}

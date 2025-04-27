<?php
// Native PHP simple router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

switch (parse_url($request, PHP_URL_PATH)) {

    case '/':
        require __DIR__ . '/../views/create_order.php';
        break;

    case '/checkout':
        require __DIR__ . '/../views/checkout.php';
        break;

    case '/payment-callback':
        require __DIR__ . '/../views/payment_callback.php';
        break;

    case '/orders':
        require __DIR__ . '/../views/orders.php';
        break;

    case '/order-details':
        require __DIR__ . '/../views/order_details.php';
        break;

    case '/success':
        require __DIR__ . '/../views/success.php';
        break;

    case '/error':
        require __DIR__ . '/../views/error.php';
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Not Found </h1>";
        break;
}

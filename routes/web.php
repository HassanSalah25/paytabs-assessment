<?php
// Native PHP simple router
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/CheckoutController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';
require_once __DIR__ . '/../controllers/RefundController.php';


$controllers = [
    '/simple_store/' => ['controller' => 'OrderController', 'method' => 'createOrder'],
    '/simple_store/checkout' => ['controller' => 'CheckoutController', 'method' => 'checkout'],
    '/simple_store/payment-callback' => ['controller' => 'PaymentController', 'method' => 'callback', 'view' => '/../views/payment_callback.php'],
    '/simple_store/orders' => ['controller' => 'OrderController', 'method' => 'showAllOrders'],
    '/simple_store/order-details' => ['controller' => 'OrderController', 'method' => 'showOrderDetails'],
    '/simple_store/refund-requests' => ['controller' => 'RefundController', 'method' => 'showRefundsByOrder'],
    '/simple_store/create-refund' => ['controller' => 'RefundController', 'method' => 'createRefund'],
    '/simple_store/success' => ['view' => '/../views/success.php'],
    '/simple_store/error' => ['view' => '/../views/error.php'],
];

if (isset($controllers[parse_url($request, PHP_URL_PATH)])) {
    $route = $controllers[parse_url($request, PHP_URL_PATH)];
    if (isset($route['view'])) {
        require __DIR__ . $route['view'];
    }
    if (isset($route['controller']) && isset($route['method'])) {
        $controllerName = $route['controller'];
        $methodName = $route['method'];
        if (class_exists($controllerName) && method_exists($controllerName, $methodName)) {
            $controller = new $controllerName();
            $controller->$methodName();
        } else {
            http_response_code(500);
            echo "<h1>500 - Server Misconfiguration</h1>";
        }
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Not Found</h1>";
}

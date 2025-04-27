<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../controllers/PaymentController.php';

class CheckoutController
{
    public function checkout()
    {
        // Protection: only allow POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /simple_store/');
            exit();
        }

        // Capture form data safely
        $customerData = [
            'name'  => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'street1' => trim($_POST['street1'] ?? ''),
            'city'    => trim($_POST['city'] ?? ''),
            'state'   => trim($_POST['state'] ?? ''),
            'country' => trim($_POST['country'] ?? 'EG'),
            'zip'     => trim($_POST['zip'] ?? ''),
        ];

        $shippingType = $_POST['shipping_type'] ?? 'shipping';

        // Optional: Capture shipping address if shipping type is 'shipping'
        $shippingAddress = null;
        if ($shippingType === 'shipping') {
            $shippingAddress = [
                'street1' => trim($_POST['street1'] ?? ''),
                'city'    => trim($_POST['city'] ?? ''),
                'state'   => trim($_POST['state'] ?? ''),
                'country' => trim($_POST['country'] ?? 'EG'),
                'zip'     => trim($_POST['zip'] ?? ''),
            ];
        }

        // Validate products selection
        $productsSelected = [];
        if (isset($_POST['products']) && is_array($_POST['products'])) {
            foreach ($_POST['products'] as $productId => $quantity) {
                $quantity = (int) $quantity;
                if ($quantity > 0) {
                    $productsSelected[$productId] = $quantity;
                }
            }
        }

        if (empty($productsSelected)) {
            echo "❌ No products selected!";
            exit();
        }

        // Instantiate controllers
        $orderController = new OrderController();
        $paymentController = new PaymentController();

        // Create Order + Customer
        $orderId = $orderController->storeOrder($customerData, $productsSelected, $shippingType);

        // Get Order Details
        $orderDetails = $orderController->getOrderDetails($orderId);
        $amount = $orderDetails['order']['total_amount'];

        // Prepare customer details for payment
        $customerDetailsForPayment = [
            'name'   => $customerData['name'],
            'email'  => $customerData['email'],
            'phone'  => $customerData['phone'],
            'street1'=> $shippingAddress['street1'] ?? 'N/A',
            'city'   => $shippingAddress['city'] ?? 'N/A',
            'state'  => $shippingAddress['state'] ?? 'N/A',
            'country'=> $shippingAddress['country'] ?? 'EG',
            'zip'    => $shippingAddress['zip'] ?? '00000',
        ];

        // Create Payment Page
        $response = $paymentController->createPaymentPage(
            $orderId,
            $customerDetailsForPayment,
            $amount,
            "/payment-callback"
        );

        $iframeUrl = $response['redirect_url'] ?? null;

        // Pass to View
        $title = "Checkout Payment";
        include __DIR__ . '/../views/checkout.php';
    }
}

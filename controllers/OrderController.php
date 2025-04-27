<?php
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController
{
    public function showAllOrders()
    {
        $customerEmail = $_SESSION['customer_email'] ?? '';
        $customer = Customer::findByEmail($customerEmail);

        if ($customer) {
            $orders = Order::getByCustomerId($customer['id']);
        } else {
            $orders = [];
        }

        $title = "My Orders";
        include __DIR__ . '/../views/orders.php';
    }

    public function createOrder()
    {
        $products = Product::all();
        $title = "Create New Order";

        include __DIR__ . '/../views/create_order.php';
    }

    public function storeOrder($customerData, $productsSelected, $shippingType)
    {
        // Step 1: Create customer
        // Step 1: Check if customer exists
        $existingCustomer = Customer::findByEmail($customerData['email']);

        if ($existingCustomer) {
            $customerId = $existingCustomer['id'];
        } else {
            // Create new customer if not exists
            $customerId = Customer::create($customerData['name'], $customerData['email'], $customerData['phone'],
                $customerData['street1'], $customerData['city'], $customerData['state'],
                $customerData['country'], $customerData['zip']);
        }


        // Step 2: Calculate total price
        $totalAmount = 0;
        foreach ($productsSelected as $productId => $quantity) {
            $product = Product::find($productId);
            if ($product) {
                $totalAmount += $product['price'] * $quantity;
            }
        }

        // Step 3: Create Order
        $orderId = Order::create($customerId, $totalAmount, $shippingType);

        // Step 4: Attach products
        foreach ($productsSelected as $productId => $quantity) {
            OrderItem::add($orderId, $productId, $quantity);
        }

        return $orderId; // send back for payment
    }


    public function showOrderDetails()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Location: /simple_store/orders');
            exit();
        }

        $orderId = (int) $_GET['id'];

        $order = Order::find($orderId);
        $items = OrderItem::getItemsByOrder($orderId);

        $orderData = [
            'order' => $order,
            'items' => $items,
        ];

        if (!$orderData['order']) {
            echo "<h3>Order not found!</h3>";
            exit();
        }

        $customer = \Customer::find($orderData['order']['customer_id']);
        $paymentRequests = \PaymentRequest::getByOrder($orderId);
        $refundRequests = \RefundRequest::getByOrder($orderId);

        $title = "Order Details";

        include __DIR__ . '/../views/order_details.php';
    }

    public function getOrderDetails($orderId)
    {
        // Step 1: Get Order
        $order = Order::find($orderId);

        if (!$order) {
            return null;
        }

        // Step 2: Get Customer
        $customer = Customer::find($order['customer_id']);

        // Step 3: Get Order Items
        $items = OrderItem::getByOrderId($orderId);

        return [
            'order' => $order,
            'customer' => $customer,
            'items' => $items,
        ];
    }


}

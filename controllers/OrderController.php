<?php
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderItem.php';
require_once __DIR__ . '/../models/Product.php';

class OrderController
{
    public function createOrder($customerData, $productsSelected, $shippingType)
    {
        // Step 1: Create customer
        $customerId = Customer::create($customerData['name'], $customerData['email'], $customerData['phone']);

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

    public function listOrders()
    {
        return Order::all();
    }

    public function getOrderDetails($orderId)
    {
        $order = Order::find($orderId);
        $items = OrderItem::getItemsByOrder($orderId);
        return [
            'order' => $order,
            'items' => $items,
        ];
    }
}

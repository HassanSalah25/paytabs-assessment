<?php
require_once __DIR__ . '/../models/PaymentRequest.php';
require_once __DIR__ . '/../models/RefundRequest.php';

class PaymentController
{
    private $profileId;
    private $serverKey;
    private $baseUrl;
    private $paymentEndpoint;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/paytabs_config.php';
        $this->profileId = $config['profile_id'];
        $this->serverKey = $config['server_key'];
        $this->baseUrl = $config['base_url'];
        $this->paymentEndpoint = $this->baseUrl . '/payment/request'; // Always build from base
    }

    public function createPaymentPage($orderId, $customerData, $amount, $returnUrl)
    {
        $fields = [
            "profile_id" => $this->profileId,
            "tran_type" => "sale",
            "tran_class" => "ecom",
            "cart_id" => "order_" . $orderId,
            "cart_currency" => "EGP",
            "cart_amount" => $amount,
            "cart_description" => "Payment for Order #$orderId",
            "customer_details" => [
                "name" => $customerData['name'],
                "email" => $customerData['email'],
                "phone" => $customerData['phone'],
                "street1" => "N/A",
                "city" => "Cairo",
                "state" => "Cairo",
                "country" => "EG",
                "zip" => "00000",
            ],
            "hide_shipping" => true,
            "callback" => $returnUrl,
            "return" => $returnUrl,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->paymentEndpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "authorization: {$this->serverKey}",
            "content-type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        $responseArr = json_decode($response, true);

        // Save payment attempt
        PaymentRequest::create($orderId, json_encode($fields), $response);

        return $responseArr;
    }

    public function handleCallback($request)
    {
        $orderId = str_replace('order_', '', $request['cart_id']);
        $paymentResult = $request['payment_result']['response_status'] ?? '';

        if ($paymentResult == 'A') {
            $this->updateOrderStatus($orderId, 'paid');
            header("Location: /success.php");
            exit();
        } else {
            $this->updateOrderStatus($orderId, 'failed');
            header("Location: /error.php");
            exit();
        }
    }

    private function updateOrderStatus($orderId, $status)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }
}

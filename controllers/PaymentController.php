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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


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

    public function callback()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // This means PayTabs sent a POST callback
            $this->handleCallback($_POST);

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            print_r($_SERVER['REQUEST_METHOD']);
            // This means PayTabs redirected the user after payment
            if (!isset($_GET['cartId']) || !isset($_GET['payment_result'])) {
                header('Location: /simple_store/error');
                exit();
            }

            $this->handleCallback($_GET);

        } else {
            return "<h3>Invalid Request Method</h3>";
        }
    }

    public function handleCallback($request)
    {
        $orderId = str_replace('order_', '', $request['cartId']);
        $paymentResult = $request['respStatus'] ?? '';

        if ($paymentResult == 'A') {
            $this->updateOrderStatus($orderId, 'paid');
            header("Location: /simple_store/success");
            exit();
        } else {
            $this->updateOrderStatus($orderId, 'failed');
            header("Location: /simple_store/error");
            exit();
        }
    }

    private function updateOrderStatus($orderId, $status)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$status, $orderId]);
    }

    public function refundPayment($orderId, $amount)
    {
        $fields = [
            "profile_id" => $this->profileId,
            "tran_type" => "refund",
            "tran_class" => "ecom",
            "cart_id" => "refund_order_" . $orderId,
            "cart_currency" => "EGP",
            "cart_amount" => $amount,
            "cart_description" => "Refund for Order #$orderId",
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response, true);
    }

}

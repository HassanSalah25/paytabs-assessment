<?php
require_once __DIR__ . '/../models/PaymentRequest.php';
require_once __DIR__ . '/../models/RefundRequest.php';

class PaymentController
{
    private $profileId = '132344'; // Given
    private $serverKey = 'SWJ992BZTN-JHGTJBWDLM-BZJKMR2ZHT'; // Given
    private $endpoint = 'https://secure-egypt.paytabs.com/payment/request';

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
        curl_setopt($ch, CURLOPT_URL, $this->endpoint);
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

        // Save the payload
        PaymentRequest::create($orderId, json_encode($fields), $response);

        return $responseArr;
    }

    public function handleCallback($request)
    {
        $orderId = str_replace('order_', '', $request['cart_id']);
        $paymentResult = $request['payment_result']['response_status'] ?? '';

        if ($paymentResult == 'A') {
            // Approved Payment
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

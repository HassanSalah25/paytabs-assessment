<?php
ob_start();
?>

<h2 class="mb-4">Order #<?= htmlspecialchars($orderData['order']['id']) ?> Details 📦</h2>

<div class="card mb-4">
    <div class="card-body">
        <h5>Customer Info</h5>
        <p><strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?></p>
        <p><strong>Phone:</strong> <?= htmlspecialchars($customer['phone']) ?></p>
        <hr>
        <h5>Order Info</h5>
        <?php if ($orderData['order']['status'] === 'paid'): ?>
            <div class="mb-4">
                <form action="/simple_store/create-refund" method="POST">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderData['order']['id']) ?>">
                    <input type="hidden" name="request_payload" value="{}">
                    <input type="hidden" name="response_payload" value="{}">
                    <button type="submit" class="btn btn-danger">
                        Refund This Order
                    </button>
                </form>
            </div>
        <?php endif; ?>


        <p><strong>Status:</strong> <?= ucfirst($orderData['order']['status']) ?></p>
        <p><strong>Shipping Type:</strong> <?= ucfirst($orderData['order']['shipping_type']) ?></p>
        <p><strong>Total Amount:</strong> EGP <?= number_format($orderData['order']['total_amount'], 2) ?></p>
        <p><strong>Created At:</strong> <?= date('Y-m-d H:i', strtotime($orderData['order']['created_at'])) ?></p>
    </div>
</div>

<h4>Ordered Products 🛒</h4>
<?php if (!empty($orderData['items'])): ?>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Product</th>
            <th>Unit Price (EGP)</th>
            <th>Quantity</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orderData['items'] as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No products found for this order. 🤔</p>
<?php endif; ?>

<h4 class="mt-5">Payment Requests History 💳</h4>

<?php if (!empty($paymentRequests)): ?>
    <?php foreach ($paymentRequests as $payment):
        $request = json_decode($payment['request_payload'], true);
        $response = json_decode($payment['response_payload'], true);
        ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <h5 class="text-primary mb-3">🔵 Request Info:</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cart ID:</strong> <?= htmlspecialchars($request['cart_id'] ?? '-') ?><br>
                        <strong>Amount:</strong> <?= htmlspecialchars($request['cart_amount'] ?? '-') ?> <?= htmlspecialchars($request['cart_currency'] ?? '') ?><br>
                        <strong>Description:</strong> <?= htmlspecialchars($request['cart_description'] ?? '-') ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Customer Name:</strong> <?= htmlspecialchars($request['customer_details']['name'] ?? '-') ?><br>
                        <strong>Email:</strong> <?= htmlspecialchars($request['customer_details']['email'] ?? '-') ?><br>
                        <strong>Phone:</strong> <?= htmlspecialchars($request['customer_details']['phone'] ?? '-') ?>
                    </div>
                </div>

                <h5 class="text-success mb-3">🟢 Response Info:</h5>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Transaction Reference:</strong> <?= htmlspecialchars($response['tran_ref'] ?? '-') ?><br>
                        <strong>Payment Result:</strong> <?= htmlspecialchars($response['payment_result']['response_status'] ?? '-') ?>
                    </div>
                    <div class="col-md-6">
                        <strong>Response Message:</strong> <?= htmlspecialchars($response['payment_result']['response_message'] ?? '-') ?><br>
                        <strong>Payment ID:</strong> <?= htmlspecialchars($response['payment_info']['payment_id'] ?? '-') ?>
                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        No payment history found. 🧹
    </div>
<?php endif; ?>
<h4 class="mt-5">Refund Requests History 💸</h4>

<?php if (!empty($refundRequests)): ?>
    <?php foreach ($refundRequests as $refund):
        $request = json_decode($refund['request_payload'], true);
        $response = json_decode($refund['response_payload'], true);
        ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-body">

                <h5 class="text-danger mb-3">🔴 Refund Request Info</h5>
                <div class="row mb-3">
                    <div class="col-md-6 mb-2">
                        <strong>Order ID:</strong> <?= htmlspecialchars($request['order_id'] ?? '-') ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Currency:</strong> <?= htmlspecialchars($request['currency'] ?? '-') ?>
                    </div>
                    <div class="col-md-6 mb-2">
                        <strong>Refund Amount:</strong> <?= htmlspecialchars(number_format($request['amount'] ?? 0, 2)) ?>
                    </div>
                </div>

                <h5 class="text-warning mb-3">🟡 Refund Response Info</h5>
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <strong>Response Code:</strong> <?= htmlspecialchars($response['code'] ?? '-') ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Message:</strong> <?= htmlspecialchars($response['message'] ?? '-') ?>
                    </div>
                    <div class="col-md-4 mb-2">
                        <strong>Trace ID:</strong> <?= htmlspecialchars($response['trace'] ?? '-') ?>
                    </div>
                </div>

            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="alert alert-info">
        No refunds made for this order. 🎯
    </div>
<?php endif; ?>



<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>

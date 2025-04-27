<?php
require_once __DIR__ . '/../controllers/OrderController.php';
require_once __DIR__ . '/../models/Customer.php';

$orderController = new OrderController();
$orders = $orderController->listOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">All Orders 📦</h2>

    <?php if (count($orders) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Total (EGP)</th>
                <th>Shipping</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($orders as $order):
                $customer = Customer::find($order['customer_id']);
                ?>
                <tr>
                    <td><?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($customer['name'] ?? 'Unknown') ?></td>
                    <td>
                        <?php if ($order['status'] == 'paid'): ?>
                            <span class="badge bg-success">Paid</span>
                        <?php elseif ($order['status'] == 'pending'): ?>
                            <span class="badge bg-warning text-dark">Pending</span>
                        <?php elseif ($order['status'] == 'failed'): ?>
                            <span class="badge bg-danger">Failed</span>
                        <?php else: ?>
                            <span class="badge bg-secondary"><?= htmlspecialchars($order['status']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td><?= number_format($order['total_amount'], 2) ?></td>
                    <td><?= ucfirst($order['shipping_type']) ?></td>
                    <td><?= date('Y-m-d H:i', strtotime($order['created_at'])) ?></td>
                    <td>
                        <a href="/order-details?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">
            No orders found yet. 🚀
        </div>
    <?php endif; ?>

</div>

</body>
</html>

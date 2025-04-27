<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Order.php';
?>

<?php
if (!isset($_SESSION['customer_email'])) {
    // If email not submitted yet, show email input form
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_email'])) {
        $_SESSION['customer_email'] = trim($_POST['customer_email']);
        header("Location: /simple_store/orders"); // Redirect to avoid resubmission
        exit;
    }
    ?>
    <div class="container py-5">
        <div class="card p-4 shadow-sm">
            <h3 class="mb-4">Please Enter Your Email 📩</h3>
            <form method="POST" action="">
                <input type="email" name="customer_email" required class="form-control mb-3" placeholder="Enter your email">
                <button type="submit" class="btn btn-primary">View My Orders</button>
            </form>
        </div>

    </div>
    <?php
    $content = ob_get_clean();
    include __DIR__ . '/layout.php';
    ?>
    <?php
    exit; // Stop script here if not authenticated yet
}

?>




<h2 class="mb-4">All Orders 📦</h2>

<?php if (!empty($orders)): ?>
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
            $customer = \Customer::find($order['customer_id']); // Still allowed here, because just a small lookup
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
                    <a href="/simple_store/order-details?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">View Details</a>
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

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>

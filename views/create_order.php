<?php
require_once __DIR__ . '/../models/Product.php';

// Load products
$products = Product::all();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Order</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">Create a New Order 🚀</h2>

    <form method="POST" action="/checkout" class="card p-4 shadow-sm">
        <div class="row mb-4">
            <div class="col-md-4">
                <label>Full Name</label>
                <input required type="text" name="name" class="form-control" placeholder="John Doe">
            </div>
            <div class="col-md-4">
                <label>Email</label>
                <input required type="email" name="email" class="form-control" placeholder="john@example.com">
            </div>
            <div class="col-md-4">
                <label>Phone</label>
                <input required type="text" name="phone" class="form-control" placeholder="01012345678">
            </div>
        </div>

        <h5>Select Products:</h5>
        <?php foreach ($products as $product): ?>
            <div class="mb-3">
                <label><?= htmlspecialchars($product['name']) ?> (EGP <?= htmlspecialchars($product['price']) ?>)</label>
                <input type="number" name="products[<?= $product['id'] ?>]" min="0" value="0" class="form-control w-25 d-inline-block">
            </div>
        <?php endforeach; ?>

        <div class="mt-4 mb-3">
            <label>Shipping Type:</label>
            <select name="shipping_type" class="form-select w-50">
                <option value="shipping">Shipping</option>
                <option value="pickup">Pickup</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Proceed to Checkout 🚚</button>
    </form>
</div>

</body>
</html>

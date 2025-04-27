<?php
// Assume $products and $title already exist
ob_start();
?>

<h2 class="mb-4">Create a New Order 🚀</h2>

<form method="POST" action="/simple_store/checkout" class="card p-4 shadow-sm">
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

    <h5 class="mb-3">Select Products:</h5>

    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="mb-1"><?= htmlspecialchars($product['name']) ?></h6>
                            <small class="text-muted">EGP <?= number_format($product['price'], 2) ?></small>
                        </div>
                        <div style="width: 100px;">
                            <input type="number" name="products[<?= $product['id'] ?>]" min="0" value="0" class="form-control form-control-sm text-center">
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="mt-4 mb-3">
        <label>Shipping Type:</label>
        <select name="shipping_type" id="shippingType" class="form-select w-50" onchange="toggleShippingAddress()">
            <option value="shipping">Ship the order to a shipping address</option>
            <option value="pickup">Pick up the order after payment</option>
        </select>
    </div>

    <div id="shippingAddressFields" class="border rounded p-3 mb-4 bg-light">
        <h5 class="mb-3">Shipping Address</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Street Address</label>
                <input type="text" name="street1" class="form-control" placeholder="123 Example Street">
            </div>
            <div class="col-md-6 mb-3">
                <label>City</label>
                <input type="text" name="city" class="form-control" placeholder="Cairo">
            </div>
            <div class="col-md-6 mb-3">
                <label>State</label>
                <input type="text" name="state" class="form-control" placeholder="Cairo Governorate">
            </div>
            <div class="col-md-6 mb-3">
                <label>Country</label>
                <input type="text" name="country" class="form-control" value="EG" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label>Postal Code</label>
                <input type="text" name="zip" class="form-control" placeholder="12345">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-success">Proceed to Checkout 🚚</button>
</form>

<script>
    function toggleShippingAddress() {
        var shippingType = document.getElementById('shippingType').value;
        var shippingFields = document.getElementById('shippingAddressFields');
        if (shippingType === 'shipping') {
            shippingFields.style.display = 'block';
        } else {
            shippingFields.style.display = 'none';
        }
    }

    // Call once on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleShippingAddress();
    });
</script>

<?php
$content = ob_get_clean();

// layout.php will now be included from controller automatically
include __DIR__ . '/layout.php';
?>

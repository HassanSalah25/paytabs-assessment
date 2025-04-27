<!-- /views/partials/sidebar.php -->
<?php
$currentRoute = $_SERVER['REQUEST_URI']; // Get current URL path
?>
<div class="d-flex flex-column p-3 bg-dark text-white col-sm-3" style="height: 100%;">
    <a href="/simple_store/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        <span class="fs-4">🛒 Simple Store</span>
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="/simple_store/" class="nav-link text-white <?= ($currentRoute == '/simple_store/' || $currentRoute == '/simple_store') ? 'active bg-primary' : '' ?>">
                🏠 Home (Create Order)
            </a>
        </li>
        <li>
            <a href="/simple_store/orders" class="nav-link text-white <?= ($currentRoute == '/simple_store/orders') ? 'active bg-primary' : '' ?>">
                📦 Orders
            </a>
        </li>
    </ul>
    <hr>
</div>

<?php
ob_start();
?>

<h2 class="mb-4">Checkout - Complete Payment 💳</h2>

<?php if ($iframeUrl): ?>
    <iframe src="<?= htmlspecialchars($iframeUrl) ?>" width="100%" height="600px" frameborder="0" allowfullscreen></iframe>
<?php else: ?>
    <div class="alert alert-danger">Failed to get PayTabs payment link.</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>

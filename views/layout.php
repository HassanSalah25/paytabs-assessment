<!-- /views/layout.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Simple Store' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-link:hover {
            background-color: #495057;
        }
    </style>
</head>
<body class="bg-light">

<div class="d-flex" style="height: 920px">
    <!-- Sidebar -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="container-fluid p-5 col-md-8">
        <?= $content ?? '' ?>
    </div>
</div>

</body>
</html>

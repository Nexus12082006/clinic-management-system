<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Clinic Management System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <?= $extraHead ?? '' ?>
</head>
<body class="bg-light">
    <?php if (isset($user)): ?>
        <nav class="navbar navbar-expand-lg navbar-dark <?= $navbarClass ?? 'bg-primary' ?> shadow-sm mb-4">
            <div class="container">
                <a class="navbar-brand fw-bold" href="<?= $base_url ?>/<?= $dashboardRoute ?? '#' ?>">
                    <?= $brandIcon ?? '' ?><?= $brandName ?? 'Clinic Management' ?>
                </a>
                <div class="navbar-nav ms-auto">
                    <span class="nav-link text-light me-3">
                        <?= $welcomeText ?? 'Welcome' ?>, <?= htmlspecialchars($user['username']) ?>
                    </span>
                    <a class="btn btn-outline-light btn-sm px-3" href="<?= $base_url ?>/logout">Logout</a>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <div class="container">
        <?php foreach ($flashes as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>

        <?= $content ?? '' ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= $base_url ?>/assets/js/app.js"></script>
    <?= $extraScripts ?? '' ?>
</body>
</html>
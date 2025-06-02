<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ' : ''; ?><?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/css/style.css">
    <link rel="icon" href="<?php echo SITE_URL; ?>/images/favicon.ico">
</head>
<body>
    <!-- Навигационное меню -->
    <?php
    $headerData = json_decode(file_get_contents(__DIR__ . '/../data/header.json'), true);
    ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo SITE_URL; ?>/<?= htmlspecialchars($headerData['logo']) ?>" alt="Логотип" height="50" class="d-inline-block align-top me-2">
                <span>НПЦ НАН по животноводству</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php foreach ($headerData['menu'] as $item): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == $item['url'] ? 'active' : ''; ?>" 
                               href="<?php echo SITE_URL . '/' . htmlspecialchars($item['url']); ?>">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/">Админ-панель</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/auth.php?action=logout">Выйти</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
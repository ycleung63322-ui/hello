<?php

declare(strict_types=1);

function render_header(string $title, string $appName, ?array $user = null): void
{
    $pageTitle = e($title) . ' · ' . e($appName);
    ?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@400;500;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="page">
        <header class="topbar">
            <a class="brand" href="/"><?= e($appName) ?></a>
            <nav class="nav">
                <?php if ($user !== null): ?>
                    <a href="/dashboard.php">會員中心</a>
                    <a href="/profile.php">個人資料</a>
                    <a href="/logout.php">登出</a>
                <?php else: ?>
                    <a href="/login.php">登入</a>
                    <a class="btn btn-small" href="/register.php">註冊</a>
                <?php endif; ?>
            </nav>
        </header>
        <main class="main">
    <?php
}

function render_footer(): void
{
    ?>
        </main>
        <footer class="footer">
            <p>簡單 PHP + MySQL 會員系統示範</p>
        </footer>
    </div>
</body>
</html>
    <?php
}

/**
 * @param list<string> $errors
 */
function render_errors(array $errors): void
{
    if ($errors === []) {
        return;
    }

    echo '<div class="alert alert-error"><ul>';
    foreach ($errors as $error) {
        echo '<li>' . e($error) . '</li>';
    }
    echo '</ul></div>';
}

function render_flash(): void
{
    $success = flash('success');
    $error = flash('error');

    if ($success !== null) {
        echo '<div class="alert alert-success">' . e($success) . '</div>';
    }

    if ($error !== null) {
        echo '<div class="alert alert-error">' . e($error) . '</div>';
    }
}

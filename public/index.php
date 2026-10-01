<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$user = current_user();

render_header('首頁', $appName, $user);
render_flash();
?>
<section class="hero">
    <h1><?= e($appName) ?></h1>
    <p class="lead">一個精簡的 PHP 8.3 + MySQL 會員系統：註冊、登入、會員中心與個人資料更新。</p>
    <div class="actions">
        <?php if ($user !== null): ?>
            <a class="btn" href="/dashboard.php">進入會員中心</a>
            <a class="btn btn-secondary" href="/profile.php">編輯個人資料</a>
        <?php else: ?>
            <a class="btn" href="/register.php">立即註冊</a>
            <a class="btn btn-secondary" href="/login.php">已經有帳號？登入</a>
        <?php endif; ?>
    </div>
</section>
<?php
render_footer();

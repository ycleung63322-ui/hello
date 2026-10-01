<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
$user = current_user();
assert($user !== null);

render_header('會員中心', $appName, $user);
render_flash();
?>
<section class="panel panel-wide">
    <h1>你好，<?= e($user['name']) ?></h1>
    <p class="lead">這裡是登入後的會員中心。</p>
    <div class="meta">
        <div>
            <strong>會員編號</strong>
            <span>#<?= e((string) $user['id']) ?></span>
        </div>
        <div>
            <strong>Email</strong>
            <span><?= e($user['email']) ?></span>
        </div>
        <div>
            <strong>註冊時間</strong>
            <span><?= e($user['created_at']) ?></span>
        </div>
    </div>
    <div class="actions">
        <a class="btn" href="/profile.php">編輯個人資料</a>
        <a class="btn btn-secondary" href="/logout.php">登出</a>
    </div>
</section>
<?php
render_footer();

<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_guest();

$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? null)) {
        $errors[] = '表單已過期，請重新再試。';
    } else {
        $result = validate_login($_POST);
        $oldEmail = $result['data']['email'];

        if (!$result['ok']) {
            $errors = $result['errors'];
        } else {
            $login = attempt_login($result['data']['email'], $result['data']['password']);

            if (!$login['ok']) {
                $errors[] = (string) $login['error'];
            } else {
                login_member((int) $login['member_id']);
                flash('success', '登入成功。');
                redirect('/dashboard.php');
            }
        }
    }
}

render_header('登入', $appName);
render_flash();
?>
<section class="panel">
    <h1>會員登入</h1>
    <p class="muted">使用註冊的 Email 與密碼登入。</p>
    <?php render_errors($errors); ?>
    <form class="form" method="post" action="/login.php" novalidate>
        <?= csrf_field() ?>
        <label>
            Email
            <input type="email" name="email" value="<?= e($oldEmail) ?>" required autocomplete="email">
        </label>
        <label>
            密碼
            <input type="password" name="password" required autocomplete="current-password">
        </label>
        <button class="btn" type="submit">登入</button>
    </form>
    <p class="muted" style="margin-top: 18px;">還沒有帳號？<a href="/register.php">立即註冊</a></p>
</section>
<?php
render_footer();

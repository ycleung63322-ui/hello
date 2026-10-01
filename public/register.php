<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_guest();

$errors = [];
$old = ['name' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? null)) {
        $errors[] = '表單已過期，請重新再試。';
    } else {
        $result = validate_register($_POST);
        $old = [
            'name' => $result['data']['name'],
            'email' => $result['data']['email'],
        ];

        if (!$result['ok']) {
            $errors = $result['errors'];
        } else {
            $created = register_member(
                $result['data']['name'],
                $result['data']['email'],
                $result['data']['password'],
            );

            if (!$created['ok']) {
                $errors[] = (string) $created['error'];
            } else {
                login_member((int) $created['member_id']);
                flash('success', '註冊成功，歡迎加入！');
                redirect('/dashboard.php');
            }
        }
    }
}

render_header('註冊', $appName);
render_flash();
?>
<section class="panel">
    <h1>建立帳號</h1>
    <p class="muted">填寫基本資料即可完成註冊。</p>
    <?php render_errors($errors); ?>
    <form class="form" method="post" action="/register.php" novalidate>
        <?= csrf_field() ?>
        <label>
            姓名
            <input type="text" name="name" value="<?= e($old['name']) ?>" required maxlength="100" autocomplete="name">
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="191" autocomplete="email">
        </label>
        <label>
            密碼
            <input type="password" name="password" required minlength="8" autocomplete="new-password">
        </label>
        <label>
            確認密碼
            <input type="password" name="password_confirm" required minlength="8" autocomplete="new-password">
        </label>
        <button class="btn" type="submit">註冊</button>
    </form>
    <p class="muted" style="margin-top: 18px;">已有帳號？<a href="/login.php">前往登入</a></p>
</section>
<?php
render_footer();

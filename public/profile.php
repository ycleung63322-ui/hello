<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();
$user = current_user();
assert($user !== null);

$errors = [];
$old = [
    'name' => $user['name'],
    'email' => $user['email'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_csrf'] ?? null)) {
        $errors[] = '表單已過期，請重新再試。';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $old = ['name' => $name, 'email' => $email];

        $updated = update_profile($user['id'], $name, $email);
        if (!$updated['ok']) {
            $errors = $updated['errors'];
        } else {
            flash('success', '個人資料已更新。');
            redirect('/profile.php');
        }
    }
}

render_header('個人資料', $appName, $user);
render_flash();
?>
<section class="panel">
    <h1>個人資料</h1>
    <p class="muted">可更新顯示名稱與 Email。</p>
    <?php render_errors($errors); ?>
    <form class="form" method="post" action="/profile.php" novalidate>
        <?= csrf_field() ?>
        <label>
            姓名
            <input type="text" name="name" value="<?= e($old['name']) ?>" required maxlength="100" autocomplete="name">
        </label>
        <label>
            Email
            <input type="email" name="email" value="<?= e($old['email']) ?>" required maxlength="191" autocomplete="email">
        </label>
        <button class="btn" type="submit">儲存變更</button>
    </form>
</section>
<?php
render_footer();

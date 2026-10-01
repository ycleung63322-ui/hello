<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

if (current_user() !== null) {
    logout_member();
}

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

flash('success', '已成功登出。');
redirect('/login.php');

<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
        return null;
    }

    if (!isset($_SESSION['_flash'][$key])) {
        return null;
    }

    $value = $_SESSION['_flash'][$key];
    unset($_SESSION['_flash'][$key]);

    return is_string($value) ? $value : null;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(?string $token): bool
{
    $sessionToken = $_SESSION['_csrf'] ?? '';

    return is_string($sessionToken)
        && $sessionToken !== ''
        && is_string($token)
        && hash_equals($sessionToken, $token);
}

/**
 * @return array{ok: bool, errors: list<string>, data: array{name: string, email: string, password: string}}
 */
function validate_register(array $input): array
{
    $name = trim((string) ($input['name'] ?? ''));
    $email = trim((string) ($input['email'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    $passwordConfirm = (string) ($input['password_confirm'] ?? '');
    $errors = [];

    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = '請輸入姓名（最多 100 字）。';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 191) {
        $errors[] = '請輸入有效的 Email。';
    }

    if (mb_strlen($password) < 8) {
        $errors[] = '密碼至少需要 8 個字元。';
    }

    if ($password !== $passwordConfirm) {
        $errors[] = '兩次輸入的密碼不一致。';
    }

    return [
        'ok' => $errors === [],
        'errors' => $errors,
        'data' => [
            'name' => $name,
            'email' => mb_strtolower($email),
            'password' => $password,
        ],
    ];
}

/**
 * @return array{ok: bool, errors: list<string>, data: array{email: string, password: string}}
 */
function validate_login(array $input): array
{
    $email = trim((string) ($input['email'] ?? ''));
    $password = (string) ($input['password'] ?? '');
    $errors = [];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '請輸入有效的 Email。';
    }

    if ($password === '') {
        $errors[] = '請輸入密碼。';
    }

    return [
        'ok' => $errors === [],
        'errors' => $errors,
        'data' => [
            'email' => mb_strtolower($email),
            'password' => $password,
        ],
    ];
}

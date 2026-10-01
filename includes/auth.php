<?php

declare(strict_types=1);

/**
 * @return array{id: int, name: string, email: string, created_at: string}|null
 */
function current_user(): ?array
{
    $id = $_SESSION['member_id'] ?? null;
    if (!is_int($id) && !(is_string($id) && ctype_digit($id))) {
        return null;
    }

    static $cached = false;
    static $user = null;

    if ($cached) {
        return $user;
    }

    $stmt = db()->prepare(
        'SELECT id, name, email, created_at FROM members WHERE id = :id LIMIT 1'
    );
    $stmt->execute(['id' => (int) $id]);
    $row = $stmt->fetch();

    $cached = true;
    $user = $row === false ? null : [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
        'email' => (string) $row['email'],
        'created_at' => (string) $row['created_at'],
    ];

    if ($user === null) {
        unset($_SESSION['member_id']);
    }

    return $user;
}

function require_guest(): void
{
    if (current_user() !== null) {
        redirect('/dashboard.php');
    }
}

function require_login(): void
{
    if (current_user() === null) {
        flash('error', '請先登入後再繼續。');
        redirect('/login.php');
    }
}

function login_member(int $memberId): void
{
    session_regenerate_id(true);
    $_SESSION['member_id'] = $memberId;
}

function logout_member(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            [
                'expires' => time() - 42000,
                'path' => $params['path'],
                'domain' => $params['domain'],
                'secure' => (bool) $params['secure'],
                'httponly' => (bool) $params['httponly'],
                'samesite' => $params['samesite'] ?? 'Lax',
            ]
        );
    }

    session_destroy();
}

/**
 * @return array{ok: bool, error: ?string, member_id: ?int}
 */
function register_member(string $name, string $email, string $password): array
{
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = db()->prepare(
            'INSERT INTO members (name, email, password_hash) VALUES (:name, :email, :password_hash)'
        );
        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'password_hash' => $hash,
        ]);

        return [
            'ok' => true,
            'error' => null,
            'member_id' => (int) db()->lastInsertId(),
        ];
    } catch (PDOException $e) {
        // 1062 = duplicate entry
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return [
                'ok' => false,
                'error' => '此 Email 已被註冊。',
                'member_id' => null,
            ];
        }

        throw $e;
    }
}

/**
 * @return array{ok: bool, error: ?string, member_id: ?int}
 */
function attempt_login(string $email, string $password): array
{
    $stmt = db()->prepare(
        'SELECT id, password_hash FROM members WHERE email = :email LIMIT 1'
    );
    $stmt->execute(['email' => $email]);
    $row = $stmt->fetch();

    if ($row === false || !password_verify($password, (string) $row['password_hash'])) {
        return [
            'ok' => false,
            'error' => 'Email 或密碼不正確。',
            'member_id' => null,
        ];
    }

    return [
        'ok' => true,
        'error' => null,
        'member_id' => (int) $row['id'],
    ];
}

/**
 * @return array{ok: bool, errors: list<string>}
 */
function update_profile(int $memberId, string $name, string $email): array
{
    $errors = [];

    if ($name === '' || mb_strlen($name) > 100) {
        $errors[] = '請輸入姓名（最多 100 字）。';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 191) {
        $errors[] = '請輸入有效的 Email。';
    }

    if ($errors !== []) {
        return ['ok' => false, 'errors' => $errors];
    }

    try {
        $stmt = db()->prepare(
            'UPDATE members SET name = :name, email = :email WHERE id = :id'
        );
        $stmt->execute([
            'name' => $name,
            'email' => mb_strtolower($email),
            'id' => $memberId,
        ]);

        return ['ok' => true, 'errors' => []];
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            return ['ok' => false, 'errors' => ['此 Email 已被其他帳號使用。']];
        }

        throw $e;
    }
}

<?php
// 🌐 Start session securely and only once
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🧑‍💻 User store (expandable to DB/env var later)
$USER_CREDENTIALS = [
    'admin' => [
        'password_hash' => password_hash('pass123', PASSWORD_DEFAULT),
        'role' => 'admin'
    ],
    'employee' => [
        'password_hash' => password_hash('secure456', PASSWORD_DEFAULT),
        'role' => 'employee'
    ],
];

// ✅ Check if user is authenticated
function is_authenticated(): bool
{
    return isset($_SESSION['user']);
}

// 🔐 Get current user's role
function get_user_role(): ?string
{
    return $_SESSION['role'] ?? null;
}

// 🔑 Attempt login, set session if valid
function login(string $username, string $password): bool
{
    global $USER_CREDENTIALS;

    $username = strtolower(trim($username));

    if (!isset($USER_CREDENTIALS[$username])) {
        return false;
    }

    $user = $USER_CREDENTIALS[$username];
    if (password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $user['role'];
        return true;
    }

    return false;
}

// 🚪 Log out and destroy session
function logout(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        session_destroy();
    }

    header('Location: index.php?page=login');
    exit;
}

// 🧭 Handle login form submission
function handle_login_request(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        $success = login($_POST['username'], $_POST['password']);
        if ($success) {
            $redirect = (get_user_role() === 'admin') ? 'dashboard' : 'employees';
        } else {
            $redirect = 'login&error=1';
        }
        header("Location: index.php?page=$redirect");
        exit;
    }
}

function generate_csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}


function generate_nonce(string $action): string
{
    $nonce = bin2hex(random_bytes(16));
    $_SESSION['nonces'][$action] = $nonce;
    return $nonce;
}

function validate_nonce(string $action, string $nonce): bool
{
    return isset($_SESSION['nonces'][$action]) && hash_equals($_SESSION['nonces'][$action], $nonce);
}

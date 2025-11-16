<?php
// includes/auth.php
require_once __DIR__ . '/config.php';

function sanitize_str($s) {
    return trim(htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'));
}

function get_user_by_username_or_email(PDO $pdo, string $ident) {
    $sql = "SELECT * FROM users WHERE username = :i OR email = :i LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':i' => $ident]);
    return $stmt->fetch();
}

function create_user(PDO $pdo, string $username, string $email, string $password) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, password) VALUES (:u, :e, :p)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':u'=>$username, ':e'=>$email, ':p'=>$hash]);
    return (int)$pdo->lastInsertId();
}

function login_user(PDO $pdo, int $user_id) {
    session_regenerate_id(true); // prevent session fixation
    $_SESSION['user_id'] = $user_id;
    $_SESSION['last_activity'] = time();
}

function logout_user(PDO $pdo) {
    // remove remember cookie and tokens if you implement tokens table (optional)
    if (!empty($_COOKIE['remember'])) {
        setcookie('remember', '', time()-3600, '/', '', isset($_SERVER['HTTPS']), true);
    }
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000,
                  $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}

function require_login(PDO $pdo) {
    if (!empty($_SESSION['user_id'])) {
        // Inactivity logout (30 min)
        if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
            logout_user($pdo);
            header('Location: /public/login.php?msg=timeout');
            exit;
        }
        $_SESSION['last_activity'] = time();
        return;
    }
    // no session -> redirect to login
    header('Location: /public/login.php');
    exit;
}

function current_user(PDO $pdo) {
    if (!empty($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

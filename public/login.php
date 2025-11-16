<?php
require_once __DIR__ . '/../includes/auth.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ident = sanitize_str($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$ident || !$password) $errors[] = 'Provide identifier and password';
    else {
        $user = get_user_by_username_or_email($pdo, $ident);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Invalid credentials';
        } else {
            login_user($pdo, (int)$user['id']);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Login</title></head><body>
<h2>Login</h2>
<?php foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Username or Email <input name="identifier" value="<?=htmlspecialchars($_POST['identifier'] ?? '')?>"></label><br>
  <label>Password <input type="password" name="password"></label><br>
  <button type="submit">Login</button>
</form>
<p><a href="signup.php">Create account</a></p>
</body></html>

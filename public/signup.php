<?php
require_once __DIR__ . '/../includes/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_str($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (!$username) $errors[] = 'Username required';
    if (!$email) $errors[] = 'Valid email required';
    if (strlen($password) < 6) $errors[] = 'Password >= 6 chars';
    if ($password !== $password2) $errors[] = 'Passwords do not match';

    if (empty($errors)) {
        // check existing
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :u OR email = :e LIMIT 1");
        $stmt->execute([':u'=>$username, ':e'=>$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Username or email already used';
        } else {
            $user_id = create_user($pdo, $username, $email, $password);
            login_user($pdo, $user_id);
            header('Location: index.php');
            exit;
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Signup</title></head><body>
<h2>Signup</h2>
<?php foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Username <input name="username" value="<?=htmlspecialchars($_POST['username'] ?? '')?>"></label><br>
  <label>Email <input name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>"></label><br>
  <label>Password <input type="password" name="password"></label><br>
  <label>Confirm <input type="password" name="password2"></label><br>
  <button type="submit">Create</button>
</form>
<p><a href="login.php">Login</a></p>
</body></html>

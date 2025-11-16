<?php
require_once __DIR__ . '/../includes/auth.php';
require_login($pdo);
$user = current_user($pdo);
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_str($_POST['name'] ?? '');
    $dob  = $_POST['date_of_birth'] ?? '';
    $address = sanitize_str($_POST['address'] ?? '');

    if (!$name) $errors[] = 'Name required';
    if (!$dob) $errors[] = 'Date of birth required';
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO patients (name, date_of_birth, address) VALUES (:n, :d, :a)");
        $stmt->execute([':n'=>$name, ':d'=>$dob, ':a'=>$address]);
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Create patient</title></head><body>
<h2>Create patient (Logged as <?=htmlspecialchars($user['username'])?>)</h2>
<?php foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Name <input name="name" value="<?=htmlspecialchars($_POST['name'] ?? '')?>"></label><br>
  <label>Date of birth <input type="date" name="date_of_birth" value="<?=htmlspecialchars($_POST['date_of_birth'] ?? '')?>"></label><br>
  <label>Address <input name="address" value="<?=htmlspecialchars($_POST['address'] ?? '')?>"></label><br>
  <button type="submit">Save</button>
</form>
<p><a href="index.php">Back</a></p>
</body></html>

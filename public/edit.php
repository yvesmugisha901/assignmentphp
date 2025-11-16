<?php
require_once __DIR__ . '/../includes/auth.php';
require_login($pdo);
$user = current_user($pdo);

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = :id LIMIT 1");
$stmt->execute([':id'=>$id]);
$patient = $stmt->fetch();
if (!$patient) { header('Location: index.php'); exit; }

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_str($_POST['name'] ?? '');
    $dob  = $_POST['date_of_birth'] ?? '';
    $address = sanitize_str($_POST['address'] ?? '');
    if (!$name) $errors[] = 'Name required';
    if (!$dob) $errors[] = 'DOB required';
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE patients SET name = :n, date_of_birth = :d, address = :a WHERE id = :id");
        $stmt->execute([':n'=>$name, ':d'=>$dob, ':a'=>$address, ':id'=>$id]);
        header('Location: index.php');
        exit;
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><title>Edit</title></head><body>
<h2>Edit patient</h2>
<?php foreach ($errors as $e) echo "<p style='color:red'>".htmlspecialchars($e)."</p>"; ?>
<form method="post">
  <label>Name <input name="name" value="<?=htmlspecialchars($_POST['name'] ?? $patient['name'])?>"></label><br>
  <label>Date of birth <input type="date" name="date_of_birth" value="<?=htmlspecialchars($_POST['date_of_birth'] ?? $patient['date_of_birth'])?>"></label><br>
  <label>Address <input name="address" value="<?=htmlspecialchars($_POST['address'] ?? $patient['address'])?>"></label><br>
  <button type="submit">Update</button>
</form>
<p><a href="index.php">Back</a></p>
</body></html>

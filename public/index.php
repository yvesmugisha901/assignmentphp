<?php
require_once __DIR__ . '/../includes/auth.php';
require_login($pdo);
$user = current_user($pdo);

// fetch patients
$stmt = $pdo->query("SELECT * FROM patients ORDER BY id DESC");
$patients = $stmt->fetchAll();
?>
<!doctype html><html><head><meta charset="utf-8"><title>Patients</title></head><body>
<header>
  <h1>Patients</h1>
  <p>Logged in as: <?=htmlspecialchars($user['username'])?> | <a href="logout.php">Logout</a></p>
</header>
<p><a href="create.php">Add patient</a></p>
<table border=1 cellpadding=6>
<thead><tr><th>ID</th><th>Name</th><th>DOB</th><th>Address</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($patients as $p): ?>
<tr>
  <td><?= (int)$p['id'] ?></td>
  <td><?= htmlspecialchars($p['name']) ?></td>
  <td><?= htmlspecialchars($p['date_of_birth']) ?></td>
  <td><?= htmlspecialchars($p['address']) ?></td>
  <td>
    <a href="edit.php?id=<?= (int)$p['id'] ?>">Edit</a> |
    <a href="delete.php?id=<?= (int)$p['id'] ?>" onclick="return confirm('Delete?')">Delete</a>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body></html>

<?php
require_once __DIR__ . '/../includes/auth.php';
require_login($pdo);
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM patients WHERE id = :id");
    $stmt->execute([':id'=>$id]);
}
header('Location: index.php');
exit;

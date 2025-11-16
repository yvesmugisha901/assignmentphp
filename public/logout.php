<?php
require_once __DIR__ . '/../includes/auth.php';
logout_user($pdo);
header('Location: login.php');
exit;

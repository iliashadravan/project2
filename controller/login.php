<?php
global $db;
require_once 'db.php';
require_once 'function.errors.php';

$errors = [];
$show_errors = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$show_errors = true;

$phone_number = request('phone_number');
$password = request('password');

if (is_null($phone_number)){
$errors['phone_number'] = 'Phone number is empty';
}
if (is_null($password)){
$errors['password'] = 'Password is empty';
}

if (empty($errors)) {
$query = "SELECT * FROM users WHERE phone_number = ?";
$stmt = $db->prepare($query);
$stmt->bind_param("s", $phone_number);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
if ($user['is_active'] == 1) {
$errors['login'] = 'You do not have permission to login.';
} elseif (password_verify($password, $user['password'])) {
if (isset($_SESSION['user_id'])) {
$errors['login'] = 'You are already logged in. Please logout first,  <a href="panel.php">go back</a>';
} else {
// ذخیره user_id در session
$_SESSION['user_id'] = $user['id'];  // ذخیره user_id
$_SESSION['phone_number'] = $phone_number;
header('Location: panel.php');
exit;
}
} else {
$errors['login'] = 'Invalid phone number or password';
}
} else {
$errors['login'] = 'Invalid phone number or password';
}
}
}
?>
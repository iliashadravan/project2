<?php
global $db, $user;
require_once 'db.php';
require_once 'function.errors.php';
require_once 'function.query.php'; // بارگذاری فایل شامل توابع

$errors = [];
$show_errors = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $show_errors = true;

    $phone_number = request('phone_number');
    $password = request('password');

    if (is_null($phone_number)) {
        $errors['phone_number'] = 'Phone number is empty';
    }
    if (is_null($password)) {
        $errors['password'] = 'Password is empty';
    }

    // فراخوانی تابع برای پیدا کردن کاربر
    $user = findUserByPhoneNumber($phone_number, $db);

    if ($user) {
        if ($user['is_active'] == 1) {
            $errors['login'] = 'You do not have permission to login.';
        } elseif (password_verify($password, $user['password'])) {
            if (isset($_SESSION['user_id'])) {
                $errors['login'] = 'You are already logged in. Please logout first,  <a href="panel.php">go back</a>';
            } else {
                // ذخیره user_id در session
                $_SESSION['user_id'] = $user['id'];
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
?>

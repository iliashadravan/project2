<?php

global $db, $user;
require_once '../controller/db.php'; // اتصال به پایگاه داده
require_once 'function.query.php';   // بارگذاری فایل توابع
$errors = [];
$success = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // بررسی فرم‌ها
    if (empty($current_password)) {
        $errors['current_password'] = 'enter current password';
    }

    if (empty($new_password)) {
        $errors['new_password'] = 'enter new password';
    }

    if ($new_password !== $confirm_password) {
        $errors['confirm_password'] = 'new password and confirm password do not match';
    }

    if (empty($errors)) {
        $user_id = $_SESSION['user_id'];

        // فراخوانی تابع برای دریافت رمز عبور فعلی کاربر
        $user = getUserPasswordById($user_id, $db);

        if ($user && password_verify($current_password, $user['password'])) {
            // رمز عبور جدید را هش کنید
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // به‌روزرسانی رمز عبور در پایگاه داده
            $update_query = "UPDATE users SET password = ? WHERE id = ?";
            $update_stmt = $db->prepare($update_query);
            $update_stmt->bind_param("si", $hashed_password, $user_id);

            if ($update_stmt->execute()) {
                $success[] = 'password changed';
            } else {
                $errors['update'] = 'error while updating password';
            }
        } else {
            $errors['current_password'] = 'current password is wrong!';
        }
    }
}
?>

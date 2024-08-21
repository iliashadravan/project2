<?php

global $db, $user;
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../function.query.php'; // بارگذاری فایل شامل تابع

$errors = [];
$success = [];
$user_id = $_GET['id'] ?? null;

// واکشی اطلاعات کاربر برای ویرایش
$user = getUserById($user_id, $db);

if (!$user) {
    die('User not found.');
}

// بررسی درخواست برای ویرایش کاربر
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $phone_number = $_POST['phone_number'] ?? '';
    $password = $_POST['password'] ?? '';

    // اعتبارسنجی ورودی‌ها
    if (empty($firstname)) {
        $errors['firstname'] = 'First name is required.';
    }
    if (empty($lastname)) {
        $errors['lastname'] = 'Last name is required.';
    }
    if (empty($phone_number)) {
        $errors['phone_number'] = 'Phone number is required.';
    }
    if (!empty($password)) {
        // رمز عبور جدید
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    } else {
        $hashed_password = $user['password']; // استفاده از رمز عبور قبلی در صورت خالی بودن
    }

    if (empty($errors)) {
        // بروزرسانی اطلاعات کاربر
        $query = "UPDATE users SET firstname = ?, lastname = ?, phone_number = ?, password = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param('ssssi', $firstname, $lastname, $phone_number, $hashed_password, $user_id);

        if ($stmt->execute()) {
            $success['message'] = "User updated successfully.";
        } else {
            $errors['message'] = "Error updating user. Please try again.";
        }
    }
}

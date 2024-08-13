<?php

global $db;
require_once 'db.php';

$errors = [];
$success = [];
$user_id = $_GET['id'] ?? null;

// بررسی اینکه آیا کاربر مجاز به ویرایش است
//if (!isset($user_id) || !is_numeric($user_id)) {
//    die('Invalid user ID.');
//}

// واکشی اطلاعات کاربر برای ویرایش
$query = "SELECT id, firstname, lastname, phone_number, password FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

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

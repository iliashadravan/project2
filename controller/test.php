<?php

// بارگذاری فایل‌های لازم
require_once '../controller/db.php';
// فرض کنید که داده‌های کاربر را از دیتابیس واکشی کرده‌اید
$user_data = [
    'id' => 1,
    'firstname' => 'John',
    'lastname' => 'Doe',
    'is_active' => 1,
    'is_admin' => 1 // یا true برای ادمین
];

$_SESSION['user'] = $user_data;
?>
<?php if (isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin'] == 1) : ?>
    <li><a href="register.php">Register New User</a></li>
    <li><a href="other.users.activities.php">Users Activities</a></li>
    <li><a href="users.situation.php">Users Situation</a></li>
<?php endif; ?>



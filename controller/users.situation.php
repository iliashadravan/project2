<?php
// اتصال به پایگاه داده
global $db;
require_once 'db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$errors = [];
$success = [];

// پردازش درخواست‌های فعال و غیرفعال کردن کاربران
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deactivate_user']) || isset($_POST['activate_user'])) {
        $user_id = intval($_POST['user_id']);

        // دریافت اطلاعات کاربر از پایگاه داده
        $query_user = "SELECT is_admin, is_active FROM users WHERE id = ?";
        $stmt = $db->prepare($query_user);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if ($user_data) {
            $is_active = isset($_POST['deactivate_user']) ? 0 : 1;

            // جلوگیری از غیرفعال کردن خودکار ادمین
            if ($user_data['is_admin'] && $is_active === 0) {
                $errors[] = "ادمین نمی‌تواند خود را غیرفعال کند.";
            } else {
                $query = "UPDATE users SET is_active = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $is_active, $user_id);
                if ($stmt->execute()) {
                    $success[] = isset($_POST['deactivate_user']) ? "User inactive successfully" : "User active successfully.";
                } else {
                    $errors[] = "خطا در به‌روزرسانی وضعیت کاربر: " . $stmt->error;
                }
            }
        } else {
            $errors[] = "کاربر یافت نشد.";
        }
    }
}

// واکشی لیست تمام کاربران
$query = "SELECT id, firstname, lastname, is_active, is_admin FROM users";
$result = $db->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

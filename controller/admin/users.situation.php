<?php
// اتصال به پایگاه داده
global $db;
require_once __DIR__ . '/../db.php';
require_once __DIR__ .'/../function.query.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;

}
$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

if ($user['is_admin'] != 1) {
    header('Location: ../goback.html');
    exit;
}

$errors = [];
$success = [];

// پردازش درخواست‌های فعال و غیرفعال کردن کاربران
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deactivate_user']) || isset($_POST['activate_user'])) {
        $user_id = intval($_POST['user_id']);

        // دریافت اطلاعات کاربر از پایگاه داده با استفاده از تابع
        $user_data = getUserStatusById($user_id, $db);

        if ($user_data) {
            $is_active = isset($_POST['deactivate_user']) ? 0 : 1;

            // جلوگیری از غیرفعال کردن خودکار ادمین
            if ($user_data['is_admin'] && $is_active === 0) {
                $errors[] = "Admin cannot be deactivated";
            } else {
                $query = "UPDATE users SET is_active = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $is_active, $user_id);
                if ($stmt->execute()) {
                    $success[] = isset($_POST['deactivate_user']) ? "user active successfully" : "user inactive successfully";
                } else {
                    $errors[] = "Error updating user status:" . $stmt->error;
                }
            }
        } else {
            $errors[] = "user not found";
        }
    }
}

// واکشی لیست تمام کاربران
$users = getAllUsers($db);
?>

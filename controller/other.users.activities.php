<?php
// اتصال به پایگاه داده
global $db;
require_once 'db.php';
$errors = [];
$success = [];

// بررسی درخواست برای غیرفعال یا فعال کردن کاربر
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['deactivate_user'])) {
        $user_id = $_POST['user_id'];

        // بررسی اینکه آیا کاربر در حال تلاش برای غیرفعال کردن خودش است
        if ($user_id == $_SESSION['user_id']) {
            $errors['message'] = "You cannot deactivate your own account.";
        } else {
            // بررسی اینکه آیا کاربر ادمین است یا نه
            $query = "SELECT is_admin FROM users WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_info = $result->fetch_assoc();

            if ($user_info['is_admin'] == 1) {
                $errors['message'] = "You cannot deactivate an admin account.";
            } else {
                // بروزرسانی وضعیت کاربر به غیرفعال
                $query = "UPDATE users SET is_active = 1 WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('i', $user_id);

                if ($stmt->execute()) {
                    $success['message'] = "User deactivated successfully.";
                } else {
                    $errors['message'] = "Error deactivating user. Please try again.";
                }
            }
        }
    }

    if (isset($_POST['activate_user'])) {
        $user_id = $_POST['user_id'];

        // بررسی اینکه آیا کاربر در حال تلاش برای فعال کردن خودش است
        if ($user_id == $_SESSION['user_id']) {
            $errors['message'] = "You cannot activate your own account.";
        } else {
            // بروزرسانی وضعیت کاربر به فعال
            $query = "UPDATE users SET is_active = 0 WHERE id = ?";
            $stmt = $db->prepare($query);
            $stmt->bind_param('i', $user_id);

            if ($stmt->execute()) {
                $success['message'] = "User activated successfully.";
            } else {
                $errors['message'] = "Error activating user. Please try again.";
            }
        }
    }
}

// واکشی لیست تمام کاربران
$query = "SELECT id, firstname, lastname, is_active, is_admin FROM users";
$result = $db->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>
<?php
global $db;
require_once 'db.php';

// بررسی ورود کاربر
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// واکشی اطلاعات کاربر برای نمایش در صفحه
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// ذخیره اطلاعات کاربر در $_SESSION
$_SESSION['user'] = $user;
$errors = [];
$success = [];

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // برای ثبت زمان ورود
    if (isset($_POST['clock_in'])) {
        $sql = "INSERT INTO work_time (user_id, clock_in, date) VALUES (?, NOW(), CURDATE())";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $user_id);

        if ($stmt->execute()) {
            $success['message'] = "The login time was successfully registered.";
        } else {
            $errors['message'] = "Error while registering time. Please try again. " . $stmt->error;
        }
    }

    // برای ثبت زمان خروج
    if (isset($_POST['clock_out'])) {
        $report = $_POST['report'];

        // بررسی اینکه گزارش نوشته شده است یا خیر
        if (empty($report)) {
            $errors['report'] = 'Writing report is necessary';
        } else {
            // پیدا کردن آخرین رکورد با زمان ورود ثبت شده و زمان خروج خالی برای تاریخ امروز
            $sql = "SELECT id FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL AND date = CURDATE() ORDER BY id DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $work_time = $result->fetch_assoc();

            if ($work_time) {
                $work_time_id = $work_time['id'];

                // ثبت زمان خروج و گزارش فقط در آخرین رکورد با زمان خروج خالی
                $sql = "UPDATE work_time SET clock_out = NOW(), report = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('si', $report, $work_time_id);

                if ($stmt->execute()) {
                    $success['message'] = "The exit time was successfully registered.";
                } else {
                    $errors['message'] = "Error updating exit time. Please try again. " . $stmt->error;
                }
            } else {
                // اگر هیچ رکوردی با زمان ورود و زمان خروج خالی پیدا نشد
                $errors['message'] = "No clock-in record found for today. Please register your clock-in time first.";
            }
        }
    }
}

// واکشی اطلاعات کاربر برای نمایش در صفحه
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// اگر کاربر پیدا نشد، به صفحه ورود برگردید
if (!$user) {
    header('Location: login.php');
    exit;
}

// واکشی داده‌های تایم ورود و خروج
$query = "SELECT * FROM work_time WHERE user_id = ? ORDER BY date DESC";
$stmt = $db->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$work_times = $stmt->get_result();
?>

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

// واکشی زمان‌های استاندارد از جدول settings
$query = "SELECT standard_clock_in, standard_clock_out FROM setting WHERE id = 1"; // یا ID مناسب برای تنظیمات شما
$stmt = $db->prepare($query);
$stmt->execute();
$settings = $stmt->get_result()->fetch_assoc();

$standard_clock_in = new DateTime($settings['standard_clock_in']);
$standard_clock_out = new DateTime($settings['standard_clock_out']);

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // برای ثبت زمان ورود
    if (isset($_POST['clock_in'])) {
        // بررسی اینکه آیا کاربر زمان خروج قبلی را ثبت کرده است یا خیر
        $sql = "SELECT id FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL ORDER BY id DESC LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $work_time = $result->fetch_assoc();

        if ($work_time) {
            // اگر رکوردی با زمان خروج ثبت نشده وجود دارد، پیام خطا نمایش داده می‌شود
            $errors['clock_in'] = "You have not clocked out yet. Please clock out before clocking in again.";
        } else {
            // ثبت زمان ورود جدید
            $sql = "INSERT INTO work_time (user_id, clock_in, date) VALUES (?, NOW(), CURDATE())";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $user_id);

            if ($stmt->execute()) {
                $success['message'] = "The login time was successfully registered.";
            } else {
                $errors['message'] = "Error while registering time. Please try again. " . $stmt->error;
            }
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
            $sql = "SELECT id, clock_in FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL AND date = CURDATE() ORDER BY id DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $work_time = $result->fetch_assoc();

            if ($work_time) {
                $work_time_id = $work_time['id'];
                $clock_in = new DateTime($work_time['clock_in']);
                $clock_out = new DateTime(); // زمان خروج فعلی

                // محاسبه تأخیر در ورود
                $delay_in = max($clock_in->getTimestamp() - $standard_clock_in->getTimestamp(), 0);

                // محاسبه تأخیر در خروج
                $delay_out = max($standard_clock_out->getTimestamp() - $clock_out->getTimestamp(), 0);

                // محاسبه تأخیر کل
                $total_delay = $delay_in + $delay_out;
                $total_delay_formatted = gmdate('H:i:s', $total_delay);

                // ثبت زمان خروج و گزارش
                $sql = "UPDATE work_time SET clock_out = NOW(), report = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('si', $report, $work_time_id);

                if ($stmt->execute()) {
                    // ثبت تأخیر در جدول delay_time
                    $sql = "INSERT INTO delay_time (date, total_delay, user_id, work_time_id) VALUES (CURDATE(), ?, ?, ?)";
                    $stmt = $db->prepare($sql);
                    $stmt->bind_param('sii', $total_delay_formatted, $user_id, $work_time_id);

                    if ($stmt->execute()) {
                        $success['message'] = "The exit time and total delay were successfully registered.";
                    } else {
                        $errors['message'] = "Error updating delay information. Please try again. " . $stmt->error;
                    }
                } else {
                    $errors['message'] = "Error updating exit time. Please try again. " . $stmt->error;
                }
            } else {
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

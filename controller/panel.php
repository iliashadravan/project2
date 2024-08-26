<?php
global $db, $result, $user, $work_time;
require_once 'db.php';
require_once 'function.query.php';
require_once '../vendor/autoload.php';
use Hekmatinasser\Verta\Verta;

// بررسی ورود کاربر
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// واکشی اطلاعات کاربر برای نمایش در صفحه
$userData = getUserDataById($db, $user_id);

// ذخیره اطلاعات کاربر در $_SESSION
$_SESSION['user'] = $user;
$errors = [];
$success = [];

// واکشی زمان‌های استاندارد از جدول settings
$settings = getStandardClockSettings($db);

$standard_clock_in = new Verta($settings['standard_clock_in']);
$standard_clock_out = new Verta($settings['standard_clock_out']);

// پردازش فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['clock_in'])) {
        $work_time = getLastUnclockedWorkTime($db, $user_id);

        date_default_timezone_set('Asia/Tehran'); // تنظیم ساعت بر تهران
        $time = date('Y-m-d H:i:s');

        if ($work_time) {
            $errors['clock_in'] = "You have not clocked out yet. Please clock out before clocking in again.";
        } else {
            $clock_in_shamsi = (new Verta())->format('Y-m-d H:i:s'); // زمان ورود به شمسی
            $sql = "INSERT INTO work_time (user_id, clock_in, date) VALUES (?, ?, CURDATE())";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ss', $user_id, $clock_in_shamsi);

            if ($stmt->execute()) {
                $success['message'] = "The login time was successfully registered.";
            } else {
                $errors['message'] = "Error while registering time. Please try again. " . $stmt->error;
            }
        }
    }

    if (isset($_POST['clock_out'])) {
        $report = $_POST['report'];

        if (empty($report)) {
            $errors['report'] = 'Writing report is necessary';
        } else {
            $work_time = getCurrentWorkTime($db, $user_id);

            date_default_timezone_set('Asia/Tehran'); // تنظیم ساعت بر تهران
            $time = date('Y-m-d H:i:s');

            if ($work_time) {
                $work_time_id = $work_time['id'];
                $clock_in = new Verta($work_time['clock_in']);
                $clock_out = new Verta(); // زمان خروج فعلی به شمسی

                // تأخیر ورود فقط وقتی محاسبه شود که کاربر دیرتر از ساعت استاندارد وارد شده باشد
                $delay_in = max($clock_in->timestamp > $standard_clock_in->timestamp ? $clock_in->timestamp - $standard_clock_in->timestamp : 0, 0);

                // تأخیر خروج فقط وقتی محاسبه شود که کاربر زودتر از ساعت استاندارد خارج شده باشد
                $delay_out = max($clock_out->timestamp < $standard_clock_out->timestamp ? $standard_clock_out->timestamp - $clock_out->timestamp : 0, 0);

                $total_delay = $delay_in + $delay_out;
                $total_delay_formatted = gmdate('H:i:s', $total_delay);

                $clock_out_shamsi = $clock_out->format('Y-m-d H:i:s'); // زمان خروج به شمسی

                $sql = "UPDATE work_time SET clock_out = ?, report = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->bind_param('ssi', $clock_out_shamsi, $report, $work_time_id);

                if ($stmt->execute()) {
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
            }
        }
    }
}

// فیلتر کردن ورود و خروج‌های روز جاری
$date_today = date('Y-m-d'); // تاریخ روز جاری به میلادی
$work_times = getWorkTimesByUserIdAndDate($db, $user_id, $date_today);

// تابع برای تبدیل تاریخ میلادی به شمسی
function convertToJalali($date) {
    $verta = new Verta($date);
    return $verta->format('Y-m-d'); // بازگشت تاریخ شمسی به فرمت مورد نظر
}

// تابع برای فرمت‌کردن ثانیه‌ها به ساعت، دقیقه و ثانیه
function formatSeconds($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?>
<?php
global $user, $db;
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer
require_once 'db.php';
require_once 'function.query.php';

use Hekmatinasser\Verta\Verta;
$errors = [];
$success = [];

$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

if ($user['is_admin'] != 1) {
    header('Location: ../view/goback.html');
    exit;
}

// دریافت ماه و سال انتخاب شده از فرم یا استفاده از مقادیر پیش‌فرض
$target_year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$target_month = isset($_POST['month']) ? str_pad(intval($_POST['month']), 2, '0', STR_PAD_LEFT) : date('m');

// تبدیل سال و ماه میلادی به شمسی
function convertToJalali($year, $month) {
    $date = new Verta("$year-$month-01");
    return [
        'year' => $date->format('Y'),
        'month' => $date->format('F')
    ];
}

$jalali_date = convertToJalali($target_year, $target_month);
$persian_year = $jalali_date['year'];
$persian_month_name = $jalali_date['month'];

// ضریب ساعات کاری در روزهای تعطیل (جمعه و شنبه)
$weekend_multiplier = 1.4;

// پردازش ساعات کاری
$query_work = "
    SELECT 
        user_id,
        TIMESTAMPDIFF(SECOND, clock_in, clock_out) AS work_seconds,
        DATE(date) AS work_date
    FROM work_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ? 
";
$stmt = $db->prepare($query_work);
$target_date = $target_year . '-' . $target_month;
$stmt->bind_param('s', $target_date);
$stmt->execute();
$work_data = $stmt->get_result();

$work_times = [];
$holiday_work_times_without_multiplier = [];

while ($row = $work_data->fetch_assoc()) {
    $user_id = $row['user_id'];
    $work_seconds = $row['work_seconds'];
    $work_date = new DateTime($row['work_date']);
    $day_of_week = $work_date->format('w'); // 0 (برای یکشنبه) تا 6 (برای شنبه)

    if ($day_of_week == 5 || $day_of_week == 6) { // جمعه (5) یا شنبه (6)
        if (!isset($holiday_work_times_without_multiplier[$user_id])) {
            $holiday_work_times_without_multiplier[$user_id] = 0;
        }
        $holiday_work_times_without_multiplier[$user_id] += $work_seconds; // ذخیره ساعات واقعی بدون ضریب

        $work_seconds *= $weekend_multiplier; // اعمال ضریب 1.4 به ساعات کاری
    }

    if (!isset($work_times[$user_id])) {
        $work_times[$user_id] = 0;
    }
    $work_times[$user_id] += $work_seconds;
}

// پردازش ساعات تأخیر (فقط برای روزهای کاری)
$query_delay = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds,
        DATE(date) AS delay_date
    FROM delay_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ?
    GROUP BY user_id, DATE(date)
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('s', $target_date);
$stmt->execute();
$delay_data = $stmt->get_result();

$delay_times = [];
$holiday_delay_times = [];

while ($row = $delay_data->fetch_assoc()) {
    $user_id = $row['user_id'];
    $delay_seconds = $row['total_delay_seconds'];
    $delay_date = new DateTime($row['delay_date']);
    $day_of_week = $delay_date->format('w'); // 0 (برای یکشنبه) تا 6 (برای شنبه)

    // فقط برای روزهای کاری (غیر تعطیل)
    if ($day_of_week != 5 && $day_of_week != 6) { // غیر از جمعه و شنبه
        if (!isset($delay_times[$user_id])) {
            $delay_times[$user_id] = 0;
        }
        $delay_times[$user_id] += $delay_seconds;
    }
}
function formatSeconds($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

$users = getAllUsers($db);
?>

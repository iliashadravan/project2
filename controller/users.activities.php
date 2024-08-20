<?php
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer

use Hekmatinasser\Verta\Verta;

// اتصال به پایگاه داده
global $db;
require_once '../controller/db.php';
require_once '../controller/function.query.php';

// شروع جلسه برای دسترسی به اطلاعات کاربر لاگین شده
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($user_id === 0) {
    die('کاربر لاگین نکرده است.');
}

$errors = [];
$success = [];

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

$weekend_multiplier = 1.4;

// دریافت ساعت کاری و تأخیر برای کاربر لاگین شده
$query_work = "
    SELECT 
        user_id,
        DATE(date) AS work_date,
        TIME_TO_SEC(TIMEDIFF(clock_out, clock_in)) AS work_seconds
    FROM work_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
";
$stmt = $db->prepare($query_work);
$target_date = $target_year . '-' . $target_month;
$stmt->bind_param('is', $user_id, $target_date);
$stmt->execute();
$work_data = $stmt->get_result();

$total_work_seconds = 0;
$holiday_work_seconds = 0;
$regular_work_seconds = 0;
$holiday_work_seconds_without_multiplier = 0; // اضافه کردن متغیر برای ذخیره ساعات بدون ضریب

while ($row = $work_data->fetch_assoc()) {
    $work_seconds = $row['work_seconds'];
    $work_date = new DateTime($row['work_date']);
    $day_of_week = $work_date->format('w'); // 0 (برای یکشنبه) تا 6 (برای شنبه)

    if ($day_of_week == 5 || $day_of_week == 6) { // جمعه یا شنبه
        $holiday_work_seconds_without_multiplier += $work_seconds; // ذخیره ساعات بدون ضریب
        $holiday_work_seconds += $work_seconds * $weekend_multiplier; // اعمال ضریب 1.4
    } else {
        $regular_work_seconds += $work_seconds;
    }
}

// تابع تبدیل ثانیه‌ها به فرمت H:i:s
function formatSeconds($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

$total_work_seconds = $regular_work_seconds + $holiday_work_seconds;

// تبدیل زمان‌ها به فرمت H:i:s با استفاده از تابع جدید
$final_work_time = formatSeconds($total_work_seconds);
$regular_work_time = formatSeconds($regular_work_seconds);
$holiday_work_time = formatSeconds($holiday_work_seconds);
$holiday_work_time_without_multiplier = formatSeconds($holiday_work_seconds_without_multiplier); // نمایش ساعت بدون ضریب

// دریافت مجموع ساعات تأخیر (فقط روزهای غیرتعطیل)
$query_delay = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds,
        DATE(date) AS delay_date
    FROM delay_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
    GROUP BY user_id, DATE(date)
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('is', $user_id, $target_date);
$stmt->execute();
$delay_data = $stmt->get_result();

$delay_time_seconds = 0;
while ($row = $delay_data->fetch_assoc()) {
    $delay_date = new DateTime($row['delay_date']);
    $day_of_week = $delay_date->format('w');

    if ($day_of_week != 5 && $day_of_week != 6) {
        $delay_time_seconds += $row['total_delay_seconds'];
    }
}

$delay_time = formatSeconds($delay_time_seconds);

// دریافت اطلاعات کاربر
$user = getUserById($user_id, $db);
if (!$user) {
    die('اطلاعات کاربر یافت نشد.');
}
?>

<?php
global $user, $db;
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer
require_once 'db.php';
require_once 'function.query.php';

use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

date_default_timezone_set('Asia/Tehran');

// دریافت شماره تلفن کاربر از session
$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number); // فرض شده تابعی برای دریافت کاربر وجود دارد

// دریافت تاریخ و زمان کنونی با استفاده از تایم‌زون تنظیم‌شده
$verta = Verta::now();
// دریافت سال و ماه جاری با در نظر گرفتن تایم‌زون
$target_year = $_POST['year'] ?? $verta->year;
$target_month = $_POST['month'] ?? $verta->month;

$verta_jalali = Verta::parse("$target_year-$target_month-1");  // ایجاد تاریخ شمسی برای اول ماه انتخابی
$start_jalali = (clone $verta_jalali)->startMonth();           // تعیین تاریخ شروع ماه انتخابی
$end_jalali = (clone $verta_jalali)->endMonth();

$start_gregorian = $start_jalali->toCarbon()->format('Y-m-d');
$end_gregorian = $end_jalali->toCarbon()->format('Y-m-d');

// ضریب ساعات کاری در روزهای تعطیل (جمعه و شنبه)
$weekend_multiplier = 1.4;
$standard_work_hours_per_day = 9; // ساعت کاری استاندارد روزانه

// پردازش ساعات کاری
$query_work = "
SELECT
user_id,
TIMESTAMPDIFF(SECOND, clock_in, clock_out) AS work_seconds,
DATE(date) AS work_date
FROM work_time
WHERE DATE(date) BETWEEN ? AND ?
";
$work_data = getWorkData($db, $query_work, $start_gregorian, $end_gregorian);

$monthly_work_times = [];
$holiday_work_times_without_multiplier = [];

// پردازش داده‌های کاری
while ($row = $work_data->fetch_assoc()) {
    $user_id = $row['user_id'];
    $work_seconds = $row['work_seconds'];
    $work_date = $row['work_date'];

    if (!isset($monthly_work_times[$user_id])) {
        $monthly_work_times[$user_id] = 0;
    }

    if (!isset($holiday_work_times_without_multiplier[$user_id])) {
        $holiday_work_times_without_multiplier[$user_id] = 0;
    }

    // محاسبه ساعات کاری واقعی و ذخیره آنها
    $day_of_week = (new Carbon($work_date))->dayOfWeek;
    if ($day_of_week == Carbon::FRIDAY || $day_of_week == Carbon::SATURDAY) {
        $holiday_work_times_without_multiplier[$user_id] += $work_seconds; // ذخیره ساعات واقعی بدون ضریب
        $work_seconds *= $weekend_multiplier; // اعمال ضریب 1.4 به ساعات کاری
    }

    $monthly_work_times[$user_id] += $work_seconds;
}

// مجموع ساعات کاری ماهانه با ضریب 1.4 برای روزهای تعطیل
$total_monthly_work_seconds = $monthly_work_times[$user['id']] ?? 0; // استفاده از شناسه کاربر برای مقداردهی صحیح
$total_days_in_month = Carbon::createFromFormat('Y-m-d', $start_gregorian)->daysInMonth;

// محاسبه تعداد روزهای غیرتعطیل
$weekend_days = 0;
for ($day = 1; $day <= $total_days_in_month; $day++) {
    $date = Carbon::create($target_year, $target_month, $day);
    if ($date->isFriday() || $date->isSaturday()) {
        $weekend_days++;
    }
}

$work_days = $total_days_in_month - $weekend_days;
$expected_monthly_work_seconds = $work_days * $standard_work_hours_per_day * 3600;

// محاسبه تأخیر ماهانه بدون در نظر گرفتن ساعات کاری جمعه و شنبه
$delay_seconds = max(0, $expected_monthly_work_seconds - $total_monthly_work_seconds);

// مجموع ساعات کاری در روزهای تعطیل بدون ضریب
$total_holiday_work_seconds = $holiday_work_times_without_multiplier[$user['id']] ?? 0;

// تابع فرمت‌کردن ثانیه‌ها به ساعت، دقیقه و ثانیه
function formatSeconds($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}


?>

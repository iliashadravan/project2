<?php
global $user, $db;
require_once __DIR__ . '/../../vendor/autoload.php'; // بارگذاری autoload Composer
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../function.query.php';

use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

date_default_timezone_set('Asia/Tehran');

// دریافت شماره تلفن کاربر از session
$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

// دریافت تاریخ و زمان کنونی با استفاده از تایم‌زون تنظیم‌شده
$verta = Verta::now();
$target_year = $_POST['year'] ?? $verta->year;
$target_month = $_POST['month'] ?? $verta->month;

$verta_jalali = Verta::parse("$target_year-$target_month-1");
$start_jalali = (clone $verta_jalali)->startMonth();
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
$user_work_dates = [];
$non_weekend_work_seconds = [];

// پردازش داده‌های کاری
while ($row = $work_data->fetch_assoc()) {
    $user_id = $row['user_id'];
    $work_seconds = $row['work_seconds'];
    $work_date = $row['work_date'];

    if (!isset($monthly_work_times[$user_id])) {
        $monthly_work_times[$user_id] = 0;
        $holiday_work_times_without_multiplier[$user_id] = 0;
        $user_work_dates[$user_id] = [];
        $non_weekend_work_seconds[$user_id] = 0;
    }

    $day_of_week = (new Carbon($work_date))->dayOfWeek;
    if ($day_of_week == Carbon::FRIDAY || $day_of_week == Carbon::SATURDAY) {
        $holiday_work_times_without_multiplier[$user_id] += $work_seconds;
        $work_seconds *= $weekend_multiplier; // اعمال ضریب به ساعات کاری
    } else {
        $non_weekend_work_seconds[$user_id] += $work_seconds;
    }

    if (!in_array($work_date, $user_work_dates[$user_id])) {
        $user_work_dates[$user_id][] = $work_date;
    }

    $monthly_work_times[$user_id] += $work_seconds;
}

// محاسبه مجموع ساعات کاری ماهانه با ضریب 1.4 برای روزهای تعطیل
$total_monthly_work_seconds = 0;
$total_days_in_month = Carbon::createFromFormat('Y-m-d', $start_gregorian)->daysInMonth;

// محاسبه تعداد روزهای تعطیل
$weekend_days = 0;
for ($day = 1; $day <= $total_days_in_month; $day++) {
    $date = Carbon::create($target_year, $target_month, $day);
    if ($date->isFriday() || $date->isSaturday()) {
        $weekend_days++;
    }
}

// محاسبه تعداد روزهای کاری
$work_days = $total_days_in_month - $weekend_days;
$expected_monthly_work_seconds = $work_days * $standard_work_hours_per_day * 3600;

// جمع‌بندی مجموع ساعات کاری در روزهای عادی و روزهای تعطیل با ضریب
foreach ($monthly_work_times as $user_id => $total_work_seconds) {
    $holiday_work_seconds = $holiday_work_times_without_multiplier[$user_id] * $weekend_multiplier;
    $user_total_work_seconds = $non_weekend_work_seconds[$user_id] + $holiday_work_seconds;
    $total_monthly_work_seconds += $user_total_work_seconds;
}

// محاسبه تأخیر ماهانه
$delay_seconds = [];
foreach ($user_work_dates as $user_id => $dates) {
    $user_work_seconds = $non_weekend_work_seconds[$user_id] ?? 0;
    $holiday_work_seconds = $holiday_work_times_without_multiplier[$user_id] * $weekend_multiplier;
    $user_total_work_seconds = $user_work_seconds + $holiday_work_seconds;
    $user_delay_seconds = max(0, $expected_monthly_work_seconds - $user_total_work_seconds);

    $delay_seconds[$user_id] = $user_delay_seconds;
}

// تابع فرمت‌کردن ثانیه‌ها به ساعت، دقیقه و ثانیه
function formatSeconds($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?>

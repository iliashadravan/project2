<?php
global $user, $db;
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer
require_once 'db.php';
require_once 'function.query.php';
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

$errors = [];
$success = [];

$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

$verta = Verta::now('Asia/Tehran');

// دریافت سال و ماه جاری با در نظر گرفتن تایم‌زون
$target_year = isset($_POST['year']) ? intval($_POST['year']) : $verta->year;
$target_month = isset($_POST['month']) ? str_pad(intval($_POST['month']), 2, '0', STR_PAD_LEFT) : $verta->month;

// تبدیل سال و ماه به شمسی با استفاده از Verta
$verta_jalali = new Verta();
$verta_jalali->setDate($target_year, $target_month, 1);

// دریافت اول و آخر ماه شمسی
$start_jalali = $verta_jalali->startMonth();
$end_jalali = $verta_jalali->endMonth();

// تبدیل تاریخ‌های شمسی به میلادی
$start_gregorian = $start_jalali->format('Y-m-d');
$end_gregorian = $end_jalali->format('Y-m-d');


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

// محاسبه مجموع ساعات کاری ماهانه با ضریب 1.4 برای روزهای تعطیل
$total_monthly_work_seconds = array_sum($monthly_work_times);
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

// تابع فرمت‌کردن ثانیه‌ها به ساعت، دقیقه و ثانیه
function formatSeconds($seconds) {
$hours = floor($seconds / 3600);
$minutes = floor(($seconds % 3600) / 60);
$seconds = $seconds % 60;
return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}

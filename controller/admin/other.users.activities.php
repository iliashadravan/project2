<?php
global $user, $db;
require_once __DIR__. '/../../vendor/autoload.php'; // بارگذاری autoload Composer
require_once __DIR__.'/../db.php';
require_once __DIR__. '/../function.query.php';
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

$errors = [];
$success = [];

// دریافت شماره تلفن کاربر از session
$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

// بررسی اینکه آیا کاربر ادمین است یا نه
if ($user['is_admin'] != 1) {
    header('Location: ../goback.html');
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
        'month' => $date->formatWord('F')
    ];
}

$jalali_date = convertToJalali($target_year, $target_month);
$persian_year = $jalali_date['year'];
$persian_month_name = $jalali_date['month'];

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
    WHERE DATE_FORMAT(date, '%Y-%m') = ? 
";
$work_data = getWorkData($db, $query_work, $target_year, $target_month);

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
$total_days_in_month = Carbon::create($target_year, $target_month, 1)->daysInMonth;
$expected_monthly_work_seconds = $total_days_in_month * $standard_work_hours_per_day * 3600;

// محاسبه تأخیر ماهانه
$delay_seconds = max(0, $expected_monthly_work_seconds - $total_monthly_work_seconds);

// تابع فرمت‌کردن ثانیه‌ها به ساعت، دقیقه و ثانیه
function formatSeconds($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?>
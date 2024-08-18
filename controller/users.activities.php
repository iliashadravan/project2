<?php
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer

use Hekmatinasser\Verta\Verta;

// اتصال به پایگاه داده
global $db;
require_once 'db.php';
require_once 'function.query.php';
$errors = [];
$success = [];

// دریافت ماه و سال انتخاب شده از فرم یا استفاده از مقادیر پیش‌فرض
$target_year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$target_month = isset($_POST['month']) ? str_pad(intval($_POST['month']), 2, '0', STR_PAD_LEFT) : date('m');

// تبدیل سال و ماه میلادی به شمسی
function convertToJalali($year, $month) {
    $date = Verta::create($year, $month, 1); // تبدیل تاریخ میلادی به تاریخ شمسی
    return [
        'year' => $date->format('Y'),
        'month' => $date->format('m')
    ];
}

$jalali_date = convertToJalali($target_year, $target_month);
$persian_year = $jalali_date['year'];
$persian_month_number = $jalali_date['month']; // شماره ماه شمسی

// لیست نام‌های ماه‌های شمسی
$persian_month_names = [
    '01' => 'فروردین',
    '02' => 'اردیبهشت',
    '03' => 'خرداد',
    '04' => 'تیر',
    '05' => 'مرداد',
    '06' => 'شهریور',
    '07' => 'مهر',
    '08' => 'آبان',
    '09' => 'آذر',
    '10' => 'دی',
    '11' => 'بهمن',
    '12' => 'اسفند'
];

$persian_month_name = $persian_month_names[$persian_month_number];

// پردازش درخواست‌های فعال و غیرفعال کردن کاربران
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deactivate_user']) || isset($_POST['activate_user'])) {
        $user_id = intval($_POST['user_id']);

        // دریافت اطلاعات کاربر از پایگاه داده
        $user_data = getUserStatusById($user_id, $db);
    }
}

// دریافت مجموع ساعت کاری و تاخیر برای هر کاربر در ماه و سال انتخاب شده
$query_work = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) AS total_work_seconds
    FROM work_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ? 
    GROUP BY user_id
";
$stmt = $db->prepare($query_work);
$target_date = $target_year . '-' . $target_month;
$stmt->bind_param('s', $target_date);
$stmt->execute();
$work_data = $stmt->get_result();

$work_times = [];
while ($row = $work_data->fetch_assoc()) {
    $work_times[$row['user_id']] = gmdate('H:i:s', $row['total_work_seconds']);
}

$query_delay = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds
    FROM delay_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ?
    GROUP BY user_id
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('s', $target_date);
$stmt->execute();
$delay_data = $stmt->get_result();

$delay_times = [];
while ($row = $delay_data->fetch_assoc()) {
    $delay_times[$row['user_id']] = gmdate('H:i:s', $row['total_delay_seconds']);
}

$users = getAllUsers($db);
?>
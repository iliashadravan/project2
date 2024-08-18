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

// دریافت مجموع ساعت کاری و تأخیر برای کاربر لاگین شده در ماه و سال انتخاب شده
$query_work = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) AS total_work_seconds
    FROM work_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ? 
    GROUP BY user_id
";
$stmt = $db->prepare($query_work);
$target_date = $target_year . '-' . $target_month;
$stmt->bind_param('is', $user_id, $target_date);
$stmt->execute();
$work_data = $stmt->get_result();

if ($work_data->num_rows > 0) {
    while ($row = $work_data->fetch_assoc()) {
        $work_times[$row['user_id']] = gmdate('H:i:s', $row['total_work_seconds']);
    }
} else {
    $work_times[$user_id] = 'N/A';
}

$query_delay = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds
    FROM delay_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%Y-%m') = ?
    GROUP BY user_id
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('is', $user_id, $target_date);
$stmt->execute();
$delay_data = $stmt->get_result();

if ($delay_data->num_rows > 0) {
    while ($row = $delay_data->fetch_assoc()) {
        $delay_times[$row['user_id']] = gmdate('H:i:s', $row['total_delay_seconds']);
    }
} else {
    $delay_times[$user_id] = 'N/A';
}

// دریافت اطلاعات کاربر لاگین شده
$user = getUserById($user_id, $db);
if (!$user) {
    die('اطلاعات کاربر یافت نشد.');
}
?>
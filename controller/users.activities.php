<?php
global $db;
require_once 'db.php';
$target_month = '08'; // ماه مورد نظر برای نمایش (August)
$user_id = $_SESSION['user_id'];

// واکشی مجموع ساعت‌های کاری ماهانه از جدول work_time
$query_work = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') AS month, 
        SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) AS total_work_seconds
    FROM work_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%m') = ? 
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month
";
$stmt = $db->prepare($query_work);
$stmt->bind_param('is', $user_id, $target_month);
$stmt->execute();
$work_data = $stmt->get_result();

$total_work_seconds = 0;
if ($row = $work_data->fetch_assoc()) {
    $total_work_seconds = $row['total_work_seconds'];
}

// واکشی مجموع تأخیرهای ماهانه از جدول delay_time
$query_delay = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') AS month, 
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds
    FROM delay_time 
    WHERE user_id = ? AND DATE_FORMAT(date, '%m') = ? 
    GROUP BY DATE_FORMAT(date, '%Y-%m') 
    ORDER BY month
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('is', $user_id, $target_month);
$stmt->execute();
$delay_data = $stmt->get_result();

$total_delay_seconds = 0;
if ($row = $delay_data->fetch_assoc()) {
    $total_delay_seconds = $row['total_delay_seconds'];
}

// تبدیل ثانیه‌ها به فرمت ساعت:دقیقه:ثانیه
$total_work_time = gmdate('H:i:s', $total_work_seconds);
$total_delay_time = gmdate('H:i:s', $total_delay_seconds);

$month_names = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
];

$month_name = $month_names[$target_month];


<?php
global $db;
require_once '../controller/other.users.activities.php';

// واکشی مجموع ساعت‌های کاری ماهانه از جدول work_time
$query_work = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') AS month, 
        SUM(TIME_TO_SEC(clock_out) - TIME_TO_SEC(clock_in)) AS total_work_seconds
    FROM work_time 
    WHERE user_id = ? 
    GROUP BY DATE_FORMAT(date, '%Y-%m') 
    ORDER BY month
";
$stmt = $db->prepare($query_work);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$work_data = $stmt->get_result();

// ایجاد آرایه برای نگهداری اطلاعات ماهانه
$months = [];
while ($row = $work_data->fetch_assoc()) {
    $months[$row['month']]['total_work'] = $row['total_work_seconds'];
}

// واکشی مجموع تأخیرهای ماهانه از جدول delay_time
$query_delay = "
    SELECT 
        DATE_FORMAT(date, '%Y-%m') AS month, 
        SUM(TIME_TO_SEC(delay_in) + TIME_TO_SEC(delay_out)) AS total_delay_seconds
    FROM delay_time 
    WHERE user_id = ? 
    GROUP BY DATE_FORMAT(date, '%Y-%m') 
    ORDER BY month
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$delay_data = $stmt->get_result();

while ($row = $delay_data->fetch_assoc()) {
    $months[$row['month']]['total_delay'] = $row['total_delay_seconds'];
}

// ماه‌های سال
$month_names = [
    '01' => 'ژانویه',
    '02' => 'فوریه',
    '03' => 'مارس',
    '04' => 'آوریل',
    '05' => 'مه',
    '06' => 'ژوئن',
    '07' => 'ژوئیه',
    '08' => 'آگوست',
    '09' => 'سپتامبر',
    '10' => 'اکتبر',
    '11' => 'نوامبر',
    '12' => 'دسامبر'
];
?>
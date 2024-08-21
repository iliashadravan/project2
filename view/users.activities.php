<?php
global $persian_month_name, $persian_year, $expected_monthly_work_seconds, $user, $target_month, $target_year;
require_once '../controller/users.activities.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش ساعت کاری و تأخیر ماهانه</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            margin: 20px;
            direction: rtl;
            background-color: #f4f4f4;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #ffffff;
        }
        th, td {
            border: 1px solid #dddddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
<h1>گزارش ساعت کاری و تأخیر ماهانه</h1>

<h2>ساعت‌های کاری برای کاربر: <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h2>
<table>
    <thead>
    <tr>
        <th>ماه</th>
        <th>کل ساعات کاری (با ضریب 1.4)</th>
        <th>تأخیر</th>
        <th>ساعت کاری در روز تعطیل (بدون ضریب)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    if ($user) {
        // اطلاعات کاربر جاری
        $user_id = $user['id'];
        $total_monthly_work_seconds = isset($monthly_work_times[$user_id]) ? $monthly_work_times[$user_id] : 0;
        $holiday_work_seconds = isset($holiday_work_times_without_multiplier[$user_id]) ? $holiday_work_times_without_multiplier[$user_id] : 0;
        $delay_seconds = max(0, $expected_monthly_work_seconds - $total_monthly_work_seconds);

        echo "<tr>";
        echo "<td>" . htmlspecialchars($persian_month_name . " " . $persian_year) . "</td>";
        echo "<td>" . formatSeconds($total_monthly_work_seconds) . "</td>";
        echo "<td>" . formatSeconds($delay_seconds) . "</td>";
        echo "<td>" . formatSeconds($holiday_work_seconds) . "</td>";
        echo "</tr>";
    } else {
        echo "<tr><td colspan='4'>کاربری یافت نشد</td></tr>";
    }
    ?>
    </tbody>
</table>
</body>
</html>

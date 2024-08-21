<?php
global $db, $persian_year, $persian_month_name;
require_once '../vendor/autoload.php'; // بارگذاری autoload Composer
require_once '../controller/other.users.activities.php';
echo "<p>تاریخ انتخابی: $persian_month_name $persian_year</p>";
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
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label {
            margin: 0 10px;
            font-weight: bold;
        }
        select, button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select {
            width: 150px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
        }
        button:hover {
            background-color: #45a049;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
            color: #333;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<h1>گزارش ساعت کاری و تأخیر ماهانه</h1>


<h2>ساعت‌های کاری</h2>
<table>
    <thead>
    <tr>
        <th>نام کاربر</th>
        <th>ماه</th>
        <th>کل ساعات کاری (با ضریب 1.4)</th>
        <th>تأخیر</th>
        <th>ساعت کاری در روز تعطیل (بدون ضریب)</th>
    </tr>
    </thead>
    <tbody>
    <?php
    // دریافت لیست تمامی کاربران
    $users_query = "SELECT id, firstname, lastname FROM users";
    $users_result = $db->query($users_query);

    // بررسی اینکه آیا کاربران وجود دارند یا خیر
    if ($users_result->num_rows > 0) {
        // حلقه برای نمایش اطلاعات هر کاربر
        while ($user = $users_result->fetch_assoc()) {
            // محاسبه ساعات کاری و تأخیر برای هر کاربر
            $user_id = $user['id'];
            $total_monthly_work_seconds = isset($monthly_work_times[$user_id]) ? $monthly_work_times[$user_id] : 0;
            $delay_seconds = isset($expected_monthly_work_seconds) ? max(0, $expected_monthly_work_seconds - $total_monthly_work_seconds) : 0;
            $holiday_work_seconds = isset($holiday_work_times_without_multiplier[$user_id]) ? $holiday_work_times_without_multiplier[$user_id] : 0;

            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['firstname']) . " " . htmlspecialchars($user['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($persian_month_name . " " . $persian_year) . "</td>";
            echo "<td>" . formatSeconds($total_monthly_work_seconds) . "</td>";
            echo "<td>" . formatSeconds($delay_seconds) . "</td>";
            echo "<td>" . formatSeconds($holiday_work_seconds) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5'>هیچ کاربری یافت نشد</td></tr>";
    }
    ?>
    </tbody>
</table>
</body>
</html>

<?php
global $persian_month_name, $persian_year, $expected_monthly_work_seconds, $user, $target_month, $target_year, $current_year;
require_once '../controller/users.activities.php';
use Hekmatinasser\Verta\Verta;

// تابع تبدیل سال میلادی به شمسی
function getJalaliYear($year) {
    $date = new Verta("$year-01-01"); // تاریخ اول ژانویه سال میلادی
    return $date->format('Y'); // تبدیل به سال شمسی
}
// دریافت سال جاری میلادی
$current_year = date('Y');
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
            margin: 0;
            padding: 0;
            direction: rtl;
            background-color: #f0f2f5;
            color: #333;
        }
        .container {
            width: 80%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            margin-right: 10px;
            color: #555;
        }
        select, button {
            padding: 10px;
            margin: 5px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        select {
            width: 160px;
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
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
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
<div class="container">
    <h1>گزارش ساعت کاری و تأخیر ماهانه</h1>
    <form method="post" action="">
        <label for="year">سال:</label>
        <select name="year" id="year">
            <?php
            // نمایش سال‌ها از 5 سال گذشته تا سال جاری
            for ($i = $current_year; $i >= $current_year - 5; $i--) {
                $jalali_year = getJalaliYear($i);
                echo "<option value=\"$i\"" . ($i == $target_year ? ' selected' : '') . ">$jalali_year</option>";
            }
            ?>
        </select>

        <label for="month">ماه:</label>
        <select name="month" id="month">
            <?php
            $months = [
                '04' => 'فروردین',
                '05' => 'اردیبهشت',
                '06' => 'خرداد',
                '07' => 'تیر',
                '08' => 'مرداد',
                '09' => 'شهریور',
                '10' => 'مهر',
                '11' => 'آبان',
                '12' => 'آذر',
                '01' => 'دی',
                '02' => 'بهمن',
                '03' => 'اسفند'
            ];
            foreach ($months as $num => $name) {
                echo "<option value=\"$num\"" . ($num == $target_month ? ' selected' : '') . ">$name</option>";
            }
            ?>
        </select>

        <button type="submit">جستجو</button>
    </form>
    <h2>ساعت‌های کاری از: <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></h2>
    <table>
        <thead>
        <tr>
            <th>ماه</th>
            <th>کل ساعات کاری</th>
            <th>تأخیر</th>
            <th>ساعت کاری در روز تعطیل</th>
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
</div>
</body>
</html>

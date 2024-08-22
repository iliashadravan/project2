<?php
global $target_year, $target_month, $persian_month_name, $persian_year, $db;
require_once '../../vendor/autoload.php'; // بارگذاری autoload Composer
require_once '../../controller/admin/other.users.activities.php';
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
    <title>فرم انتخاب تاریخ</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            direction: rtl;


        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
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
            margin-right: 10px;
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
            margin-top: 20px;
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
        .styled-link {
            text-decoration: none; /* حذف خط زیر لینک */
            color: #ffffff; /* رنگ متن */
            background-color: #007bff; /* رنگ پس‌زمینه */
            padding: 10px 20px; /* فضای داخلی */
            border-radius: 5px; /* گرد کردن گوشه‌ها */
            font-weight: bold; /* ضخیم کردن متن */
            transition: background-color 0.3s ease; /* افکت تغییر رنگ پس‌زمینه */
        }

        .styled-link:hover {
            background-color: #0056b3; /* تغییر رنگ پس‌زمینه هنگام هاور */
        }
    </style>
</head>
<body>
<div class="container">
    <h1>working and delay times report</h1>

    <form method="post" action="">
        <label for="year">سال:</label>
        <select name="year" id="year">
            <?php
            // نمایش سال‌ها از 5 سال گذشته تا سال جاری
            for ($i = $current_year + 1; $i >= $current_year - 5; $i--) {
                $jalali_year = getJalaliYear($i);
                echo "<option value=\"$i\"" . ($i == $target_year +1 ? ' selected' : '') . ">$jalali_year</option>";
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

        <button type="submit">Search</button>
        <a href="../panel.php" class="styled-link">Panel</a>

    </form>

    <h2>Working hours</h2>
    <table>
        <thead>
        <tr>
            <th> Users name</th>
            <th>month</th>
            <th>Total working hours</th>
            <th>Delay</th>
            <th>working hour on holiday</th>
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
</div>
</body>
</html>

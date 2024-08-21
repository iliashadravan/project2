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
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<div class="container">
    <h1>گزارش ساعت کاری و تأخیر ماهانه</h1>
    <form method="post" action="">
        <label for="year">سال:</label>
        <select name="year" id="year">
            <?php
            // نمایش سال‌ها از 5 سال گذشته تا سال جاری
            for ($i = $current_year+1; $i >= $current_year - 10; $i--) {
                $jalali_year = getJalaliYear($i);
                echo "<option value=\"$i\"" . ($i == $target_year+1 ? ' selected' : '') . ">$jalali_year</option>";
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

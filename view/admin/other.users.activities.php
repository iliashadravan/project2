<?php
global $target_year, $target_month, $persian_month_name, $persian_year, $db;
require_once '../../vendor/autoload.php'; // بارگذاری autoload Composer
require_once '../../controller/admin/other.users.activities.php';
use Hekmatinasser\Verta\Verta;
use Carbon\Carbon;

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
    <link rel="stylesheet" href="../../public/css/style.other.users.activities.css">
</head>
<body>
<div class="container">
    <h1>گزارش ساعات کاری و تأخیر</h1>

    <form method="post" action="">
        <label for="year">سال:</label>
        <select name="year" id="year">
            <?php
            // نمایش سال‌ها از 5 سال گذشته تا سال جاری
            for ($i = $current_year ; $i >= $current_year - 5; $i--) {
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
        <a href="../panel.php" class="styled-link">پنل</a>
    </form>

    <h2>ساعات کاری</h2>
    <table>
        <thead>
        <tr>
            <th>نام کاربر</th>
            <th>ماه</th>
            <th>مجموع ساعات کاری</th>
            <th>تأخیر</th>
            <th>ساعات کاری در تعطیلات</th>
            <th>تعداد روزهای کاری</th>
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

                // محاسبه تأخیر بدون در نظر گرفتن ساعات کاری جمعه و شنبه
                $total_work_days = isset($work_days_count[$user_id]) ? $work_days_count[$user_id] : 0;
                $total_days_in_month = Carbon::create($target_year, $target_month, 1)->daysInMonth;
                $weekend_days = 0;

                for ($day = 1; $day <= $total_days_in_month; $day++) {
                    $date = Carbon::create($target_year, $target_month, $day);
                    if ($date->isFriday() || $date->isSaturday()) {
                        $weekend_days++;
                    }
                }

                $work_days = $total_days_in_month - $weekend_days;
                $expected_monthly_work_seconds = $work_days * $standard_work_hours_per_day * 3600;
                $delay_seconds = max(0, $expected_monthly_work_seconds - $total_monthly_work_seconds);

                $holiday_work_seconds = isset($holiday_work_times_without_multiplier[$user_id]) ? $holiday_work_times_without_multiplier[$user_id] : 0;
                $work_days = isset($work_days_count[$user_id]) ? $work_days_count[$user_id] : 0; // تعداد روزهای کاری

                echo "<tr>";
                echo "<td>" . htmlspecialchars($user['firstname']) . " " . htmlspecialchars($user['lastname']) . "</td>";
                echo "<td>" . htmlspecialchars($persian_month_name . " " . $persian_year) . "</td>";
                echo "<td>" . formatSeconds($total_monthly_work_seconds) . "</td>";
                echo "<td>" . formatSeconds($delay_seconds) . "</td>";
                echo "<td>" . formatSeconds($holiday_work_seconds) . "</td>";
                echo "<td>" . htmlspecialchars($work_days) . "</td>"; // نمایش تعداد روزهای کاری
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>هیچ کاربری یافت نشد</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

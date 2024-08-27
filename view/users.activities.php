<?php
// فایل کنترلر مرتبط را شامل می‌کنیم
global $delay_seconds, $weekend_days, $work_days, $total_monthly_work_seconds, $user_phone_number, $user, $target_year, $target_month, $holiday_work_times_without_multiplier, $start_gregorian, $end_gregorian;
require_once '../controller/users.activities.php';
use Hekmatinasser\Verta\Verta;

// تبدیل سال و ماه به شمسی برای نمایش
$verta_jalali = new Verta();
$verta_jalali->setDate($target_year, $target_month, 1);
$jalali_year = $verta_jalali->year;
$jalali_month = $verta_jalali->month;

// تبدیل تاریخ‌های میلادی به شمسی برای نمایش
$start_jalali = Verta::parse($start_gregorian)->format('Y/m/d');
$end_jalali = Verta::parse($end_gregorian)->format('Y/m/d');
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش ساعات کاری</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .user-info, .date-selection, .work-report {
            margin-bottom: 20px;
        }
        label {
            margin-right: 10px;
        }
        input[type="number"], button {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>گزارش ساعات کاری ماهانه</h1>

    <!-- نمایش اطلاعات کاربر -->
    <div class="user-info">
        <p>شماره تلفن: <?php echo htmlspecialchars($user_phone_number); ?></p>
    </div>

    <!-- نمایش فرم برای انتخاب سال و ماه -->
    <div class="date-selection">
        <form action="" method="post">
            <label for="year">سال شمسی:</label>
            <input type="number" name="year" id="year" value="<?php echo htmlspecialchars($jalali_year); ?>" min="1400" max="1500" required>

            <label for="month">ماه شمسی:</label>
            <input type="number" name="month" id="month" value="<?php echo htmlspecialchars($jalali_month); ?>" min="1" max="12" required>

            <button type="submit">نمایش گزارش</button>
        </form>
    </div>

    <!-- نمایش گزارش ساعات کاری -->
    <div class="work-report">
        <h3>گزارش ساعات کاری برای <?php echo htmlspecialchars($jalali_year . '/' . $jalali_month); ?></h3>
        <p>مجموع ساعات کاری: <?php echo formatSeconds($total_monthly_work_seconds); ?></p>
        <p>مجموع ساعات کاری روزهای تعطیل: <?php echo formatSeconds(array_sum($holiday_work_times_without_multiplier)); ?></p>
        <p>ساعات تاخیر: <?php echo formatSeconds($delay_seconds); ?></p>
    </div>
</div>
</body>
</html>

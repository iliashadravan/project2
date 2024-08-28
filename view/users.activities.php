<?php
// فایل کنترلر مرتبط را شامل می‌کنیم
global $delay_seconds, $weekend_days, $work_days, $total_monthly_work_seconds, $user_phone_number, $target_year, $target_month, $holiday_work_times_without_multiplier, $start_gregorian, $end_gregorian, $verta, $db, $total_holiday_work_seconds;
require_once '../controller/users.activities.php';
use Hekmatinasser\Verta\Verta;

// تبدیل سال و ماه به شمسی برای نمایش
$verta_jalali = new Verta();

// تبدیل تاریخ‌های میلادی به شمسی برای نمایش
$start_jalali = Verta::parse($start_gregorian)->format('Y/m/d');
$end_jalali = Verta::parse($end_gregorian)->format('Y/m/d');

// دریافت جزئیات ورود و خروج از پایگاه داده
$query_details = "
SELECT 
    DATE(date) AS work_date,
    TIME(clock_in) AS clock_in_time,
    TIME(clock_out) AS clock_out_time,
    TIMESTAMPDIFF(SECOND, clock_in, clock_out) AS work_seconds
FROM work_time
WHERE user_id = ? AND DATE(date) BETWEEN ? AND ?
ORDER BY date
";

$stmt = $db->prepare($query_details);
$stmt->bind_param('iss', $user['id'], $start_gregorian, $end_gregorian);
$stmt->execute();
$details_data = $stmt->get_result();
// اضافه کردن کد دیباگ برای بررسی نتایج
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش ساعات کاری</title>
    <link rel="stylesheet" href="sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
    <link rel="stylesheet" href="../public/css/style.users.activities.css">
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="container">
    <h1>Monthly working hours report</h1>
    <!-- نمایش فرم برای انتخاب سال و ماه -->
    <div class="date-selection">
        <form action="" method="post">
            <label for="year">year:</label>
            <select name="year" id="year" required>
                <?php
                for ($i = ($verta->year); $i >= ($verta->year) - 5; $i--) {
                    echo "<option value='" . htmlspecialchars($i) . "' " . (($i == $target_year) ? 'selected' : '') . ">" . htmlspecialchars($i) . "</option>";
                }
                ?>
            </select>

            <label for="month">month:</label>
            <select name="month" id="month" required>
                <?php
                $jalali_months = [
                    1 => "فروردین",
                    2 => "اردیبهشت",
                    3 => "خرداد",
                    4 => "تیر",
                    5 => "مرداد",
                    6 => "شهریور",
                    7 => "مهر",
                    8 => "آبان",
                    9 => "آذر",
                    10 => "دی",
                    11 => "بهمن",
                    12 => "اسفند"
                ];

                foreach ($jalali_months as $key => $month_name) {
                    echo "<option value='" . htmlspecialchars($key) . "' " . (($key == $target_month) ? 'selected' : '') . ">" . htmlspecialchars($month_name) . "</option>";
                }
                ?>
            </select>

            <button type="submit">Show report</button>
        </form>
    </div>

    <!-- نمایش گزارش ساعات کاری -->
    <div class="work-report">
        <h3>Working hours report for <?php echo htmlspecialchars($target_year . '/' . $target_month); ?></h3>

        <!-- مجموع ساعات کاری کاربر جاری -->
        <p>Total working hours:
            <?php
            echo isset($total_monthly_work_seconds) ? formatSeconds($total_monthly_work_seconds) : '00:00:00';
            ?>
        </p>
        <!-- مجموع ساعات کاری در روزهای تعطیل برای کاربر جاری -->
        <p>Total working hours on holidays:
            <?php
            echo isset($total_holiday_work_seconds) ? formatSeconds($total_holiday_work_seconds) : '00:00:00';

            ?>

        </p>

        <!-- مجموع ساعات تأخیر برای کاربر جاری -->
        <p>Delay times:
            <?php
            echo isset($delay_seconds) ? formatSeconds($delay_seconds) : '00:00:00';
            ?>
        </p>
    </div>


    <!-- نمایش جدول ورود و خروج -->
    <div class="work-details">
        <h3>Entry and exit details</h3>
        <?php if ($details_data->num_rows > 0): ?>
            <table>
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Entry time</th>
                    <th>Exit time</th>
                    <th>Working time</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $details_data->fetch_assoc()):
                    // تبدیل تاریخ به شمسی
                    $work_date_jalali = Verta($row['work_date'])->format('Y/m/d');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($work_date_jalali); ?></td>
                        <td><?php echo htmlspecialchars($row['clock_in_time']); ?></td>
                        <td><?php echo htmlspecialchars($row['clock_out_time']); ?></td>
                        <td><?php echo formatSeconds($row['work_seconds']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>هیچ اطلاعاتی برای نمایش وجود ندارد.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

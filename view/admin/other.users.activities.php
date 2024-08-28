<?php
global $target_year, $target_month, $db, $delay_seconds, $monthly_work_times, $end_gregorian, $start_gregorian, $verta, $holiday_work_times_without_multiplier, $non_weekend_work_seconds;
require_once '../../controller/admin/other.users.activities.php';
use Hekmatinasser\Verta\Verta;
$verta_jalali = new Verta();

// Fetch all user details (names) from the database
$user_details = [];
$query = "SELECT id, firstname, lastname FROM users";
$result = $db->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $user_details[$row['id']] = $row['firstname'] . ' ' . $row['lastname'];
    }
}

// Convert Gregorian dates to Jalali for display
$start_jalali = Verta::parse($start_gregorian)->format('Y/m/d');
$end_jalali = Verta::parse($end_gregorian)->format('Y/m/d');
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش ساعات کاری و تأخیر</title>
    <link rel="stylesheet" href="../../public/css/style.other.users.activities.css">
</head>
<body>
<div class="container">
    <h1>گزارش ساعات کاری و تأخیر</h1>

    <div class="date-selection">
        <form action="" method="post">
            <label for="year">سال شمسی:</label>
            <select name="year" id="year" required>
                <?php
                for ($i = ($verta->year); $i >= ($verta->year) - 5; $i--) {
                    echo "<option value='" . htmlspecialchars($i) . "' " . (($i == $target_year) ? 'selected' : '') . ">" . htmlspecialchars($i) . "</option>";
                }
                ?>
            </select>

            <label for="month">ماه شمسی:</label>
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

            <button type="submit">جستجو</button>
            <a href="../panel.php" class="styled-link">پنل</a>
        </form>
    </div>

    <h2>ساعات کاری</h2>
    <div class="work-report">
        <h3>گزارش ساعات کاری برای <?php echo htmlspecialchars($target_year . '/' . $target_month); ?></h3>
<!--        <p>دوره زمانی: از --><?php //echo htmlspecialchars($start_jalali); ?><!-- تا --><?php //echo htmlspecialchars($end_jalali); ?><!--</p>-->
        <?php if (!empty($monthly_work_times)): ?>
            <table>
                <thead>
                <tr>
                    <th>نام و نام خانوادگی</th>
                    <th>ساعات کاری روزی های تعطیل</th>
                    <th>مجموع ساعات کاری </th>
                    <th>ساعات تأخیر</th>
                    <th>تعداد روزهای کاری</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($monthly_work_times as $user_id => $total_work_seconds): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user_details[$user_id] ?? 'نامشخص'); ?></td>
                        <!-- نمایش ساعات کاری بدون ضریب 1.4 -->
                        <td><?php echo formatSeconds(array_sum($holiday_work_times_without_multiplier)); ?></td>
                        <!-- نمایش مجموع ساعات کاری (روزهای عادی + روزهای تعطیل با ضریب) -->
                        <td><?php echo formatSeconds($total_work_seconds); ?></td>
                        <!-- نمایش ساعات تأخیر -->
                        <td><?php echo formatSeconds($delay_seconds[$user_id] ?? 0); ?></td>
                        <!-- نمایش تعداد روزهای کاری -->
                        <td><?php echo htmlspecialchars(count($user_work_dates[$user_id] ?? [])); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>هیچ اطلاعاتی برای نمایش وجود ندارد.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

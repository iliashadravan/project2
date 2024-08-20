<?php
global $holiday_work_time_without_multiplier, $users, $persian_month_name, $persian_year, $target_month, $target_year;
require_once '../controller/other.users.activities.php';

?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>گزارش ساعت کاری</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            background-color: #4a4a4a; /* Slate gray */
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #4a4a4a; /* Slate gray */
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .form-group label {
            flex: 1;
            font-weight: bold;
        }
        .form-group select, .form-group button {
            flex: 2;
            margin-left: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group button {
            background-color: #4a4a4a; /* Slate gray */
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #3b3b3b; /* Darker slate gray */
        }
        .date-display {
            font-family: 'Tahoma', sans-serif; /* فونت فارسی ساده */
            font-size: 18px;
            color: #4A90E2; /* رنگ متن */
            background-color: #f7f9fc; /* رنگ پس‌زمینه */
            padding: 10px;
            border-radius: 8px; /* گرد کردن گوشه‌ها */
            text-align: center; /* وسط‌چین کردن متن */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* سایه برای جلوه بیشتر */
            margin: 20px auto;
            width: fit-content;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>گزارش ساعت کاری و تأخیر</h1>
    <form method="POST" action="" class="select-month-year">
        <div class="form-group">
            <label for="month">Select month:</label>
            <select id="month" name="month">
                <?php foreach (range(1, 12) as $month) : ?>
                    <?php
                    $month_num = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $jalali_month = convertToJalali($target_year, $month_num)['month'];
                    ?>
                    <option value="<?= $month_num ?>" <?= $month_num == $target_month ? 'selected' : '' ?>><?= $jalali_month ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Select year:</label>
            <select id="year" name="year">
                <?php foreach (range(date('Y'), date('Y') - 5) as $year) : ?>
                    <?php
                    $jalali_year = convertToJalali($year, '01')['year'];
                    ?>
                    <option value="<?= $year ?>" <?= $year == $target_year ? 'selected' : '' ?>><?= $jalali_year ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <button type="submit">Show report</button>
        </div>
    </form>

    <p class="date-display">Month: <?= htmlspecialchars($persian_month_name) ?>، Year: <?= htmlspecialchars($persian_year) ?></p>
    <table>
        <thead>
        <tr>
            <th>نام کاربر</th>
            <th>ساعات کاری</th>
            <th>ساعات تأخیر</th>
            <th>ساعات کاری در روزهای تعطیل</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></td>
                <td><?php echo formatSeconds($work_times[$user['id']] ?? 0); ?></td>
                <td><?php echo formatSeconds($delay_times[$user['id']] ?? 0); ?></td>
                <td>
                    <div class="tooltip">
                        <?php echo formatSeconds(isset($holiday_work_times_without_multiplier[$user['id']]) ?($holiday_work_times_without_multiplier[$user['id']])  : 0); ?>
                    </div>
                </td>

                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

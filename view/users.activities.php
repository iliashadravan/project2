<?php
global $month_names;
require_once '../controller/users.activities.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش ماهانه</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .month-container {
            display: flex;
            overflow-x: auto;
            white-space: nowrap;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .month {
            display: inline-block;
            width: 120px;
            padding: 10px;
            margin: 0 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .month:nth-child(even) {
            background-color: #e9e9e9;
        }
        .month h3 {
            margin: 0;
            font-size: 16px;
        }
        .month p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
<h1>گزارش ماهانه</h1>

<div class="month-container">
    <?php foreach ($month_names as $key => $month_name): ?>
        <div class="month">
            <h3><?php echo htmlspecialchars($month_name); ?></h3>
            <?php if (isset($months[date('Y').'-'.$key])): ?>
                <?php
                $total_delay_seconds = $months[date('Y').'-'.$key]['total_delay'] ?? 0;
                $total_work_seconds = $months[date('Y').'-'.$key]['total_work'] ?? 0;

                // تبدیل ثانیه‌ها به فرمت ساعت:دقیقه:ثانیه
                $delay_hours = floor($total_delay_seconds / 3600);
                $delay_minutes = floor(($total_delay_seconds % 3600) / 60);
                $delay_seconds = $total_delay_seconds % 60;

                $work_hours = floor($total_work_seconds / 3600);
                $work_minutes = floor(($total_work_seconds % 3600) / 60);
                $work_seconds = $total_work_seconds % 60;
                ?>
                <p>تاخیر: <?php echo sprintf('%02d:%02d:%02d', $delay_hours, $delay_minutes, $delay_seconds); ?></p>
                <p>ساعت کاری: <?php echo sprintf('%02d:%02d:%02d', $work_hours, $work_minutes, $work_seconds); ?></p>
            <?php else: ?>
                <p>داده‌ای موجود نیست</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>

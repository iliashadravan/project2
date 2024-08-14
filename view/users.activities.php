<?php
global $month_names, $total_delay_time, $total_work_time, $month_name;
require_once '../controller/users.activities.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .header {
            font-size: 24px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Monthly Report</h1>
    <p>Month: <?php echo htmlspecialchars($month_name); ?></p>
</div>
<table>
    <thead>
    <tr>
        <th>Description</th>
        <th>Time</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Total Work Time</td>
        <td><?php echo htmlspecialchars($total_work_time); ?></td>
    </tr>
    <tr>
        <td>Total Delay Time</td>
        <td><?php echo htmlspecialchars($total_delay_time); ?></td>
    </tr>
    </tbody>
</table>
</body>
</html>
<?php
global $delay_times, $work_times, $user_id, $persian_month_name, $persian_year, $target_month, $target_year;
require_once '../controller/users.activities.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>گزارش زمانی</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-right: 50px;
        }
        .header {
            background-color: #3f51b5; /* Bootstrap primary color */
            color: #fff;
            padding: 15px 25px;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        form {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .form-group select, .form-group button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group button {
            background-color: #3f51b5; /* Bootstrap primary color */
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .form-group button:hover {
            background-color: #4980e7; /* Darker blue */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #3f51b5; /* Bootstrap primary color */
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .date-display {
            font-family: 'Tahoma', sans-serif;
            font-size: 18px;
            color: #007bff; /* Bootstrap primary color */
            background-color: #e9f5ff;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .sidebar {
            width: 200px;
            background-color: #343a40;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-x: hidden;
            transition: all 0.3s;
        }
        .sidebar:hover {
            width: 230px;
        }
        .sidebar h2 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 20px;
        }
        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            font-size: 1.1em;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .sidebar a:hover {
            color: #fff;
            background-color: #495057;
        }
    </style>
</head>
<body>
<?php include '../view/sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>time report</h1>
    </div>
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

    <p class="date-display">month: <?= htmlspecialchars($persian_month_name) ?>، year: <?= htmlspecialchars($persian_year) ?></p>

    <table>
        <thead>
        <tr>
            <th>ساعات کاری</th>
            <th>ساعات تأخیر</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= htmlspecialchars($work_times[$user_id]) ?></td>
            <td><?= htmlspecialchars($delay_times[$user_id]) ?></td>
        </tr>
        </tbody>
    </table>
</div>
</body>
</html>

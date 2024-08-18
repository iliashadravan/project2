<?php
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
            font-family: 'Tahoma', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 28px;
            color: #4CAF50;
            margin: 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #4CAF50;
            display: inline-block;
        }
        .select-month-year {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        label {
            font-weight: bold;
        }
        select {
            padding: 5px;
            font-size: 16px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        table {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: #fff;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        p {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }
        .sidebar {
            width: 200px;
            background-color: #333;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-x: auto;
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
        }
        .sidebar a:hover {
            color: #fff;
            background-color: #555;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.4s;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="header">
    <h1>گزارش ماهانه</h1>
</div>
<form method="POST" action="" class="select-month-year">
    <label for="month">انتخاب ماه:</label>
    <select id="month" name="month">
        <?php foreach ($persian_month_names as $key => $name) : ?>
            <option value="<?= $key ?>" <?= $key == $target_month ? 'selected' : '' ?>><?= $name ?></option>
        <?php endforeach; ?>
    </select>

    <label for="year">انتخاب سال:</label>
    <select id="year" name="year">
        <?php for ($i = date('Y'); $i >= date('Y') - 4; $i--) : ?>
            <option value="<?= $i ?>" <?= $i == $target_year ? 'selected' : '' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>
    <button type="submit">نمایش گزارش</button>
</form>

<p>ماه: <?= $persian_month_name ?>، سال: <?= $persian_year ?></p>

<table>
    <thead>
    <tr>
        <th>شناسه کاربر</th>
        <th>ساعات کاری</th>
        <th>ساعات تأخیر</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user) : ?>
        <tr>
            <td><?= htmlspecialchars($user['username']) ?></td>
            <td><?= isset($work_times[$user['id']]) ? $work_times[$user['id']] : '00:00:00' ?></td>
            <td><?= isset($delay_times[$user['id']]) ? $delay_times[$user['id']] : '00:00:00' ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table### ادامه کد HTML و PHP

        ```php
</body>
</html>
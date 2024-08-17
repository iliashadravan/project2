<?php
global $users, $target_year, $target_month;
require_once '../controller/other.users.activities.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>User management</title>
</head>
<style>
    /* استایل‌ها به همان صورت */
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
        margin-left: 300px;
    }
    .header {
        background-color: #4a4a4a; /* Slate gray */
        color: #fff;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        margin-bottom: 20px;
        width: 50%;
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
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
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
    .error, .success {
        background-color: #fff;
        padding: 10px;
        border: 1px solid;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .error {
        border-color: #dc3545;
        color: #dc3545;
    }
    .success {
        border-color: #28a745;
        color: #28a745;
    }
    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
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

    .btn.edit {
        color: #000; /* سیاه */
        background-color: #ccc; /* می‌توانید رنگ پس‌زمینه را تغییر دهید */
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none; /* حذف خط زیرین */
    }

    .btn.edit:hover {
        background-color: #bbb; /* رنگ پس‌زمینه کمی تیره‌تر هنگام هاور */
    }

</style>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>Users management</h1>
    </div>

    <!-- فرم انتخاب ماه و سال -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="month">month:</label>
            <select name="month" id="month">
                <?php
                $months = [
                    '01' => 'January',
                    '02' => 'February',
                    '03' => 'March',
                    '04' => 'April',
                    '05' => 'May',
                    '06' => 'June',
                    '07' => 'July',
                    '08' => 'August',
                    '09' => 'September',
                    '10' => 'October',
                    '11' => 'November',
                    '12' => 'December'
                ];
                foreach ($months as $value => $name) {
                    echo "<option value=\"$value\" " . ($value == $target_month ? "selected" : "") . ">$name</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="year">year:</label>
            <select name="year" id="year">
                <?php
                $current_year = date('Y');
                for ($i = $current_year; $i >= $current_year - 4; $i--) {
                    echo "<option value=\"$i\" " . ($i == $target_year ? "selected" : "") . ">$i</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <button type="submit">Search</button>
        </div>
    </form>
    
    <!-- نمایش پیام‌های موفقیت یا خطا -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <ul>
                <?php foreach ($success as $msg): ?>
                    <li><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- جدول نمایش کاربران و اطلاعات آنها -->
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Total work time</th>
                <th>Total delay time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></td>
                    <td><?php echo isset($work_times[$user['id']]) ? $work_times[$user['id']] : '00:00:00'; ?></td>
                    <td><?php echo isset($delay_times[$user['id']]) ? $delay_times[$user['id']] : '00:00:00'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
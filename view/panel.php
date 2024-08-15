<?php
global $work_times, $db;
require_once '../controller/panel.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c2c2c;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;

        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea, .form-group button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        .form-group button {
            background-color: #4a4a4a;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #4a4a4a;
            color: #fff;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .error, .success {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .error {
            border: 1px solid #dc3545;
            color: #dc3545;
        }
        .success {
            border: 1px solid #28a745;
            color: #28a745;
        }
        .error p, .success p {
            margin: 0;
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
        button {
            padding: 10px 20px;
            background-color: #2C2C2CFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #848684;
            transition: 0.3s;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>Users panel</h1>
    </div>

    <!-- فرم ثبت زمان ورود -->
    <form method="POST" action="">
        <button type="submit" name="clock_in">Entry</button>
    </form>

    <!-- فرم ثبت زمان خروج -->
    <?php
    // بررسی اینکه آیا زمان ورود ثبت شده است یا خیر
    $sql = "SELECT id FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL AND date = CURDATE() ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $clock_in_record = $result->fetch_assoc();
    ?>

    <?php if ($clock_in_record): ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="report">report:</label>
                <textarea name="report" id="report"></textarea>
            </div>
            <button type="submit" name="clock_out">Exit</button>
        </form>
    <?php endif; ?>

    <!-- نمایش پیام‌ها -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <?php foreach ($errors as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <?php foreach ($success as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- جدول تایم‌های ورود و خروج -->
    <table>
        <thead>
        <tr>
            <th>Date</th>
            <th>Entry time</th>
            <th>Exit time</th>
            <th>Report</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $work_times->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['clock_in']); ?></td>
                <td><?php echo htmlspecialchars($row['clock_out']); ?></td>
                <td><?php echo htmlspecialchars($row['report']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

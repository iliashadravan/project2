<?php
global $work_times, $db;
require_once '../controller/panel.php';
require_once '../vendor/autoload.php';
use Hekmatinasser\Verta\Verta;

?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>dashboard</title>
    <link rel="stylesheet" href="sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
            padding: 20px;
            margin-right: 50px;
        }
        .header {
            background-color: #3f51b5;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            background-color: #3f51b5;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .form-group button:hover {
            background-color: #303f9f;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #3f51b5;
            color: #fff;
            font-weight: bold;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tr:hover {
            background-color: #e8eaf6;
        }
        .error, .success {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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


    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>Users Panel</h1>
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
                <label for="report">Report:</label>
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
            <?php
            $date_shamsi = (new Verta($row['date']))->format('Y-m-d');
            $clock_in_shamsi = $row['clock_in'] ? (new Verta($row['clock_in']))->format('H:i:s') : '---';
            $clock_out_shamsi = $row['clock_out'] ? (new Verta($row['clock_out']))->format('H:i:s') : '---';
            ?>
            <tr>
                <td><?php echo htmlspecialchars($date_shamsi); ?></td>
                <td><?php echo htmlspecialchars($clock_in_shamsi); ?></td>
                <td><?php echo htmlspecialchars($clock_out_shamsi); ?></td>
                <td><?php echo htmlspecialchars($row['report']); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>

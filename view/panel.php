<?php
global $db, $user, $work_times;
require_once '../controller/panel.php'; // فایل کنترلر برای پنل
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>user panel</title>
    <link rel="stylesheet" href="style.css"> <!-- بارگذاری استایل -->
</head>
<body>
<?php include 'sidebar.php'; ?>

<div class="content">
    <h1>Welcome <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>!</h1>

    <!-- محتوای صفحه -->
    <div class="report-section">
        <h2>Write your report:</h2>
        <br>
        <form method="post">
            <textarea name="report" placeholder="Write your report..."></textarea>
            <div class="clock-buttons">
                <button type="submit" name="clock_in">Entry</button>
                <button type="submit" name="clock_out">Exit</button>
            </div>
        </form>
    </div>

    <?php if (isset($errors['report'])) { ?>
        <div class="error"><?php echo htmlspecialchars($errors['report']); ?></div>
    <?php } ?>

    <?php if (isset($success['message'])) { ?>
        <div class="message"><?php echo htmlspecialchars($success['message']); ?></div>
    <?php } ?>

    <h2>Entry and exit history</h2>
    <table>
        <thead>
        <tr>
            <th>date</th>
            <th>Entry time</th>
            <th>Exit time</th>
            <th>Report</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $work_times->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['date']); ?></td>
                <td><?php echo htmlspecialchars($row['clock_in']); ?></td>
                <td><?php echo htmlspecialchars($row['clock_out']); ?></td>
                <td><?php echo htmlspecialchars($row['report']); ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>

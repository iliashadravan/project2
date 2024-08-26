<?php
global $work_times, $db, $user_id, $user;
require_once '../controller/panel.php';
require_once '../vendor/autoload.php';
require_once '../controller/function.query.php';
use Hekmatinasser\Verta\Verta;
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>dashboard</title>
    <link rel="stylesheet" href="sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
    <link rel="stylesheet" href="../public/css/style.panel.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>Users Panel</h1>
        <?php
        $user = getUserById($user_id, $db);
        ?>

        <?php if (isset($user['id']) && isset($user['firstname'])): ?>
            <h2 style="color: #b2ebf2">Welcome <?php echo $user['firstname']  ,' '. $user['lastname'] , '  !' ?> </h2>
        <?php else: ?>
            <h2>User information not found.</h2>
        <?php endif; ?>
    </div>

    <!-- فرم ثبت زمان ورود -->
    <form method="POST" action="">
        <button type="submit" name="clock_in" class="button-entry">Entry</button>
    </form>

    <!-- فرم ثبت زمان خروج -->
    <?php
    $clock_in_record = getCurrentWorkTime($db, $user_id);
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

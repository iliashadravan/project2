<?php
// فرض می‌کنیم کاربر در سیستم لاگین کرده و user_id در سشن ذخیره شده
global $user;
require_once '../controller/sidebar.php';
?>
<div class="sidebar">
    <ul>
        <!-- لینک‌هایی که برای همه کاربران نمایش داده می‌شود -->
        <li><a href="panel.php">User Panel</a></li>
        <li><a href="edit.password.php">Edit Password</a></li>
        <li><a href="../controller/logout.php">Log Out</a></li>
        <li><a href="users.activities.php">Activities</a></li>

        <!-- لینک‌های مخصوص ادمین -->
        <?php if ($user['is_admin'] == 1) { ?>
            <li><a href="register.php">Register User</a></li>
            <li><a href="other.users.activities.php">Users Activities</a></li>
            <li><a href="users.situation.php">Users Situation</a></li>
        <?php } ?>
    </ul>
</div>

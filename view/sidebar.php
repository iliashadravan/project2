<?php
// برای دسترسی به متغیر $user از صفحه دیگر، باید مطمئن شوید که $user مقداردهی شده است
// به طور مثال، می‌توانید از session استفاده کنید

// بررسی وجود متغیر $user و مقداردهی آن از $_SESSION
if (isset($_SESSION['user'])) {
    $user = $_SESSION['user'];
} else {
    $user = []; // تنظیم مقدار پیش‌فرض
}
?>
<div class="sidebar">
    <ul>
        <li><a href="panel.php">User Panel</a></li>
        <li><a href="edit.password.php">Edit Password</a></li>
        <li><a href="../controller/logout.php">Log Out</a></li>
        <li><a href="users.activities.php">Activities</a></li>

        <!-- نمایش گزینه‌های اضافی برای ادمین -->
        <?php if (isset($user['is_admin']) && $user['is_admin'] == 1) { ?>
            <li><a href="register.php">Register New User</a></li>
            <li><a href="other.users.activities.php">Other Users Activities</a></li>
            <li><a href="users.situation.php"> Users situation</a></li>

        <?php } ?>
    </ul>
</div>

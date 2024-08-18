<?php
require_once '../controller/test.php';
?>
<div class="sidebar">
    <ul>
        <li><a href="panel.php">User Panel</a></li>
        <li><a href="edit.password.php">Edit Password</a></li>
        <li><a href="../controller/logout.php">Log Out</a></li>
        <li><a href="users.activities.php">Activities</a></li>
        <?php if (isset($user['is_admin']) && $user['is_admin'] == 1) : ?>
            <li><a href="register.php">Register New User</a></li>
            <li><a href="other.users.activities.php">Users Activities</a></li>
            <li><a href="users.situation.php">Users Situation</a></li>
        <?php endif; ?>
    </ul>
</div>
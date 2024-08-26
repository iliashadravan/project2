<?php
require_once '../controller/edit.password.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>edit password</title>
    <link rel="stylesheet" href="../public/css/sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
    <link rel="stylesheet" href="../public/css/style.edit.password.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="edit-password-container">
    <h1>Edit password</h1>
    <form method="post">
        <label for="current_password">Current password</label>
        <input type="password" id="current_password" name="current_password">
        <?php if (isset($errors['current_password'])) { ?>
            <div class="error"><?php echo htmlspecialchars($errors['current_password']); ?></div>
        <?php } ?>

        <label for="new_password">New password</label>
        <input type="password" id="new_password" name="new_password">
        <?php if (isset($errors['new_password'])) { ?>
            <div class="error"><?php echo htmlspecialchars($errors['new_password']); ?></div>
        <?php } ?>

        <label for="confirm_password">Repeat the new password</label>
        <input type="password" id="confirm_password" name="confirm_password">
        <?php if (isset($errors['confirm_password'])) { ?>
            <div class="error"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
        <?php } ?>

        <?php if (isset($errors['update'])) { ?>
            <div class="error"><?php echo htmlspecialchars($errors['update']); ?></div>
        <?php } ?>

        <?php if (isset($success[0])) { ?>
            <div class="success"><?php echo htmlspecialchars($success[0]); ?></div>
        <?php } ?>

        <button type="submit">Update password</button>
        <br><br>
        <a href="panel.php">Back to panel</a>
    </form>
</div>
</body>
</html>

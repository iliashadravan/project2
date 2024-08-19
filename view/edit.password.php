<?php
require_once '../controller/edit.password.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>edit password</title>
    <link rel="stylesheet" href="sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #e0f7fa 30%, #b2ebf2 100%);
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .edit-password-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
            margin: auto;
            box-sizing: border-box;
        }

        h1 {
            color: #00796b;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #b2dfdb;
            box-sizing: border-box;
            font-size: 1em;
        }

        .error {
            color: #d32f2f;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        .success {
            color: #004d40;
            font-weight: bold;
            margin-top: 20px;
        }

        button {
            color: #ffffff;
            background-color: #00796b;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            margin-top: 20px;
        }

        button:hover {
            background-color: #004d40;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
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

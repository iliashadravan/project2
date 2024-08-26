<?php
global $user;
require_once '../../controller/admin/edit.users.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="../sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
<link rel="stylesheet" href="../../public/css/style.edit.users.css">
</head>
<body>
<div class="container">
    <h1>Edit User</h1>

    <?php if (!empty($errors)): ?>
        <div class="message">
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

    <form method="post">
        <label for="firstname">First Name:</label>
        <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>

        <label for="lastname">Last Name:</label>
        <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>

        <label for="password">Password (leave blank to keep current):</label>
        <input type="password" id="password" name="password">

        <button type="submit">Update User</button>
        <a href="users.situation.php">Back to panel</a>
    </form>
</div>
</body>
</html>

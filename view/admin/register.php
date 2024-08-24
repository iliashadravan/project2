<?php
require_once '../../controller/admin/register.php';
global $show_errors;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Page</title>
    <link rel="stylesheet" href="../../public/css/style.register.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');
    </style>
</head>
<body>
<form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
    <h2>Register Page</h2>
    <label for="firstName">First Name:</label>
    <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars(request('firstName')); ?>">
    <?php if ($show_errors && has_error('firstName')) { ?>
        <div class="error"><?php echo get_error('firstName'); ?></div>
    <?php } ?>

    <label for="lastName">Last Name:</label>
    <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars(request('lastName')); ?>">
    <?php if ($show_errors && has_error('lastName')) { ?>
        <div class="error"><?php echo get_error('lastName'); ?></div>
    <?php } ?>

    <label for="phone_number">Phone Number:</label>
    <input type="number" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars(request('phone_number')); ?>">
    <?php if ($show_errors && has_error('phone_number')) { ?>
        <div class="error"><?php echo get_error('phone_number'); ?></div>
    <?php } ?>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password">
    <?php if ($show_errors && has_error('password')) { ?>
        <div class="error"><?php echo get_error('password'); ?></div>
    <?php } ?>

    <label for="confirm_password">Confirm Password:</label>
    <input type="password" id="confirm_password" name="confirm_password">
    <?php if ($show_errors && has_error('confirm_password')) { ?>
        <div class="error"><?php echo get_error('confirm_password'); ?></div>
    <?php } ?>

    <button type="submit">Register</button>
    <br>
    <a href="../panel.php" class="styled-link">Panel</a>

</form>
</body>
</html>

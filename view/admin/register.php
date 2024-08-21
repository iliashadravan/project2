<?php
require_once '../../controller/admin/register.php';
global $show_errors;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Page</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        body {
            background: linear-gradient(135deg, #8A2387, #E94057, #F27121);
            font-family: 'Roboto', sans-serif;
            text-align: center;
            color: #ffffff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h2 {
            color: #ffffff;
            font-size: 2.5em;
            margin-bottom: 30px;
        }

        form {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            color: #ffffff;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
            text-align: left;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ffffff;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 6px;
            box-sizing: border-box;
            background-color: rgba(255, 255, 255, 0.3); /* تغییر رنگ پس‌زمینه */
            color: #333333; /* تغییر رنگ متن داخل فیلدها */
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="number"]:focus {
            border-color: #E94057;
            box-shadow: 0 0 8px rgba(233, 64, 87, 0.6);
            outline: none;
            background-color: rgba(255, 255, 255, 0.9); /* تغییر رنگ پس‌زمینه در حالت فوکوس */
        }

        .error {
            color: #ff6b6b;
            font-size: 0.9em;
            margin-top: -10px;
            margin-bottom: 10px;
        }

        button {
            color: #ffffff;
            background-color: #E94057;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
            display: block;
            margin: 20px auto 0;
            width: 100%;
        }

        button:hover {
            background-color: #C13554;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .form-footer {
            margin-top: 20px;
        }

        .form-footer a {
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }
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
    <a href="../panel.php">Go to panel</a>

</form>
</body>
</html>

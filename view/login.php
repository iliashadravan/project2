<?php
require_once '../controller/login.php'; // فایل کنترلر برای ورود

// نمایش خطاها
$show_errors = isset($errors) && !empty($errors);
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>Login page</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #f0f0f0 30%, #c0c0c0 100%);
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            display: inline-block;
            width: 100%;
            max-width: 400px;
            margin-top: 50px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .error {
            color: #ff0000;
            font-size: 0.9em;
            margin-bottom: 10px;
        }

        button {
            color: #ffffff;
            background-color: #008CBA;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s;
        }

        button:hover {
            background-color: #005f73;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<div class="login-container">
    <h1>Login</h1>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="phone_number">Phone number</label>
        <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars(request('phone_number')); ?>">
        <?php if ($show_errors && isset($errors['phone_number'])) { ?>
            <div class="error"><?php echo $errors['phone_number']; ?></div>
        <?php } ?>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password">
        <?php if ($show_errors && isset($errors['password'])) { ?>
            <div class="error"><?php echo $errors['password']; ?></div>
        <?php } ?>

        <?php if ($show_errors && isset($errors['login'])) { ?>
            <div class="error"><?php echo $errors['login']; ?></div>
        <?php } ?>

        <button type="submit">login</button>
    </form>
</div>
</body>
</html>

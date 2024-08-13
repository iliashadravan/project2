<?php
global $user;
require_once '../controller/edit.users.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .message {
            color: red;
        }
        .success {
            color: green;
        }
        form {
            max-width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
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
    <a href="other.users.activities.php">Back to panel</a>
</form>
</body>
</html>

<?php
global$users;
require_once '../controller/other.users.activities.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست کاربران</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: #ffffff;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .message, .success {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .message {
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
        }
        .success {
            color: #28a745;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
        }
        form {
            display: inline;
        }
        button {
            background-color: #007BFF;
            color: #ffffff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        a {
            color: #007BFF;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .back-link {
            margin-top: 20px;
            font-size: 16px;
        }
        .sidebar {
            width: 200px;
            background-color: #333;
            color: white;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            overflow-x: auto;
        }

        .sidebar h2 {
            color: #fff;
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: #ddd;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            font-size: 1.1em;
        }

        .sidebar a:hover {
            color: #fff;
            background-color: #555;
            padding: 5px;
            border-radius: 4px;
            transition: all 0.4s;
        }
    </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <h1>لیست کاربران</h1>

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

    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>نام</th>
            <th>نام خانوادگی</th>
            <th>وضعیت</th>
            <th>عملیات</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                <td><?php echo $user['is_active'] ? 'غیرفعال' : 'فعال'; ?></td>
                <td>
                    <?php if ($user['is_admin'] == 0): // فقط برای کاربران غیر ادمین ?>
                        <?php if ($user['is_active'] == 0): // فعال است ?>
                            <form method="post">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="deactivate_user">غیرفعال کردن</button>
                            </form>
                        <?php else: // غیرفعال است ?>
                            <form method="post">
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <button type="submit" name="activate_user">فعال کردن</button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>

                    <a href="edit.users.php?id=<?php echo htmlspecialchars($user['id']); ?>" style="margin-left: 10px;">ویرایش اطلاعات</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>

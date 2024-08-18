<?php
global $users;
require_once '../controller/users.situation.php';

?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>User management</title>
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        margin-left: 300px;
    }
    .header {
        background-color: #4a4a4a; /* Slate gray */
        color: #fff;
        padding: 10px;
        text-align: center;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .form-group {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .form-group label {
        flex: 1;
        font-weight: bold;
    }
    .form-group select, .form-group button {
        flex: 2;
        margin-left: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 16px;
    }
    .form-group button {
        background-color: #4a4a4a; /* Slate gray */
        color: #fff;
        border: none;
        cursor: pointer;
    }
    .form-group button:hover {
        background-color: #3b3b3b; /* Darker slate gray */
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        background-color: #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        border-radius: 10px;
    }
    table th, table td {
        padding: 15px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }
    table th {
        background-color: #4a4a4a; /* Slate gray */
        color: #fff;
    }
    table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .error, .success {
        background-color: #fff;
        padding: 10px;
        border: 1px solid;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .error {
        border-color: #dc3545;
        color: #dc3545;
    }
    .success {
        border-color: #28a745;
        color: #28a745;
    }
    .btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
    }
    .btn.deactivate {
        background-color: #dc3545;
    }
    .btn.activate {
        background-color: #28a745;
    }
    .sidebar {
        width: 200px;
        background-color: #343a40;
        color: #fff;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0, 0, 0, 0.2);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        overflow-x: hidden;
        transition: all 0.3s;
    }
    .sidebar:hover {
        width: 230px;
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
        padding: 10px;
        border-radius: 5px;
        transition: background-color 0.3s;
    }

    .sidebar a:hover {
        color: #fff;
        background-color: #495057;
    }


    .btn.edit {
        color: #000; /* سیاه */
        background-color: #ccc; /* می‌توانید رنگ پس‌زمینه را تغییر دهید */
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-decoration: none; /* حذف خط زیرین */
    }

    .btn.edit:hover {
        background-color: #bbb; /* رنگ پس‌زمینه کمی تیره‌تر هنگام هاور */
    }

</style>
<body>
<?php include 'sidebar.php'; ?>
<div class="container">
    <div class="header">
        <h1>Users management</h1>
    </div>

    <!-- نمایش پیام‌های موفقیت یا خطا -->
    <?php if (!empty($errors)): ?>
        <div class="error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success">
            <ul>
                <?php foreach ($success as $msg): ?>
                    <li><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- جدول نمایش کاربران و وضعیت فعال یا غیرفعال آنها -->
    <table>
        <thead>
        <tr>
            <th>Name</th>
            <th>Condition</th>
            <th>edit</th>
            <th>Operation</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></td>
                <td><?php echo $user['is_active'] ? 'Inactive' : 'Active'; ?></td>
                <td><a href="edit.users.php?id=<?php echo $user['id']; ?>" class="btn edit">Edit</a></td>
                <td>
                    <?php if ($user['is_admin'] && !$user['is_active']): ?>
                        <!-- ادمین‌های غیرفعال را نمی‌توان فعال کرد -->
                    <?php elseif ($user['is_active']): ?>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="deactivate_user" class="btn deactivate">Activate</button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <button type="submit" name="activate_user" class="btn activate">Deactivate</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
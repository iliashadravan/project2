<?php
global $users;
require_once '../../controller/admin/users.situation.php';
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <title>User management</title>
    <link rel="stylesheet" href="../sidebar.style.css"> <!-- آدرس صحیح فایل CSS سایدبار -->
    <link rel="stylesheet" href="../../public/css/style.users.situation.css">
</head>
<body>
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
                <td><?php echo htmlspecialchars($user['firstname'])  ,'  ',  htmlspecialchars($user['lastname']); ?></td>
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
    <a href="../panel.php" class="styled-link">Panel</a>

</div>
</body>
</html>
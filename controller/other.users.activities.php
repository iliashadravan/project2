<?php
// اتصال به پایگاه داده
global $db;
require_once 'db.php';
$errors = [];
$success = [];

// دریافت ماه و سال انتخاب شده از فرم یا استفاده از مقادیر پیش‌فرض
$target_year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
$target_month = isset($_POST['month']) ? str_pad(intval($_POST['month']), 2, '0', STR_PAD_LEFT) : date('m');

// پردازش درخواست‌های فعال و غیرفعال کردن کاربران
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['deactivate_user']) || isset($_POST['activate_user'])) {
        $user_id = intval($_POST['user_id']);

        // دریافت اطلاعات کاربر از پایگاه داده
        $query_user = "SELECT is_admin, is_active FROM users WHERE id = ?";
        $stmt = $db->prepare($query_user);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $user_data = $stmt->get_result()->fetch_assoc();

        if ($user_data) {
            $is_active = isset($_POST['deactivate_user']) ? 0 : 1;

            // جلوگیری از غیرفعال کردن خودکار ادمین
            if ($user_data['is_admin'] && $is_active === 0) {
                $errors[] = "ادمین نمی‌تواند خود را غیرفعال کند.";
            } else {
                $query = "UPDATE users SET is_active = ? WHERE id = ?";
                $stmt = $db->prepare($query);
                $stmt->bind_param('ii', $is_active, $user_id);
                if ($stmt->execute()) {
                    $success[] = isset($_POST['deactivate_user']) ? "کاربر با موفقیت غیرفعال شد." : "کاربر با موفقیت فعال شد.";
                } else {
                    $errors[] = "خطا در به‌روزرسانی وضعیت کاربر: " . $stmt->error;
                }
            }
        } else {
            $errors[] = "کاربر یافت نشد.";
        }
    }
}

// دریافت مجموع ساعت کاری و تاخیر برای هر کاربر در ماه و سال انتخاب شده
$query_work = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(TIMEDIFF(clock_out, clock_in))) AS total_work_seconds
    FROM work_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ? 
    GROUP BY user_id
";
$stmt = $db->prepare($query_work);
$target_date = $target_year . '-' . $target_month;
$stmt->bind_param('s', $target_date);
$stmt->execute();
$work_data = $stmt->get_result();

$work_times = [];
while ($row = $work_data->fetch_assoc()) {
    $work_times[$row['user_id']] = gmdate('H:i:s', $row['total_work_seconds']);
}

$query_delay = "
    SELECT 
        user_id,
        SUM(TIME_TO_SEC(total_delay)) AS total_delay_seconds
    FROM delay_time 
    WHERE DATE_FORMAT(date, '%Y-%m') = ?
    GROUP BY user_id
";
$stmt = $db->prepare($query_delay);
$stmt->bind_param('s', $target_date);
$stmt->execute();
$delay_data = $stmt->get_result();

$delay_times = [];
while ($row = $delay_data->fetch_assoc()) {
    $delay_times[$row['user_id']] = gmdate('H:i:s', $row['total_delay_seconds']);
}

// واکشی لیست تمام کاربران
$query = "SELECT id, firstname, lastname, is_active, is_admin FROM users";
$result = $db->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

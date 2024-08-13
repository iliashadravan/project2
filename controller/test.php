<?php

global $db;
require_once 'db.php';
$sql = "SELECT id FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL AND date = CURDATE() ORDER BY id DESC LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$work_time = $result->fetch_assoc();
$result = $work_time;
?>



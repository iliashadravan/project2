<?php
global $db;

function findUserByPhoneNumber($phone_number, $db) {
    $query = "SELECT * FROM users WHERE phone_number = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserPasswordById($user_id, $db) {
    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserById($user_id, $db) {
    $query = "SELECT id, firstname, lastname, phone_number, password FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getUserStatusById($user_id, $db) {
    $query = "SELECT is_admin, is_active FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getUserDataById($db, $user_id) {
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getCurrentWorkTime($db, $user_id) {
    $sql = "SELECT id, clock_in FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL AND date = CURDATE() ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// واکشی داده‌های تایم ورود و خروج
function getWorkTimesByUserId($db, $user_id) {
    $query = "SELECT * FROM work_time WHERE user_id = ? ORDER BY date DESC";
    $stmt = $db->prepare($query);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    return $stmt->get_result();
}

function getLastUnclockedWorkTime($db, $user_id) {
    $sql = "SELECT id FROM work_time WHERE user_id = ? AND clock_in IS NOT NULL AND clock_out IS NULL ORDER BY id DESC LIMIT 1";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function getStandardClockSettings($db) {
    $query = "SELECT standard_clock_in, standard_clock_out FROM setting WHERE id = 1"; // یا ID مناسب برای تنظیمات شما
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getAllUsers($db) {
    $query = "SELECT id, firstname, lastname, is_active, is_admin FROM users";
    $result = $db->query($query);
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getWorkData($db, $query_work, $user_id, $target_date) {
    $stmt = $db->prepare($query_work);
    $stmt->bind_param('is', $user_id, $target_date);
    $stmt->execute();
    $work_data = $stmt->get_result();
    return $work_data;
}
function getDelayData($db, $query_delay, $user_id, $target_date) {
    $stmt = $db->prepare($query_delay);
    $stmt->bind_param('is', $user_id, $target_date);
    $stmt->execute();
    $delay_data = $stmt->get_result();
    return $delay_data;
}

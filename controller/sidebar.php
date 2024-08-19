<?php
global $db, $user;
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if($user_id === 0){
    die('User not logged in');
}
require_once 'db.php';
$user = getUserAdminStatus($db, $user_id);
if(!$user){
    die('User not found');
}
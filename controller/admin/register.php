<?php
global $db, $user;
require_once __DIR__.'/../db.php';
require_once __DIR__.'/../function.errors.php';
require_once __DIR__.'/../function.query.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_phone_number = $_SESSION['phone_number'];
$user = getUserByPhoneNumber($db, $user_phone_number);

if ($user['is_admin'] != 1) {
    header('Location: ../goback.html');
    exit;
}
$errors = [];
$show_errors = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $show_errors = true;

    $phone_number = request('phone_number');
    $password = request('password');
    $firstName = request('firstName');
    $lastName = request('lastName');
    $confirm_password = request('confirm_password');

    if (is_null($phone_number)){
        $errors['phone_number'] = 'Phone number is empty';
    }
    if (is_null($password)){
        $errors['password'] = 'Password is empty';
    }elseif (strlen($password) < 6) {
        $errors['password'] = 'Password must be at least 6 characters long';
    }
    if (is_null($firstName)){
        $errors['firstName'] = 'First name is empty';
    }
    if (is_null($lastName)){
        $errors['lastName'] = 'Last name is empty';
    }
    if (is_null($confirm_password)){
        $errors['confirm_password'] = 'Confirm password is empty';
    }elseif ($password != $confirm_password) {
        $errors['confirm_password'] = 'Confirm password does not match';
    }
    $phone_number_query = "SELECT phone_number FROM users WHERE phone_number = '$phone_number'";
    $result = mysqli_query($db, $phone_number_query);

    if (mysqli_num_rows($result) > 0) {
        $errors['phone_number'] = 'Phone number already exists';
    }else{
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $SQL = "INSERT INTO users (phone_number, password, firstName, lastName) VALUES ('$phone_number', '$hashed_password', '$firstName', '$lastName')";
        if (mysqli_query($db, $SQL)) {
            header('Location: ../view/panel.php');
            exit;
        }else{
            $errors['db'] = 'Database error'. mysqli_error($db);
        }
    }

}
<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "presence absence";

//create connection
$db = mysqli_connect($servername, $username, $password, $dbname);

// check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
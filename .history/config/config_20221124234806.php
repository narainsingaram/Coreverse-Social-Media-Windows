<?php

ob_start(); //Turns on output buffering

session_start();

//Development
//$host = '127.0.0.1';
// $db = 'attendance_db';
// $user = 'root';
// $pass = '';
// $charset= 'utf8mb4';

//remote 
$host = 'remotemysql.com';
$db = 'attendance_db';
$user = 'root';
$pass = '';
$charset= 'utf8mb4';

$timezone = date_default_timezone_set("America/New_York");

$con = mysqli_connect("localhost", "root", "", "social");

if(mysqli_connect_errno()) {

echo "Failed to connect: " . mysqli_connect_errno();

}


?> 
<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");

if(isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);

}

else {
    header("Location: register.php");

}



?> 


<html lang="en">
<head>                  
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
    <script src="assets\js\corverse.js"></script>
    <script src="assets\js\jquery.Jcrop.js"></script>
	<script src="assets\js\jcrop_bits.js"></script>
    

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="assets\css\jquery.Jcrop.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" id='themeStylesheetLink'href="assets/css/themes/default.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/e1623e6969.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Flow+Rounded&family=Poppins:ital,wght@0,100;0,200;0,300
    ;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300;400;500;6
    00;700&family=Roboto:wght@100&family=Rubik+Mono+One&family=Space+Mono&display=swap" rel="stylesheet">
    
</head>




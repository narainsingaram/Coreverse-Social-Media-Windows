<?php 
include("includes/header.php");
?>

<div class="main_column column" id="main_column">
	<center>User Closed</center>

	<center> This account has been closed. There is nothing to view here.</center>

    <br>

	<a href="index.php" class="back_to_home_btn"> Return Home</a>

</div>

<style>
    .main_column {
     display: block; 
     height: 250px;
     text-align: center;
}

center {
    font-size: 30px;
    font-weight: 600;

}

center:nth-of-type(2n) {
    font-size: 15px;
    font-weight: 500;
    margin-top: 5px;
}

.back_to_home_btn {
    background: red;
    padding: 5px 10px;
    border-radius: 3px;
    color: white;
}

.back_to_home_btn:hover {
    background: red;
    padding: 5px 10px;
    border-radius: 5px;
    color: white;
}

</style>
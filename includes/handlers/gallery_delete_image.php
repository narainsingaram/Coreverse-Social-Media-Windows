<?php
 
require_once "../../config/config.php";
 
$image_to_delete = $_POST["imagePath"];
$userLoggedIn = $_POST["userLoggedIn"];
 
$delete_img = mysqli_query($con, "UPDATE user SET gallery=REPLACE(gallery, '$image_to_delete', '') WHERE username='$userLoggedIn'");
 
unlink('../../' . $image_to_delete);


?>
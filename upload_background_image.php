<?php
include("includes/header_without_nav.php");
include_once("includes/classes/User.php");
include_once("includes/classes/Post.php");
 
$text=""; //a global variable that will store the text from the text area
 
if(isset($_POST['post'])) {
 
  $text = $_POST['post_text']; //now we store the text from the text area here
 
  $uploadOk = 1;
  $imageName = $_FILES['fileToUpload']['name'];
  
  $errorMessage = "";
  if($imageName != "") {
    $targetDir = "assets/images/posts/";
    $imageName = $targetDir . uniqid() . basename($imageName);
    $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
    if($_FILES['fileToUpload']['size'] > 10000000) {
      $errorMessage = "Sorry, your file is too large!";
      $uploadOk = 0;
    }
    if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
      $errorMessage = "Sorry, only jpeg, jpg and png files are allowed!";
      $uploadOk = 0;
    }
    if($uploadOk) {
      if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
        //image uploaded successfully
      }
      else {
        //image did not upload
        $uploadOk = 0;
      }
    }
  }
  if($uploadOk) {
  $post = new Post($con, $userLoggedIn);
  $post->submitPost($_POST['post_text'], 'none', $imageName);
  }
  else {
      echo "<div style='text-align: center;' class='alert alert-danger'>
              $errorMessage
            </div>";
  }
}
  if(strpos($text, "@") !== false) { // this is the added code that checks for @ and if it finds it, then checks for the usernames and sends notifications
    $returned_id = $_SESSION["returned_id"];
    $pos = strpos($text, "@");
    $sub = substr($text, $pos + 1);
    $usernow = $_SESSION['username'];
    $usernow = new User($con, $usernow);
    $friends = array();
    $friends = $usernow->getFriendArrays();
    $frexpl = explode(",", $friends);
    foreach ($frexpl as $key => $value) {
      $frlist = mysqli_query($con, "SELECT username FROM user WHERE username='$value'");
      $row = mysqli_fetch_assoc($frlist);
      if (strpos($sub, $row['username']) !== false) {
        $notification = new Notification($con, $_SESSION['username']);
        $notification->insertNotification($returned_id, $row['username'],'tag');
      }
    }
  } // the end of the added code


  if(isset($_POST["submit"])) {
 
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";
   
    if($imageName !== "") {
   
      $targetDir = "assets/images/profile_background/";
      $imageName = $targetDir . uniqid() . basename($imageName);
      $imageName = str_replace(" ", "_", $imageName);
      $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
   
      if($_FILES['fileToUpload']['size'] > 10000000) {
        $errorMessage = "Sorry your file is too large";
        $uploadOk = 0;
      }
   
      if(strtolower($imageFileType) !== "jpeg" && strtolower($imageFileType) !== "png" && strtolower($imageFileType) !== "jpg") {
        $errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
        $uploadOk = 0;
      }
   
      if($uploadOk) {
        if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
   
          $del_old = mysqli_query($con, "SELECT profile_background FROM user WHERE username='$userLoggedIn'");
          $get_old = mysqli_fetch_array($del_old);
          $old_image = $get_old["profile_background"];
          if($old_image !== "")
            unlink($old_image);
   
          $set_bckg = mysqli_query($con, "UPDATE user SET profile_background='$imageName' WHERE username='$userLoggedIn'");//image uploaded okay
        }
        else {
          $uploadOk = 0;
          $errorMessage = "There has been an error. Please try again";
          echo "<div style='text-align:center;' class='alert alert-danger'>
                   $errorMessage
                 </div>";
        }
      }
      else {
   
        echo "<div style='text-align:center;' class='alert alert-danger'>
                 $errorMessage
              </div>";
     }
   
    }
   
    else {
   
      $del_old = mysqli_query($con, "SELECT profile_background FROM user WHERE username='$userLoggedIn'");
      $get_old = mysqli_fetch_array($del_old);
      $old_image = $get_old["profile_background"];
      if($old_image !== "")
        unlink($old_image);
   
      $set_to_null = mysqli_query($con, "UPDATE user SET profile_background='' WHERE username='$userLoggedIn'");
    }
   
  }


?>

<?php
 
 $get_img = mysqli_query($con, "SELECT profile_background FROM user WHERE username='$userLoggedIn'");
 $fetch = mysqli_fetch_array($get_img);

 $background_image = $fetch["profile_background"]; 

 if($background_image !== "" && $userLoggedIn !== $userLoggedIn) {

?>

 
<div class="background_img" style="background: url(<?php echo $background_image; ?>);">
   <center class='image_preview_profile_background'> Preview </center>
       <form method="POST" enctype="multipart/form-data">
         <input type="file" name="fileToUpload"> <br>
         <input type="submit" name="submit" value="Delete background image"> <br>
         <span>Page backgrounds do not appear on profile</span>
       </form>
   </div>

<?php

 }

 else if($userLoggedIn === $userLoggedIn && $background_image !== "") {

?>

<div class="background_img" style="background: url(<?php echo $background_image; ?>);">
   <center class='image_preview_profile_background'> Preview </center>
       <form method="POST" enctype="multipart/form-data">
       <center class='image_preview_profile_background' style='font-size: 2em;'> Edit Background Image </center>
         1. <input type="file" name="fileToUpload"> <br>
         2. <input type="submit" name="submit" value="Change background image"> <br>
       </form>
   </div>
<?php

 }

 else if($userLoggedIn === $userLoggedIn && $background_image === "") {

?>

<div class="no_img">
       <form method="POST" enctype="multipart/form-data">
         <input type="file" name="fileToUpload">
         <input type="submit" name="submit" value="Upload background image">
       </form>
   </div>

<?php

 }

?>

</div>
</main>
</div>
</body>
</html>
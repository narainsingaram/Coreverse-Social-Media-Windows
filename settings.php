<link rel="stylesheet" type="text/css" href="assets/css/settings.css">

<?php
include("includes/header_without_nav.php");
include("includes/form_handlers/settings_handler.php");
?>

<div class="sidebar">
    <div class="logo-details">
      <i class='bx bxs-cog icon'></i>
        <div class="logo_name">Settings</div>
        <i class='bx bx-menu' id="btn" ></i>
    </div>
    <ul class="nav-list">
      <li>
          <i class='bx bx-search' ></i>
         <input type="text" placeholder="Search...">
         <span class="tooltip">Search</span>
      </li>
      <li>
        <a class="tablink" onclick="openPage('Account', this, '#2d00f7')" id="defaultOpen">
        <i class='bx bxs-user'></i>
          <span class="links_name">Dashboard</span>
        </a>
         <span class="tooltip">Dashboard</span>
      </li>
      <li>
       <a class="tablink" onclick="openPage('Personalization', this, '#2d00f7')">
       <i class='bx bxs-paint'></i>
         <span class="links_name">Personalization</span>
       </a>
       <span class="tooltip">Personalization</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-chat' ></i>
         <span class="links_name">Messages</span>
       </a>
       <span class="tooltip">Messages</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-pie-chart-alt-2' ></i>
         <span class="links_name">Analytics</span>
       </a>
       <span class="tooltip">Analytics</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-folder' ></i>
         <span class="links_name">File Manager</span>
       </a>
       <span class="tooltip">Files</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-cart-alt' ></i>
         <span class="links_name">Order</span>
       </a>
       <span class="tooltip">Order</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-heart' ></i>
         <span class="links_name">Saved</span>
       </a>
       <span class="tooltip">Saved</span>
     </li>
     <li>
       <a href="#">
         <i class='bx bx-cog' ></i>
         <span class="links_name">Settings</span>
       </a>
       <span class="tooltip">Settings</span>
     </li>
     <li class="profile">
         <div class="profile-details">
         <img class="profile-card-photo" src=" <?php echo $user['profile_pic']; ?>">
           <div class="name_job">
             <div class="name">
               <?php echo $user['first_name'] . " " . $user['last_name'] . ' '; ?>
           </div>
         </div>
         <i class='bx bx-log-out' id="log_out" ></i>
     </li>
    </ul>
  </div>


<div id="Account" class="tabcontent">
<div class="text">Account Settings</div>

<div class="column" id="column_margin_styles">
<?php
  echo $user['first_name'] . " " . $user['last_name'] . ' ';
  echo "<img src=' ". $user['profile_pic'] . "' id='small_profile_pic'>";
?>
<br>

<a href="upload.php"> Change profile picture</a> 

<div>
  </div>
    </div>

<div class="column" id="column_margin_styles">

<h4>Edit User Profile</h4>

<?php
  $user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM user WHERE username='$userLoggedIn'");
  $row = mysqli_fetch_array($user_data_query);

  $first_name = $row['first_name'];
  $last_name = $row['last_name'];
  $email = $row['email'];
?>


<form action="settings.php" method='POST'>
<label for="first_name">First Name</label>
  <input type="text" name="first_name" id='first_name' value="<?php echo $first_name; ?> "> 
<label for="last_name">Last Name</label>
  <input type="text" name="last_name" id='last_name' value="<?php echo $last_name; ?> ">
<label for="email">Your Email</label>
  <input type="text" name="email" id='email' value="<?php echo $email; ?> ">   
    <?php echo $message;?>
  <input type="submit" name='update_details' id='save_details' value="Update Profile">
</form>

<div>
</div>
</div>

<div class="column" id="column_margin_styles">
  
<h4>Change Password</h4>
<form action="settings.php" method='POST'>
  <label for="old_password">Old Password</label>
  <input type="password" name="old_password" id='password'> <br>
  <label for="new_password">New Password</label>
  <input type="password" name="new_password" id='password'><br>
  <label for="new_password_2">Copy Password</label>
  <input type="password" name="new_password_2" id='password'>  <br>
  <?php echo $password_message;?>
  <input type="submit" name='update_password' id='save_details' value="Update Password">
</form>


</div>

<div class="column" id="column_margin_styles">
<?php
  echo $user['first_name'] . " " . $user['last_name'] . ' ';
  echo "<img src=' ". $user['profile_pic'] . "' id='small_profile_pic'>";
?>
<form action="settings.php" method="POST">
  <input type="submit" name='close_account' id='close_account' value='Delete Account'>
</form>
</div>

</div>
</div>





  <section id="Personalization" class="tabcontent">
  <div class="text">Personalization</div>
  <?php

 
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
  </section>

  
  
  <script>
  let sidebar = document.querySelector(".sidebar");
  let closeBtn = document.querySelector("#btn");
  let searchBtn = document.querySelector(".bx-search");

  closeBtn.addEventListener("click", ()=>{
    sidebar.classList.toggle("open");
    menuBtnChange();//calling the function(optional)
  });

  searchBtn.addEventListener("click", ()=>{ // Sidebar open when you click on the search iocn
    sidebar.classList.toggle("open");
    menuBtnChange(); //calling the function(optional)
  });

  // following are the code to change sidebar button(optional)
  function menuBtnChange() {
   if(sidebar.classList.contains("open")){
     closeBtn.classList.replace("bx-menu", "bx-menu-alt-right");//replacing the iocns class
   }else {
     closeBtn.classList.replace("bx-menu-alt-right","bx-menu");//replacing the iocns class
   }
  }

  function openPage(pageName,elmnt,color) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablink");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].style.backgroundColor = "";
  }
  document.getElementById(pageName).style.display = "block";
  elmnt.style.backgroundColor = color;
}

// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

  </script>


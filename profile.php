<?php
include("includes/header.php");


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
 
    if(strtolower($imageFileType) !== "jpeg" && strtolower($imageFileType) !== "png" && strtolower($imageFileType) !== "jpg" ) {
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


if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

include 'includes/handlers/get_gallery_images.php';

if(isset($_POST['remove_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);

}

if(isset($_POST['add_friend'])) {
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);

}

if(isset($_POST['respond_request'])) {
    header("Location: requests.php");

}



?>

<div class="index_wrapper">

<main>
  
<div class="container">
  

<div class="left">
    
<div class="user_details column">
 <a href="<?php echo $added_by; ?>"> <img class="profile-card-photo" src="<?php echo $user_array['profile_pic']; ?>">  </a>
 

 <div class="user_details_left_right">
 <a href="<?php echo $added_by; ?>">
 

 
<?php

    echo $username;;
    

?>
</a>
</div>


<form class='sidebar_form' action="<?php echo $username; ?>" method="POST">
     
     <?php 
         $profile_user_obj = new User($con, $username); 
             if($profile_user_obj->isClosed()) {
                 header("Location: user_closed.php");
         }

     $logged_in_user_obj = new User($con, $userLoggedIn); 

     if($userLoggedIn != $username) {

     if($logged_in_user_obj->isFriend($username)) {
         echo '<button type="submit" name="remove_friend" class="danger"> Remove Friend </button>';
     }

     else if ($logged_in_user_obj->didReceiveRequest($username)) {
        echo ' <input type="submit" name="respond_request" class="warning" value="Respond to Request"> ';

     }

     else if ($logged_in_user_obj->didSendRequest($username)) {
        echo '<button type="submit" name="" class="default"> <i class="fas fa-circle-notch fa-spin"></i> Request Sent</button>';

     }

     else 
        echo '<input type="submit" name="add_friend" class="success" value="Add Friend">';
    }
    
    
        ?>
</form>
        </div>
   
<button class="show_profile_stats_modal" onclick="functionShowProfileStatsModal()">View More</button>
    
        <div id="profile_info" class="profile_info">
        <p><i class="uil uil-comment-image"></i> <?php echo "Posts: " . $user_array['num_posts']; ?></p>
        <p><i class="uil uil-heart"></i> <?php echo "Likes: " . $user_array['num_likes']; ?></p>
        <p><i class="uil uil-users-alt"></i> <?php echo "Friends: " . $num_friends; ?></p>
        <?php 
            if($userLoggedIn != $username) {
                echo '<p> <i class="uil uil-comment-image"></i> ';
                    echo "Mutual Friends" . ":" . " " . $logged_in_user_obj->getMutualFriends($username);
                    echo '</p>';
            } 
            ?>
    </div>      
    

    
<script>
function functionShowProfileStatsModal() {
  var x = document.getElementById("profile_info");
  if (x.style.display === "none") {
    x.style.display = "block";
  } else {
    x.style.display = "none";
  }
}
</script>

<div class="sidebar">
            <a href="" class="menu-item active">
              <span><i class="uil uil-estate"></i></span> <h3>Home</h3>
            </a>

            <a href="" class="menu-item"><span><i class="uil uil-compass"></i></span> <h3>Explore</h3> 
            
            </a>

            <a href="" class="menu-item" id="notifictions"><span><i class="uil uil-bell"><small class="notification-count">9+</small></i></span> <h3>Notifications</h3> 
            
            </a>

            <a href="" class="menu-item" id="message-notifications"><span><i class="uil uil-message"><small class="notification-count">6</small></i></span> <h3>Messages</h3> 
            
            </a>

            <a href="" class="menu-item"><span><i class="uil uil-star"></i></span> <h3>Starred</h3> 
            
            </a>

            <a href="" class="menu-item"><span><i class="uil uil-arrow-growth"></i></span> <h3>Analytics</h3> 
            
            </a>

            <a href="" class="menu-item"><span><i class="uil uil-setting"></i></span> <h3>Settings</h3> 
            
            </a>
          
         </div>
</div>


<div class="middle">

<div class="tab">
  <button class="tablink" onclick="openPage('Profile_Posts', this, '#eee')" id="defaultOpen" >London</button>
  <button class="tablink" onclick="openPage('Messages', this, '#eee')">Paris</button>
  <button class="tablink" onclick="openPage('Galleries', this, '#eee')"">Memento</button>
</div>


<div class="profile_main_column column" id="create_post_column">

<section id="Profile_Posts" class="tabcontent" id="defaultOpen">
<div class="posts_area"></div>
<img id="#loading" src="assets\icons\loading.gif">
</section>

<section id="Messages" class="tabcontent">

</section>

<section id="Galleries" class="tabcontent">
  
<div class="gallery_photos">
<?php if($userLoggedIn === $username) {

?>

 <form method="POST" enctype="multipart/form-data">
   <input type="file" name="galleryUpload" id="galleryUpload">
   <button type="submit" name="submitGallery" id="submitGallery" value="Upload Memento">Upload Memento</button>
 </form>

<?php

}

?>
 <div class="gallery_container">

   <div class="row">

     <div class="itemsContainer">

       <ul class="items">

       


   <?php

   $query_gallery = mysqli_query($con, "SELECT gallery FROM user WHERE username='$username'");
   
   while($row = mysqli_fetch_array($query_gallery)) {

     $image_array[] = $row["gallery"];
   }

   $string = implode(",", $image_array);

   $string = substr($string, 0, -1);

   $array_again = explode(",", $string);

   foreach ($array_again as $image) {

     if($image !== "") {

       if($userLoggedIn === $username) {

         echo "<li class='apps col-xs-6 col-sm-4 col-md-3 col-lg-3'>

      
                 <div class='item'>
                   <img src='$image'>
                   <div class='icons_gallery'>
                      <div class='dropdown_image_gallery'>
				<span class='update_stats'> <i class='uil uil-ellipsis-h specific_ellipsis'></i> </span>
				<div class='dropdown-content_image_gallery'>
				  <a href='$image' class='openButton' data-fancybox> <i class='uil uil-search-plus'></i> View Image </a>
				  <a href='javascript:void(0)' class='projectLink' id='$image'> <i class='uil uil-trash'></i> Delete </a>
				</div>
			  </div>
                   </div>
                   <div class='imageOverlay'></div>
                 </div>
               </li>";

       }

       else {

         echo "<li class='apps col-xs-6 col-sm-4 col-md-3 col-lg-3'>
                 <div class='item'>
                   <img src='$image'>
                   <div class='icons'>
                     <a href='$image' class='openButton other' data-fancybox>
                       <i class='fa fa-search' title='View larger image'></i>
                     </a>
                   </div>
                   <div class='imageOverlay'></div>
                 </div>
               </li>";  

       }

     }

   }

   ?>

       </ul>

     </div>

   </div>

 </div>
 
</div>


</section>


</div>

<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>
</div>
</div>
</div>
</main>

<script>
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

<script>
  var userLoggedIn = '<?php echo $userLoggedIn; ?>';
  var profileUsername = '<?php echo $username; ?>';

  $(document).ready(function() {

    $('#loading').show();

    //Original ajax request for loading first posts 
    $.ajax({
      url: "includes/handlers/ajax_load_profile_posts.php",
      type: "POST",
      data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
      cache:false,

      success: function(data) {
        $('#loading').hide();
        $('.posts_area').html(data);
      }
    });

    $(window).scroll(function() {
      var height = $('.posts_area').height(); //Div containing posts
      var scroll_top = $(this).scrollTop();
      var page = $('.posts_area').find('.nextPage').val();
      var noMorePosts = $('.posts_area').find('.noMorePosts').val();

      if ((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
        $('#loading').show();

        var ajaxReq = $.ajax({
          url: "includes/handlers/ajax_load_profile_posts.php",
          type: "POST",
          data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
          cache:false,

          success: function(response) {
            $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
            $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 

            $('#loading').hide();
            $('.posts_area').append(response);
          }
        });

      } //End if 

      return false;

    }); //End (window).scroll(function())


  });

  </script>


</body>
</html>

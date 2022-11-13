<?php
include("includes/header.php");
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
    if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "svg") {
      $errorMessage = "Sorry, only jpeg, jpg, svg and png files are allowed!";
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


  ?>


<div class="index_wrapper">

<main>
<div class="container">

<div class="left">

<div class="user_details column">
 <a href="<?php echo $userLoggedIn; ?>"> <img class="profile-card-photo" src=" <?php echo $user['profile_pic']; ?>">  </a>

 <div class="user_details_left_right">
 <a href="<?php echo $userLoggedIn; ?>">
<?php

    echo $user['first_name'] . " " . $user['last_name'] . ' ';

?>
</a>

<br>
 <?php echo "<span>Posts:</span>"  . ' ' . $user['num_posts'] ;  ?> &nbsp;

 <?php echo "Likes:" .  ' ' . $user['num_likes']; ?>
</div>
</div>


<div class="sidebar">

<?php
    //Unread Messages
    $messages = new Message ($con, $userLoggedIn);
    $num_messages = $messages->getUnreadNumber();

    $notifications = new Notification ($con, $userLoggedIn);
    $num_notifications = $notifications->getUnreadNumber();

    $user_obj = new User ($con, $userLoggedIn);
    $num_requests = $user_obj->getNumberOfFriendRequests();


?>


            <a href="" class="menu-item active">
              <span><i class="uil uil-estate"></i></span> <h3>Home</h3>
            </a>

            <a href="" class="menu-item"><span><i class="uil uil-compass"></i></span> <h3>Explore</h3> 
            
            </a>

            <a class="menu-item" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')"><i class="uil uil-bell"></i>

            <?php
            if($num_messages > 0)
            echo '<small class="notification-count" id="unread_message">' .  $num_messages .  '</small>';

            ?>
            
            <h3>Notifications</h3> 
            
            </a>

            <a class="menu-item" href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')"> <i class="uil uil-message"></i> 
            <?php
            if($num_messages > 0)
            echo '<small class="notification-count" id="unread_notification">' .  $num_messages .  '</small>';

            ?>
            <h3>Messages</h3> 
            </a>

            <a class="menu-item" href="requests.php" ><i class="uil uil-user-plus"></i> 
            <?php
            if($num_requests > 0)
            echo '<small class="notification-count" id="unread_request">' .  $num_requests .  '</small>';
            ?>
            <h3>Friend Requests</h3> 
            </a>

            <a href="settings.php" class="menu-item"><span><i class="uil uil-setting"></i></span> <h3>Settings</h3> 
            
            </a>

            <a class="menu-item" href="includes/handlers/logout.php"><i class="uil uil-sign-out-alt"></i> <h3>Logout</h3> 
            
            </a>
          
         </div>

</div>


<div class="middle">


<div class="main_column column" id="create_post_column">
    



<div class="wrapper_tweet">
    <div class="bottom">
    <form class="profile_post" action="index.php" method="POST" enctype="multipart/form-data">
    </div>
    <div class="input-box">
      <div class="tweet-area">
      <input type="file" name="fileToUpload" id="fileToUpload" method="POST">
      <textarea onblur="this.focus();" name="post_text" id="post_text" placeholder="What's happening today, <?php echo $user['first_name'] . "?"; ?>"></textarea>
      <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
      <input type="hidden" name="user_to" value="<?php echo $username;?>" >
    </div>
    
        <input type="submit" name="post" id="post_button" value="Post">
        


    </form>
    </div>
  </div>
</div>


<form>
   <div class="mb-4 w-full bg-gray-50 rounded-lg border border-gray-200 dark:bg-gray-700 dark:border-gray-600">
       <div class="py-2 px-4 bg-white rounded-t-lg dark:bg-gray-800">
           <label for="comment" class="sr-only">Your comment</label>
           <textarea id="comment" rows="4" class="px-0 w-full text-sm text-gray-900 bg-white border-0 dark:bg-gray-800 focus:ring-0 dark:text-white dark:placeholder-gray-400" placeholder="Write a comment..." required=""></textarea>
       </div>
       <div class="flex justify-between items-center py-2 px-3 border-t dark:border-gray-600">
           <button type="submit" class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 dark:focus:ring-blue-900 hover:bg-blue-800">
               Post comment
           </button>
           <div class="flex pl-0 space-x-1 sm:pl-2">
               <input type="file" name="fileToUpload" id="fileToUpload" method="POST" type="button" class="inline-flex justify-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                   <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"></path></svg>
                   <span class="sr-only">Attach file</span>
               </input>
           </div>
       </div>
   </div>
</form>
<p class="ml-auto text-xs text-gray-500 dark:text-gray-400">Remember, contributions to this topic should follow our <a href="#" class="text-blue-600 dark:text-blue-500 hover:underline">Community Guidelines</a>.</p>


<div class="tag_results"></div>




<div class="posts_area"></div>
<img id="#loading" src="assets\icons\loading.gif" class="infinite_scroll_loading">
</div>


<div class="right">
         


<div class="trending_wrapper">
<h4>Trending Words</h4>
      <div class="content">
        <ul class="menu">

        <?php 
  $query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

  foreach ($query as $row) {
    
    $word = $row['title'];
    $word_dot = strlen($word) >= 14 ? "..." : "";

    $trimmed_word = str_split($word, 14);
    $trimmed_word = $trimmed_word[0];

    echo "<li class='item'>
     <i class='uil uil-fire'></i>  ";
    echo  $trimmed_word . $word_dot;
    echo "</li>";


  }

  ?>
        

</div>
<br>
</div>
<?php
$message_obj = new Message($con, $userLoggedIn);


if(isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false)
        $user_to = 'new';
}

if($user_to != "new")
    $user_to_obj = new User($con, $user_to);

if(isset($_POST['post_message'])) {

    if(isset($_POST['message_body'])) {
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
        header("Location: messages.php?u=$user_to");
    }

}
?>

 

    <br>
    
<div class="column" id="conversations"> 

<div class="friend_search_index">
        <form action="" method="POST">
            <?php
                if("water" == "water") {
                    ?>
                     <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Search friends to message' autocomplete='off' autofocus="autofocus" id='search_text_input'>
                    <?php
                    echo "<div class='results'></div>";
                    
                }
            ?>
        </form>
    </div>

        <div class="loaded_conversations">
        <a class='add_a_message' href="messages.php?u=new"><i class="uil uil-plus"></i></a>
                <?php echo $message_obj->getConvos(); ?>
        </div>
</div>



</div>
</div>
</main>

</div>


    
<script>

$(function() {
 
 $("#post_text").keydown(function (e) { 
    if(e.which === 50 && e.shiftKey === true)
      userTag('<?php echo $userLoggedIn; ?>');
    if(e.which !== 50)
      $('.tag_results').html("");
});

$('.tag_results').hover(function(e) {
    textTag();
});

$(document).click(function (e) {
  if(e.target.className !== "tag_results")
        $('.tag_results').html("");                      
});

});


   $(function(){
 
       var userLoggedIn = '<?php echo $userLoggedIn; ?>';
       var inProgress = false;
 
       loadPosts(); //Load first posts
 
       $(window).scroll(function() {
           var bottomElement = $(".status_post").last();
           var noMorePosts = $('.posts_area').find('.noMorePosts').val();
 
           // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
           if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
               loadPosts();
           }
       });
 
       function loadPosts() {
           if(inProgress) { //If it is already in the process of loading some posts, just return
               return;
           }
          
           inProgress = true;
           $('#loading').show();
 
           var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
 
           $.ajax({
               url: "includes/handlers/ajax_load_posts.php",
               type: "POST",
               data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
               cache:false,
 
               success: function(response) {
                   $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                   $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
 
                   $('#loading').hide();
                   $(".posts_area").append(response);
 
                   inProgress = false;
               }
           });
       }
 
       //Check if the element is in view
       function isElementInView (el) {
             if(el == null) {
                return;
            }
 
           var rect = el.getBoundingClientRect();
 
           return (
               rect.top >= 0 &&
               rect.left >= 0 &&
               rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
               rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
           );
       }
   });
 
   </script>



</div>
</main>
</div>
</body>
</html>
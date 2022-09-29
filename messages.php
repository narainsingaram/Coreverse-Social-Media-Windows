<?php 
include("includes/header.php");

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
    <br>
<div class="column" id="conversations"> 

        <div class="loaded_conversations">
        <a class='add_a_message' href="messages.php?u=new"><i class="uil uil-plus"></i></a>
                <?php echo $message_obj->getConvos(); ?>
        </div>
</div>
<br>
</div>


<div class="column" id="chat_column">
<?php 
    if($user_to != "new"){
         $open_query = mysqli_query($con, "SELECT opened, id FROM messages WHERE user_from='$userLoggedIn' AND user_to='$user_to' ORDER BY id DESC LIMIT 1"); //my last message
         $latest_query_rec = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$userLoggedIn' AND user_from='$user_to' ORDER BY id DESC LIMIT 1");//friend's last message
 
         $check_mess = mysqli_fetch_array($open_query);
         $check_latest = mysqli_fetch_array($latest_query_rec);
 
         $seen = $check_mess['opened'] === 'yes' ? "Seen" : ""; //check if he opened my last message
 
         echo "<h4>&nbsp;You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";
         echo "<div class='loaded messages' id='scroll_messages'>";
         echo $message_obj->getMessages($user_to);
 
         if($check_mess['id'] > $check_latest['id']) //check if mine is the last message in the conversation
              echo "<div style='float:right; position:relative; bottom:5px; right:3px;'>" . $seen . "</div><br>";
					
         echo "</div>";
    }

    ?>
    
    <div class="message_post">
        <form action="" method="POST">
            <?php
                if($user_to == "new") {
                    ?>
                     <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Search friends to message' autocomplete='off' autofocus="autofocus" id='search_text_input'>
                    <?php
                    echo "<div class='results'></div>";
                    
                }

                else {
                    echo " <div class='message_form'> <textarea name='message_body' id='message_textarea' placeholder='Say something'> </textarea>";
                    echo "<button type='submit' name='post_message' class='info' id='message_submit' value='Send'> <i class='uil uil-message'></i> </button> </div>"; 

                }
            ?>
        </form>
    </div>
    <script>
    var div = document.getElementById("scroll_messages");
    if(div != null) {
    div.scrollTop = div.scrollHeight;

}
</script>

</div>
</div>
</main>


<?php 
include("includes/header.php"); 

if(isset($_GET['id'])) {
	$id = $_GET['id'];
}
else {
	$id = 0;
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

</div>

<div class="middle">


    <div class="posts_area">
    <?php 
				$post = new Post($con, $userLoggedIn);
				$post->getSinglePost($id);
			?>
    </div>
</div>



</div>
</main>

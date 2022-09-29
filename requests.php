<?php
    include("includes/header.php"); //Header file

?>
<div class="main_column" id="main_column"> 

<style>
    .main_column {
        display: block;
        background: rgba(25,25,25,.05) !important;
    }

    

</style>


    <h3 class="friend_requests_title"> Friend Requests</h3>
<section class="whole_friend_request_container">
    
    <?php

if(isset($_GET['profile_username'])) {
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);

	$num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}

        $query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
        if(mysqli_num_rows($query) == 0)
            echo "You have no friend requests at this time.";

        else {

            while($row = mysqli_fetch_array($query)) {
                $user_from = $row['user_from'];
                $user_from_obj = new User($con, $user_from);

                ?>

                <div class='profile_hover_wrapper'>
						<div class='img-area'>
						  <div class='inner-area'>
						  <img class="friend_request_profile_pic" src="<?php echo $user_from_obj->getProfilePic(); ?>" width='40'>
						  </div>
						</div>
						<div class='icon help'><i class="uil uil-question"></i></div>
						<div> <form action="requests.php" method="POST"> <button type="submit" class='icon close' name="ignore_request<?php echo $user_from; ?>" value="Ignore"> <i class="uil uil-times"></i> </button> </form> </div>
						<div class='name'><?php echo $user_from_obj-> getFirstAndLastName() ?></div>
						<div class='about'><a href='<?php echo $user_from ?>'> View Profile </a></div>

						<div class='social-icons'>
						  <a href='messages.php?u=<?php echo $user_from ?>' class='fb'><i class="uil uil-comment-lines"></i></a>
						  <a href='#' class='twitter'><i class='fab fa-twitter'></i></a>
						  <a href='#' class='insta'><i class='fab fa-instagram'></i></a>
						  <a href='#' class='yt'><i class='fab fa-youtube'></i></a>
						</div>
        <form action="requests.php" class="buttons" method="POST">
            <button type="submit" name="accept_request<?php echo $user_from; ?>"  id="accept_button" value="Accept"> Accept </button>
        </form>
					
                </div>
                

                <?php 

                $user_from_friend_array = $user_from_obj->getFriendArrays();

                if(isset($_POST['accept_request' . $user_from])) {
                    $add_friend_query = mysqli_query($con, "UPDATE user SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
                    $add_friend_query = mysqli_query($con, "UPDATE user SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                    echo "You are now friends.";
                    header("Location: requests.php");
                }
                
                if(isset($_POST['ignore_request' . $user_from])) {
                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
                    echo "Request ignored.";
                    header("Location: requests.php");

                }

                ?>
                
            </article>    

    <?php

            }

    ?>

            </section>

        <?php
        }
    ?>
    

</div>
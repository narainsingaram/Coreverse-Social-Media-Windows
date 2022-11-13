<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <script src="https://kit.fontawesome.com/e1623e6969.js" crossorigin="anonymous"> </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.38.1/dist/full.css" rel="stylesheet" type="text/css" />
</head>
<body class="likes_body">

<?php  
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
    include("includes/classes/Notification.php");

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}

	?>
    
    <?php 

    	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

    $get_likes = mysqli_query($con, "SELECT likes, added_by FROM user_posts WHERE id='$post_id'");
    $row = mysqli_fetch_array($get_likes);
    $total_likes = $row['likes'];
    $user_liked = $row['added_by'];

    $user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['num_likes'];

    //Like button
    if(isset($_POST['like_button'])) {
		$total_likes++;
		$query = mysqli_query($con, "UPDATE user_posts SET likes='$total_likes' WHERE id='$post_id'");
		$total_user_likes++;
		$user_likes = mysqli_query($con, "UPDATE user SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		$insert_user = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

		//Insert Notification
		if($user_liked != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_liked, "like");
		}
	}
	//Unlike button
	if(isset($_POST['unlike_button'])) {
		$total_likes--;
		$query = mysqli_query($con, "UPDATE user_posts SET likes='$total_likes' WHERE id='$post_id'");
		$total_user_likes--;
		$user_likes = mysqli_query($con, "UPDATE user SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		$insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	}

    //Check for previous likes 
    $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if($num_rows > 0) {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST" class="like_post_form">

        <button class="comment_like" name="unlike_button">
        <span class="bg-red-100 hover:bg-red-200 border-none btn btn-circle"> <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M22 8.692c0 1.19-.19 2.29-.52 3.31H2.52c-.33-1.02-.52-2.12-.52-3.31 0-3.09 2.49-5.59 5.56-5.59 1.81 0 3.43.88 4.44 2.23a5.549 5.549 0 0 1 4.44-2.23c3.07 0 5.56 2.5 5.56 5.59Z" fill="#ef4444"></path><path opacity=".4" d="M21.48 12c-1.58 5-6.45 7.99-8.86 8.81-.34.12-.9.12-1.24 0C8.97 19.99 4.1 17 2.52 12h18.96Z" fill="#ef4444"></path></svg></span>
    </button>
    </form>
';

    }

    else {
        echo '<form action="like.php?post_id=' . $post_id . '" method="POST" class="like_post_form">
            
        <button  class="comment_like" name="like_button">
        <span class="bg-red-100 hover:bg-red-200 border-none btn btn-circle">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M12 21.652c-.31 0-.61-.04-.86-.13-3.82-1.31-9.89-5.96-9.89-12.83 0-3.5 2.83-6.34 6.31-6.34 1.69 0 3.27.66 4.44 1.84a6.214 6.214 0 0 1 4.44-1.84c3.48 0 6.31 2.85 6.31 6.34 0 6.88-6.07 11.52-9.89 12.83-.25.09-.55.13-.86.13Zm-4.44-17.8c-2.65 0-4.81 2.17-4.81 4.84 0 6.83 6.57 10.63 8.88 11.42.18.06.57.06.75 0 2.3-.79 8.88-4.58 8.88-11.42 0-2.67-2.16-4.84-4.81-4.84-1.52 0-2.93.71-3.84 1.94-.28.38-.92.38-1.2 0a4.77 4.77 0 0 0-3.85-1.94Z" fill="#ef4444"></path></svg>
        </span>
    </button>
    </form>
';


    }
    

    ?>

    
</body>
</html>

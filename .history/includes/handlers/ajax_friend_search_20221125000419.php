<?php
include_once("../../config/config.php");
include_once("../classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
 
$names = explode(" ", $query);

$postReturnedQuery = '';

//If query contains an underscore, assume user is searching for usernames
if(strpos($query, '_') !== false) {
	$usersReturnedQuery = mysqli_query($con, "SELECT first_name,last_name,body FROM user, user_posts WHERE body LIKE '$query%' AND first_name LIKE '$query%' LIMIT 8");
}
//If there are two words, assume they are first and last names respectively
else if(count($names) == 2) {
	$usersReturnedQuery = mysqli_query($con, "SELECT first_name,last_name FROM user WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%' OR username LIKE '$names[0]%' ) AND user_closed='no' LIMIT 8");
}
else if ($query == 'Posts') {
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM user_posts LIMIT 8");
}
//If query has one word only, search first names or last names 
else  {
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM user WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%' OR username LIKE '$names[0]%' ) AND user_closed='no' LIMIT 8");
}
 
 
if($query != "" && $query != "Posts"){

	while($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($con, $userLoggedIn); 
		if($row['username'] != $userLoggedIn)
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		else 
			$mutual_friends = "";

			$q_display = '';

			$q_display .= "<div class='resultDisplay' id='transparent_results'>
				<a href='" . $row['username'] . "' style='color: #1485BD'>
					<div class='liveSearchProfilePic'>
						<img src='" . $row['profile_pic'] ."'>
					</div>

					<div class='liveSearchText'>
						" . $row['first_name'] . " " . $row['last_name'] . "
						<p>" . $row['username'] ." </p>
						<p id='grey'>" . $mutual_friends ."</p>
					</div>
				</a>
				</div>";

			echo $q_display;

	}
	
}

else if($query == "Posts") {
	while($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($con, $userLoggedIn); 
			$q_display = '';

			$q_display .= "
			<div class='resultDisplay' id='transparent_results'>
			<a href='" . $row['username'] . "' style='color: #1485BD'>
				<div class='liveSearchProfilePic'>
					<img src='" . $row['profile_pic'] ."'>
				</div>

				<div class='liveSearchText'>
					" . $row['first_name'] . " " . $row['last_name'] . "
					<p>" . $row['username'] ." </p>
					<p id='grey'>" . $mutual_friends ."</p>
				</div>
			</a>
			</div>
			";

			echo $q_display;

	}
}


?>
<?php
include_once("../../config/config.php");
include_once("../classes/User.php");

 
$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];
 
$names = explode(" ", $query); //Search splits at the space so....Jon ....(LECTURE 112)
 
if(strpos($query, "_") !== false) {
	$returnUser = mysqli_query($con, "SELECT * FROM user WHERE username LIKE '$query%' AND user_closed='no'");
}
else if(count($names) == 2) {
	$returnUser = mysqli_query($con, "SELECT * FROM user WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");
}
else {
	$returnUser = mysqli_query($con, "SELECT * FROM user WHERE (first_name LIKE '%$names[0]%' OR last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
}

if($query != "") {
	while($usersQ = mysqli_fetch_array($returnUser) || $postsQ = mysqli_fetch_array($returnUser)) {
		$user = new User($con, $userLoggedIn);

		echo $$usersQ['username'];
		
		if($usersQ['username'] != $userLoggedIn) {
			$mutual_friends = $user->getMutualFriends($usersQ['username']) . " friends in common";
		}
		else {
			$mutual_friends = "";
		}
		if($user->isFriend($usersQ['username'])) {
			echo "<div class='resultDisplay'>
				  <a href='messages.php?u=" . $usersQ['username'] . "' style='color: #000'>
					<div class='liveSearchProfilePic'>
						<img src='".$user['profile_pic'] . "'>
					</div>
					<div class='liveSearchText'>
						".$usersQ['first_name'] . " " . $usersQ['last_name']. "
						<p style='margin: 0;'>". $usersQ['username'] . "</p>
						<p id='grey'>".$mutual_friends . "</p>
					</div>
				  </a>
				  </div>";
		}
	}
}
?>

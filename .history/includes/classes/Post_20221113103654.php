
<link rel="stylesheet" type="text/css" href="assets/css/style.css">
<link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Material+Icons|Material+Icons+Outlined|Material+Icons+Two+Tone|Material+Icons+Round|Material+Icons+Sharp">
<link href="https://cdn.jsdelivr.net/npm/daisyui@2.38.1/dist/full.css" rel="stylesheet" type="text/css" />



    <script src="https://kit.fontawesome.com/e1623e6969.js" crossorigin="anonymous"> </script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<?php
class Post {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to, $imageName) {
 
		$body = strip_tags($body); //The added part starts here
		
		$checkDb = mysqli_query($this->con, "SELECT username FROM user");

		while ($row = mysqli_fetch_array($checkDb)) {
		
			$fullName = '@' . $row['username'];
		
			if(strpos($body, $fullName) !== false) {
		
			 $name = substr($fullName, 1);
		
			 $name = "<a href='" . $name ."' style='background-color:pink; color:#000;'>" . $name . "</a>";
		
			 $body = str_replace($fullName, "@" . $name, $body);
		
		   }
		
		} // The added part ends here
		
		$body = mysqli_real_escape_string($this->con, $body);
		$body = strip_tags($body); //removes html tags 
		$body = mysqli_real_escape_string($this->con, $body);
		$check_empty = preg_replace('/\s+/', '', $body); //Deletes all spaces 
      
		if($check_empty != "" || $imageName != "") {


			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value) {

				if(strpos($value, "www.youtube.com/watch?v=") !== false) {

					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<br><iframe width=\'420\' height=\'315\' src=\'" . $value ."\'></iframe><br>";
					$body_array[$key] = $value;

				}

			}
			$body = implode(" ", $body_array);



			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//If user is on own profile, user_to is 'none'
			if($user_to == $added_by) {
				$user_to = "none";
			}


			//insert post 
			$query = mysqli_query($this->con, "INSERT INTO user_posts VALUES('', '$body', '$added_by', '$user_to', '$date_added', 'no', 'no', '0', '$imageName')");
			$returned_id = mysqli_insert_id($this->con);

			$_SESSION["returned_id"] = $returned_id; 



			//Insert notification 

			if($user_to != 'none') {
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");

			}

			//Update post count for user 
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE user SET num_posts='$num_posts' WHERE username='$added_by'");


			$stopWords = "a about above across after again against all almost alone along already
			 also although always among am an and another any anybody anyone anything anywhere are 
			 area areas around as ask asked asking asks at away b back backed backing backs be became
			 because become becomes been before began behind being beings best better between big 
			 both but by c came can cannot case cases certain certainly clear clearly come could
			 d did differ different differently do does done down down downed downing downs during
			 e each early either end ended ending ends enough even evenly ever every everybody
			 everyone everything everywhere f face faces fact facts far felt few find finds first
			 for four from full fully further furthered furthering furthers g gave general generally
			 get gets give given gives go going good goods got great greater greatest group grouped
			 grouping groups h had has have having he her here herself high high high higher
		     highest him himself his how however i im if important in interest interested interesting
			 interests into is it its itself j just k keep keeps kind knew know known knows
			 large largely last later latest least less let lets like likely long longer
			 longest m made make making man many may me member members men might more most
			 mostly mr mrs much must my myself n necessary need needed needing needs never
			 new new newer newest next no nobody non noone not nothing now nowhere number
			 numbers o of off often old older oldest on once one only open opened opening
			 opens or order ordered ordering orders other others our out over p part parted
			 parting parts per perhaps place places point pointed pointing points possible
			 present presented presenting presents problem problems put puts q quite r
			 rather really right right room rooms s said same saw say says second seconds
			 see seem seemed seeming seems sees several shall she should show showed
			 showing shows side sides since small smaller smallest so some somebody
			 someone something somewhere state states still still such sure t take
			 taken than that the their them then there therefore these they thing
			 things think thinks this those though thought thoughts three through
	         thus to today together too took toward turn turned turning turns two
			 u under until up upon us use used uses v very w want wanted wanting
			 wants was way ways we well wells went were what when where whether
			 which while who whole whose why will with within without work
			 worked working works would x y year years yet you young younger
			 youngest your yours z lol haha omg hey ill iframe wonder else like 
             hate sleepy reason for some little yes bye choose";
			 
			 $stopWords = preg_split("/[\s,]+/", $stopWords);

			 //Remove all punctionation
			 $no_punctuation = preg_replace("/[^a-zA-Z 0-9]+/", "", $body);
 
			 //Predict whether user is posting a url. If so, do not check for trending words
			 if(strpos($no_punctuation, "height") === false && strpos($no_punctuation, "width") === false
				 && strpos($no_punctuation, "http") === false && strpos($no_punctuation, "youtube") === false){
				 //Convert users post (with punctuation removed) into array - split at white space
				 $keywords = preg_split("/[\s,]+/", $no_punctuation);
 
				 foreach($stopWords as $value) {
					 foreach($keywords as $key => $value2){
						 if(strtolower($value) == strtolower($value2))
							 $keywords[$key] = "";
					 }
				 }
 
				 foreach ($keywords as $value) {
					 $this->calculateTrend(ucfirst($value));
				 }
 
			  }
 
		 }
	 }

	 public function calculateTrend($term) {

		if($term != '') {
			$query = mysqli_query($this->con, "SELECT * FROM trends WHERE title='$term'");

			if(mysqli_num_rows($query) == 0)
				$insert_query = mysqli_query($this->con, "INSERT INTO trends(title,hits) VALUES('$term','1')");
			else 
				$insert_query = mysqli_query($this->con, "UPDATE trends SET hits=hits+1 WHERE title='$term'");
		}

	}

	public function loadPostsFriends($data, $limit) {

		$page = $data['page']; 
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM user_posts WHERE deleted='no' ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];
				$imagePath = $row['image'];

				//Prepare user_to string so it can be included even if not posted to a user
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to_obj = new User($this->con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
				}

				//Check if user who posted, has their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()) {
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);

				

					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}
					
					if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				else 
					$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM user WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


                    ?>

				<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>



                    <?php

                    $comments_check  = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

					$likes_check = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
					$likes_check_num = mysqli_num_rows($likes_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval->y == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else 
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
					else if ($interval->m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month ". $days;
						}
						else {
							$time_message = $interval->m . " months ". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}
					
					if($imagePath != "") {
						$imageDiv = "
										<img class='my-2 rounded-2xl shadow-lg w-10/12 m-auto h-8/12 object-cover' src='$imagePath'>
									";
					}
					else {
						$imageDiv = "";
					}

					$get_likes = mysqli_query($this->con, "SELECT likes, added_by FROM user_posts WHERE id='$post_id'");
					$row = mysqli_fetch_array($get_likes);
					$total_likes = $row['likes'];
					$user_liked = $row['added_by'];
				
					$user_details_query = mysqli_query($this->con, "SELECT * FROM user WHERE username='$user_liked'");
					$row = mysqli_fetch_array($user_details_query);
					$total_user_likes = $row['num_likes'];
				
					//Like button
					if(isset($_POST['like_button'])) {
						$total_likes++;
						$query = mysqli_query($this->con, "UPDATE user_posts SET likes='$total_likes' WHERE id='$post_id'");
						$total_user_likes++;
						$user_likes = mysqli_query($this->con, "UPDATE user SET num_likes='$total_user_likes' WHERE username='$user_liked'");
						$insert_user = mysqli_query($this->con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");
				
						//Insert Notification
						if($user_liked != $userLoggedIn) {
							$notification = new Notification($this->con, $userLoggedIn);
							$notification->insertNotification($post_id, $user_liked, "like");
						}
					}
					//Unlike button
					if(isset($_POST['unlike_button'])) {
						$total_likes--;
						$query = mysqli_query($this->con, "UPDATE user_posts SET likes='$total_likes' WHERE id='$post_id'");
						$total_user_likes--;
						$user_likes = mysqli_query($this->con, "UPDATE user SET num_likes='$total_user_likes' WHERE username='$user_liked'");
						$insert_user = mysqli_query($this->con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
					}
				
					//Check for previous likes 
					$check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
					$num_rows = mysqli_num_rows($check_query);
				
					if($num_rows > 0) {
						echo '<form action="like.php?post_id=' . $post_id . '" method="POST" class="like_post_form">
				
						<button class="comment_like" name="unlike_button">
						<span id="liked_animation" class="material-icons-round" style="color: red; font-size: 30px;"> favorite</span>
					</button>
					</form>
				';
				
					}
				
					else {
						echo '<form action="like.php?post_id=' . $post_id . '" method="POST" class="like_post_form">
							
						<button  class="comment_like" name="like_button">
						<span id="about_to_like" class="material-icons-round" style="color: black; font-size: 30px;"> favorite_border</span>
					</button>
					</form>
				';
				
				
					}
					

            
                $str .= "<div class='status_post' id='on_hover_post_info_modal'>
				<div class='dropdown'>
				<span class='update_stats'> <i class='uil uil-ellipsis-h'></i> </span>
				<div class='dropdown-content'>
				  <a> <i class='uil uil-sync'></i> Update Statistics </a>
				  <a> <i class='uil uil-confused'></i> Report Post </a>
				  <a> <i class='uil uil-trash-alt'></i> Remove Post </a>
				</div>
			  </div>
			  
                        <div class='aligned-flex-css'>
                    <div class='post_profile_pic'>
                        <img src='$profile_pic'>
                    </div>

                    <span class='posted_by' style='color:#ACACAC;'>
                        <a class='first_name__last_name_post tooltip' href='$added_by'> $first_name $last_name <div class='tooltiptext'>  </div></a>  $user_to <br>
            
						<span class='time_num_info'> $time_message </span>  
							<span class='comment_num_info'><i class='uil uil-comment'></i> $comments_check_num </span> 
								<span class='like_num_info'> <i class='uil uil-heart'></i> $likes_check_num </span>  
					
                    </div>

					$imageDiv
                    <div id='post_body'>
                        $body
                    </div>
        <div class='newsfeedPostOptions'>
		<span> <i class='uil uil-share shareicon'></i> </span>
		<div class='share-dropdown-content'>
		<div class='popup'>
		<header>
      <span>Share Modal</span>
    </header>
    <div class='content'>
      <p>Share this link via</p>
      <ul class='icons'>
        <a href='#'><i class='uil uil-message'></i></a>
        <a href='#'><i class='fab fa-twitter'></i></a>
        <a href='#'><i class='fab fa-instagram'></i></a>
        <a href='#'><i class='fab fa-whatsapp'></i></a>
        <a href='#'><i class='fab fa-telegram-plane'></i></a>
      </ul>
      <p>Or copy link</p>
      <div class='field'>
        <i class='url-icon uil uil-link' ></i>
        <input type='text' id='myInput' contentEditable='true' value='http://localhost/demo/post.php?id=$id'>
      </div>
    </div>
	  </div>
		</button>
		
    </div>
	</div>
	
                </div>

				<div class='card bg-red-200 px-4 py-6 shadow-lg'>
					$imageDiv
				<div class='card-body'>
				  <p>$body</p>
				  <div class='card-actions justify-end'>
					<button class='btn btn-primary'>Learn now!</button>
					<iframe src='like.php?post_id=$id' class='btn btn-ghost h-30 w-20' scrolling='no'></iframe>
					<button  class='btn btn-ghost' name='comment-toggle-button' onClick='javascript:toggle$id()'> 
					<i class='uil uil-comment comment_animation'></i>
					</button>
				  </div>
				</div>
			  </div>			
			  
			<div class='post_comment' id='toggleComment$id' style='display: none;'>
				<iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
			</div>

				";

        }

		?>

<script>
     $(document).ready(function(){
         $('#post<?php echo $id; ?>').on('click', function(event){
             bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                $.post("includes/form_handlers/delete_post.php", {result:result, post_id: '<?php echo $id; ?>'});
				if(result) {
    	setTimeout(function(){
		location.reload();
    	}, 300);
}
              }); 
         });
      });


</script>



		<?php

        if($count > $limit) 
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
            <input type='hidden' class='noMorePosts' value='false'>";
            
        else 
            $str .= "<input type='hidden' class='noMorePosts' value='true'> <aside><img class='no-results-img' src='assets\images\m-no-results.png'><p style='text-align: center;'> 
            No more posts to show. </p></aside>";

        

    }

        echo $str;


    }

	public function loadProfilePosts($data, $limit) {

		$page = $data['page']; 
		$profile_user = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1) 
			$start = 0;
		else 
			$start = ($page - 1) * $limit;


		$str = ""; //String to return 
		$data_query = mysqli_query($this->con, "SELECT * FROM user_posts WHERE deleted='no' AND ((added_by='$profile_user' AND user_to='none') OR user_to='$profile_user') ORDER BY id DESC");

		if(mysqli_num_rows($data_query) > 0) {


			$num_iterations = 0; //Number of results checked (not necasserily posted)
			$count = 1;

			while($row = mysqli_fetch_array($data_query)) {
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];



					if($num_iterations++ < $start)
						continue; 


					//Once 10 posts have been loaded, break
					if($count > $limit) {
						break;
					}
					else {
						$count++;
					}
					
					if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				else 
					$delete_button = "";

					$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM user WHERE username='$added_by'");
					$user_row = mysqli_fetch_array($user_details_query);
					$first_name = $user_row['first_name'];
					$last_name = $user_row['last_name'];
					$profile_pic = $user_row['profile_pic'];


                    ?>

				<script> 
						function toggle<?php echo $id; ?>() {

							var target = $(event.target);
							if (!target.is("a")) {
								var element = document.getElementById("toggleComment<?php echo $id; ?>");

								if(element.style.display == "block") 
									element.style.display = "none";
								else 
									element.style.display = "block";
							}
						}

					</script>



                    <?php

                    $comments_check  = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
                    $comments_check_num = mysqli_num_rows($comments_check);

					$likes_check = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
					$likes_check_num = mysqli_num_rows($likes_check);


					//Timeframe
					$date_time_now = date("Y-m-d H:i:s");
					$start_date = new DateTime($date_time); //Time of post
					$end_date = new DateTime($date_time_now); //Current time
					$interval = $start_date->diff($end_date); //Difference between dates 
					if($interval->y >= 1) {
						if($interval == 1)
							$time_message = $interval->y . " year ago"; //1 year ago
						else 
							$time_message = $interval->y . " years ago"; //1+ year ago
					}
					else if ($interval-> m >= 1) {
						if($interval->d == 0) {
							$days = " ago";
						}
						else if($interval->d == 1) {
							$days = $interval->d . " day ago";
						}
						else {
							$days = $interval->d . " days ago";
						}


						if($interval->m == 1) {
							$time_message = $interval->m . " month". $days;
						}
						else {
							$time_message = $interval->m . " months". $days;
						}

					}
					else if($interval->d >= 1) {
						if($interval->d == 1) {
							$time_message = "Yesterday";
						}
						else {
							$time_message = $interval->d . " days ago";
						}
					}
					else if($interval->h >= 1) {
						if($interval->h == 1) {
							$time_message = $interval->h . " hour ago";
						}
						else {
							$time_message = $interval->h . " hours ago";
						}
					}
					else if($interval->i >= 1) {
						if($interval->i == 1) {
							$time_message = $interval->i . " minute ago";
						}
						else {
							$time_message = $interval->i . " minutes ago";
						}
					}
					else {
						if($interval->s < 30) {
							$time_message = "Just now";
						}
						else {
							$time_message = $interval->s . " seconds ago";
						}
					}
            
                $str .= "<div class='status_post'>
				<div class='dropdown'>
				<span class='update_stats' onclick='myDropdownMenuFunction'> <i class='uil uil-ellipsis-h'></i> </span>
				<div id='dropdown-content' class='dropdown-content'>
				  <a> <i class='uil uil-sync'></i> Update Statistics </a>
				  <a> <i class='uil uil-trash-alt'></i> Remove Post </a>
				</div>
			  </div>
                        <div class='aligned-flex-css'>
                    <div class='post_profile_pic'>
                        <img src='$profile_pic' width='60'>
                    </div>

                    <span class='posted_by' style='color:#ACACAC;'>
                        <a class='first_name__last_name_post' href='$added_by'> $first_name $last_name</a> &nbsp;&nbsp;&nbsp;&nbsp; 
                        <sup>
						<span class='time_num_info'> $time_message </span>  
							<span class='comment_num_info'> Comments: $comments_check_num </span> 
								<span class='like_num_info'> Likes: $likes_check_num </span>  
								</sup>
                    </div>
                    <hr class='profile_pic_posted_by--post_body__divider'>
                    <div id='post_body'>
                        $body
						
                    </div>
        <div class='newsfeedPostOptions'>'
		<button class='comment-toggle-button' name='comment-toggle-button' onClick='javascript:toggle$id()'> 
		<i class='uil uil-comment comment_animation'></i>
		</button>
		<iframe src='like.php?post_id=$id' id='like_iframe' scrolling='no'></iframe>
		
    </div>
	
                </div>    
                <div class='post_comment' id='toggleComment$id' style='display: none;'>
                    <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                </div>
                ";

        

		?>

<script>
 
                    $(document).ready(function() {
 
                        $('#post<?php echo $id; ?>').on('click', function() {
                            bootbox.confirm("Are you sure you want to delete this post?", function(result) {
 
                                $.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});
 
                                if(result)
                                    location.reload();
 
                            });
                        });
 
 
                    });
                </script>

		<?php

        if($count > $limit) 
            $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
            <input type='hidden' class='noMorePosts' value='false'>";


        

    }

        echo $str;


    }

}

public function getSinglePost($post_id) {

	$userLoggedIn = $this->user_obj->getUsername();

	$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$post_id'");

	$str = ""; //String to return 
	$data_query = mysqli_query($this->con, "SELECT * FROM user_posts WHERE deleted='no' AND id='$post_id'");

	if(mysqli_num_rows($data_query) > 0) {


		$row = mysqli_fetch_array($data_query); 
			$id = $row['id'];
			$body = $row['body'];
			$added_by = $row['added_by'];
			$date_time = $row['date_added'];

			//Prepare user_to string so it can be included even if not posted to a user
			if($row['user_to'] == "none") {
				$user_to = "";
			}
			else {
				$user_to_obj = new User($this->con, $row['user_to']);
				$user_to_name = $user_to_obj->getFirstAndLastName();
				$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";
			}

			//Check if user who posted, has their account closed
			$added_by_obj = new User($this->con, $added_by);
			if($added_by_obj->isClosed()) {
				return;
			}

			$user_logged_obj = new User($this->con, $userLoggedIn);
			if($user_logged_obj->isFriend($added_by)){


				if($userLoggedIn == $added_by)
					$delete_button = "<button class='delete_button btn-danger' id='post$id'>X</button>";
				else 
					$delete_button = "";


				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM user WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];


				?>
				<script> 
					function toggle<?php echo $id; ?>() {

						var target = $(event.target);
						if (!target.is("a")) {
							var element = document.getElementById("toggleComment<?php echo $id; ?>");

							if(element.style.display == "block") 
								element.style.display = "none";
							else 
								element.style.display = "block";
						}
					}

				</script>
				<?php

				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				$comments_check_num = mysqli_num_rows($comments_check);


				//Timeframe
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time); //Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date); //Difference between dates 
				if($interval->y >= 1) {
					if($interval == 1)
						$time_message = $interval->y . " year ago"; //1 year ago
					else 
						$time_message = $interval->y . " years ago"; //1+ year ago
				}
				else if ($interval->m >= 1) {
					if($interval->d == 0) {
						$days = " ago";
					}
					else if($interval->d == 1) {
						$days = $interval->d . " day ago";
					}
					else {
						$days = $interval->d . " days ago";
					}


					if($interval->m == 1) {
						$time_message = $interval->m . " month". $days;
					}
					else {
						$time_message = $interval->m . " months". $days;
					}

				}
				else if($interval->d >= 1) {
					if($interval->d == 1) {
						$time_message = "Yesterday";
					}
					else {
						$time_message = $interval->d . " days ago";
					}
				}
				else if($interval->h >= 1) {
					if($interval->h == 1) {
						$time_message = $interval->h . " hour ago";
					}
					else {
						$time_message = $interval->h . " hours ago";
					}
				}
				else if($interval->i >= 1) {
					if($interval->i == 1) {
						$time_message = $interval->i . " minute ago";
					}
					else {
						$time_message = $interval->i . " minutes ago";
					}
				}
				else {
					if($interval->s < 30) {
						$time_message = "Just now";
					}
					else {
						$time_message = $interval->s . " seconds ago";
					}
				}

				$str .= "<div class='status_post'>
				<div class='dropdown'>
				<span class='update_stats' onclick='myDropdownMenuFunction'> <i class='uil uil-ellipsis-h'></i> </span>
				<div id='dropdown-content' class='dropdown-content'>
				  <a> <i class='uil uil-sync'></i> Update Statistics </a>
				  <a> <i class='uil uil-trash-alt'></i> Remove Post </a>
				</div>
			  </div>
                        <div class='aligned-flex-css'>
                    <div class='post_profile_pic'>
                        <img src='$profile_pic' width='60'>
                    </div>

                    <span class='posted_by' style='color:#ACACAC;'>
                        <a class='first_name__last_name_post' href='$added_by'> $first_name $last_name</a> &nbsp;&nbsp;&nbsp;&nbsp; 
                        <sup>
						<span class='time_num_info'> $time_message </span>  
							<span class='comment_num_info'> Comments: $comments_check_num </span> 
								</sup>
                    </div>
                    <hr class='profile_pic_posted_by--post_body__divider'>
                    <div id='post_body'>
                        $body
                    </div>
        <div class='newsfeedPostOptions'>
		<button class='comment-toggle-button' name='comment-toggle-button' onClick='javascript:toggle$id()'> 
		<i class='uil uil-comment comment_animation'></i>
		</button>
		<iframe src='like.php?post_id=$id' id='like_iframe' scrolling='no'></iframe>
    </div>
	
                </div>    
                <div class='post_comment' id='toggleComment$id' style='display: none;'>
                    <iframe src='comment_frame.php?post_id=$id' id='comment_iframe' frameborder='0'></iframe>
                </div>
                ";

			?>
			<script>

				$(document).ready(function() {

					$('#post<?php echo $id; ?>').on('click', function() {
						bootbox.confirm("Are you sure you want to delete this post?", function(result) {

							$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>", {result:result});

							if(result)
								location.reload();

						});
					});


				});

			</script>
			<?php
			}
			else {
				echo "<p>You cannot see this post because you are not friends with this user.</p>";
				return;
			}
	}
	else {
		echo "<p>No post found. If you clicked a link, it may be broken.</p>";
				return;
	}

	echo $str;
}


}
    


?>

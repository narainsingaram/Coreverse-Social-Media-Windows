<?php
 
    include("../../config/config.php");
    include("../classes/User.php");
 
    $userLoggedIn = $_POST['userLoggedIn'];
 
    $userLoggedIn = new User($con, $userLoggedIn);
 
    $result = array();
    $result = $userLoggedIn->getFriendArrays();
 
    $friend_array_string = trim($result, ",");
 
    if ($friend_array_string !== "") {
 
    $no_commas = explode(",", $friend_array_string);
 
      foreach ($no_commas as $value) {
 
          $friend = mysqli_query($con, "SELECT first_name, last_name, username, profile_pic FROM user WHERE username='$value'");
 
          $row = mysqli_fetch_assoc($friend);
 
           echo "<div class='displayTag column'>
           <div class='resultDisplay'>
           <div class='user_details_left_right'>
                        <a href=" . $row['username'] . ">         

                            <div class='TagProfilePic'>
						<img src='".$row['profile_pic'] . "'>
					        </div>
 

                            <div class='liveSearchText'>
                            ".$row['first_name'] . " " . $row['last_name']. "
                            <p style='margin: 0;'>". $row['username'] . "</p>
                        </div>
 
                            </a>    

                            </div>
                            </div>
                            </div>
                            </div>
				  </div>

                    </div>";
 
      }
 
    }
 
    else {
 
    echo "<br><p id='ynf'>You have no friends. Please add someone</p>";
    }
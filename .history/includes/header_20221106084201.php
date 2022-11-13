<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");

if(isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM user WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);

}

else {
    header("Location: register.php");

}


?> 


<html lang="en" data-theme="light">
<head>                  
    <meta charset="UTF-8">  
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="assets/js/bootstrap.js"></script>
	<script src="assets/js/bootbox.min.js"></script>
    <script src="assets\js\corverse.js"></script>
    <script src="assets\js\jquery.Jcrop.js"></script>
	<script src="assets\js\jcrop_bits.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="assets\css\jquery.Jcrop.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" id='themeStylesheetLink'href="assets/css/themes/default.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/e1623e6969.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Flow+Rounded&family=Poppins:ital,wght@0,100;0,200;0,300
    ;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300;400;500;6
    00;700&family=Roboto:wght@100&family=Rubik+Mono+One&family=Space+Mono&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.38.1/dist/full.css" rel="stylesheet" type="text/css" />
</head>

   <nav>
     <div class="container">   
  <h2 class="log"> 
 Corverse
  </h2>
<div class="search-bar">
<form action="search.php" method="GET" name="search_form">
  <i class="uil uil-search"></i>
  <input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')"  name="q" autocomplete="off" placeholder="Search for people" id="search_text_input">  

</form>

      
<div class="search_results">
                
    </div>
    
<div class="search_results_footer_empty">
    
              </div>


</div>
<div class="create">
    <label class="create_post_label"for="create a post"> Create</label>
    <div class="profile-photo">
        <img src="<?php echo $user['profile_pic']; ?>" alt="">
    </div>
  </div>
</div>   
</div>      
   </nav>

    <div class="navbar bg-base-100">
    <div class="flex-1">
        <a class="btn btn-ghost normal-case text-xl">daisyUI</a>
    </div>
    <div class="flex-none">
        <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-circle">
            <div class="indicator">
                <img class="h-4 w-5" src="<?php echo $user['profile_pic']; ?>" alt="">
            <span class="badge badge-sm indicator-item">8</span>
            </div>
        </label>
        <div tabindex="0" class="mt-3 card card-compact dropdown-content w-52 bg-base-100 shadow">
            <div class="card-body">
            <span class="font-bold text-lg">8 Items</span>
            <span class="text-info">Subtotal: $999</span>
            <div class="card-actions">
                <button class="btn btn-primary btn-block">View cart</button>
            </div>
            </div>
        </div>
        </div>
        <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-ghost btn-circle avatar">
            <div class="w-10 rounded-full">
            <img src="https://placeimg.com/80/80/people" />
            </div>
        </label>
        <ul tabindex="0" class="menu menu-compact dropdown-content mt-3 p-2 shadow bg-base-100 rounded-box w-52">
            <li>
            <a class="justify-between">
                Profile
                <span class="badge">New</span>
            </a>
            </li>
            <li><a>Settings</a></li>
            <li><a>Logout</a></li>
        </ul>
        </div>
    </div>
    </div>


<?php
    //Unread Messages
    $messages = new Message ($con, $userLoggedIn);
    $num_messages = $messages->getUnreadNumber();

    $notifications = new Notification ($con, $userLoggedIn);
    $num_notifications = $notifications->getUnreadNumber();

    $user_obj = new User ($con, $userLoggedIn);
    $num_requests = $user_obj->getNumberOfFriendRequests();
?>

      

    <div class="dropdown_data_window" style='height: 0px;'>    
        <input type="hidden" id="dropdown_data_type" value="">
    </div>


    <script>
        
if (window.history.replaceState) {
         window.history.replaceState(null, null, window.location.href);
}


	var userLoggedIn = '<?php echo $userLoggedIn; ?>';

	$(document).ready(function() {

		$('.dropdown_data_window').scroll(function() {
			var inner_height = $('.dropdown_data_window').innerHeight(); //Div containing data
			var scroll_top = $('.dropdown_data_window').scrollTop();
			var page = $('.dropdown_data_window').find('.nextPageDropdownData').val();
			var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

			if ((scroll_top + inner_height >= $('.dropdown_data_window')[0].scrollHeight) && noMoreData == 'false') {

				var pageName; //Holds name of page to send ajax request to
				var type = $('#dropdown_data_type').val();


				if(type == 'notification')
					pageName = "ajax_load_notifications.php";
				else if(type = 'message')
					pageName = "ajax_load_messages.php"


				var ajaxReq = $.ajax({
					url: "includes/handlers/" + pageName,
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
					cache:false,

					success: function(response) {
						$('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
						$('.dropdown_data_window').find('.noMoreDropdownData').remove(); //Removes current .nextpage 


						$('.dropdown_data_window').append(response);
					}
				});

			} //End if 

			return false;

		}); //End (window).scroll(function())


	});

	</script>


<body>







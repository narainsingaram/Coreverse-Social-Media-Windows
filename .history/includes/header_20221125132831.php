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
    <script src="assets\js\corverse.js"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
    <link rel="stylesheet" href="assets\css\jquery.Jcrop.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <link rel="stylesheet" type="text/css" id='themeStylesheetLink'href="assets/css/themes/default.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Flow+Rounded&family=Poppins:ital,wght@0,100;0,200;0,300
    ;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300;400;500;6
    00;700&family=Roboto:wght@100&family=Rubik+Mono+One&family=Space+Mono&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.38.1/dist/full.css" rel="stylesheet" type="text/css" />
</head>

<?php
    //Unread Messages
    $messages = new Message ($con, $userLoggedIn);
    $num_messages = $messages->getUnreadNumber();

    $notifications = new Notification ($con, $userLoggedIn);
    $num_notifications = $notifications->getUnreadNumber();

    $user_obj = new User ($con, $userLoggedIn);
    $num_requests = $user_obj->getNumberOfFriendRequests();


?>


<div class="navbar bg-base-100">
    <div class="navbar-start">
        <div class="dropdown">
        <label tabindex="0" class="btn btn-ghost btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
        </label>
        <ul tabindex="0" class="menu dropdown-content p-2 shadow bg-base-100 rounded-box w-52">
            <li><a>Homepage</a></li>
            <li><a>Portfolio</a></li>
            <li><a>About</a></li>
        </ul>
        </div>
    </div>
    <a class="btn btn-ghost normal-case text-xl">
            Corverse
        </a>
    <div class="navbar-end">
        <button href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')" class="btn btn-ghost btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"><path opacity=".4" d="m19.34 14.488-1-1.66c-.21-.37-.4-1.07-.4-1.48v-2.53c0-3.26-2.65-5.92-5.92-5.92S6.1 5.558 6.1 8.818v2.53c0 .41-.19 1.11-.4 1.47l-1.01 1.67c-.4.67-.49 1.41-.24 2.09.24.67.81 1.19 1.55 1.44 1.94.66 3.98.98 6.02.98 2.04 0 4.08-.32 6.02-.97.7-.23 1.24-.76 1.5-1.45s.19-1.45-.2-2.09Z" fill="#000"></path>
            <path d="M14.25 3.32c-.69-.27-1.44-.42-2.23-.42-.78 0-1.53.14-2.22.42.43-.81 1.28-1.32 2.22-1.32.95 0 1.79.51 2.23 1.32ZM14.83 20.01A3.014 3.014 0 0 1 12 22c-.79 0-1.57-.32-2.12-.89-.32-.3-.56-.7-.7-1.11.13.02.26.03.4.05.23.03.47.06.71.08.57.05 1.15.08 1.73.08.57 0 1.14-.03 1.7-.08.21-.02.42-.03.62-.06l.49-.06Z" fill="#000"></path></svg>
            <?php echo $num_messages; ?>
        </button>
        <label for='search_modal'  class="btn btn-ghost btn-circle">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none"><path opacity=".4" d="M11.01 20.02a9.01 9.01 0 1 0 0-18.02 9.01 9.01 0 0 0 0 18.02Z" fill="#000"></path><path d="M21.99 18.95c-.33-.61-1.03-.95-1.97-.95-.71 0-1.32.29-1.68.79-.36.5-.44 1.17-.22 1.84.43 1.3 1.18 1.59 1.59 1.64.06.01.12.01.19.01.44 0 1.12-.19 1.78-1.18.53-.77.63-1.54.31-2.15Z" fill="#000"></path></svg>        </label>
        <button class="btn btn-ghost btn-circle">
            <img class="h-8 w-8 rounded-full" src="<?php echo $user['profile_pic']; ?>" alt="">
        </button>
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










<?php

require 'C:\xampp\htdocs\Demo\config\config.php';
require 'C:\xampp\htdocs\Demo\includes\form_handlers\register_handler.php';
require 'C:\xampp\htdocs\Demo\includes\form_handlers\login_handler.php';

?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="assets\css\register_real_style.css">
    <script src="assets\js\register.js"> </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>        
    <link href="https://fonts.googleapis.com/css2?family=Flow+Rounded&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Quicksand:wght@300;400;500;600;700&family=Roboto:wght@100&family=Rubik+Mono+One&family=Space+Mono&display=swap" rel="stylesheet">
</head>
<body>

<div class="wrapper">
    <div class="login_box">
    <div class="login_header">
        <h1>Whizkit</h1>
    </div>

    <div class="first">



<form action="register.php" method="POST">

<input type="email" name="log_email" placeholder="Email Address" value="<?php

if (isset($_SESSION['log_email'])){

echo $_SESSION['log_email'];

}

?>"required>

<br>

<input type="password" name="log_password" placeholder="Password">

<br>

<?php if(in_array("Email or Password was incorrect<br>", $error_array)) echo "Email or Password was incorrect<br>"?>

<input type="submit" name="login_button" value="Login">

<br>

<a href="register.php" id="signup" class="signup">Need an account?</a>

</form>



</div>

</div>

</div>

</body>

</html>
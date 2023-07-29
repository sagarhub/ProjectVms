<?php
require_once("./authentication/login.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="login.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="./assets/icons8-online-maintenance-portal-100.png">

</head>

<body>
  <form id="login_style" method="post">
    <img id="vms" src="assets/logo-with-name-png.png" width="250px" height="100px">
    <!-- <h3 id="heading">Login or Sign Up</h3> -->
    <div style="width:85%; margin: 0 auto;">
      <input id="userPass_input" type="text" name="user" placeholder="Username" required> <br>
      <input id="userPass_input" type="password" name="pass" placeholder="Password" required><br>
      <button id="lg_btn" name="login" style="cursor:pointer; ">Log In </button>
      <!-- <a style="color:green;" href="./Reset Password.php">
        <p style="text-align: center; cursor:pointer;  ">Forgot password?</p>
      </a> -->
      <hr>
      <a  class="secondary_button" href="/register/register.php">Register</a>

      <br>
    </div>
    <?php if ($error) : ?>
      <div style="color: red; text-align:center;"><?php echo $error ?></div>
    <?php elseif ($username_err) : ?>
      <div style="color: red; text-align:center;"><?php echo $username_err ?></div>
    <?php elseif ($status) : ?>
      <div style="color: green; text-align:center;"><?php echo $status ?></div>
    <?php endif ?>
  </form>

</body>

</html>
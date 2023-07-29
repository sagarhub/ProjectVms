<?php
$error = "";
$status = "";
$repeat = "";

require("../dbconnection.php");
header("location.php");
if (isset($_POST['save'])) {
    $conn = get_dbconnection();
    $username = $_POST['user'];
    $stmt = $conn->prepare("SELECT * FROM login WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    //check if username is taken or not 
    if ($stmt->num_rows() > 0) {
        //error
        $repeat = "username aready taken";
    } else {
        //insert
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $country = $_POST['country'];
        $city = $_POST['city'];
        $contact = $_POST['contact'];
        $email = $_POST['email'];
        $username = $_POST['user'];
        $password = $_POST['pass'];
        $confirm_password = $_POST['c_pass'];
        if ($password == $confirm_password) {
            $conn2 = get_dbconnection();
            //parameterise to avoid sql injection
            $stmt = $conn->prepare("INSERT INTO login(first_name , last_name,country,city,contact,email,username,password) VALUES(?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssisss", $first_name, $last_name, $country, $city,  $contact, $email, $username, $hash_password);
            //hashing
            $hash_password = password_hash($password, PASSWORD_BCRYPT);
            if ($stmt->execute()) {
                $status = "Data saved successfully";
                header("location:/index.php");
            }
        } else {
            $error = "password doesn't match";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="REGISTER.CSS">
  <title>User Registration</title>

  <link rel="icon" type="image/x-icon" href="../assets/icons8-online-maintenance-portal-100.png">

</head>

<body>
    <form method="post">
        <h3 style="color: green; text-align:center; margin-top:3px">Register to continue</h3>
        <div style="width: 80%; margin:auto;">

            <div class="flex mb-2">
                <input type="text" name="first_name" placeholder="First Name" required>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
            <div class="flex mb-2">

                <input type="text" name="country" placeholder="Country" required>
                <input type="text" name="city" placeholder="City" required>
            </div>
            <div>
                <input style="margin-bottom: 10px;" type="text" name="contact" placeholder="Contact" required>
                <input type="text" name="email" placeholder="Email" required>
            </div><br>
            <p>Login credentials</p>
            <hr>
            <div>
            <input style="margin-bottom: 10px;" type="text" name="user" placeholder="Username" required>
            </div>
            <div class="flex mb-2">
                <input type="password" name="pass" placeholder="New password" required>
                <input type="password" name="c_pass" placeholder="Confirm password">
            </div>



            <button style="cursor:pointer;" id="reg_btn" name="save">Register</button>
</div>

            <?php
            if ($status) : ?>
                <div style="color: green;"><?php echo $status ?>✅</div>

            <?php elseif ($error) :
            ?>
                <div style="color: red;"><?php echo $error ?>🛑</div>
            <?php
            elseif ($repeat) : ?>
                <div style="color: red;"><?php echo $repeat ?></div>
            <?php
            endif ?>
    </form>

</body>

</html>
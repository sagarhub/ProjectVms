<?php
$error = "";
$status = "";
$repeat = "";

require("../PdoConnection.php");
require("../session.php");

header("location.php");
if (isset($_POST['save'])) {
    $conn = get_PdoConnection();
    $UserId= $_GET["ResetId"];
    $oldPassword = $_POST['Old_pass'];
    $stmt = $conn->prepare("SELECT * FROM login WHERE id = :UserId");
    $stmt->bindParam(":UserId", $UserId);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $password = $_POST['pass'];
        $confirm_password = $_POST['c_pass'];
        foreach($result as $data)
        {
            $OldPass = $data['password'];
        }
        if(password_verify($oldPassword, $OldPass))
        {
            
        if ($password == $confirm_password) {
            
            //parameterise to avoid sql injection
            $hash_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt2 = $conn->prepare("update login set password = :ResetPass where id = :UserId");
            $stmt2->bindParam("ResetPass",$hash_password);
            $stmt2->bindParam(":UserId", $UserId);
            //hashing
            if ($stmt2->execute()) {
                $status = "Password reset  successfully";
                header("location:/User_profile/profile.php");
            }
        } else {
            $error = "password doesn't match";
        }
    }
    else
    {
        $error = "Old password doesn't match";
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
    <?php require("../html/layout.php"); ?>
    <link rel="stylesheet" href="./profile.css">
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form class="random" method="post">
        <h3 style="color: green; text-align:center; margin-top:3px">Reset Password</h3>
        <div style="width: 80%; margin:auto">

            <p>Login credentials</p>
            <hr>

            <input style="margin-bottom: 10px;" type="password" name="Old_pass" placeholder="Old password" required>

            <div class="flex mb-2">
                <input type="password" name="pass" placeholder="New password" required>
                <input type="password" name="c_pass" placeholder="Confirm password" required>
            </div>



            <button  style="cursor:pointer;" id="reg_btn" name="save">Reset</button>
            </>

            <?php
            if ($status) : ?>
                <div style="color: green;"><?php echo $status ?>✅</div>

            <?php elseif ($error) :
            ?>
                <div style="color: red; text-align:center;"><?php echo $error ?>🛑</div>
            <?php
            elseif ($repeat) : ?>
                <div style="color: red;"><?php echo $repeat ?></div>
            <?php
            endif ?>
    </form>

</body>

</html>
<?php
$error = "";
$status = "";
$repeat = "";

require("../dbconnection.php");
require_once("../PdoConnection.php");
header("location.php");
$conn = get_PdoConnection();

$ID = $_GET['id'];
$select = $conn->prepare("SELECT * FROM login WHERE id = :Edit_id");
$select->bindParam("Edit_id", $ID);
$select->execute();
$Values = $select->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //insert
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    //parameterise to avoid sql injection
    $stmt = $conn->prepare("UPDATE login SET first_name =:FirstName, last_name=:LastName,country=:Country,city=:City,contact=:Contact,email=:Email WHERE id=:Edit_id");
    $stmt->bindParam("FirstName", $first_name);
    $stmt->bindParam("LastName", $last_name);
    $stmt->bindParam("Country", $country);
    $stmt->bindParam("City", $city);
    $stmt->bindParam("Contact", $contact);
    $stmt->bindParam("Email", $email);
    $stmt->bindParam("Edit_id", $ID);
    $stmt->execute();

    header("location:/user_profile/profile.php");
}


?>
<!DOCTYPE html>
<html lang="en">


    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <?php require_once("../html/layout.php"); ?>
    <link rel="stylesheet" href="./profile.css">
</head>

<body>
<?php require_once("../nav.php"); ?>
    <form class="random" method="post">
        <h3 style="color: green; text-align:center; margin-top:3px">Edit Details</h3>
        <div style="width: 80%; margin:auto">
            <?php
            foreach ($Values as $data) { ?>


                <div class="flex mb-2">
                    <input type="text" value="<?= $data["first_name"] ?>" name="first_name" placeholder="First Name" required>
                    <input type="text" value="<?= $data["last_name"] ?>" name="last_name" placeholder="Last Name" required>
                </div>
                <div class="flex mb-2">

                    <input type="text" value="<?= $data["country"] ?>" name="country" placeholder="Country" required>
                    <input type="text" value="<?= $data["city"] ?>" name="city" placeholder="City" required>
                </div>
                <div>
                    <input style="margin-bottom: 10px;" value="<?= $data["contact"] ?>" type="text" name="contact" placeholder="Contact" required>
                    <input type="text" value="<?= $data["email"] ?>" name="email" placeholder="Email" required>
                </div><br>


                <button style="cursor:pointer;" id="reg_btn" name="save">Save</button>
            <?php } ?>

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
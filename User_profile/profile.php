<?php

require_once("../PdoConnection.php");
require_once("../session.php");
require_once("../functions/functions.php");
$conn = get_PdoConnection();
$current_user = $_SESSION['id'];
$stmt= $conn->prepare("select * from login l where id=:current_id");
$stmt->bindParam(":current_id",$current_user);
$stmt->execute();
$get_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">

<title>User Profile</title>
</head>

<body>
<?php require("../nav.php"); ?>
<form method="get" style="margin:10px 10px;">
<h3 style="text-align:center; margin-top:1%;">User Details</h3>

<table class="table table-bordered table-striped table-hover table-sn ">
            <thead>

                <tr class="table-primary">
                    <th>Name</th>
                    <th>Address</th>
                    <th>Contact</th>
                    <th>Email</th>
        
                    <th>Username</th>
                    <th>Action</th>
                  
                </tr>

            </thead>
            <tbody>
            <?PHP foreach($get_data as $data) {?>
                    <tr>
                        <td><?=$data["first_name"] ?> <?=$data["last_name"]?></td>
                        <td><?=$data["city"]?></td>
                        <td><?=$data["contact"]?></td>
                        <td><?=$data["email"]?></td>
                        <td><?=$data["username"]?></td>
                        
                        <td> <a class="btn btn-outline-primary " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./profileEdit.php?id=<?=$data["id"]?>">Edit</a>
                        <a class="btn btn-outline-danger " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./ResetPAss.php?ResetId=<?=$data["id"]?>">Reset Password</a>
                        </td>
            </tr>
            <?php } ?>



            </tbody>
        </table>

    </form>
    <style>
        .table {
            font-size: 15px;
        }
    </style>

</body>

</html>


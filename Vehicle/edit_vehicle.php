<?php
require_once("../dbconnection.php");
require_once("../PdoConnection.php");
require_once("../session.php");
$conn = get_dbconnection();
$conn2 = get_PdoConnection();
$vehicleType_Info = $conn2->prepare("select * from vehicles");
$vehicleType_Info->execute();
$result = $vehicleType_Info->fetchAll(PDO::FETCH_ASSOC);
$id = $_GET["edit_id"] ?? "";
$stmt = $conn2->prepare("select * from vehicle_details where (v_id= :vehicleId)");
$stmt->bindParam("vehicleId", $id);
$stmt->execute();
$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
$current_user = "";
$repeated = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $v_number = $_POST['vehicle_num'];
    $stmt = $conn2->prepare("SELECT count(*) FROM vehicle_details where v_number = :vehicle_number and v_id!= :vehicle_id");
    $stmt->bindParam("vehicle_number", $v_number);
    $stmt->bindParam("vehicle_id",$id);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if ($count > 1  ) {
        $repeated = "Vehicle number already registered";
    } else {
        $v_type = $_POST['vehicle_type'];
        $v_name = $_POST['vehicle_name'];
        $v_model = $_POST['model'];
        $v_number = $_POST['vehicle_num'];
        $initial_km = $_POST['initial_km'];
        $v_chesis = $_POST['engine_chesis'];
        $reg_date = $_POST['reg_date'];
        $current_user = $_SESSION['id'];
    
        $stmt = $conn2->prepare("UPDATE vehicle_details SET type_id=:vehicle_id,v_name=:vehicle_name,v_model=:vehicle_model
        ,v_number=:vehicle_num,initial_km=:km ,v_chesis=:chesis,reg_date=:register,user_id= :user where v_id = :id");
        $stmt->bindParam("vehicle_id", $v_type);
        $stmt->bindParam("vehicle_name", $v_name);
        $stmt->bindParam("vehicle_model",$v_model);
        $stmt->bindParam("vehicle_num",$v_number);
        $stmt->bindParam("km",$initial_km);
        $stmt->bindParam("chesis",$v_chesis);
        $stmt->bindParam("register",$reg_date);
        $stmt->bindParam("user",$current_user);
        $stmt->bindParam("id",$id);
        
        $stmt->execute();
        header("location:vehicle_report.php");
        //$stmt("isssssss", $v_type, $v_name, $v_model, $v_number, $initial_km, $v_chesis, $reg_date, $current_user);
        //$stmt->execute();
        //header("vehicle_report.php");
    }
}

?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="addvehicle.css">
<title>edit vehicle</title>

</head>

<body>
    <?php
    require_once("../nav.php");
    ?>
    <div class="flexform">
        <form class="vehiclestyle" method="post">
            <div style="width: 100%; margin-right:10%;">
                <h5 style="color: green; text-align:left;">Edit Vehicle</h5>
                <hr>
                <div class="flex">
                    <?php
                    foreach ($values as $value) {
                    ?>
                        <label for="">Vehicle Type
                            <select class="typeahead" name="vehicle_type">
                                <option>Select</option>
                                <?php
                                foreach ($result as $data) {
                                ?>
                                    <option value="<?= $data['id'] ?>"><?= $data['vehicle_type'] ?></option>

                                <?php
                                } ?>



                            </select>
                        </label>
                        <label for="">Vehicle Name
                            <input type="text" name="vehicle_name" value="<?= $value["v_name"] ?>"></label>

                </div>
                <div class="flex">

                    <label for="">Vehicle Model
                        <input type="text" name="model" value="<?= $value["v_model"] ?>"></label>


                    <label for="">Vehicle Number
                        <input type="text" name="vehicle_num" value="<?= $value["v_number"] ?>"></label>



                </div>


                <div class="flex">

                    <label for="">Initial distance
                        <input type="text" name="initial_km" value="<?= $value["initial_km"] ?>"></label>



                    <label for="">Engine chesis
                        <input type="text" name="engine_chesis" value="<?= $value["v_chesis"] ?>"></label>


                </div>
                <div class="flex">

                    <label for="">Register Date
                        <input type="date" name="reg_date" value="<?= $value["reg_date"] ?>"></label>




                 
                </div>
                <?php } ?>

            <button class="btn btn-primary btn-sm" name="save">Update</button>
            </div>
            <?php
            if ($repeated) : ?>
                <div style="text-align: center;"><?php echo $repeated ?></div>
            <?php endif ?>
        </form>

        <form style="margin-top:5%;" class="help">
            <p style="font-size: 20px; color:green;"><img src="../assets//icons8-question-mark-48.png" width="25px" height="20x">Initial Distance</p>
            <p style="font-size: 19px; margin-left:30px; margin-bottom:30px;">For new vehicle user it will be 0KM , for your old vehicle it will be latest travelled distance.</p>
            <p style="font-size: 20px; color:green;"><img src="../assets//icons8-question-mark-48.png" width="25px" height="20x">Register Date</p>
            <p style="font-size: 19px; margin-left:30px;">The date when vehicle was officially register</p>
        </form>
    </div>
</body>

</html>
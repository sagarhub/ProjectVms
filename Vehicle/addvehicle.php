<?php
require_once("../dbconnection.php");
require_once("../session.php");
require_once("../PdoConnection.php");
$current_user = "";
$repeated = "";
$conn = get_PdoConnection();
$query = $conn->prepare("SELECT* FROM vehicles");
$query->execute();
$result = $query->fetchAll(PDO::FETCH_ASSOC);
$today = date('Y-m-d', strtotime("today"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $v_number = $_POST['vehicle_num'];
    $stmt = $conn->prepare("SELECT * FROM vehicle_details where v_number = :Vnum");
    $stmt->bindParam("Vnum", $v_number);
    $stmt->execute();
    $count = $stmt->fetchColumn();

    if ($count > 1) {
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
        $attachment = $_FILES['attachment'];
        $file_name = $_FILES['attachment']['name'];
        $file_size = $_FILES['attachment']['size'];
        $file_temp = $_FILES['attachment']['tmp_name'];
        $file_type = $_FILES['attachment']['type'];
        move_uploaded_file($file_temp, "../attachments/" . $file_name);
        $stmt2 = $conn->prepare("INSERT INTO vehicle_details(type_id,v_name,v_model,v_number,initial_km,v_chesis,reg_date,user_id,attachments)
        VALUES(:TypeId,:Vname,:Vmodel,:Vnum,:Km,:Chesis,:RegDate,:U_Id,:att)");
        $stmt2->bindParam("TypeId",$v_type);
        $stmt2->bindParam("Vname",$v_name);
        $stmt2->bindParam("Vmodel",$v_model);
        $stmt2->bindParam("Vnum",$v_number);
        $stmt2->bindParam("Km",$initial_km);
        $stmt2->bindParam("Chesis",$v_chesis);
        $stmt2->bindParam("RegDate",$reg_date);
        $stmt2->bindParam("U_Id",$current_user);
        $stmt2->bindParam("att",$file_name);      
        $stmt2->execute();
        header("location:/Vehicle/vehicle_report.php");
    }
}

?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="addvehicle.css">
<title>Add vehicle</title>

</head>

<body>
    <?php
    require_once("../nav.php");
    ?>
    <div class="flexform">
        <form class="vehiclestyle" method="post" enctype="multipart/form-data">
            <div style="width: 100%; margin-right:10%;">
                <h5 style="color: green; text-align:left;">Add Vehicle</h5>
                <hr>
                <div class="flex">

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
                    <label for="">Vehicle Name(Company or brand)
                        <input type="text" name="vehicle_name"></label>

                </div>
                <div class="flex">

                    <label for="">Vehicle Model(year)
                        <input type="text" name="model"></label>


                    <label for="">Vehicle Number
                        <input type="text" name="vehicle_num"></label>



                </div>


                <div class="flex">

                    <label for="">Initial distance
                        <input type="number" name="initial_km"></label>



                    <label for="">Engine chassis
                        <input type="text" name="engine_chesis"></label>


                </div>
                <div class="flex">

                    <label for="">Register Date
                        <input  type="date" value="<?= $today ?>"  name="reg_date"></label>




                    <label for="">Bluebook:
                        <input type="file" name="attachment"></label>

                </div>

                <button class="btn btn-primary btn-sm" name="save">Save</button>
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
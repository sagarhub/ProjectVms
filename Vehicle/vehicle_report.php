<?php
require_once("../PdoConnection.php");
require_once("../session.php");
require_once("../functions/functions.php");
$conn = get_PdoConnection();
//FOR VEHICLE NAME
$stmt = $conn->query("SELECT * FROM vehicle_details");
$output = $stmt->fetchAll(PDO::FETCH_ASSOC);
$selected_vehicle = $_GET['name_search'] ?? null;
//FOR VEHICLE TYPE
$stmt2= $conn->query("SELECT* FROM vehicles");
$output2= $stmt2->fetchAll(PDO::FETCH_ASSOC);
$selected_type = $_GET['type_search'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sn = "";
    $vehicle_id = getParam("name_search");
    $vehicletype_id = getParam("type_search");
    //var_dump($vehicletype_id);
    //$vehicle_type = $_GET['type_search'] ?? null;
    $current_user = $_SESSION['id'];
    $stmt = $conn->prepare("select vd.attachments , vd.v_id ,vd.v_name ,vd.v_model ,vd.v_number ,vd.initial_km ,vd.reg_date ,vd.v_chesis ,v.vehicle_type  from vehicle_details vd 
    inner join vehicles v on v.id = vd.type_id WHERE (:vehicle_type is null or type_id = :vehicle_type) and (:vehicle_id is null or v_id = :vehicle_id) and (user_id = :u_id)");
    $stmt->bindParam("vehicle_id", $vehicle_id);
    $stmt->bindParam("u_id", $current_user);
    $stmt->bindParam("vehicle_type", $vehicletype_id);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Vehicle report</title>

</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form class="report" method="get">
    <div class="row" style="margin-top: 1%;">
        <div class="col">
            <div class="form-group">
                <label for="">Vehicle</label>
                <select class="typeahead form-control" name="type_search">
                    <option value="">Select Vehicle</option>
                    <?php foreach ($output2 as $filter_type) {
                        $selected = $filter_type["id"] == $selected_type? 'selected' : '';
                    ?>

                        <option value="<?= $filter_type["id"] ?>" <?= $selected ?>><?= $filter_type["vehicle_type"] ?></option>
                    <?php } ?>

                </select>
            </div>
        </div>
        <div class="col">
            <div class="form-group">
                <label for="">Name</label>
                <select class="typeahead form-control" name="name_search">

                    <option value="">Select Vehicle</option>
                    <?php foreach ($output as $filter_name) {
                        $selected = $filter_name["v_id"] == $selected_vehicle ? 'selected' : '';
                    ?>

                        <option value="<?= $filter_name["v_id"] ?>" <?= $selected ?>><?= $filter_name["v_name"] ?></option>
                    <?php } ?>

                </select>
            </div>
        </div>

        <div class="col flex-grow-1">
                <button style="margin-top:5.5%;" class="btn btn-primary btn-sm" name="serach">go</button>
            </div>
        </div>
      

        <h3 style="text-align: center; margin-top:1%;">Vehicle Details</h3>
        <table class="table table-bordered table-striped table-hover table-sn ">
            <thead>
                <tr class="table-primary">
                    <th>S.N</th>
                    <th>Vehicle</th>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Number</th>
                    <th>Engine chassis</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result as $row) {
                    $filename = $row["attachments"];
                    $filePath = 'attachments';
                    $Name_Path = $filePath."/" . $filename;
                  


                    $sn++;
                ?>
                    <tr>
                        <td><?php echo $sn ?></td>
                        <td><?php echo $row['vehicle_type'] ?></td>
                        <td><?php echo $row['v_name'] ?></td>
                        <td><?php echo $row['v_model'] ?></td>
                        <td><?php echo $row['v_number'] ?></td>
                        <td><?php echo $row['v_chesis'] ?></td>
                        <td><?php echo $row['reg_date'] ?></td>
                       
                        <td> <a class="btn btn-outline-primary " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./edit_vehicle.php?edit_id=<?=$row["v_id"] ?>">Edit</a>
                        <a class="btn btn-outline-danger " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./delete_vehicle.php?edit_id=<?=$row["v_id"] ?>">Delete</a>
                        <a class="btn btn-outline-primary " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./view_attachment.php?attachment_id=<?=$row["v_id"] ?>">Attachment</a>

                        </td>

                    </tr>
                <?php
                }
                ?>

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
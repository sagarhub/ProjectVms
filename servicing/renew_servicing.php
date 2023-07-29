<?php
require_once("../session.php");
$current_user = "";
$row = "";
$status = "";
$today = date('Y-m-d', strtotime("today"));

require_once("../dbconnection.php");
require_once("../PdoConnection.php");
$conn2 = get_PdoConnection();

$ID = $_GET["id"] ?? "";
$select = $conn2->prepare("select s.id,vd.v_id,vd.v_name ,s.active_status from servicing s inner join vehicle_details vd on vd.v_id = s.vehicle_id where s.id =:Edit_id ");
$select->bindParam("Edit_id", $ID);
$select->execute();

$get_data = $select->fetchAll(PDO::FETCH_ASSOC);
foreach($get_data as $CheckStauts)
{
$Already_renewed = "Renewed";
$Db_status = $CheckStauts['active_status'];
}
if($Db_status==$Already_renewed)
{
header("location:/servicing/servicing_report.php");
}
else
{
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $servicing_date = $_POST['date'];
    $km = $_POST['km'];
    $s_charge = $_POST['charge'];
    $add_cost = $_POST['add_cost'];
    $remarks = $_POST['remarks'];
    $next_date = $_POST['next_date'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i < count($km); $i++) {
        if (!$km[$i]) {
            continue;
        }
        $s_date = $servicing_date[$i];
        $kilo_m = $km[$i];
        $ser_charge = $s_charge[$i];
        $ad_cost = $add_cost[$i];
        $remark = $remarks[$i];
        $n_date = $next_date[$i];
        $rec_status = "A";
        $status = "Active";
        $statusRenew = "Renewed";
        $v_id = $vehicle_id[$i] ?? null;
        $current_user = $_SESSION['id'];
        $stmt = $conn2->prepare("INSERT INTO servicing(servicing_date,km,servicing_charge,additional_exp,remarks,next_s_date,vehicle_id,user_id,rec_status,active_status)
        values(:ServiceDate,:KiloMeter,:ServiceCharge,:AdditionalExp,:Remark,:NextDate,:VId ,:U_Id,:RecStatus,:ActiveStatus)");
        $stmt->bindParam("ServiceDate", $s_date);
        $stmt->bindParam("KiloMeter", $kilo_m);
        $stmt->bindParam("ServiceCharge", $ser_charge);
        $stmt->bindParam("AdditionalExp", $ad_cost);
        $stmt->bindParam("Remark", $remark);
        $stmt->bindParam("NextDate", $n_date);
        $stmt->bindParam("VId", $v_id);
        $stmt->bindParam("U_Id", $current_user);
        $stmt->bindParam("RecStatus", $rec_status);
        $stmt->bindParam("ActiveStatus", $status);
        $stmt->execute();
        $get_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt2 = $conn2->prepare("update servicing set active_status =:ServiceStatus where id = :Renew_id");
        $stmt2->bindParam("ServiceStatus", $statusRenew);
        $stmt2->bindParam("Renew_id", $ID);
        $stmt2->execute();
        header("location:/servicing/servicing_report.php");
    }
}
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="servicing_report.css">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.0/jquery.min.js"></script> -->
<title>New Servicing</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Renew Servicing</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>KM</th>
                        <th>Servicing Charge</th>
                        <th>Additional Expenses</th>
                        <th>Remarks</th>
                        <th>Next servicing Date</th>
                    </tr>
                </thead>
                <tbody id="dynamicRows">

                    <?php foreach ($get_data as $data) { ?>
                        <tr>
                            <input type="hidden" name="vehicle_id[]" value="<?= $data["v_id"] ?>"> 
                            <td><input type="text" value="<?= $data["v_name"] ?>" class="form-control" disabled></td>
                            <td><input type="date" value="<?= $today ?>" class="form-control" name="date[]"></td>
                            <td><input type="number" class="form-control" name="km[]"></td>
                            <td><input type="number" class="form-control" name="charge[]"> </td>
                            <td><input type="number" class="form-control" name="add_cost[]"> </td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>
                            <td><input type="date" class="form-control" name="next_date[]"> </td>
                        </tr>
                    <?php } ?>

                </tbody>
            </table>
            <button name="save" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save</button>
            <?php if ($status) : ?>
                <div class="alert alert-success">
                    <strong><?= $status ?></strong>
                </div>
            <?php endif ?>

    </form>

    <style>
        .table {
            font-size: 12px;
        }
    </style>

</body>

</html>
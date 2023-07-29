<?php
require_once("../session.php");
$current_user = "";
$row = "";
$status = "";
$today = date('Y-m-d', strtotime("today"));
require_once("../dbconnection.php");
require_once("../PdoConnection.php");
$conn2 = get_PdoConnection();
$conn = get_dbconnection();
$ID = $_GET["id"];
$select = $conn2->prepare("select i.id,vd.v_id ,vd.v_name,i.InsuranceStatus  from  insurance i inner join vehicle_details vd  on vd.v_id = i.vehicle_id where id=:Edit_id");
$select->bindParam("Edit_id", $ID);
$select->execute();
$get_data = $select->fetchAll(PDO::FETCH_ASSOC);
foreach($get_data as $CheckStatus)
{
    $Already_renewed = "Renewed";
    $Db_status = $CheckStatus['InsuranceStatus'];
    }
    if($Db_status==$Already_renewed)
    {
    header("location:/Insurance/insurance_report.php");
    }
    else{
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $insurance_date = $_POST['date'];
    $type = $_POST['type'];
    $company = $_POST['company'];
    $amount = $_POST['amount'];
    $remarks = $_POST['remarks'];
    $exp_date = $_POST['exp_date'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i < count($company); $i++) {
        if (!$company[$i]) {
            continue;
        }
        $i_date = $insurance_date[$i];
        $i_type = $type[$i];
        $i_company = $company[$i];
        $i_amount = $amount[$i];
        $remark = $remarks[$i];
        $e_date = $exp_date[$i];
        $rec_status = "A";
        $status = "Active";
        $statusRenew = "Renewed";
        $v_id = $vehicle_id[$i] ?? null;
        $current_user = $_SESSION['id'];
        $stmt = $conn2->prepare("insert into insurance(insurance_company,insurance_type ,insurance_date ,expire_date ,insurance_amount ,remarks,vehicle_id ,user_id,rec_status,InsuranceStatus )
        values(:company,:InsuranceType,:InsuranceDate,:ExpDate,:InsuranceAmt,:Remarks,:VId,:UId,:RecStatus,:InsuranceStatus)");
        $stmt->bindParam("company", $i_company);
        $stmt->bindParam("InsuranceType", $i_type);
        $stmt->bindParam("InsuranceDate", $i_date);
        $stmt->bindParam("ExpDate", $e_date);
        $stmt->bindParam("InsuranceAmt", $i_amount);
        $stmt->bindParam("Remarks", $remark);
        $stmt->bindParam("VId", $v_id);
        $stmt->bindParam("UId", $current_user);
        $stmt->bindParam("RecStatus",$rec_status);
        $stmt->bindParam("InsuranceStatus", $status);
        $stmt->execute();
        $stmt2 = $conn2->prepare("update insurance set InsuranceStatus =:Insurance_status where id = :Renew_id");
        $stmt2->bindParam("Insurance_status",$statusRenew);
        $stmt2->bindParam("Renew_id",$ID);
        $stmt2->execute();
        header("location:./insurance_report.php");
        $status = "Servicing saved!";
    }
}
    }
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="/servicing/servicing_report.css">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.0/jquery.min.js"></script> -->
<title>Renew Insurance</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Renew Insurance</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Company</th>
                        <th>Amount</th>
                        <th>Remarks</th>
                        <th>Expiring Date</th>
                    </tr>
                </thead>
                <tbody id="dynamicRows">
                    <?php
                    foreach ($get_data as $data) {
                    ?>


                        <tr>

                            <input type="hidden" name="vehicle_id[]" value="<?= $data["v_id"] ?>">
                            <td><input type="text" class="form-control" value="<?= $data["v_name"] ?>" disabled></td>
                            <td><input type="date" value="<?= $today ?>" class="form-control" name="date[]"></td>
                            <td><input type="text" class="form-control" name="type[]"></td>
                            <td><input type="text" class="form-control" name="company[]"> </td>
                            <td><input type="number" class="form-control" name="amount[]"> </td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>
                            <td><input type="date" class="form-control" name="exp_date[]"> </td>
                        </tr>

                    <?php } ?>
                </tbody>
            </table>
            <button name="save" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Renew</button>
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
<?php
require_once("../session.php");
require_once("../dbconnection.php");
require_once("../PdoConnection.php");
$row = "";
$status = "";

$today = date('Y-m-d', strtotime("today"));

$conn2 = get_PdoConnection();
$query = $conn2->prepare("SELECT * FROM vehicle_details");
$query->execute();
$output = $query->fetchAll(PDO::FETCH_ASSOC);
$selectedVehicleId = $_GET['vehicle_id'] ?? "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vehicle_id = $_POST['vehicle_id'];
    $date = $_POST['date'];
    $amt = $_POST['amt'];
    $next_date = $_POST['next_date'];
    $remarks = $_POST['remarks'];
    for ($i = 0; $i < count($amt); $i++) {
        if (!$amt[$i]) {
            continue;
        }
        $v_id = $vehicle_id[$i];
        $status = "Active";
        $statusRenew = "Renewed";
        $rec_status = "A";
        $payment_date = $date[$i];
        $amount = $amt[$i];
        $next_pmt_date = $next_date[$i];
        $remark = $remarks[$i];
        $current_user = $_SESSION['id'];
        $stmt = $conn2->prepare("INSERT INTO tax(tax_payment_date,tax_amount,next_payment_date, remarks,vehicle_id,user_id,TaxStatus,rec_status)
        values(:NewDate,:Amt,:NextDate,:Remarks,:VehicleId,:UserId,:TaxStatus,:RecStatus)");
        $stmt->bindParam("NewDate",$payment_date);
        $stmt->bindParam("Amt",$amount);
        $stmt->bindParam("NextDate",$next_pmt_date);
        $stmt->bindParam("Remarks",$remark);
        $stmt->bindParam("VehicleId",$v_id);
        $stmt->bindParam("UserId",$current_user);
        $stmt->bindParam("TaxStatus",$status);
        $stmt->bindParam("RecStatus",$rec_status);
        $stmt->execute();
        $stmt2 = $conn2->prepare("update tax set TaxStatus = :StatusRenew where id=:Renew_id");
        $stmt2->bindParam("StatusRenew",$statusRenew);
        $stmt2->bindParam("Renew_id",$ID);
        header("location:/tax/tax_report.php");
    }
}


?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Tax Payment</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>


    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Tax Payment</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Next Payment Date</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="dynamicRows">
                    <?php while ($row < 5) {
                        $row++;
                    ?>

                        <tr>
                            <td>
                                <select class="typeahead" name="vehicle_id[]">
                                    <option value="">Select Vehicle</option>
                                    <?php foreach ($output as $data) {
                                        $selected = $data["v_id"] == $selectedVehicleId ? 'selected' : '';
                                    ?>
                                        <option value="<?= $data["v_id"] ?>" <?= $selected ?>><?= $data["v_name"] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </td>
                            <td><input type="date" value="<?= $today ?>" class="form-control" name="date[]"></td>
                            <td><input type="number" class="form-control" name="amt[]"></td>
                            <td><input type="date" class="form-control" name="next_date[]"> </td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>
                        </tr>
                    <?php
                    } ?>
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
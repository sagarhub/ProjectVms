<?php
require_once("../session.php");
$current_user = "";
$row = "";
$status = "";
require_once("../dbconnection.php");
$today = date('Y-m-d', strtotime("today"));

$selectedVehicleId = null;
require_once("../PdoConnection.php");
$conn2 = get_PdoConnection();
$selectedVehicleId = null;
$filter_query = $conn2->prepare("select * from vehicle_details");
$filter_query->execute();
$output = $filter_query->fetchAll(PDO::FETCH_ASSOC);
$selectedVehicleId = $_GET['vehicle_id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $filling_date = $_POST['date'];
    $qty = $_POST['qty'];
    $amount = $_POST['amount'];
    $remarks = $_POST['remarks'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i < count($qty); $i++) {
        if (!$qty[$i]) {
            continue;
        }
        $f_date = $filling_date[$i];
        $quantity = $qty[$i];
        $f_amount = $amount[$i];
        $remark = $remarks[$i];
        $v_id = $vehicle_id[$i] ?? null;
        $current_user = $_SESSION['id'];
        $stmt = $conn2->prepare("INSERT INTO fuel_filling(filling_date,quantity,amount,remarks,vehicle_id,user_id)
        values(:FillDate,:Qty,:Amt,:Remark,:VId,:U_id)");
        $stmt->bindParam("FillDate",$f_date);
        $stmt->bindParam("Qty",$quantity);
        $stmt->bindParam("Amt",$f_amount);
        $stmt->bindParam("Remark",$remark);
        $stmt->bindParam("VId",$v_id);
        $stmt->bindParam("U_id",$current_user);
        $stmt->execute();
        header("location:/filling/fuel_fillingReport.php");
    }
}
?>
<?php require_once("../html/layout.php"); ?>

<link rel="stylesheet" href="../servicing//servicing_report.css">
<title>Fuel filling</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Fuel Filling</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Quantity(ltr)</th>
                        <th>Amount</th>
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
                            <td><input type="number" class="form-control" name="qty[]"></td>
                            <td><input type="number" class="form-control" name="amount[]"> </td>
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
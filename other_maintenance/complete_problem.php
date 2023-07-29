<?php
require_once("../PdoConnection.php");
$conn = get_PdoConnection();
$rec = 'A';
$id = $_GET['id'] ?? "";
$select = $conn->prepare("select om.id , om.vehicle_id , vd.v_name ,om.rec_date ,om.problem , om.remarks  from other_maintenance om inner join vehicle_details vd on vd.v_id = om.vehicle_id 
where (om.id = :Complete_id)");
$select->bindParam("Complete_id", $id);
$select->execute();
$get_data = $select->fetchAll(PDO::FETCH_ASSOC);
require_once("../session.php");
$current_user = "";
$row = "";
$status = "";
$today = date('Y-m-d', strtotime("today"));
header("location");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $problem = $_POST['problem'];
    $remarks = $_POST['remarks'];
    $Amount = $_POST['amount'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i < count($problem); $i++) {
        if (!$problem[$i]) {
            continue;
        }
        $prob = $problem[$i];
        $remark = $remarks[$i];
        $Amt = $Amount[$i];
        $v_id = $vehicle_id[$i] ?? null;
        $current_user = $_SESSION['id'];
        $rec_status = "P";
        $stmt = $conn->prepare("Update other_maintenance set problem = :prob , amount=:amt ,remarks=:Remark,rec_status=:rec WHERE(id=:om_id)");
        $stmt->bindParam("prob", $prob);
        $stmt->bindParam("amt", $Amt);
        $stmt->bindParam("Remark", $remark);
        $stmt->bindParam("om_id", $id);
        $stmt->bindParam("rec", $rec);
        $stmt->execute();
       
header("location:/other_maintenance/problem_report.php");

    }
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="/servicing/servicing_report.css">
<title>Complete Problem</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Complete Problem</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Problem</th>

                        <th>Amount</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="dynamicRows">

                    <?php
                    foreach($get_data as $data) { ?>
                        <tr>
                            <input type="hidden" value="<?= $data["vehicle_id"]?>" name="vehicle_id[]" >
                            <td><input type="text" value="<?= $data["v_name"] ?>" class="form-control" disabled></td>
                            <td><input type="date" value="<?= $data["rec_date"] ?>" class="form-control" name="date[]" disabled></td>
                            <td><input type="text" value="<?= $data["problem"] ?>" class="form-control" name="problem[]"></td>
                            <td><input type="number" class="form-control" name="amount[]"> </td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>

                        </tr>
                    <?php } ?>

                </tbody>
            </table>
            <button name="save" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Complete</button>
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
<?php
require_once("../session.php");
require_once("../dbconnection.php");
require_once("../PdoConnection.php");
$row = "";
$status = "";

$conn2 = get_PdoConnection();

$ID = $_GET["id"];
$Select = $conn2->prepare("select t.id ,t.tax_payment_date ,t.TaxStatus,t.next_payment_date ,vd.v_name,vd.v_id  from tax t inner join vehicle_details vd on vd.v_id = t.vehicle_id where t.id = :Edit_id");
$Select->bindParam("Edit_id",$ID);
$Select->execute();
$get_data = $Select->fetchAll(PDO::FETCH_ASSOC);
foreach($get_data as $CheckStatus)
{
    $Already_renewed = "Renewed";
    $Db_status = $CheckStatus['TaxStatus'];
    }
    if($Db_status==$Already_renewed)
    {
    header("location:/Tax/tax_report.php");
    }
    else{
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
        values(:NewDate,:Amt,:NextDate,:Remarks,:VehicleId,:UserId,:TaxStatus,:rec_status)");
        $stmt->bindParam("NewDate",$payment_date);
        $stmt->bindParam("Amt",$amount);
        $stmt->bindParam("NextDate",$next_pmt_date);
        $stmt->bindParam("Remarks",$remark);
        $stmt->bindParam("VehicleId",$v_id);
        $stmt->bindParam("UserId",$current_user);
        $stmt->bindParam("TaxStatus",$status);
        $stmt->bindParam("rec_status",$rec_status);
        $stmt->execute();
        $stmt2 = $conn2->prepare("update tax set TaxStatus = :StatusRenew where id=:Renew_id");
        $stmt2->bindParam("StatusRenew",$statusRenew);
        $stmt2->bindParam("Renew_id",$ID);
        $stmt2->execute();
        header("location:./tax_report.php");

    }
}
    }

?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Renew Tax</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>


    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Renew Tax</h3>
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
                   

                        <tr>
                            
                                
                                   
                                    <?php foreach ($get_data as $data) {
                                       
                                    ?>
                                    
                            <input value=<?=$data["v_id"]?>  type="hidden" class="form-control" name="vehicle_id[]">
                            <td><input  type="text" class="form-control" value="<?=$data["v_name"]?>"  disabled></td>

                            <td><input   type="date" class="form-control" name="date[]"></td>
                            <td><input  type="number" class="form-control" name="amt[]"></td>
                            <td><input  type="date" class="form-control" name="next_date[]"> </td>
                            <td><input  type="text" class="form-control" name="remarks[]"> </td>
                        </tr>
                        <?php
                                    }
                                    ?>
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
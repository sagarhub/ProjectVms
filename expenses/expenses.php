<?php
$sn = "";
$result = 0;
require_once("../PdoConnection.php");
require_once("../functions/functions.php");
require_once("../session.php");
$conn = get_PdoConnection();
$conn = get_PdoConnection();
$filter = $conn->query("select * from vehicle_details");
$output = $filter->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //servicing expenses
    $month_start = date('Y-m-d', strtotime('first day of this month'));
    $month_end = date('Y-m-d', strtotime('last day of this month'));
    $search_name = getParam("name_search") ?? null;
    $from_date = getParam("from_date", $month_start);
    $to_date = getParam("to_date", $month_end);
    $current_user = $_SESSION['id'];
    $servicing_exp = $conn->prepare("select count(*)count , sum(servicing_charge+additional_exp)sum from servicing s  where (:vehicle_id is null or vehicle_id = :vehicle_id)
and (user_id = :user_id) and (servicing_date between :from_date and :to_date)");
    $servicing_exp->bindParam("vehicle_id", $search_name);
    $servicing_exp->bindParam("user_id", $current_user);
    $servicing_exp->bindParam("from_date", $from_date);
    $servicing_exp->bindParam("to_date", $to_date);
    $servicing_exp->execute();
    $TotalService_exp = $servicing_exp->fetchAll(PDO::FETCH_ASSOC);
    //Tax expenses
    $tax_exp = $conn->prepare("select count(*)count , sum(tax_amount)sum from  tax WHERE (user_id = :user_id)
    and (:vehicle_id is null or vehicle_id = :vehicle_id) and (tax_payment_date between :from_date and :to_date) ");
    $tax_exp->bindParam("user_id", $current_user);
    $tax_exp->bindParam("vehicle_id", $search_name);
    $tax_exp->bindParam("from_date", $from_date);
    $tax_exp->bindParam("to_date", $to_date);
    $tax_exp->execute();
    $TotalTax_exp = $tax_exp->fetchAll(PDO::FETCH_ASSOC);
    //insurance expenses
    $insurance_exp = $conn->prepare("select count(*)count , sum(insurance_amount)sum from insurance  where (:vehicle_id is null or vehicle_id = :vehicle_id)
    and (user_id = :user_id) and (insurance_date between :from_date and :to_date)");
    $insurance_exp->bindParam("user_id", $current_user);
    $insurance_exp->bindParam("vehicle_id", $search_name);
    $insurance_exp->bindParam("from_date", $from_date);
    $insurance_exp->bindParam("to_date", $to_date);
    $insurance_exp->execute();
    $TotalInsurance_exp = $insurance_exp->fetchAll(PDO::FETCH_ASSOC);
    //fuel expenses
    $fuel_exp = $conn->prepare("select sum(quantity)count , sum(amount)sum from fuel_filling WHERE
     (:vehicle_id is null or vehicle_id = :vehicle_id) and (user_id = :user_id) and (filling_date between :from_date and :to_date) ");
    $fuel_exp->bindParam("user_id", $current_user);
    $fuel_exp->bindParam("vehicle_id", $search_name);
    $fuel_exp->bindParam("from_date", $from_date);
    $fuel_exp->bindParam("to_date", $to_date);
    $fuel_exp->execute();
    $TotalFuel_exp = $fuel_exp->fetchAll(PDO::FETCH_ASSOC);
    //other maintenance expenses
     $Maintenance_Exp = $conn->prepare("select sum(amount)sum from other_maintenance om  WHERE
     (:vehicle_id is null or vehicle_id = :vehicle_id) and (user_id = :user_id) and (rec_date between :from_date and :to_date) ");
    $Maintenance_Exp->bindParam("user_id", $current_user);
    $Maintenance_Exp->bindParam("vehicle_id", $search_name);
    $Maintenance_Exp->bindParam("from_date", $from_date);
    $Maintenance_Exp->bindParam("to_date", $to_date);
    $Maintenance_Exp->execute();
    $TotalMaintenance_exp = $Maintenance_Exp->fetchAll(PDO::FETCH_ASSOC);
    //travelled Distance
    $Travelled_dist = $conn->prepare("select sum(distance) from reading  WHERE
    (vehicle_id = :vehicle_id) and (user_id = :user_id) and (rec_date between :from_date and :to_date) ");
    $Travelled_dist->bindParam("user_id", $current_user);
    $Travelled_dist->bindParam("vehicle_id", $search_name);
    $Travelled_dist->bindParam("from_date", $from_date);
    $Travelled_dist->bindParam("to_date", $to_date);
    $Travelled_dist->execute();
    $dist = $Travelled_dist->fetchColumn();
    //incomplete problems
    $Pending_status = "P";
    $Complete_status = "A";
     $Incomplete_problems = $conn->prepare("select count(*)count from other_maintenance om where rec_status = :ActiveStatus and
    (:vehicle_id is null or vehicle_id = :vehicle_id) and (user_id = :user_id) and (rec_date between :from_date and :to_date) ");
    $Incomplete_problems->bindParam("user_id", $current_user);
    $Incomplete_problems->bindParam("vehicle_id", $search_name);
    $Incomplete_problems->bindParam("from_date", $from_date);
    $Incomplete_problems->bindParam("to_date", $to_date);
    $Incomplete_problems->bindParam("ActiveStatus",$Pending_status);
    $Incomplete_problems->execute();
    $Incomplete_Prob = $Incomplete_problems->fetchColumn();
    //Complete Problems
     $Complete_problems = $conn->prepare("select count(*)count from other_maintenance om where rec_status = :ActiveStatus and
    (:vehicle_id is null or vehicle_id = :vehicle_id) and (user_id = :user_id) and (rec_date between :from_date and :to_date) ");
    $Complete_problems->bindParam("user_id", $current_user);
    $Complete_problems->bindParam("vehicle_id", $search_name);
    $Complete_problems->bindParam("from_date", $from_date);
    $Complete_problems->bindParam("to_date", $to_date);
    $Complete_problems->bindParam("ActiveStatus",$Complete_status);
    $Complete_problems->execute();
    $Complete_Prob = $Complete_problems->fetchColumn();
   foreach($TotalService_exp as $Exp1)
   {
    $result +=  $Exp1["sum"];
    
   }
   foreach($TotalTax_exp as $Exp2)
   {
    $result += $Exp2["sum"];
   }
   foreach($TotalInsurance_exp as $Exp3)
   {
    $result += $Exp3["sum"];
   }
   foreach($TotalFuel_exp as $Exp4)
   {
    $result += $Exp4["sum"];
   }
   foreach($TotalMaintenance_exp as $Exp5)
   {
    $result+= $Exp5["sum"];
   }
   
    $selected_vehicle = $_GET["name_search"] ?? "";
}
?>
<?php require_once("../html/layout.php"); ?>

<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Expenses details</title>
</head>

<body>
    <?php require_once("../nav.php");

    ?>
    <form class="report" method="get">
        <div class="row" style="margin-top: 1%;">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Vehicle</label>
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
            <div class="col">
                <div class="form-group">
                    <label for="">From Date </label>
                    <input class="form-control" value="<?= $from_date ?>" type="date" name="from_date">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="">To Date</label>
                    <input class="form-control" value="<?= $to_date ?>" type="date" name="to_date">
                </div>
            </div>
            <div class="col flex-grow-1">
                <button style="margin-top:7.5%;" class="btn btn-primary btn-sm" name="serach">go</button>
            </div>
        </div>
        </div>
        <hr>
        <div style="width:100%; margin-top:3%;" class="row">
            <div class="col">
                <h5 style="text-align:center; margin-top:1%;">Expenses Details</h5>
                <table class="table table-bordered table-striped table-hover table-sn">
                    <thead>

                        <tr class="table-primary">
                            <th>Particulars</th>
                            <th>Expenses</th>
                        </tr>
                        <tr>
                            <th><i class="fa fa-wrench"></i> Servicing</th>
                            <?php foreach ($TotalService_exp as $ServicingExp) { ?>
                                <th  >Rs. <?= $ServicingExp["sum"] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><i class="fa fa-money"></i> Tax</th>
                            <?php foreach ($TotalTax_exp as $TaxExp) { ?>
                                <th>Rs. <?= $TaxExp["sum"] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-car-burst"></i> Insurance</th>
                            <?php foreach ($TotalInsurance_exp as $InsuranceExp) { ?>
                                <th>Rs. <?= $InsuranceExp["sum"] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-gas-pump"></i> Fuel</th>
                            <?php foreach ($TotalFuel_exp as $FuelExp) { ?>
                                <th>Rs. <?= $FuelExp["sum"] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-screwdriver-wrench"></i> Other Maintenance Cost</th>
                            <?php foreach($TotalMaintenance_exp as $MaintenanceExp) { ?>
                            <th>Rs. <?=$MaintenanceExp["sum"] ?></th>
                                <?php } ?>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <th>Rs. <?=$result?></th>

                        </tr>
                     
                    </thead>
                </table>
            </div>
            <div class="col">
                <h5 style="text-align:center; margin-top:1%;">Maintenance Details</h5>
                <table class="table table-bordered table-striped table-hover table-sn">
                    <thead>

                        <tr class="table-primary">
                            <th>Particulars</th>
                            <th>Details</th>
                        </tr>
                        <tr>
                            <th><i class="fa fa-wrench"></i> Servicing count</th>
                            <?php foreach ($TotalService_exp as $ServicingCount) { ?>
                                <th> <?= $ServicingCount["count"] ?></th>
                            <?php } ?>
                        </tr>
                        <tr>
                            <th><i class="fa fa-money"></i> Tax Count</th>
                            <?php foreach($TotalTax_exp as $TaxCount){?>
                            <th> <?= $TaxCount["count"] ?></th>
                            <?php }?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-car-burst"></i> Insurance</th>
                            <?php foreach($TotalInsurance_exp as $InsuranceCount){?>
                            <th> <?= $InsuranceCount["count"] ?></th>
                            <?php }?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-gas-pump"></i> Fuel</th>
                            <?php foreach($TotalFuel_exp as $FuelCount){?>
                            <th> <?= $FuelCount["count"] ?>Ltr.</th>
                            <?php }?>
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-road-barrier"></i> Distance Travelled</th>
                            
                            <th> <?=$dist?>Km.</th>
                         
                        </tr>
                        <tr>
                            <th><i class="fa-regular fa-circle-xmark"></i> Incomplete Problems</th>
                         
                            <th> <?=$Incomplete_Prob ?> </th>
                          
                        </tr>
                        <tr>
                            <th><i class="fa-solid fa-check"></i> Completed Problems</th>
                            <th><?=$Complete_Prob?></th>
                        </tr>
                    </thead>
                </table>

            </div>
        </div>
</body>

</html>
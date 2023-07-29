<?php
$sn = "";
$status = "";
$selected_vehicle = "";
require_once("../PdoConnection.php");
require_once("../session.php");
require_once("../functions/functions.php");
$current_user = $_SESSION['id'];
$conn = get_PdoConnection();
$filter_query = $conn->query("SELECT * FROM vehicle_details");
$output = $filter_query->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $month_start = date('Y-m-d', strtotime('first day of this month'));
    $month_end = date('Y-m-d', strtotime('last day of this month'));
    $from_date = getParam("from_date", $month_start);
    $to_date = getParam("to_date", $month_end);
    $Active_status = getParam("status_filter") ?? null;

    $vehicle_id = getParam('name_search');
    $rec_status = "A";
    $stmt = $conn->prepare("select t.TaxStatus, vd.v_name ,t.id ,t.tax_payment_date ,t.tax_amount ,t.next_payment_date ,t.remarks from tax t 
    inner join vehicle_details vd on vd.v_id = t.vehicle_id WHERE (t.user_id = :user_id) and (rec_status =:RecStatus)
    and (:vehicle_id is null or t.vehicle_id = :vehicle_id) and (tax_payment_date between :from_date and :to_date)and (:ActiveStatus is null or TaxStatus =:ActiveStatus) ");
    $stmt->bindParam("user_id", $current_user);
    $stmt->bindParam("vehicle_id", $vehicle_id);
    $stmt->bindParam("from_date", $from_date);
    $stmt->bindParam("to_date", $to_date);
    $stmt->bindParam("RecStatus", $rec_status);
    $stmt->bindParam("ActiveStatus",$Active_status);
    
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $StatusFilter = $conn->prepare("select distinct(TaxStatus) from tax t ");
    $StatusFilter->execute();
    $statusResult = $StatusFilter->fetchAll(PDO::FETCH_ASSOC);
    $selected_vehicle = $_GET['name_search'] ?? "";
    $selected_status = $_GET["status_filter"] ?? "";
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Tax Report</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>
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
            <div class="col-2">
                <div class="form-group">
                    <label for="">Status</label>
                    <select class="typeahead form-control" name="status_filter">
                        <option value="">Status</option>
                        <?php foreach ($statusResult as $StatusData) {
                            $selected = $StatusData["TaxStatus"] == $selected_status ? 'selected' : '';
                        ?>

                            <option value="<?= $StatusData["TaxStatus"] ?>" <?= $selected ?>><?= $StatusData["TaxStatus"] ?></option>
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
                <button style="margin-top:10%;" class="btn btn-primary btn-sm" name="serach">go</button>
            </div>
        </div>
        </div>

        <h3 style="text-align:center; margin-top:1%;">Tax Details</h3>
        <table class="table table-bordered table-striped table-hover table-sn ">
            <thead>

                <tr class="table-primary">
                    <th>S.N</th>
                    <th>Vehicle</th>
                    <th>Payment date</th>
                    <th>Amount</th>
                    <th>Next payment Date</th>
                    <th>Status</th>
                    <th>Remarks</th>
                    <th>Action</th>
                </tr>

            </thead>
            <tbody>
                <?php foreach ($result as $data) {
                    $TaxStatus = $data["TaxStatus"];
                    $sn++;
                    $today =  date('Y-m-d', strtotime('today'));
                    if ($data["next_payment_date"] > $today && $TaxStatus != ("Renewed")) {
                        $status = "Active";
                    } else if ($TaxStatus == "Renewed") {
                        $status = "Renewed";
                    } else {
                        $status = "Expired";
                    }


                ?>


                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $data["v_name"] ?></td>
                        <td><?= $data["tax_payment_date"] ?></td>
                        <td><?= $data["tax_amount"] ?></td>
                        <td><?= $data["next_payment_date"] ?></td>
                        <td><?= $status ?></td>
                        <td><?= $data["remarks"] ?></td>
                        <td> <a class="btn btn-outline-primary " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./Renew_tax.php?id=<?= $data["id"] ?>">Renew</a></td>




                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </form>

</body>

</html>
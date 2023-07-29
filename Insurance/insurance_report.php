<?php
$sn = "";
require_once("../PdoConnection.php");
require_once("../functions/functions.php");
require_once("../session.php");
$conn = get_PdoConnection();
$filter = $conn->query("select * from vehicle_details");
$output = $filter->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    //$search_name = $_GET["name_search"] ?? null;
    // if (!$search_name) {
    //     $stmt = "select vd.v_name , s.servicing_date ,s.km ,s.servicing_charge ,s.additional_exp ,s.remarks ,s.next_s_date  from vehicle_details vd  
    //  inner join servicing s on  s.id =vd.v_id";
    //     $result = mysqli_query($conn, $stmt);
    // } else {

    $month_start = date('Y-m-d', strtotime('first day of this month'));
    $month_end = date('Y-m-d', strtotime('last day of this month'));
    $search_name = getParam("name_search") ?? null;
    $Active_status = getParam("status_filter") ?? null;
    $from_date = getParam("from_date", $month_start);
    $to_date = getParam("to_date", $month_end);
    $current_user = $_SESSION['id'];
    $stmt = $conn->prepare(" select i.InsuranceStatus, i.id, vd.v_name , i.insurance_company ,i.insurance_type ,i.insurance_date ,i.expire_date ,i.insurance_amount,i.remarks  from insurance i inner join vehicle_details vd on 
    i.vehicle_id = vd.v_id   where (:vehicle_id is null or v_id = :vehicle_id)
     and (i.user_id = :user_id) and (insurance_date between :from_date and :to_date) and (:ActiveStatus is null or InsuranceStatus =:ActiveStatus) ");
    $stmt->bindParam("vehicle_id", $search_name);
    $stmt->bindParam("user_id", $current_user);
    $stmt->bindParam("from_date", $from_date);
    $stmt->bindParam("to_date", $to_date);
    $stmt->bindParam("ActiveStatus",$Active_status);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $StatusFilter = $conn->prepare("select distinct(InsuranceStatus) from insurance i ");
    $StatusFilter->execute();
    $statusResult = $StatusFilter->fetchAll(PDO::FETCH_ASSOC);
    $selected_vehicle = $_GET["name_search"] ?? "";
    $selected_status =$_GET["status_filter"] ?? "";
}
//}
?>
<?php require_once("../html/layout.php"); ?>

<link rel="stylesheet" href="/servicing/servicing_report.css">
<title>Insurance details</title>
</head>

<body>

    <?php require_once("../nav.php"); ?>
    <form class="report" method="get">
        <div class="row" style="margin-top: 1%;">
            <div class="col-2">
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
                    <label for="">Status</label>
                    <select class="typeahead form-control" name="status_filter">
                        <option value="">Status</option>
                        <?php foreach ($statusResult as $StatusData) {
                            $selected = $StatusData["InsuranceStatus"] == $selected_status ? 'selected' : '';
                        ?>

                            <option value="<?= $StatusData["InsuranceStatus"] ?>" <?= $selected ?>><?= $StatusData["InsuranceStatus"] ?></option>
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
            <div class="col ">
                <div class="form-group">
                <button style="margin-top: 10%;"  class="btn btn-primary btn-sm" name="serach">go</button>
                </div>
            </div>
        </div>
        
        
        <h3 style="text-align:center; margin-top:1%;">Insurance Details</h3>
        <table class="table table-bordered table-striped table-hover table-sn ">
            <thead>

                <tr class="table-primary">
                    <th>S.N</th>
                    <th>Vehicle</th>
                    <th>Insurance date</th>
                    <th>Type</th>
                    <th>Company</th>
                    <th>Amount</th>
                    <th>Remarks</th>
                    <th>Exp.Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

            </thead>
            <tbody>
            <?php foreach ($result as $data) {

                    $InsuranceStatus = $data["InsuranceStatus"] ?? "";
                    $sn++;
                    $today =  date('Y-m-d', strtotime('today'));
                    if ($data["expire_date"] > $today && $InsuranceStatus!=("Renewed")) {
                        $status = "Active";
                    } else if($InsuranceStatus=="Renewed") {
                        
                        $status = "Renewed";
                        
                    }
                    else
                    {
                        $status = "Expired";
                    }
                    ?>
                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $data["v_name"] ?></td>
                        <td><?= $data["insurance_date"] ?></td>
                        <td><?= $data["insurance_type"] ?></td>
                        <td><?= $data["insurance_company"] ?></td>
                        <td><?= $data["insurance_amount"] ?></td>
                        <td><?= $data["remarks"] ?></td>
                        <td ><?= $data["expire_date"] ?></td>
                        <td><?=$status?></td>
                        <td> <a  class="btn btn-outline-primary btn-sm" style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./Renew_insurance.php?id=<?= $data["id"] ?>">Renew</a></td>

                    </tr>
                <?php } ?>
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
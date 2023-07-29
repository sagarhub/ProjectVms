<?php
$sn = "";
$status = "";
$Filter_S="";
header("location.php");
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
    $from_date = getParam("from_date", $month_start);
    $to_date = getParam("to_date", $month_end);
    $Active_status = getParam("status_filter") ?? null;

    $current_user = $_SESSION['id'];
    $stmt = $conn->prepare(" select om.id, vd.v_name ,om.problem,om.rec_date,om.remarks,om.reminder ,om.rec_status from other_maintenance om inner join vehicle_details vd on om.vehicle_id = vd.v_id   where (:vehicle_id is null or v_id = :vehicle_id)
     and (om.user_id = :user_id) and (rec_date between :from_date and :to_date) and (:ActiveStatus is null or rec_status =:ActiveStatus)");
    $stmt->bindParam("vehicle_id", $search_name);
    $stmt->bindParam("user_id", $current_user);
    $stmt->bindParam("from_date", $from_date);
    $stmt->bindParam("to_date", $to_date);
    $stmt->bindParam("ActiveStatus",$Active_status);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $StatusFilter = $conn->prepare("select distinct(rec_status) from other_maintenance");
    $StatusFilter->execute();
    $statusResult = $StatusFilter->fetchAll(PDO::FETCH_ASSOC);

    $selected_vehicle = $_GET["name_search"] ?? "";
    $selected_status =$_GET["status_filter"] ?? "";
}

//}
?>
<?php require_once("../html/layout.php"); ?>

<link rel="stylesheet" href="/servicing/servicing_report.css">
<title>Problem Report</title>
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
            <div class="col">
                <div class="form-group">
                    <label for="">Status</label>
                    <select class="typeahead form-control" name="status_filter">
                        <option value="">Status</option>
                        <?php foreach ($statusResult as $StatusData) {
                            $Get_status = $StatusData["rec_status"];
                            if($Get_status=="A")
                            {
                                $Filter_S = "Completed";
                            }
                            elseif($Get_status == "P")
                            {
                                $Filter_S = "Pending";
                            }
                       
                            $selected = $StatusData["rec_status"] == $selected_status ? 'selected' : '';

                        ?>

                            <option value="<?= $StatusData["rec_status"] ?>" <?= $selected ?>> <?=$Filter_S ?></option>
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

        <h3 style="text-align:center; margin-top:1%;">Problem Details</h3>
        <table class="table table-bordered table-striped table-hover table-sn ">
            <thead>

                <tr class="table-primary">
                    <th>S.N</th>
                    <th>Vehicle</th>
                    <th>Record date</th>
                    <th>Problem</th>
                    <th>Remarks</th>
                    <th>Reminder</th>
                    <th>Status</th>
                 

                    <th>Actions</th>

                </tr>

            </thead>
            <tbody>
                <?php foreach ($result as $data) {
                    $rec_status = $data["rec_status"];
                    if ($rec_status == 'P') {
                        $status = "Pending";
                    } else if ($rec_status == 'A') {
                        $status = "Completed";

                    } else if($rec_status == 'E') {
                        $status = "Pending";
                    }
                    $sn++;
                ?>

                    <tr>
                        <td><?= $sn ?></td>
                        <td><?= $data["v_name"] ?></td>
                        <td><?= $data["rec_date"] ?></td>
                        <td><?= $data["problem"] ?></td>
                        <td><?= $data["remarks"] ?></td>
                        <td><?= $data["reminder"] ?></td>
                        <td><?= $status ?></td>
                      

                        <td> <a  class="btn btn-outline-primary " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;" href="./complete_problem.php?id=<?= $data["id"] ?>">Complete</a>

                         <a class="btn btn-outline-danger " style="--bs-btn-padding-y: .10rem; --bs-btn-padding-x: .4rem; --bs-btn-font-size: .75rem;"href="./delete_problem.php?id=<?= $data["id"] ?>">Delete</a>
                        </td>


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
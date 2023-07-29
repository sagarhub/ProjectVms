<?php
require_once("../PdoConnection.php");
require_once("../session.php");
require_once("../functions/functions.php");
$conn = get_PdoConnection();
//FOR VEHICLE NAME
$month_start = date('Y-m-d', strtotime('first day of this month'));
$month_end = date('Y-m-d', strtotime('last day of this month'));
$from_date = getParam("from_date", $month_start);
$to_date = getParam("to_date", $month_end);
$query = "SELECT * FROM vehicle_details";
$filter = $conn->query($query);
$output= $filter->fetchAll(PDO::FETCH_ASSOC);
$selected_vehicle = $_GET['name_search'] ?? null;
//FOR VEHICLE TYPE

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sn = "";
    $vehicle_id = getParam("name_search");
    //var_dump($vehicletype_id);
    //$vehicle_type = $_GET['type_search'] ?? null;
    $current_user = $_SESSION['id'];
    $stmt = $conn->prepare("select vd.v_name ,r.rec_date , r.start_km ,r.end_km ,r.distance  from reading r 
    inner join vehicle_details vd on r.vehicle_id = vd.v_id  WHERE (:vehicle_id is null or v_id = :vehicle_id) and (r.user_id = :u_id)");
    $stmt->bindParam("vehicle_id", $vehicle_id);
    $stmt->bindParam("u_id", $current_user);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing/servicing_report.css">
<title>Vehicle report</title>

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
        
        <h3 style="text-align: center; margin-top:1%;">Meter Reading Details</h3>
        <table class="table table-bordered table-striped table-hover table-sn ">
            <thead>
                <tr class="table-primary">
                    <th>S.N</th>
                    <th>Vehicle</th>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Travelled distance</th>
        
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($result as $row) {
                    $sn++;
                ?>
                    <tr>
                        <td><?php echo $sn ?></td>
                        <td><?php echo $row['v_name'] ?></td>
                        <td><?php echo $row['rec_date'] ?></td>
                        <td><?php echo $row['start_km'] ?></td>
                        <td><?php echo $row['end_km'] ?></td>
                        <td><?php echo $row['distance'] ?></td>
                       

                    </tr>
                <?php
                }
                ?>

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
<?php
require_once("../session.php");
$current_user = "";
$row = "";
$status = "";
$today = date('Y-m-d', strtotime("today"));
header("location");
require_once("../dbconnection.php");
$conn = get_dbconnection();
$selectedVehicleId = null;
$filter_query = "select * from vehicle_details";
$result = mysqli_query($conn, $filter_query);
$output = mysqli_fetch_all($result, MYSQLI_ASSOC);
$selectedVehicleId = $_GET['vehicle_id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn2 = get_dbconnection();
    $record_date = $_POST['date'];
    $problem = $_POST['problem'];
    $remarks = $_POST['remarks'];
    $reminder_date = $_POST['reminder'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i < count($problem); $i++) {
        if (!$problem[$i]) {
            continue;
        }
        $rec_date = $record_date[$i];
        $prob = $problem[$i];
        $remark = $remarks[$i];
        $reminder = $reminder_date[$i];
        $v_id = $vehicle_id[$i] ?? null;
        $current_user = $_SESSION['id'];
        $rec_status = "P";
        $stmt = $conn2->prepare("INSERT INTO other_maintenance (rec_date,problem,vehicle_id,user_id,remarks,reminder,rec_status) 
        values(?,?,?,?,?,?,?)");
        $stmt->bind_param("ssiisss", $rec_date, $prob,  $v_id,$current_user, $remark, $reminder,$rec_status);
        $stmt->execute();
        header("location:/other_maintenance/problem_report.php");
    }
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="/servicing/servicing_report.css">
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.0/jquery.min.js"></script> -->
<title>Record Problem</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>

    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Record Problem</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Problem</th>
                        <th>Remarks</th>
                        <th>Reminder</th>
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
                            <td><input type="text" class="form-control" name="problem[]"></td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>
                            <td><input type="date" class="form-control" name="reminder[]"> </td>
                            
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
<?php
$row = "";
require_once("../PdoConnection.php");
require_once("../session.php");
$conn = get_PdoConnection();
$today = date('Y-m-d', strtotime("today"));

$query = "SELECT * FROM vehicle_details";
$result = $conn->query($query);
$output = $result->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $start = $_POST['opening'];
    $end = $_POST['closing'];
    $distance = $_POST['balance'];
    $remarks = $_POST['remarks'];
    $vehicle_id = $_POST['vehicle_id'];
    for ($i = 0; $i <= $start[$i]; $i++) {
        if (!$start[$i]) {
            continue;
        }
        $dates = $date[$i];
        $starting = $start[$i];
        $ending = $end[$i];
        $travelled_distance = $distance[$i];
        $remark = $remarks[$i];
        $v_id = $vehicle_id[$i];
        $currrent_user = $_SESSION['id'];
        $stmt = $conn->prepare("INSERT INTO reading(rec_date,start_km,end_km,distance,remarks,vehicle_id,user_id)
    values(:dates,:start_km,:end_km,:travel_dist,:remarks,:v_id,:u_id)");
        $stmt->bindParam("dates", $dates);
        $stmt->bindParam("start_km", $starting);
        $stmt->bindParam("end_km", $ending);
        $stmt->bindParam("travel_dist", $travelled_distance);
        $stmt->bindParam("remarks", $remark);
        $stmt->bindParam("v_id", $v_id);
        $stmt->bindParam("u_id", $currrent_user);
        $stmt->execute();
        header("location:/Meter_reading/meterReading_report.php");
    }
}
?>
<?php require_once("../html/layout.php"); ?>
<link rel="stylesheet" href="../servicing//servicing_report.css">

<title>Meter Reading</title>
</head>

<body>
    <?php require_once("../nav.php"); ?>
    <form method="post" id="submit">
        <div style="margin:1% 2%;">
            <h3 style="text-align:center;">Meter Reading</h3>
            <table class="table" id="copy_tbl">
                <thead>
                    <tr class="table-primary" style="text-align:center;">
                        <th style="width:20%;">Vehicle</th>
                        <th>Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Distance Travelled</th>
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
                            <td><input type="number" min="0" step="any" class="form-control opening" name="opening[]"></td>
                            <td><input type="number" min="0" step="any" class="form-control closing" name="closing[]"> </td>
                            <td><input type="text" class="form-control balance" name="balance[]" readonly> </td>
                            <td><input type="text" class="form-control" name="remarks[]"> </td>
                        </tr>
                    <?php
                    } ?>
                </tbody>
            </table>
            <button name="save" class="btn btn-primary btn-sm"><i class="fa fa-save"></i> Save</button>
          
    </form>
    <script>
                document.addEventListener("DOMContentLoaded", () => {
                    const openingElements = document.querySelectorAll(".opening");
                    const closingElements = document.querySelectorAll(".closing");
                    function updateDistance(e) {
                        const targetRow = e.target.closest("tr");
                        const openingElm = targetRow.querySelector(".opening");
                        const closingElm = targetRow.querySelector(".closing");
                        const balanceElm = targetRow.querySelector(".balance");
                        balanceElm.value = +closingElm.value - +openingElm.value;

                    }
                    openingElements.forEach(x => x.addEventListener("input", updateDistance));
                    closingElements.forEach(x => x.addEventListener("input", updateDistance));

                

                });
            </script>
    <style>
        .table {
            font-size: 12px;
        }
    </style>

</body>
</html>
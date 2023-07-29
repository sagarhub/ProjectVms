<?php
$status = "";
require_once("../PdoConnection.php");
$conn = get_PdoConnection();
$id = $_GET['id'] ?? "";
$check = $conn->prepare("select * from other_maintenance where (id=:om_id)");
$check->bindParam("om_id", $id);
$check->execute();
$result = $check->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $data) {
    $rec = $data["rec_status"];
}
if ($rec == "A") {
    
    $status = "already completed";
} else {
    $stmt = $conn->prepare("delete from other_maintenance 
where (id=:om_id)");
    $stmt->bindParam("om_id", $id);
    $stmt->execute();
}


?>
<?php
require_once("../html/layout.php"); ?>
</head>

<body>
    <?php if ($status) : ?>
        <div class="alert alert-primary" role="alert"><?= $status ?></div>
    <?php endif ?>
    <?php     header("location:/other_maintenance/problem_report.php");
?>
</body>
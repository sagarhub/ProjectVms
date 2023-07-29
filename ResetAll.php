<?php 
require_once("./PdoConnection.php");
$conn = get_PdoConnection();
$stmt1 = $conn->prepare("truncate vehicle_details");
$stmt1->execute();
$stmt2 = $conn->prepare("truncate servicing");
$stmt2->execute();
$stmt3 = $conn->prepare("truncate tax");
$stmt3->execute();
$stmt4 = $conn->prepare("truncate reading");
$stmt4->execute();
$stmt5 = $conn->prepare("truncate other_maintenance");
$stmt5->execute();
$stmt6 = $conn->prepare("truncate login");
$stmt6->execute();
$stmt7 = $conn->prepare("truncate insurance");
$stmt7->execute();
$stmt8 = $conn->prepare("truncate fuel_filling");
$stmt8->execute();
echo "Succcess!!";
?>

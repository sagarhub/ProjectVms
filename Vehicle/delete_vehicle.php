<?php
$status = "";
require_once("../PdoConnection.php");
$conn = get_PdoConnection();
$id = $_GET['edit_id'] ?? "";
$check = $conn->prepare(" 
select sum(x.cnt) as count from
(
select count(*) cnt from servicing s where vehicle_id =:vehicleId
union all
select count(*) from tax t where vehicle_id =:vehicleId
union all 
select count(*) from fuel_filling ff where vehicle_id =:vehicleId
union all
select count(*) from insurance i where vehicle_id =:vehicleId
union all 
select count(*) from other_maintenance om where vehicle_id = :vehicleId
union all 
select count(*) from reading r where vehicle_id = :vehicleId
)x"); 
$check->bindParam("vehicleId", $id);
$check->execute();
$result = $check->fetchcolumn();

        if ($result > 0) {
            echo "already in use";
            header("location:vehicle_report.php");
        } else {
           $dlt = $conn->prepare("DELETE FROM vehicle_details WHERE v_id = :v_id");
           $dlt->bindParam("v_id",$id);
           $dlt->execute();
           header("location:vehicle_report.php");
    
        }
    
    


?>

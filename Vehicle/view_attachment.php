<?php 
require_once("../PdoConnection.php");
require_once("../session.php");
$conn= get_PdoConnection();
$id = $_GET["attachment_id"];
$stmt= $conn->prepare("select attachments from vehicle_details where v_id=:id");
$stmt->bindParam("id",$id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($result as $row) {
    $filename = $row["attachments"];
    $filePath = '../attachments';
    $Name_Path = $filePath."/" . $filename;

    
}
?>
<?php require_once("../html/layout.php"); ?>
</head>
<body>
    <?php require_once("../nav.php"); ?>
    <div style="margin-top:2%; margin-left:30%;">
<?php
 echo '<img  src="' . $Name_Path . '" alt="Attachment" width="550" height="600">';
 ?>
    </div>
   

</body>
<?php
require("session.php");
if(isset($_SESSION['logged_in']))
{
  header("location:dashboard.php");
}

$error = "";
$status = "";
$username_err = "";
header("location.php");
require("dbconnection.php");
require_once("./PdoConnection.php");
$conn2 = get_PdoConnection();
if (isset($_POST['login'])) {
  $conn = get_dbconnection();
  $username = $_POST['user'];
  $stmt = $conn->prepare("SELECT * FROM login WHERE username =?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->store_result();
  //to check entered username in database
  if ($stmt->num_rows() == 1) {
    $conn1 = get_dbconnection();
    $password = $_POST['pass'];
    $username = $_POST['user'];
    $stmt = $conn1->prepare("SELECT id,username , password FROM login WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $data = $stmt->get_result();
    while ($row = $data->fetch_assoc())
    //password check
    {
      $db_password = $row['password'];
      if (password_verify($password, $db_password)) {
        //session
        $_SESSION['id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['logged_in']=true;
        //set expired Tax
        $ActiveStatus = "Expired";
        $RenewStatus = "Renewed";
        $Rec = "E";
        $Complete_Status = "A";
        $SetExp_Tax = $conn2->prepare("update tax set Taxstatus = :ActiveStatus where next_payment_date <=cast(current_timestamp as date) and TaxStatus!=:RenewStatus");
        $SetExp_Tax->bindParam("ActiveStatus",$ActiveStatus);
        $SetExp_Tax->bindParam("RenewStatus",$RenewStatus);
        $SetExp_Tax->execute();
        //Set Expired Insurance
        $SetExp_insurance = $conn2->prepare("update insurance set InsuranceStatus = :ActiveStatus where expire_date <=cast(current_timestamp as date) and InsuranceStatus!=:RenewStatus");
        $SetExp_insurance->bindParam("ActiveStatus",$ActiveStatus);
        $SetExp_insurance->bindParam("RenewStatus",$RenewStatus);
        $SetExp_insurance->execute();
        
        //Set Expired Serrvicing
        $SetExp_servicing = $conn2->prepare("update servicing set active_status = :ActiveStatus where next_s_date <=cast(current_timestamp as date) and active_status!=:RenewStatus");
        $SetExp_servicing->bindParam("ActiveStatus",$ActiveStatus);
        $SetExp_servicing->bindParam("RenewStatus",$RenewStatus);
        $SetExp_servicing->execute();

        //redirect
        header("location:dashboard.php");
      } else {
        $error = "invalid username or password ";
      }
    }
  } else {
    //error
    $username_err = "invalid username or pasword";
  }
}
?>
<?php
require("session.php");
$name = "";
if(isset($_SESSION['username']))
{
  $name = $_SESSION['username'];
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#2e73a5;">
  <div class="container-fluid">
    <a style="font-size: 20px;;" class="navbar-brand" href="#">VMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav me-auto">
    
        <li class="nav-item">
          <a style="margin-left: 15px;" class="nav-link active" href="/dashboard.php"><img src="../assets/icons8-home-page-24.png" width="20px" height="19px" style="margin-left: 10px;">
            <span class="visually-hidden">(current)</span>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Vehicle</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/vehicle/addvehicle.php">Add Vehicle</a>
            <a class="dropdown-item" href="/vehicle/vehicle_report.php">Vehicle Details</a>
          </div>
        </li>
        
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Servicing</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/servicing/newservicing.php">New Servicing</a>
            <a class="dropdown-item" href="/servicing/servicing_report.php">Servicing Report</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Tax</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/Tax/Tax_payment.php">Tax Payment</a>
            <a class="dropdown-item" href="/Tax/tax_report.php">Tax Report</a>
          </div>
          <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Meter Reading</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/Meter_reading/meter_reading.php">New</a>
            <a class="dropdown-item" href="/Meter_reading/meterReading_report.php">Report</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Fuel Filling</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/filling/fuel_filling.php">New</a>
            <a class="dropdown-item" href="/filling/fuel_fillingReport.php">Report</a>
          </div>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Insurance</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/insurance/New_insurance.php">New</a>
            <a class="dropdown-item" href="/Insurance/insurance_report.php">Report</a>
          </div>
        </li>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Other Maintenance</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/other_maintenance/record_problem.php">New</a>
            <a class="dropdown-item" href="/other_maintenance/problem_report.php">Report</a>
          </div>
        </li>
        </li>
     
       
        <!-- <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Dropdown</a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <a class="dropdown-item" href="#">Something else here</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Separated link</a>
          </div>
        </li> -->
       
      </ul>
      <form class="d-flex">
      <ul class="navbar-nav me-auto">
        <li>
          <!-- <img style="margin-top: 4px;" src="assets/icons8-customer-32.png" width="30px" height="27px"> -->
        </li>
      <li class="nav-item dropdown">
          <a  class="nav-link dropdown-toggle active " data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user-circle" style="margin-top:3px;"></i> <?php echo $name ?></a>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="/User_profile/profile.php">Profile</a>
            <a class="dropdown-item" href="../logout.php">Log Out</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="#">About
            <span class="visually-hidden">(current)</span>
          </a>
      </ul>
        
        <!-- <input class="form-control me-sm-2" type="search" placeholder="Search"> -->
      </form>
    </div>
  </div>
</nav>
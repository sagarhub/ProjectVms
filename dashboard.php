<?php
require("session.php");
if (!isset($_SESSION['logged_in'])) {
  header("location:login.php");
}
?>
<?php
require_once("./PdoConnection.php");
$conn = get_PdoConnection();
$currentUser = $_SESSION['id'];
$rec_status = "A";
$ActiveStatus = "Renewed";
//total expenses 
$TotalEXp = $conn->prepare("select sum(x.amt) from
(
select sum(servicing_charge+additional_exp) amt from servicing s  where user_id =:current_id
union all
select sum(tax_amount)  from tax t where user_id =:current_id
union all
select sum(amount)  from fuel_filling ff where user_id =:current_id
union all
select sum(insurance_amount) from insurance i where user_id =:current_id
union all
select sum(amount) from other_maintenance om where user_id =:current_id

)x");
$TotalEXp->bindParam("current_id", $currentUser);
$TotalEXp->execute();
$result = $TotalEXp->fetchColumn();
//vehicle count
$VehicleCount = $conn->prepare(" select count(*) from vehicle_details vd where user_id =:current_id");
$VehicleCount->bindParam("current_id", $currentUser);
$VehicleCount->execute();
$count = $VehicleCount->fetchColumn();
//tax expiry
$TaxExpiry = $conn->prepare("select count(*) from tax where next_payment_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and user_id=:current_id and rec_status=:RecStatus and TaxStatus!=:Active");
$TaxExpiry->bindParam("current_id", $currentUser);
$TaxExpiry->bindParam("RecStatus", $rec_status);
$TaxExpiry->bindParam("Active", $ActiveStatus);
$TaxExpiry->execute();
$TaxExpiry_count = $TaxExpiry->fetchColumn();
//insurance expiry
$InsuranceExpiry = $conn->prepare("select count(*)  from insurance i  where expire_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and user_id = :current_id and rec_status=:RecStatus and InsuranceStatus!=:Active");
$InsuranceExpiry->bindParam("current_id", $currentUser);
$InsuranceExpiry->bindParam("RecStatus", $rec_status);
$InsuranceExpiry->bindParam("Active", $ActiveStatus);
$InsuranceExpiry->execute();
$InsuranceExpiry_count = $InsuranceExpiry->fetchColumn();
//expired servicing
$ServicingExpiry = $conn->prepare("select count(*)  from servicing s  where next_s_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and user_id =:current_id and rec_status=:RecStatus and active_status!=:Active");
$ServicingExpiry->bindParam("current_id", $currentUser);
$ServicingExpiry->bindParam("RecStatus", $rec_status);
$ServicingExpiry->bindParam("Active", $ActiveStatus);
$ServicingExpiry->execute();
$ServicingExpiry_count = $ServicingExpiry->fetchColumn();

//expired Maintenance
$Maintenance_Expiry = $conn->prepare("select count(*)  from other_maintenance om  where reminder  <= CAST(CURRENT_TIMESTAMP AS DATE) and user_id =:current_id and rec_status!=:Active ");
$Maintenance_Expiry->bindParam("current_id", $currentUser);
$Maintenance_Expiry->bindParam("Active", $rec_status);

$Maintenance_Expiry->execute();
$MaintenanceExpiry_count = $Maintenance_Expiry->fetchColumn();

?>
<?php require_once("./html/layout.php");

?>
<link rel="stylesheet" href="./assets/fontpop.css">
<title>Dashboard</title>
</head>

<body>
  <?php require_once("nav.php");

  ?>
  <div style="width:90%; margin:20px 15px;">
    <div class="row">
      <div class="col-sm-2">
        <a href="./expenses/expenses.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">



            <h3 class="card-title"><i class="fa-solid fa-money-bill-1-wave" style="margin-right:10px; "></i>Rs.<?= $result ?> </h3>
            <h7 class="card-text">Total Expenses</h7>



          </div>
        </a>
      </div>

      <div class="col-sm-2" >
        <a href="./vehicle/vehicle_report.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">


            <h3 class="card-title"><i class="fa fa-car " style="margin-right:10px;"></i><?= $count ?></h3>
            <h7 class="card-text">Vehicle Count</h7>





          </div>

        </a>
      </div>


      <div class="col-sm-2">
        <a href="./tax/tax_report.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">



            <h3 class="card-title"><i class="fa fa-money" style="margin-right:10px; "></i><?= $TaxExpiry_count ?> </h3>
            <h7 class="card-text">Expired Tax</h7>

          </div>
        </a>
      </div>
      <div class="col-sm-2">
        <a href="./Insurance/insurance_report.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">
            <h3 class="card-title"><i class="fa-solid fa-car-burst"></i> <?= $InsuranceExpiry_count ?> </h3>
            <h7 class="card-text">Expired Insurance </h7>
          </div>
        </a>
      </div>
      <div class="col-sm-2">
        <a href="./servicing/servicing_report.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">
            <h3 class="card-title"><i class="fa-solid fa-wrench"></i> <?= $ServicingExpiry_count ?> </h3>
            <h7 class="card-text">Exceeded Servicing </h7>
          </div>
        </a>
      </div>
      <div class="col-sm-2">
        <a href="./other_maintenance/problem_report.php" class="text-decoration-none card" style="box-shadow: 0px 0px 6px grey;">
          <div class="card-body">
            <h3 class="card-title"><i class="fa-solid fa-screwdriver-wrench"></i> <?= $MaintenanceExpiry_count ?> </h3>
            <h7 class="card-text"> Exceeded Maintenance </h7>
          </div>
        </a>
      </div>
    </div>
  </div>


  <?php
  //for total expenses chart 
  $TotalMonth_Exp = $conn->prepare("
select year,month,sum(x.total_amount)Amt from
(
SELECT YEAR(filling_date) AS year, MONTHNAME(filling_date) AS month, SUM(amount) AS total_amount FROM fuel_filling ff  where user_id =:current_id
GROUP BY YEAR(filling_date), MONTH(filling_date)
union all
SELECT YEAR(servicing_date) AS year, MONTHNAME(servicing_date) AS month, SUM(servicing_charge+additional_exp) AS total_amount FROM servicing s where user_id =:current_id
GROUP BY YEAR(servicing_date), MONTH(servicing_date)
union all 
SELECT YEAR(tax_payment_date) AS year, MONTHNAME(tax_payment_date) AS month, SUM(tax_amount) AS total_amount FROM tax t where user_id =:current_id
GROUP BY YEAR(tax_payment_date), MONTH(tax_payment_date)
union all
SELECT YEAR(insurance_date) AS year, MONTHNAME(insurance_date) AS month, SUM(insurance_amount) AS total_amount FROM insurance i where user_id =:current_id
GROUP BY YEAR(insurance_date), MONTH(insurance_date)
union all
SELECT YEAR(rec_date) AS year, MONTHNAME(rec_date) AS month, SUM(amount) AS total_amount FROM other_maintenance om where user_id =:current_id
GROUP BY YEAR(rec_date), MONTH(rec_date)
)x GROUP BY YEAR, case month  WHEN 'January' THEN 1
        WHEN 'February' THEN 2
        WHEN 'March' THEN 3
        WHEN 'April' THEN 4
        WHEN 'May' THEN 5
        WHEN 'June' THEN 6
        WHEN 'July' THEN 7
        WHEN 'August' THEN 8
        WHEN 'September' THEN 9
        WHEN 'October' THEN 10
        WHEN 'November' THEN 11
        WHEN 'December' THEN 12
    END;");
  $TotalMonth_Exp->bindParam("current_id", $currentUser);
  $TotalMonth_Exp->execute();
  $AllExpenses = $TotalMonth_Exp->fetchAll();
  //servicing chart
  $MonthlyServicing = $conn->prepare("SELECT YEAR(servicing_date) AS year, MONTHNAME(servicing_date) AS month, SUM(servicing_charge+additional_exp) AS total_amount
FROM servicing s where user_id =:current_id GROUP BY YEAR(servicing_date), MONTH(servicing_date); ");
  $MonthlyServicing->bindParam("current_id", $currentUser);
  $MonthlyServicing->execute();
  $MonthlyServicing_exp = $MonthlyServicing->fetchAll();

  //fuel chart
  $MonthlyFuel = $conn->prepare("SELECT YEAR(filling_date) AS year, MONTHNAME(filling_date) AS month, SUM(amount) AS total_amount FROM fuel_filling ff  where user_id =:current_id
GROUP BY YEAR(filling_date), MONTH(filling_date);
");
$MonthlyFuel->bindParam("current_id",$currentUser);
  $MonthlyFuel->execute();
  $MonthlyFuel_exp = $MonthlyFuel->fetchAll();
  //expired transaction chart 
  $ExpiredCount = $conn->prepare("select sum(x.cnt)cnt from
(
select count(*)cnt from tax where next_payment_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and TaxStatus != 'Renewed' and  user_id =:current_id
union all
select count(*) from servicing s  where next_s_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and active_status!='Renewed' and  user_id =:current_id
union all
select count(*) from insurance i  where expire_date  <= CAST(CURRENT_TIMESTAMP AS DATE) and InsuranceStatus != 'Renewed'  and   user_id =:current_id
)x
");
$ExpiredCount->bindParam("current_id",$currentUser);
  $ExpiredCount->execute();
  $ExpiredTxn = $ExpiredCount->fetchAll();
  //active transaction chart
  $ActiveCount = $conn->prepare("select sum(x.cnt)cnt from
(
select count(*)cnt from tax where  TaxStatus != 'Expired'
union all
select count(*) from servicing s  where  active_status !='Expired'
 
union all
select count(*) from insurance i  where  InsuranceStatus != 'Expired' 
)x");
  $ActiveCount->execute();
  $ActiveTxn = $ActiveCount->fetchAll();
  //unsolved problem
  $MaintenanceStatus_A= 'A';
  $MaintenanceStatus_P = 'P';
  $Unsolved_prob = $conn->prepare("select count(*)cnt from other_maintenance om where rec_status = :rec and  user_id =:current_id");
  $Unsolved_prob->bindParam("rec",$MaintenanceStatus_P);
  $Unsolved_prob->bindParam("current_id",$currentUser);
  $Unsolved_prob->execute();
  $unsolved_count  = $Unsolved_prob->fetchAll();

  //solved problem
  $solved_prob = $conn->prepare("select count(*)cnt from other_maintenance om where rec_status = :rec and  user_id =:current_id");
  $solved_prob->bindParam("rec",$MaintenanceStatus_A);
  $solved_prob->bindParam("current_id",$currentUser);
  $solved_prob->execute();
  $solved_count  = $solved_prob->fetchAll();
  //for solved and unsolved
  foreach($unsolved_count as $pending)
  {
    $Pending_prob  = $pending['cnt'];
  }
  foreach($solved_count as $completed)
  {
    $Completed_prob = $completed['cnt'];
  }
  foreach ($ExpiredTxn as $ExpTxn) {
    $TotalExpired[] = $ExpTxn['cnt'];
  }
  foreach ($ActiveTxn as $Active_Txn) {
    $TotalActive[] = $Active_Txn['cnt'];
  }
  foreach ($MonthlyServicing_exp as $Service_data) {
    $month[] = $Service_data['month'];
    $amount[] = $Service_data['total_amount'];
  }

  foreach ($MonthlyFuel_exp as $Fuel_data) {
    $FuelMonth[] = $Fuel_data['month'];
    $Fuel_amount[] = $Fuel_data['total_amount'];
  }
  foreach ($AllExpenses as $All) {
    $TotalExp_month[] = $All['month'];
    $TotalExp_amt[] = $All['Amt'];
  }
  ?>
  <div style="display:flex; gap:10px;">
    <div class="card" style="width:50%; margin-left:1%;">
      <h6 style="text-align: center; font-weight:bold; margin-top:2%;" class="card-title">Total Expenses</h6>
      <canvas id="chart3"></canvas>
    </div>
    <div class="card" style="width:50%;margin-left:1%;">
      <h6 style="text-align: center; font-weight:bold; margin-top:2%;" class="card-title">Servicing Expenses</h6>
      <canvas id="chart1"></canvas>
    </div>
  </div>
  <div style="display:flex; gap:10px; margin-top:2%; margin-bottom:2%;">
    <div class="card" style="width:50%; height:20%; margin-left:1%;">
      <h6 style="text-align: center; font-weight:bold; margin-top:2%;" class="card-title">Fuel Expenses</h6>
      <canvas id="chart2"></canvas>
    </div>
    <div class="card" style="width:50%;  margin-left:1%;">
      <h6 style="text-align: center; font-weight:bold; margin-top:2%;" class="card-title">Expired Trasnaction</h6>
      <canvas  id="chart4"></canvas>
    </div>
  </div>


  <script>
    function createChart1() {
      var labels = <?php echo json_encode($month) ?>;
      var data = {
        labels: labels,
        datasets: [{
          label: 'Monthly Exp',
          data: <?php echo json_encode($amount) ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 1
        }]
      };
      const config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        },
      };

      const ctx1 = document.getElementById('chart1');

      var chart1 = new Chart(ctx1, config);
    }
    createChart1();
    function createChart2() {
      var labels = <?php echo json_encode($FuelMonth) ?>;
      var data = {
        labels: labels,
        datasets: [{
          label: 'Monthly Exp',
          data: <?php echo json_encode($Fuel_amount) ?>,
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)',
            'rgba(255, 159, 64, 0.2)',
            'rgba(255, 205, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(201, 203, 207, 0.2)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 1
        }]
      };
      const config = {
        type: 'bar',
        data: data,
        options: {
          scales: {
            y: {
              beginAtZero: true
            }
          }
        },
      };

      const ctx2 = document.getElementById('chart2');

      var chart2 = new Chart(ctx2, config);
    }
    createChart2();
    function createChart3() {
      var labels = <?php echo json_encode($TotalExp_month) ?>;
      var data = {
        labels: labels,
        datasets: [{
          label: 'Total Monthly Expenses',
          data: <?php echo json_encode($TotalExp_amt) ?>,
          fill: false,
          borderColor: 'rgb(75, 192, 192)',
          tension: 0.1
        }]
      };
      const config = {
        type: 'line',
        data: data,
      };
      const ctx3 = document.getElementById('chart3');
      var chart3 = new Chart(ctx3, config);
    }
   
    createChart3();
    function createChart4() {
      var result1 = <?php echo json_encode($TotalExpired) ?>;
      var result2 = <?php echo json_encode($TotalActive) ?>;
      var result3 = <?php echo json_encode($Pending_prob) ?>;
      var result4 = <?php echo json_encode($Completed_prob)?>;
      var data = {
        labels: [
          'Expired Transactions',
          'Active Transactions',
          'Unsolved Problem',
          'Solved Problem',

        ],
        datasets: [{
          label: 'Transaction Count',
          data: [result1, result2,result3,result4],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgb(54, 162, 235)',
            'rgb(255,165,0)',
            'rgb(50,205,50)',

          ],
          hoverOffset: 4
        }]

      };
      const config = {
        type: 'doughnut',
        data: data,
        options: {
        aspectRatio: 2,
        
    }

      };
      const ctx4 = document.getElementById('chart4');
      var chart4 = new Chart(ctx4, config);


    }


    createChart4();
  </script>



</body>
</head>

</html>
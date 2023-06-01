<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (strlen($_SESSION['uid']) == 0) {
  header('location:logout.php');
} else {
?>
  <!DOCTYPE html>
  <html class="loading" lang="en" data-textdirection="ltr">

  <head>
    <title>RVU-GADA : Student || Registration</title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Quicksand:300,400,500,700" rel="stylesheet">
    <link href="https://maxcdn.icons8.com/fonts/line-awesome/1.1/css/line-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="app-assets/css/vendors.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/app.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/menu/menu-types/vertical-menu-modern.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/charts/jquery-jvectormap-2.0.3.css">
    <link rel="stylesheet" type="text/css" href="app-assets/vendors/css/charts/morris.css">
    <link rel="stylesheet" type="text/css" href="app-assets/fonts/simple-line-icons/style.css">
    <link rel="stylesheet" type="text/css" href="app-assets/css/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  </head>

  <body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    <!-- fixed-top-->
    <?php include_once('includes/header.php'); ?>
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <?php include_once('includes/leftbar.php'); ?>
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
          <?php
          $uid = $_SESSION['uid'];
          //taking only the date out of the stamp
          $sql = mysqli_query($con, "SELECT DATE(CourseApplieddate) AS date_part FROM tbladmapplications WHERE UserID='$uid'");
          $result = mysqli_fetch_array($sql);
          $application_date = $row['date_part'];

          $ret = mysqli_query($con, "SELECT * FROM tbladmapplications WHERE UserID='$uid'");
          $row = mysqli_fetch_array($ret);
          $fname = $row['FirstName'];

          $admission_status = $row['AdminStatus'];
          $application_ID = $row['ID'];
          $full_name = $row['FirstName'] . " " . $row['MiddleName'];
          $course_name = $row['CourseApplied'];
          $application_date = $row['CourseApplieddate'];
          $decision_date = $row['AdminRemarkDate'];

          ?>
          <h4>
            Dear <?php echo $full_name ?>, <br><br>

            We have received your decision on our admission offer and We want to take a moment to acknowledge and appreciate your decision.
            We genuinely value your interest in our institution and the time and effort you invested in your application. Your application
            was thoroughly reviewed, and we are grateful for your consideration of our university for your academic pursuits. <br><br>
            Please be informed that we have noted your decision to decline our admission offer. Consequently, your application will be withdrawn
            from further consideration in our admission process. <br><br>
            Once again, We wish you the utmost success in your future endeavors and in finding a university that aligns perfectly with your
            aspirations and goals. <br><br>
            <strong>
              Best regards,<br><br>
              Rift Valley University Admissions Office
            </strong>
          </h4>
          <div align="center">
            <button onclick="window.location.href = 'dashboard.php';" type="submit" id="submit_button" name="submit" class="btn btn-success mx-2 mt-5" style="width: 300px;">Back to home</button>
          </div>
        </div>
      </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <!-- BEGIN VENDOR JS-->
    <!-- General Javascript functions definitions -->
    <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->
    <script src="app-assets/vendors/js/charts/chart.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/charts/raphael-min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/charts/morris.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/charts/jvector/jquery-jvectormap-2.0.3.min.js" type="text/javascript"></script>
    <script src="app-assets/vendors/js/charts/jvector/jquery-jvectormap-world-mill.js" type="text/javascript"></script>
    <script src="app-assets/data/jvector/visitor-data.js" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN MODERN JS-->
    <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app.js" type="text/javascript"></script>
    <script src="app-assets/js/scripts/customizer.js" type="text/javascript"></script>
    <!-- END MODERN JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="app-assets/js/scripts/pages/dashboard-sales.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL JS-->
  </body>

  </html>
<?php } ?>
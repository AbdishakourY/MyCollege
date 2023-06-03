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
    <style>
      #error-message {
        color: red;
        margin-top: -20px;
      }
    </style>
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
          //taking out only the date
          $sql = mysqli_query($con, "SELECT DATE(CourseApplieddate) AS date_part FROM tbladmapplications WHERE UserID='$uid'");
          $result = mysqli_fetch_array($sql);
          $application_date = $row['date_part'];

          $ret = mysqli_query($con, "SELECT * FROM tbladmapplications WHERE UserID='$uid'");
          $row = mysqli_fetch_array($ret);
          $fname = $row['FirstName'];
          ?>
          <h4>
            Dear <?php echo $row['FirstName'] ?>, <br><br>
            Please follow the mentioned steps to fully register for the program and obtain a student ID.<br><br>
            Make a deposit of 150 ETB to one of the accounts mentioned below and then fill all the fields with the accurate
            information from your payment reciept. You also need to submit a screenshot or photo of the payment receipt. <br><br>
            It might take us sometime to confirm your payment so we would really appreciate your patience. But if you don't hear
            from us in <b>two working days</b>, please don't hesitate to reach us through <a style="color:coral">rvu.admissions.sup@gmail.com</a>.<br><br>
            <em>Please mention your application reference number and also attach your payment receipt when contacting us!</em>
          </h4>
          <br><br>

          <form name="submit" method="post" enctype="multipart/form-data">
            <!--start of section-->
            <section class="formatter" id="formatter">
              <div class="row">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header pb-0">
                      <h4 class="card-title">Payment Confirmation Form</h4>
                    </div>
                    <hr>
                    <div class="card-content">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-xl-4 col-lg-6">
                            <fieldset>
                              <h5>Full Name <span class="text-muted">(as written in the receipt)</span> </h5>
                              <div class="form-group">
                                <input class="form-control white_bg" id="name" name="name" type="text" required>
                              </div>
                            </fieldset>
                          </div>

                          <div class="col-xl-4 col-lg-6">
                            <fieldset>
                              <h5>Payment Reference </h5>
                              <div class="form-group">
                                <input class="form-control white_bg" id="pay_ref" name="pay_ref" type="text" required>
                              </div>
                            </fieldset>
                          </div>

                          <div class="col-xl-4 col-lg-6">
                            <fieldset>
                              <h5>Payment Date</h5>
                              <div class="form-group">
                                <input class="form-control white_bg" id="pay_date" name="pay_date" type="date" required>
                              </div>
                            </fieldset>
                          </div>

                          <div class="col-xl-4 col-lg-6">
                            <fieldset>
                              <h5>Receipt Photo </h5>
                              <div class="form-group">
                                <input class="form-control white_bg" id="pay_pic" name="pay_pic" type="file" accept="image/*" required>
                              </div>
                              <div id="error-message"></div>
                            </fieldset>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div align="center" class="mt-2 mb-2">
                      <button type="submit" id="submit_button" name="submit" class="btn btn-success mx-2" style="width: 300px;">Confirm payment</button>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </form>
        </div>


      </div>
    </div>

    <?php include('includes/footer.php'); ?>
    <!-- BEGIN VENDOR JS-->
    <!-- General Javascript functions definitions -->
    <!-- Image input validator-->
    <script>
      const fileInput = document.querySelector('#userpic');
      const errorMessage = document.querySelector('#error-message');

      fileInput.addEventListener('change', function(event) {
        const selectedFile = event.target.files[0];
        const fileTypePic = selectedFile.type;

        if (!fileTypePic.startsWith('image/')) {
          // Display error message and highlight the file input field
          errorMessage.textContent = 'Please select an image file';
          fileInput.classList.add('error');
          fileInput.style.border = '1px solid red';

          // Prevent form submission
          event.preventDefault();
        } else {
          // Clear error message and remove highlight from file input field
          errorMessage.textContent = '';
          fileInput.classList.remove('error');
          fileInput.style.border = '';
        }
      });
    </script>
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
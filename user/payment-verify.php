<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include('includes/dbconnection.php');

//require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

use phpMailer\PHPMailer\PHPMailer;
use phpMailer\PHPMailer\SMTP;
//use phpMailer\PHPMailer\Exception;

if (isset($_GET['uid'])) {
  $uid = $_GET['uid'];
  // extract out info from tbladmapplications and tblregistered
  $ret = mysqli_query($con, "SELECT * FROM tbladmapplications WHERE UserId = $uid");
  $ret2 = mysqli_query($con, "SELECT * FROM tblregistered WHERE Reg_User_ID = $uid");
  $row2 = mysqli_fetch_array($ret2);

  // ID card generator starts here
  if (mysqli_num_rows($ret2) > 0) {
    $html = "<div class='card' style='width:350px; padding:0;'>";
    $html .= "";
    while ($row = mysqli_fetch_array($ret)) {
      $name = $row["FirstName"] . " " . $row["MiddleName"] . " " . $row["LastName"];
      $id_no = $row2["Reg_ID"];
      $department = $row2['Reg_Course'];
      $id_issue = date('Y-m-d', strtotime($row2['Reg_date']));
      $id_issue_year = date('Y', strtotime($id_issue));
      $id_expire = date('Y-m-d', strtotime('+1 year', strtotime($id_issue)));
      $modality = $row['AdmissionType'];
      $image = $row['UserPic'];

      $html .= "
      <div class='container-0' style='text-align:left;' id='Student_ID'>
        <div class='header'>
        </div>
        
        <div class='container-2'>
        <div class='box-1'>
          <img src=\"userimages/$image\">
        </div>
          
          <div class='box-2'>
            <h2><b>$name</b></h2>
            <p style='font-size: 14px;'>Student</p>
          </div>
        </div>

        <div class='container-3'>
          <div class='info-1'>
            <div class='id'>
              <h4>ID Number</h4>
              <p><b>RVGDTR\\$id_no\\$id_issue_year</b></p>
            </div>
            <div class='department'>
              <h4>Department</h4>
              <p><b>$department</b></p>
            </div>
            <div class='modality'>
              <h4>Modality</h4>
              <p><b>$modality</b></p>
            </div>
          </div>
          
          <div class='info-2'>
            <div class='join-date'>
              <h4>ID Issue Date</h4>
              <p><b>$id_issue</b></p>
            </div>
            <div class='expire'>
              <h4>ID Expire Date</h4>
              <p><b>$id_expire</b></p>
            </div>
            <div class='campus'>
              <h4>Campus</h4>
              <p><b>Gada Campus</b></p>
            </div>
          </div>

          <div class='info-4'>
            <div class='sign'>
              <br>
              <p style='font-size:12px;'><em>Head of Registrar and Alumni's Signature Here</em></p>
            </div>
          </div>
        </div>
      </div>";
    }
  }
  // ID card generator ends here


  if (isset($_POST['submit'])) {
    $check_pay = mysqli_query($con, "SELECT Payer_ID, Pay_Confirmed FROM tblpayments WHERE Payer_ID='$uid'");
    if ($row = mysqli_fetch_array($check_pay)) {
      // applicant has already submitted payment information.
      if ($row['Pay_Confirmed'] == 'verified') {
        // redirect to congratulations page and show generated ID.
        //echo "Payment Verified!";
        $redirectUrl = "student.php?uid=" . urlencode($uid);
        header("Location: " . $redirectUrl);
        exit();
      } else {
        // payment not yet verified
        //echo "You have submitted your payment details successfully. Please wait to hear from us!";
      }
    } else {
      $name = $_POST['pay_name'];
      $payRef = $_POST['pay_ref'];
      $payDate = $_POST['pay_date'];
      $payPic = $_FILES["pay_pic"]["name"];

      // image file validation
      $extension_pic = substr($payPic, strlen($payPic) - 4, strlen($payPic));
      $allowed_ext_pic = array(".jpg", ".png", ".jpeg", ".gif");
      if (!in_array($extension_pic, $allowed_ext_pic)) {
        echo "<script>alert('Invalid format. Only image files are allowed');</script>";
      } else {
        $return_app_id = mysqli_query($con, "SELECT ID FROM tbladmapplications WHERE UserId = $uid");
        $row_id = mysqli_fetch_array($ret);
        $app_id = $row['ID'];

        $pay_receipt = $name . "_" . md5($payPic) . $extension_pic;
        move_uploaded_file($_FILES["pay_pic"]["tmp_name"], "userimages/payments" . $pay_receipt);
        // now the system can push the data into tblpayments
        $query_pay = mysqli_query($con, "INSERT INTO tblpayments (Application_ID, Payer_ID, Payer_Name, Pay_Ref, Pay_Date, Pay_Receipt)
                  VALUES('$app_id', '$uid', '$name', '$payRef', '$payDate', '$payPic')");
        $query_adm = mysqli_query($con, "UPDATE tbladmissions SET Adm_Payment_Status = 'paid', Adm_Pay_Date = CURRENT_TIMESTAMP WHERE Adm_App_ID = '$app_id'");

        if ($query_pay && $query_adm) {
          // Create a hidden form and submit it dynamically
          echo '<form id="hiddenForm" action="pay-ver-parser.php" method="post">';
          echo  '<input type="hidden" name="payRef" value="' . $payRef . '">';
          echo  '<input type="hidden" name="payDate" value="' . $payDate . '">';
          echo  '<input type="hidden" name="uid" value="' . $uid . '">';
          echo '</form>';
          echo '<script>document.getElementById("hiddenForm").submit();</script>';
        }
      }
    }
    //$query = mysqli_query($con, "");  
  }
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dom-to-image/2.6.0/dom-to-image.min.js"></script>


    <style>
      #Student_ID {
        border: 2px solid brown;
        /* Style the element to be printed */
      }

      .lavkush img {
        border-radius: 8px;
        border: 2px solid blue;
      }

      span {
        font-family: 'Orbitron', sans-serif;
        font-size: 16px;
      }

      hr.new2 {
        border-top: 1px dashed black;
        width: 350px;
        text-align: left;
        align-items: left;
      }

      p {
        font-size: 13px;
        margin-top: -5px;
      }

      .container-0 {
        width: 450px;
        height: 295px;
        margin: auto;
        background-color: white;
        box-shadow: 0 1px 10px rgb(146 161 176 / 50%);
        overflow: hidden;
        border-radius: 10px;
      }

      .header {
        /* border: 2px solid black; */
        width: 98%;
        height: 50px;
        margin: 3px auto;
        background-color: white;
        border-radius: 8px;
        /* box-shadow: 0 1px 10px rgb(146 161 176 / 50%); */
        /* border-radius: 10px; */
        background-image: url(../images/rvu-poster.png);
        overflow: hidden;
        background-size: cover;
        /* font-family: 'Poppins', sans-serif; */
      }

      .header h1 {
        color: rgb(27, 27, 49);
        text-align: right;
        margin-right: 20px;
        margin-top: 15px;
      }

      .header p {
        color: rgb(157, 51, 0);
        text-align: right;
        margin-right: 22px;
        margin-top: -10px;
      }

      .container-2 {
        /* border: 2px solid red; */
        width: 100%;
        height: 40%;
        margin: 0px auto;
        margin-top: -20px;
        display: flex;
      }

      .box-1 {
        border: 1px solid black;
        width: 30%;
        height: 122px;
        margin: -20px 15px;
        border-radius: 3px;
      }

      .box-1 img {
        width: 115px;
        height: 120px;
        border-radius: 3px;
      }

      .box-2 {
        /* border: 2px solid purple; */
        width: 75%;
        height: 8vh;
        margin: 25px 5px;
        padding: 5px 7px 0px 0px;
        text-align: left;
        font-family: 'Poppins', sans-serif;
      }

      .box-2 h2 {
        font-size: 1.3rem;
        margin-top: 0px;
        color: rgb(27, 27, 49);
      }

      .box-2 p {
        font-size: 0.7rem;
        margin-top: -5px;
        color: rgb(179, 116, 0);
      }

      .container-3 {
        /* border: 2px solid rgb(111, 2, 161); */
        width: 100%;
        height: 50%;
        margin: 0px auto;
        margin-top: -5px;
        display: flex;
        font-family: 'Shippori Antique B1', sans-serif;
        font-size: 0.7rem;
      }

      .info-1 {
        /* border: 1px solid rgb(255, 38, 0); */
        width: 40%;
        height: 100%;
        margin-left: 15px;
        text-align: left;
      }

      .id {
        /* border: 1px solid rgb(2, 92, 17); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .id h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .department {
        /* border: 1px solid rgb(0, 46, 105); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .department h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .modality {
        /* border: 1px solid rgb(0, 46, 105); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .modality h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .info-2 {
        /* border: 1px solid rgb(4, 0, 59); */
        width: 35%;
        height: 100%;
        margin: 0px;
      }

      .join-date {
        /* border: 1px solid rgb(2, 92, 17); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .join-date h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .expire {
        /* border: 1px solid rgb(0, 46, 105); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .expire h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .campus {
        /* border: 1px solid rgb(0, 46, 105); */
        width: 17vh;
        height: 30%;
        margin: 4px 0px 0px 0px;
      }

      .campus h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .info-4 {
        /* border: 2px solid rgb(255, 38, 0); */
        width: 25%;
        height: 100%;
        margin-right: 10px;
      }

      .phone h4 {
        color: rgb(179, 116, 0);
        font-size: 15px;
      }

      .sign {
        /* border: 1px solid rgb(0, 46, 105); */
        width: auto;
        height: 5vh;
        margin: 10px 0px 0px 20px;
        text-align: center;
      }

      #footer_part {
        position: fixed;
        bottom: 0;
        width: 100%;
      }
    </style>
  </head>

  <body class="vertical-layout vertical-menu-modern 2-columns menu-expanded fixed-navbar" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">
    <!-- fixed-top-->
    <?php
    include_once('includes/header.php');
    include_once('includes/leftbar.php');
    // fetching payment data from tblpayments
    $check_pay = mysqli_query($con, "SELECT * FROM tblpayments WHERE Payer_ID='$uid'");
    $pay_data = mysqli_fetch_array($check_pay);
    ?>
    <div class="app-content content">
      <div class="content-wrapper">
        <div class="content-header row"></div>
        <div class="content-body">
          <?php
          if ($pay_data['Pay_Confirmed'] === 'verified') {
            // fetch data from tblregistered
            $fetch_reg = mysqli_query($con, "SELECT * FROM tblregistered WHERE Reg_User_ID = '$uid'");
            $row = mysqli_fetch_array($fetch_reg);
            $R_ID = $row['Reg_ID'];
            $R_User = $row['Reg_User_ID'];
            $R_Course = $row['Reg_Course'];
            $R_Date = $row['Reg_date'];
          ?>
            <div class="row">
              <div class="col-xl-12 col-lg-12 col-12">
                <div class="card pull-up">
                  <div class="card-content">
                    <a href="Pay-cond.php">
                      <div class="card-body">
                        <div class="media d-flex">
                          <div class="media-body text-left">
                            <h4 align="center">Registration Completed successfully <br> Click here for details</h4>
                          </div>
                          <div>
                            <i class="icon-file success font-large-2 float-right"></i>
                          </div>
                        </div>
                        <div class="progress progress-sm mt-1 mb-0 box-shadow-2">
                          <div class="progress-bar bg-gradient-x-success" role="progressbar" style="width: 100%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                      </div>
                    </a>
                  </div>
                </div>
                <hr>
                <h3><b>Your Digital ID</b> <em>(You still need to collect your phyical ID on reporting to the university)</em></h3>
                <div class="card-body" id="mycard">
                  <?php echo $html ?>
                </div>
              </div>
            </div>

          <?php
          } else { ?>
            <h4>
              <strong>Payment reference:</strong> <?php echo $pay_data['Pay_Ref'] ?> <br><br>
              <strong>Payment Date:</strong> <?php echo date('d-M-Y', strtotime($pay_data['Pay_Date'])) ?> <br><br>
              Dear <?php echo $pay_data['Payer_Name'] ?>, <br><br>
              We are pleased to learn that you have reached the last phase of your enrollement in our University.<br><br>
              We will process your payment as fast as possible and let you know of the outcome soon.<br><br>
              Should you require any further information or in case you have submitted wrong payment details,
              you can reach us at <a style="color:coral">rvu.admissions.sup@gmail.com</a><br><br>
              <strong>Kind regards,<br><br>
                Rift Valley University Admissions Office
              </strong>
            </h4>
          <?php } ?>
        </div>
      </div>
    </div>
    <?php //include('includes/footer.php'); 
    ?>

    <!--to handle the forwarding of values to pay-ver-parser.php -->
    <!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
    <script src="app-assets/vendors/js/jquery-3.6.0.min.js"></script>
    <script src="app-assets/vendors/js/vendors.min.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app-menu.js" type="text/javascript"></script>
    <script src="app-assets/js/core/app.js" type="text/javascript"></script>
  </body>
<?php } ?>
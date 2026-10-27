<?php 
include('dbconfig.php');
include('comm.php');
//print_r($_POST);
//include('views/custom_logout.php');
session_start();
if (isset($_POST['submit'])){
   // print_r($_POST); //exit;
    if($_POST['submit']=="submit") {
        if($_POST['new_password'] != $_POST['conpassword']){
            //echo "not match";
            header("location:reset_password.php?error=6008");
        }else{
          //echo "ravi"; exit;
          $data = urlencode(json_encode($_POST));
          $url = API."?mode=resetpassword&data=".$data;
          $rows = file_get_contents($url, false, $context);
          $row = json_decode($rows,1);
         if($row['error'] == '6009'){
              //echo "data"; exit;
              header("location:reset_password.php?error=6009");
          }else{
              //echo $row['error']; exit;
              //$error = $row['error'];
            //echo "page redirect";
             $_SESSION['reset_password'] = 1;
              header("location:login.php");
          }
        }  
    }
}
//print_r($_SESSION['reset_password']);
function getIPAddress(){
    return $_SERVER['REMOTE_ADDR'];
}?> 
<!DOCTYPE html>
<html lang="en">

<head>
    <title>TimelyComply | Login</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
      <meta name="keywords" content="bootstrap, bootstrap admin template, admin theme, admin dashboard, dashboard template, admin template, responsive" />
      <meta name="author" content="colorlib" />
      <!-- Favicon icon -->

      <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
      <!-- Google font-->     
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
      <!-- waves.css -->
      <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all"><!-- feather icon --> 
      <link rel="stylesheet" type="text/css" href="assets/css/style.css"><link rel="stylesheet" type="text/css" href="assets/css/pages.css">
        <link rel="stylesheet" type="text/css" href="assets/css/animate.css">

        <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
        <style>
            html {  height: 100%;}
        </style>
  </head>

  <body class="bg-white reset-page-body">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <div class="mobile-view-logo bg-primary text-center">
        <img class="img-100 m-3" src="assets/images/sign-in/compliance_logo.png">   
    </div>
    <div class="row align-items-center">
        <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 bg-white text-center">
            <div class="p-5 custom-login-on">
               <?php 
                      $error = $_GET['error'];
                      $dis_error['6009'] = "The old password entered is incorrect, </br>please check and reenter";
                      $dis_error['6008'] = "New password and confirm password does not match,</br please check";
                      if($error !='') { 
                        if($error == '6008' || $error == '6009'){   ?>
                      <div class="alert alert-warning alert-dismissible">
                          <span class="f-14"><?php echo $dis_error[$error]; ?></span>
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                          </div>
                    <?php }} ?>
                <h2 class="f-26 f-w-600 mt-4">Reset Password</h2>
                <form class="md-float-material form-material" action="" method="post">
                    <div class="width-60 text-left mt-5"> 
                        <div class="form-group">
                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" placeholder="Old Password" id="chrUserName" name="old_password" autocomplete="off" required="">
                        </div>
                        <div class="form-group">
                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" placeholder="New Password" id="chrUserName" name="new_password" autocomplete="off" required="">
                        </div>
                        <div class="form-group mb-1">
                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" placeholder="Confirm Password" name="conpassword" autocomplete="off" required="">
                        </div>
                        <div class="form-group mt-4">
                            <input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>" />
                            <input type="hidden" name="submit" value="submit" />
                            <button  class="btn btn-primary btn-md waves-effect text-center b-radius m-b-20 fw-bold">Submit</button></div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 text-center desktop-view-bg">
            <div class="p-3">
                <div class="text-center d-block">
                    <img class="img-100 mt-3" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">
                </div>
                <!-- <img class="mt-5 mb-4" src="assets/images/sign-in/bg11.png"> -->
                <div class="mt-2 mb-5 text-center">
                    <h2 class="text-white f-30 f-w-600"><!-- Never miss any compliance deadline --></h2>
                    <p class="f-14 text-white p-2 m-l-30 m-r-30"><!-- Timely Comply facilitates compliance management teams to keep themselves updated with the status of all compliance submissions. An innovative solution that has been developed by experts and experienced product developers to smoothen the coordination between internal stakeholders and external advisors. --></p>
                </div>
            </div>
        </div>
    </div>
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/pages/waves/js/waves.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<script type="text/javascript" src="assets/js/script.js"></script>


</body>

</html>

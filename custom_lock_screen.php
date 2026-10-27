<?php 
include('dbconfig.php');
session_start();
//session_destroy();
$_SESSION['userid']="";

if($_COOKIE['user_email']=="" || !isset($_COOKIE['user_email'])){
     header("location:index.php");
}
if(isset($_POST['submit'])){
    if($_POST['submit']=="Login") {
     $data = urlencode(json_encode($_POST));
        $url = API."?mode=login&data=".$data;
        $response = file_get_contents($url, false, $context);
        $row = json_decode($response,1);  
        if ($row['id'] !=''){
            $_SESSION['userid'] = $row['id'];
            $_SESSION['us_name']=$row['user_name'];
            $_SESSION['company_id'] = $row['company_id'];
            $_SESSION['email']=$row['user_email'];
            $_SESSION['lastlogin']=$row['lastlogin'];
            $_SESSION['role_id']=$row['role_id'];
            $_SESSION['DB_NAME']=$row['db'];
            $_SESSION['lastlogin']=$row['lastlogin'];
            $_SESSION['reset_password']=$row['reset_password'];
            $_SESSION['confirmed_basic_ino'] = 1;
            $_SESSION['admin_access_employee'] ="N"; //rohan added
            $_SESSION['work_module_setup_id'] = $_POST['work_module_setup_id'];
            $_SESSION['working_role_short_code'] =  $_POST['working_role_short_code'];
            if($_POST['option']==""){
                header("location:index.php"); 
            }else{
                 header("location:index.php?option=".$_POST['option']); 
            } 
        }else {
            //echo $row['error']; exit;
            $error = $row['error'];
             header("location:custom_lock_screen.php?error=$error");
        }
        exit;
    }
}
function getBrowser() { 
  $u_agent = $_SERVER['HTTP_USER_AGENT'];
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version= "";
  //First get the platform?
  if (preg_match('/linux/i', $u_agent)) {
    $platform = 'linux';
  }elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
    $platform = 'mac';
  }elseif (preg_match('/windows|win32/i', $u_agent)) {
    $platform = 'windows';
  }
  // Next get the name of the useragent yes seperately and for good reason
  if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)){
    $bname = 'Internet Explorer';
    $ub = "MSIE";
  }elseif(preg_match('/Firefox/i',$u_agent)){
    $bname = 'Mozilla Firefox';
    $ub = "Firefox";
  }elseif(preg_match('/OPR/i',$u_agent)){
    $bname = 'Opera';
    $ub = "Opera";
  }elseif(preg_match('/Chrome/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
    $bname = 'Google Chrome';
    $ub = "Chrome";
  }elseif(preg_match('/Safari/i',$u_agent) && !preg_match('/Edge/i',$u_agent)){
    $bname = 'Apple Safari';
    $ub = "Safari";
  }elseif(preg_match('/Netscape/i',$u_agent)){
    $bname = 'Netscape';
    $ub = "Netscape";
  }elseif(preg_match('/Edge/i',$u_agent)){
    $bname = 'Edge';
    $ub = "Edge";
  }elseif(preg_match('/Trident/i',$u_agent)){
    $bname = 'Internet Explorer';
    $ub = "MSIE";
  }
  // finally get the correct version number
  $known = array('Version', $ub, 'other');
  $pattern = '#(?<browser>' . join('|', $known) .
')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
  if (!preg_match_all($pattern, $u_agent, $matches)) {
    // we have no matching number just continue
  }
  // see how many we have
  $i = count($matches['browser']);
  if ($i != 1) {
    //we will have two since we are not using 'other' argument yet
    //see if version is before or after the name
    if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
        $version= $matches['version'][0];
    }else {
        $version= $matches['version'][1];
    }
  }else {
    $version= $matches['version'][0];
  }
  // check if we have a number
  if ($version==null || $version=="") {$version="?";}

  $data = array(  'userAgent' => $u_agent,'name'      => $bname, 'version'   => $version,'platform'  => $platform, 'pattern'    => $pattern);
  return $data['name'].",".$data['version'];
}
function getIPAddress(){
    return $_SERVER['REMOTE_ADDR'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
      <title>Infistaq | Home</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Admindek Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
    <meta name="author" content="colorlib" />
    <!-- Favicon icon -->
   <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
      <!-- Google font-->     
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet"><link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
      <!-- Required Fremwork -->
      <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
      <!-- waves.css -->
      <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all"><!-- feather icon --> <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
      <!-- themify-icons line icon -->
      <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
      <!-- ico font -->
      <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
      <!-- Font Awesome -->
      <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
      <!-- Style.css -->
      <link rel="stylesheet" type="text/css" href="assets/css/style.css"><link rel="stylesheet" type="text/css" href="assets/css/pages.css">
  </head>

  <body themebg-pattern="theme1">
 
  <!-- Pre-loader end -->
  <section class="login-block">
        <!-- Container-fluid starts -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Login card start -->
                                   

                    <form class="md-float-material form-material"  action="" method="post" novalidate>
                
                         
                               <div class="text-center">
                            <img src="assets/images/logos/logo-dark_2.png" alt="logo.png">
                        </div>

                        <div class="auth-box card">
                            <div class="card-block">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="text-center"><i class="icofont icofont-lock text-primary f-80"></i></h3>
                                    </div>
                                </div>
                                              <?php 
                            $error = $_GET['error'];
                            $dis_error['6001'] = "Thank you very much for confirming your registration, </br> please login using email ID.";
                            $dis_error['6002'] = "Incorrect password. Please try again.";
                            $dis_error['6003'] = "We have sent a verification email, </br> please login into your email and verify.";
                            $dis_error['6004']  = "Your email verification is pending,</br>  kindly verify your email to access the system.";
                            $dis_error['6005']  = "The email is not registered with us,</br>  please register to access the system";
                            $dis_error['6006']  = "Details are sent to your registered email,</br>  please check";

                            if($error !='') { 
                            if($error == '6002' || $error == '6004' || $error == '6005' ){ ?>
                          
                            <div class="alert alert-warning alert-dismissible">
<strong class="f-14"><?php echo $dis_error[$error]; ?></strong>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
                            <?php }else{ ?>
                                               <div class="alert alert-warning alert-dismissible">
<strong class="f-14"><?php echo $dis_error[$error]; ?></strong>
<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
                          
                        <?php } }?>

    <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
    <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
    <input type="hidden" name="submit" value="Login" />
    <input type="hidden" name="chrUserName" id="chrUserName" class="form-control" value="<?php echo $_COOKIE['user_email']; ?>">
    <input type="hidden" name="option" id="option" class="form-control" value="<?php echo $_COOKIE['cookie_option']; ?>">
    <input type="hidden" name="work_module_setup_id" id="work_module_setup_id" class="form-control" value="<?php echo $_COOKIE['work_module_setup_id']; ?>">
    <input type="hidden" name="working_role_short_code" id="working_role_short_code" class="form-control" value="<?php echo $_COOKIE['working_role_short_code']; ?>">
        


                                 <div class="form-group form-primary">
                                    <input type="password" name="chrPassword" class="form-control" required="" >
                                    <span class="form-bar"></span>
                                    <label class="float-label">Password</label>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="d-grid">

                                            <button type="submit" class="btn btn-primary btn-md waves-effect text-center b-radius m-b-20 fw-bold">
                                                <i class="icofont icofont-lock"></i> Unlock Screen 
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-inverse text-end">Back to <a href="index.php?option=COM_logout" class="text-primary cursor-pointer"><b> &nbsp; &nbsp; Login </b></a></p>
                             
                            </div>
                        </div>
                    </form>
                    <!-- Login card end -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
<!-- Warning Section Ends -->
<!-- Required Jquery -->
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="assets/bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- waves js -->
<script src="assets/pages/waves/js/waves.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="assets/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>
<!-- i18next.min.js -->
<script type="text/javascript" src="assets/bower_components/i18next/js/i18next.min.js"></script>
<script type="text/javascript" src="assets/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
<script type="text/javascript" src="assets/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
<script type="text/javascript" src="assets/bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
<!--Color Script Common-->
<script type="text/javascript" src="assets/js/common-pages.js"></script>


</body>

</html>

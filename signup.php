<?php 
 /*if(isset($_POST['submit'])){
print_r($_POST); exit;
} */
include('dbconfig.php');
session_start();
/*
unset($_SESSION['userid']);

unset($_SESSION['us_name']);

unset($_SESSION['lastlogin']);

unset($_SESSION['roletype']);

unset($_SESSION['email']);

unset($_SESSION['adminmemberjoinedon']);

unset($_SESSION['last_activity']);

unset($_SESSION['application_pages']);

unset($_SESSION['application_menu']);
session_destroy ();

$_SESSION = array();*/
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
if($_SESSION['userid']!=''){
  header("location:index.php");
}
if(isset($_GET['confirm']) && $_GET['confirm'] != ''){
  $confirm = base64_decode($_GET['confirm']);
    //echo $confirm;
  $url = API."?mode=emailconfirm&confirm=".$confirm;
    //echo $url; exit;  
  $response = file_get_contents($url, false, $context);
    //echo $response;
  header("location:login.php?error=6001");

}
if (isset($_POST['submit'])){
    //print_r($_POST); exit;
  if($_POST['submit']=="Login") {
    $data = urlencode(json_encode($_POST));
    $url = API."?mode=login&data=".$data;
        //echo $url; //exit;
    $response = file_get_contents($url, false, $context);
        //echo $response;
    $row = json_decode($response,1);  
        //print_r($row); //exit;
    if ($row['id'] !=''){
            //echo "data"; exit;
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
            //echo "<pre>"; print_r($_SESSION); exit;
            //$_SESSION['adminmemberjoinedon']=$row['createdon'];
      header("location:index.php");
    }else {
            //echo $row['error']; exit;
      $error = $row['error'];
      header("location:login.php?error=$error");
    }
    exit;
  }
  if($_POST['submit']=="signup"){
   $_POST['email_check'] =0;
   $url = API."?mode=signup&data=".json_encode($_POST,1);
        //echo $url; exit;  
   $response = file_get_contents($url, false, $context);
        //echo $response;
   header("location:login.php?error=6003");
 }
 if($_POST['submit']=="forgot"){
  $url = API."?mode=forgotpassword&data=".$_POST['email'];
       // echo $url; exit;  
  $response = file_get_contents($url, false, $context);
  $row = json_decode($response,1);  
        //print_r($row); exit;
  if ($row['error'] =='6005'){
    header("location:login.php?error=6005");
  }else{
    header("location:login.php?error=6006");
  }

}
}
function getIPAddress(){
  return $_SERVER['REMOTE_ADDR'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Recruitstaq | Login</title>
  <!-- HTML5 Shim and Respond.js IE10 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <!-- Meta -->
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
    <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all"><!-- feather icon --> <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"><link rel="stylesheet" type="text/css" href="assets/css/pages.css">
    <link rel="stylesheet" type="text/css" href="assets/css/animate.css">

    <link rel="stylesheet" type="text/css" href="assets/css/custom.css">
    <style>
     html {  height: 100%;}
   </style>
 </head>


 <body class="bg-white" style="background-image:linear-gradient(rgb(255 255 255 / 70%) 240px, #ffffff 0%);">
  <div class="loader-bg">
    <div class="loader-bar"></div>
  </div>
  <div class="mobile-view-logo bg-primary text-center">
    <img class="img-100 m-3" src="assets/images/sign-in/HRstaq-1.png">   
  </div>
  <div class="row align-items-center fancy-sign-in">
    <div class="col-lg-6 bg-white">
      <div class="m-l-50 p-l-50 margin-0 mob-pd">
        <div class="text-center1" style="padding-left: 2rem!important;"> <img src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" >  </div>
        <div class="p-5 regsterform" >
         <form class="md-float-material form-material" action="" method="post" novalidate>

          <p class="text-muted f-w-600 text-center f-18 text-muted">Already A Member <a class="text-primary" href="#" onclick="loginpagedisplay()" >Sign In</a></p>

          <h2 class="f-26 f-w-600 mt-4">Create an Account</h2>
          <p class="text-muted f-14 f-w-600">Get unlimited access & earn money</p>
          <div class="text-left mt-4">
            <div class="form-group">
              <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="name" id="chrUserName" required="" placeholder="User Name">
            </div>
            <div class="form-group mb-3">
              <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="email" id="chrUserName" required="" placeholder="Email Id">
            </div>
            <div class="form-group mb-1">
              <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="password"   required="" placeholder="Password">
              <span class="text-muted f-12 text-left ">Use 8 or more characters with a mix of letters, numbers & symbols.</span>

            </div>
            <div class="form-group mb-1 text-left mt-2">
              <input type="checkbox" name="" id="term">
              <label for="term">I Accept the <a class="text-primary" href="">Terms</a></label>
            </div>
            <div class="row mt-4">
              <div class="col-lg-6">
                <div class="form-group">
                  <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                  <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                  <input type="hidden" name="submit" value="signup" />
                  <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4">Submit</button>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="width-60">

      </div>
    </div>
  </div>
  <div class="col-lg-6 text-center desktop-view-bg" style="">
    <img class="w-100 fancy-bg" src="assets/images/sign-in/recruitstaq_side_image.png" style="">
  </div>
</div>

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
<script type="text/javascript" src="assets/js/common-pages.js"></script>
<script type="text/javascript" src="assets/js/script.js"></script>
<script>
 function registerform(){
  $(".forgotpasswordon").hide();
  $(".custom-login-on").hide();
  $(".regsterform").show('slide');
}
function loginpagedisplay(){
	$(".regsterform").hide();
  $(".forgotpasswordon").hide();
  $(".custom-login-on").show('slide');
}
function fergotpassworddisplay(){
  $(".custom-login-on").hide();
  $(".regsterform").hide();
  $(".forgotpasswordon").show('slide');

}

</script>

</body>

</html>

<?php 
//echo $_SERVER['DOCUMENT_ROOT'];
 /*if(isset($_POST['submit'])){
print_r($_POST); exit;
} */
//echo "Good Boy"; exit;
include('dbconfig.php');
session_start();
$session_id = session_id();
//echo "check"; exit;
$image = "assets/images/logo.png";
 /**/    $domain = $_SERVER['HTTP_HOST'];
    //echo "Main domain name: $domain";exit;
    $subdomain = '';
    $host = $_SERVER['HTTP_HOST'];
    // Split host into parts
    $parts = explode('.', $host);
    // Check if host has subdomain

    if (count($parts) > 2) {
      // Get subdomain
      $subdomain = $parts[0];
      if($subdomain == "www"){
          $subdomain = $parts[1];
      }
    }
/*
    $domainurl = API."?mode=domainverifye&domain_name=".$subdomain."&data_name=".MAINDB;
    //echo  $domainurl;  exit;
    $domainres = file_get_contents($domainurl, false, $context);
    $domainresult = json_decode($domainres,1);
    //print_r($domainresult); exit;
    $zqr_connect = $domainresult['zqr_connect'];
    $domainresult = $domainresult['domain_registrations']; 
      
    $_POST['zqr_connect'] = $zqr_connect; 
    $host = $_SERVER['HTTP_HOST'];
    $parts = explode('.', $host);
    // Check if domain has subdomain
    if (count($parts) > 2) {
      // Remove subdomain and get main domain
      $mainDomain = $parts[count($parts)-2] . '.' . $parts[count($parts)-1];
    } else {
      // No subdomain, main domain is same as host
      $mainDomain = $host;
    }
    if(!isset($domainresult['id']) || $domainresult['id'] ==""){
        $httpshttp = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        header("location:".$httpshttp."://".$mainDomain."/signin");
    }
**/
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
  $u_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
  $bname = 'Unknown';
  $platform = 'Unknown';
  $version= "";
  $ub = "Unknown";

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
if(!empty($_SESSION['userid'])){
    header("location:index.php");
}
if(isset($_GET['confirm']) && $_GET['confirm'] != ''){
    $confirm = base64_decode($_GET['confirm']);
    //echo $confirm;
    $url = API."?mode=emailconfirm&confirm=".$confirm;
    //echo $url; exit;  
    $response = file_get_contents($url, false, $context);
    $response = json_decode($response,1);  
    // echo "<pre>";
    // print_r($response);
    //  echo "</pre>";
    //echo $response;
    //echo $response;
   header("location:login.php?error=6001");

}
if (isset($_POST['submit'])){
    $_POST['subdomain'] = $subdomain;
    
    if($_POST['submit']=="Login") {
      // code for check server side validation
      //if(empty($_SESSION['captcha_code'] ) || strcasecmp($_SESSION['captcha_code'], $_POST['captcha_code']) == 0){ 
      //if(empty($_SESSION['captcha'] ) || strcasecmp($_SESSION['captcha'], $_POST['captcha']) == 0){  
          $_POST['php_session'] = $session_id;
            //echo "yes captcha code"; exit;
            //$data = urlencode(json_encode($_POST));
            $url = API."?mode=login&method=post";
            //echo $url; print_r($_POST); exit;
            $postdata = http_build_query($_POST);
            $opts = array('http' =>array(
              'method'  => 'POST',
              'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
              'content' => $postdata
            )
          );
        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        
        //echo $url; exit;
        //$response = file_get_contents($url, false, $context);
        //echo $response;
        if (is_string($response)) {
            $response = preg_replace('/^[\xEF\xBB\xBF\r\n\s]+/', '', $response);
        }
        $row = json_decode($response,1);  
        //print_r($row); //exit;
        if (isset($row['id']) && $row['id'] !=''){
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
            $_SESSION['admin_access_employee'] ="N"; //rohan added
            $_SESSION['domain_name'] =$subdomain;
            $_SESSION['user_token'] =$row['user_token'];
            $_SESSION['HTTP_INFI_TOKEN'] =$row['infi_token'];
            //echo "<pre>"; print_r($_SESSION); exit;
            //$_SESSION['adminmemberjoinedon']=$row['createdon'];
           header("location:index");
        }else {
            //echo $row['error']; exit;
            $error = !empty($row['error']) ? $row['error'] : '6002';
            header("location:login?error=$error");
           exit;
        }
      // }else{// Captcha verification is Correct. Final Code Execute here!    
      //   //echo "No captcha code"; exit;
      //   $error = '6007'; 
      //    header("location:login?error=$error");
      //      exit;   
      // }
    }
    if($_POST['submit']=="signup"){
    $_POST['email_check'] =0;
        $url = API."?mode=signup&data=".json_encode($_POST,1);
        //echo $url; exit;  
        $response = file_get_contents($url, false, $context);
        //echo $response;
        header("location:login?error=6003");
    }
    if($_POST['submit']=="forgot"){
        $url = API."?mode=forgotpassword&data=".$_POST['email'];
       // echo $url; exit;  
        $response = file_get_contents($url, false, $context);
        $row = json_decode($response,1);  
        //print_r($row); exit;
        if ($row['error'] =='6005'){
            header("location:login?error=6005");
        }else{
            header("location:login?error=6006");
        }

    }
}
function getIPAddress(){
    return $_SERVER['REMOTE_ADDR'];
}
if(isset($datadetail['image'])){
  $image = $datadetail['image'];

}
//$image = 'uploads/all/file_1704095913_659270a975d4a.png';
$error = isset($_GET['error']) && $_GET['error'] !== '' ? $_GET['error'] : '';
$dis_error['6001'] = "Thank you very much for confirming your registration, </br> please login using email ID.";
$dis_error['6002'] = "Incorrect username or password.  Please try again.";
$dis_error['6003'] = "We have sent a verification email, </br> please login into your email and verify.";
$dis_error['6004']  = "Your email verification is pending,</br>  kindly verify your email to access the system.";
$dis_error['6005']  = "The email is not registered with us,</br>  please register to access the system";
$dis_error['6006']  = "Details are sent to your registered email,</br>  please check";
$dis_error['6007']  = "The Captcha code does not match!";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Infistaq | Login</title>
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
      <meta name="description" content="Unlock your business's full potential with affordable ERP software Infistaq. ERP that supports manufacturing, distribution, retail, trading, services, education, non profits and healthcare. Try our customizable ERP solutions today." />
      <meta name="keywords" content="ERP software, Enterprise Resource Planning, ERP system, Cloud ERP, On-premise ERP, ERP solutions, ERP implementation, ERP integration, Business process automation, Supply chain management, Inventory management, Financial management, Manufacturing ERP, Human resources management, Customer relationship management (CRM), Sales and marketing automation, ERP consulting, ERP customization, Enterprise software, ERP for small businesses, ERP for medium-sized businesses, ERP for large enterprises, Industry-specific ERP (e.g., ERP for manufacturing, retail, healthcare, etc.), ERP benefits, ERP features, ERP comparison, ERP reporting and analytics, ERP training, ERP support and maintenance, ERP scalability, ERP ROI (Return on Investment), Cloud-based ERP solutions, ERP for business growth, ERP best practices, ERP compliance, ERP case studies/testimonials" />
      <meta name="author" content="infisuite" />
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
      body{
      -webkit-touch-callout: none;
      -webkit-user-select: none;
      -khtml-user-select: none;
      -moz-user-select: none;
      -ms-user-select: none;
      user-select: none;
    }

        </style>
  </head>

  <?php 
  if(isset($_GET['page'])){
    $pageid  = $_GET['page'];  
  }else{
    $pageid  = LOGIN_PAGE_ID;
  } 
    //echo LOGIN_ARRAY[$pageid];
    if(isset(LOGIN_ARRAY[$pageid]) AND LOGIN_ARRAY[$pageid] !=""){
        include(LOGIN_ARRAY[$pageid]);
    }else{
        include("common_login.php"); 
    }

    ?>

  <div id="kt_modal_dynamic_popup2" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content"  id="dynamic_popup2" style="border:none;">
            
        </div>
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
 <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr-custom.js"></script>
<!-- <script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script> -->
<script type="text/javascript" src="assets/js/common-pages.js"></script>
 <script type="text/javascript" src="assets/js/script.js"></script>
<script>

function refreshCaptcha(){
  var img = document.images['captchaimg'];
  img.src = img.src.substring(0,img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}

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

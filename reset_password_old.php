<?php 
include('dbconfig.php');
include('comm.php');
//include('views/custom_logout.php');
session_start();
if (isset($_POST['submit'])){
    //print_r($_POST); //exit;
    if($_POST['submit']=="submit") {
        if($_POST['new_password'] != $_POST['conpassword']){
            header("location:reset_password.php?error=6008");
        }
        //echo "ravi"; exit;
        $data = urlencode(json_encode($_POST));
        echo $url = API."?mode=resetpassword&data=".$data;
        echo $rows = file_get_contents($url, false, $context);
        $row = json_decode($row,1);  
		if ($row['error'] == '6009'){
            //echo "data"; exit;
            header("location:reset_password.php?error=6009");
        }else {
            //echo $row['error']; exit;
            //$error = $row['error'];
            $_SESSION['reset_password'] = 1;
             header("location:login.php");
        } 
    }
}
function getIPAddress(){
    return $_SERVER['REMOTE_ADDR'];
}
?>
<!DOCTYPE html>

<html lang="en">
    <!--begin::Head-->
  <head>
  <title>Infistaq | Home</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="description" content="Admindek Bootstrap admin template made using Bootstrap 5 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
  <meta name="keywords" content="flat ui, admin Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
  <meta name="author" content="colorlib" />
  <!-- Favicon icon -->
  <link rel="icon" href="/assets/images/favicon.ico" type="image/x-icon">
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet">
  <!-- Required Fremwork -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap/css/bootstrap.min.css">
  <!-- waves.css -->
  <link rel="stylesheet" href="assets/pages/waves/css/waves.min.css" type="text/css" media="all">
  <!-- feather icon -->
  <link rel="stylesheet" type="text/css" href="assets/icon/feather/css/feather.css">
  <link rel="stylesheet" type="text/css" href="assets/icon/themify-icons/themify-icons.css">
  <!-- ico font -->
  <link rel="stylesheet" type="text/css" href="assets/icon/icofont/css/icofont.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" type="text/css" href="assets/icon/font-awesome/css/font-awesome.min.css">
  <!-- Data Table Css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" type="text/css" href="assets/pages/data-table/extensions/buttons/css/buttons.dataTables.min.css">
  <!-- font-awesome-n -->
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome-n.min.css">
  <!-- Chartlist chart css -->
  <link rel="stylesheet" href="assets/bower_components/chartist/css/chartist.css" type="text/css" media="all">
  <!--Rohan  -->
  <!-- Bootstrap date-time-picker js -->
  <link rel="stylesheet" type="text/css" href="assets/pages/advance-elements/css/bootstrap-datetimepicker.css">
  <!-- Date-range picker css  -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-daterangepicker/css/daterangepicker.css" />
  <!-- Date-Dropper css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/datedropper/css/datedropper.min.css" />
  <link rel="stylesheet" href="assets/bower_components/select2/css/select2.min.css" />
  <!-- Multi Select css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-multiselect/css/bootstrap-multiselect.css" />
  <link rel="stylesheet" type="text/css" href="assets/bower_components/multiselect/css/multi-select.css" />

  <!-- Color Picker css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/spectrum/css/spectrum.css" />
  <!-- Mini-color css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery-minicolors/css/jquery.minicolors.css" />
  <!-- Switch component css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/switchery/css/switchery.min.css">
  <!-- Tags css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/bootstrap-tagsinput/css/bootstrap-tagsinput.css" />
  <!-- animation nifty modal window effects css -->
  <link rel="stylesheet" type="text/css" href="assets/css/component.css">
  <!-- Calender css -->
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.css">
  <link rel="stylesheet" type="text/css" href="assets/bower_components/fullcalendar/css/fullcalendar.print.css" media='print'>
  <!--Rohan -->


  <!-- Style.css -->
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
  <link rel="stylesheet" type="text/css" href="assets/css/widget.css">
  <link rel="stylesheet" type="text/css" href="assets/css/pages.css">
  

  <link rel="stylesheet" type="text/css" href="assets/css/custom.css">

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" type="text/css" href="assets/bower_components/jquery.steps/css/jquery.steps.css">

  </head>
    <!--end::Head-->
    <!--begin::Body-->
     <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading"
        <!--begin::Main-->
        <div class="d-flex flex-column flex-root">
            <!--begin::Login-->
            <div class="login login-2 login-signin-on d-flex flex-column flex-lg-row flex-column-fluid bg-white" id="kt_login">
                <!--begin::Aside-->
                <div class="login-aside order-2 order-lg-1 col-lg-6  j-pro">
                    <!--begin: Aside Container-->
                    <div class="d-flex flex-column-fluid flex-column justify-content-between py-9 px-7 py-lg-13 px-lg-35">
                        <!--begin::Logo-->
                         <a href="#" class="text-center pt-2">
                <img src="assets/media/logos/infistaq_login.png" class="max-h-175px" alt=""/>
            </a>
            <!--end::Logo-->
          
                        <!--end::Logo-->
                        <!--begin::Aside body-->
                        <div class="d-flex flex-column-fluid flex-column ">
                            <!--begin::Signin-->
							
                            <div class="login-form login-signin p-5" id="kt_login_signin_form">
                                <!--begin::Form-->
                                <form class="kt-form p-5" novalidate="novalidate" id="" action="" method="post">

                                    <!--begin::Title-->
                                    <div class="text-center pb-8" >
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
                                        <h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Reset Password</h2>
                                    </div>
                                    <!--end::Title-->
                                     <!--begin::Form group-->
                        <div class="form-group">
                                       <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="text" placeholder="Old Password" id="chrUserName" name="old_password" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                       <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="password" placeholder="New Password" id="chrUserName" name="new_password" autocomplete="off">
                                    </div>
                                    <div class="form-group">
                                      <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="password" placeholder="Confrim Password" name="conpassword" autocomplete="off">
                                    </div>
                                     
                        <!--end::Form group-->

                       
                        <!--begin::Action-->
                        <div class="text-center pt-2">
							<input type="hidden" name="email" value="<?php echo $_SESSION['email']; ?>" />
								<input type="hidden" name="submit" value="submit" />
								<button  class="btn btn-primary btn-md waves-effect text-center b-radius m-b-20 fw-bold">Submit</button>
							</div>
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Signin-->
                            <!--begin::Signup-->
                            <?php /** ?>
							<div class="login-form login-signup pt-11"  id="">
                                <!--begin::Form--> <form class="kt-form" id="" action="" method="post">

                                    <!--begin::Title-->
                                    <div class="text-center pb-8" >
                                        <h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Sign Up</h2>
                                        <p class="text-muted font-weight-bold font-size-h4">Enter your details to create your account</p>
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                       <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="text" placeholder="Name" name="ccname" >
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="text" placeholder="Email" name="email" />
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                      
                                        <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="password" placeholder="Password" name="ccpassword" autocomplete="off" />
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                   <!--  <div class="form-group">
                                        <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="password" placeholder="Confirm password" name="cpassword" autocomplete="off" />
                                    </div> -->
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <label class="checkbox mb-0">
                                        <input type="checkbox" name="agree" />I Agree the
                                        <a href="#">terms and conditions</a>.
                                        <span></span></label>
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group d-flex flex-wrap flex-center pb-lg-0 pb-3">
                                        <input type="hidden" name="submit" value="signup" />
                                        <button id="" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mx-4">Submit</button>
                                        <button type="button" id="kt_login_signup_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mx-4">Cancel</button>
                                    </div>
                                    <!--end::Form group-->
                                </form>
                                <!--end::Form-->
                            </div>
                            <!--end::Signup-->
                            <!--begin::Forgot-->
                            <div class="login-form login-forgot pt-11" id='kt_login_forgot_form'>
                                <!--begin::Form-->  <form class="kt-form"  novalidate="novalidate" action="" method="post">
                                    <div class="text-center pb-8" >
                                        <h2 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg">Forgotten Password ?</h2>
                                        <p class="text-muted font-weight-bold font-size-h4">Enter your email to reset your password</p>
                                    </div>
                                    <!--end::Title-->
                                    <!--begin::Form group-->
                                    <div class="form-group">
                                        <input class="form-control form-control-solid h-auto py-7 px-6 rounded-lg font-size-h6" type="text" placeholder="Email" name="email"  />
                                    </div>
                                    <!--end::Form group-->
                                    <!--begin::Form group-->
                                    <div class="form-group d-flex flex-wrap flex-center pb-lg-0 pb-3">
                                        <input type="hidden" name="submit" value="forgot"> 
                                        <button   id="" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mx-4">Submit</button>
                                        <button type="button" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mx-4">Cancel</button>
                                    </div>
                                    <!--end::Form group-->
                                </form>
                                <!--end::Form-->
                            </div>
							                        <?php **/ ?>
                            <!--end::Forgot-->
                        </div>
                        <!--end::Aside body-->
                        <!--begin: Aside footer for desktop-->
                        <!-- <div class="text-center">
                            <button type="button" class="btn btn-light-primary font-weight-bolder px-8 py-4 my-3 font-size-h6">
                            <span class="svg-icon svg-icon-md">
                               
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M19.9895 10.1871C19.9895 9.36767 19.9214 8.76973 19.7742 8.14966H10.1992V11.848H15.8195C15.7062 12.7671 15.0943 14.1512 13.7346 15.0813L13.7155 15.2051L16.7429 17.4969L16.9527 17.5174C18.879 15.7789 19.9895 13.221 19.9895 10.1871Z" fill="#4285F4" />
                                    <path d="M10.1993 19.9313C12.9527 19.9313 15.2643 19.0454 16.9527 17.5174L13.7346 15.0813C12.8734 15.6682 11.7176 16.0779 10.1993 16.0779C7.50243 16.0779 5.21352 14.3395 4.39759 11.9366L4.27799 11.9466L1.13003 14.3273L1.08887 14.4391C2.76588 17.6945 6.21061 19.9313 10.1993 19.9313Z" fill="#34A853" />
                                    <path d="M4.39748 11.9366C4.18219 11.3166 4.05759 10.6521 4.05759 9.96565C4.05759 9.27909 4.18219 8.61473 4.38615 7.99466L4.38045 7.8626L1.19304 5.44366L1.08875 5.49214C0.397576 6.84305 0.000976562 8.36008 0.000976562 9.96565C0.000976562 11.5712 0.397576 13.0882 1.08875 14.4391L4.39748 11.9366Z" fill="#FBBC05" />
                                    <path d="M10.1993 3.85336C12.1142 3.85336 13.406 4.66168 14.1425 5.33717L17.0207 2.59107C15.253 0.985496 12.9527 0 10.1993 0C6.2106 0 2.76588 2.23672 1.08887 5.49214L4.38626 7.99466C5.21352 5.59183 7.50242 3.85336 10.1993 3.85336Z" fill="#EB4335" />
                                </svg>
                                
                            </span>Sign in with Google</button>
                        </div> -->
                        <!--end: Aside footer for desktop-->
                    </div>
                    <!--end: Aside Container-->
                </div>
                <!--begin::Aside-->
                <!--begin::Content-->
                <div class="content order-1 order-lg-2 d-flex flex-column pb-0 col-lg-6" style="background-color: #B1DCED;">
                    <!--begin::Title-->
                    <div class="d-flex flex-column justify-content-center text-center pt-lg-40 pt-md-5 pt-sm-5 px-lg-0 pt-5 px-7">
                        <h3 class="display4 font-weight-bolder my-7 text-dark f-40" style="color: #986923;">Amazing Wireframes</h3>
                        <p class="font-weight-bolder font-size-h2-md font-size-lg text-dark opacity-70 f-20">User Experience &amp; Interface Design, Product Strategy
                        <br />Web Application SaaS Solutions</p>
                    </div>
                    <!--end::Title-->
                    <!--begin::Image-->
                    <div class="content-img d-flex flex-row-fluid bgi-no-repeat bgi-position-y-bottom bgi-position-x-center" style="background-image: url(assets/media/svg/illustrations/login-visual-2.svg);"></div>
                    <!--end::Image-->
                </div>
                <!--end::Content-->
            </div>
            <!--end::Login-->
        </div>
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
<script type="text/javascript" src="assets/js/common-pages.js"></script>
    </body>
    <!--end::Body-->
</html>
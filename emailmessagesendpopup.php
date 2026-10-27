<?php 
include('dbconfig.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
 
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
<script type="text/javascript">
  function submitmesg(){
    var name = $("#name").val();
    var email = $("#email").val();
    var message = $("#message").val();
    $('#name_err').hide();
    $('#email_err').hide();
    $('#message_err').hide();
    if(name == ""){
      $('#name_err').show();
    }
    if(email == ""){
      $('#email_err').show();
    }
    if(message == ""){
      $('#message_err').show();
    }
    var captcha_code = '<?php echo $_SESSION['captcha_code']; ?>';
    console.log("captcha_code - "+captcha_code);
    if(name != "" && email != "" && message !=""){
      //var formdata = $("#from_data").serialize();
      // jQuery.ajax({
      //     url: "send_email.php",
      //     data:$("#from_data").serialize(),
      //     type: "POST",
      //     success:function(data){
      //         console.log(data);
      //         sendtothanku();
      //     },
      //     error:function (){}
      // });
    }
  }
  function sendtothanku(){
    jQuery.ajax({
          url: "emailmessagethankspopup.php",
          data:'option=option',
          type: "POST",
          success:function(data){
              $('#dynamic_popup2').html(data);
              $('#kt_modal_dynamic_popup2').modal("show");
          },
          error:function (){}
      });
  }
</script>
  <body themebg-pattern="theme1">
  <!-- Pre-loader start -->
  <div class="theme-loader">
      <div class="loader-track">
          <div class="preloader-wrapper">
              <div class="spinner-layer spinner-blue">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
              <div class="spinner-layer spinner-red">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-yellow">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
            
              <div class="spinner-layer spinner-green">
                  <div class="circle-clipper left">
                      <div class="circle"></div>
                  </div>
                  <div class="gap-patch">
                      <div class="circle"></div>
                  </div>
                  <div class="circle-clipper right">
                      <div class="circle"></div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  <!-- Pre-loader end -->

    <!-- Login page start here -->
    <section class="login-block1">
        <!-- Container-fluid starts -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    
                    <!-- Authentication card start -->
                    <form class="md-float-material form-material" id="from_data">
                        <div class="auth-box">
                            <div class="card-block">
                                <div class="row align-items-center">
                                  <div class="col-lg-12 text-right">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                                  </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 get-in-touch">
                                        <img src="assets/images/msg.png" class="" style="width: 100%;">
                                    </div>
                                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 p-r-40 padd-mob">
                                        <h3 class="txt-primary f-w-600">Get in Touch</h3>
                                        <div class="form-group form-primary mt-4 mb-0">
                                            <input type="text" required name="name" id="name" class="form-control" required="">
                                            <span class="form-bar"></span>
                                            <label class="form-label float-label">Name*</label>
                                        </div>
                                        <div id="name_err" class="text-danger f-12 position-absolute" style="display:none;">Please enter the name</div>
                                        <div class="form-group form-primary mt-4 mb-0">
                                            <input type="text" name="email" id="email" class="form-control" required="">
                                            <span class="form-bar"></span>
                                            <label class="form-label float-label">Email ID*</label>
                                        </div>
                                        <div id="email_err" class="text-danger f-12 position-absolute" style="display:none;">Please enter the email</div>
                                        <div class="form-group form-primary mt-4 mb-0 txtarea-sec">
                                            <textarea style="height: 100px;" class="form-control" name="message" id="message"></textarea>
                                            <span class="form-bar"></span>
                                            <label class="form-label float-label">Message*</label>
                                        </div>
                                        <div id="message_err" class="text-danger f-12 position-absolute" style="display:none;">Please enter the message</div>



                                        <div class="form-group mb-1">
                                            <img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'><br>
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray"  id="captcha_code11" name="captcha_code" type="text" placeholder="Enter Captcha">
                                           
                                            <br>
                                            Can't read the image? click <a href='javascript: refreshCaptcha();' class="text-primary">here</a> to refresh.
                                        </div>
                                         <input type="hidden" name="captch_hide_code" id="captch_hide_code" value="<?php echo $pub_text; ?>">


                                        <div class="d-grid mt-4">
                                            <button type="button" onclick="submitmesg()" class="btn btn-primary btn-md waves-effect text-center">SEND MESSAGE</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Login page end here -->


    
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

</html>

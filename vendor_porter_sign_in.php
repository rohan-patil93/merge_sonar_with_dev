<script type="text/javascript">
    function loginemailpopup(){
        jQuery.ajax({
            url: "emailmessagesendpopup.php",
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
<body class="bg-white" style="background-image:linear-gradient(rgb(255 255 255 / 70%) 240px, #ffffff 0%);">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <div class="mobile-view-logo bg-primary text-center">
        <img class="img-100 m-0" src="<?php echo $image; ?>">   
    </div>
    <div class="row align-items-center">
        <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 bg-white text-center position-relative" style="">
            <div class="p-4 custom-login-on">
                <img class="img-100 mt-0" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">
                <h2 class="f-26 f-w-600 mt-4">Sign In</h2>
                  <?php 
                            $error = $_GET['error'];
                            $dis_error['6001'] = "Thank you very much for confirming your registration, </br> please login using email ID.";
                            $dis_error['6002'] = "Incorrect username or password.  Please try again.";
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
               <!---  <p class="text-muted f-14 f-w-600">Your Social Campaigns</p>
                 <div class="d-block mt-5">
                    <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/google-icon.svg"> Sign in with Google</button>
                    <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/apple-black.svg"> Sign in with Apple</button>
                </div>
                <div class="d-block mt-5">
                    <h3 class="text-muted f-14">Or with email</h3>
                </div>   -->
                    <form class="md-float-material form-material" action="" method="post">
                           
                <div class="width-60 text-left mt-3"> 
                    <div class="form-group">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="chrUserName" id="chrUserName" required="" placeholder="User Name">
                    </div>
                    <div class="form-group mb-1">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="chrPassword" required="" placeholder="password">
                    </div>
                    <span class="float-right f-w-600 text-primary forgot-pwd-open cursor-pointer" onclick="fergotpassworddisplay()">Forgot Password?</span>
                    <div class="form-group mt-4">
                    <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="Login" />
                        <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4 btn-custom-color" style="">Sign In</button>
                    </div>
                    <!-- <p class="text-muted f-w-600">Not a Member yet? <a class="text-primary" href="#" onclick="registerform()">Sign up</a></p> -->
                </div>
                </form>
            </div>
            <div class="p-2 regsterform" style="display:none">
                <img class="img-100 mt-0 rentalji" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">
                <h2 class="f-26 f-w-600 mt-2 text-center">Sign Up</h2>
               <!---  <p class="text-muted f-14 f-w-600 text-center">Your Social Campaigns</p>
                 <div class="d-block mt-5 text-center">
                    <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/google-icon.svg"> Sign in with Google</button>
                    <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/apple-black.svg"> Sign in with Apple</button>
                </div>
                <div class="d-block mt-4 text-center">
                    <h3 class="text-muted f-14">Or with email</h3>
                </div>   -->
                    <form class="md-float-material form-material" action="" method="post" novalidate>
                           
                <div class="width-60 text-left mt-4">
                    <div class="form-group">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="name" id="chrUserName" required="" placeholder="User Name">
                    </div>
                    <div class="form-group mb-3">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray"  type="text" name="email" id="chrUserName" required="" placeholder="Email Id">
                    </div>
                    <div class="form-group mb-1">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="password"   required="" placeholder="Password">
                       <!-- <span class="text-muted f-12 text-left ">Use 8 or more characters with a mix of letters, numbers & symbols.</span>  -->
                   
                    </div>
                 <!--   <div class="form-group mb-1 text-left mt-2">
                        <input type="checkbox" name="" id="term">
                        <label for="trem">I Accept the <a class="text-primary" href="">Terms</a></label>
                    </div>  -->
                    <div class="form-group">
                      <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="signup" />
                        <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-3" >Sign Up</button>
                    </div>
                    <p class="text-muted f-w-600 text-center sign-in-click">Already A Member <a class="text-primary" href="#" onclick="loginpagedisplay()">Sign In</a></p>
                </div>
                </form>
            </div>
            <div class="p-3 forgotpasswordon" style="display:none">
                <form class="md-float-material form-material" action="" method="post" novalidate>
                           
                <div class="width-60">
                    <img class="img-100 mt-0 rentalji" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>">
                    <h2 class="f-26 f-w-600 mt-2">Forgot Password</h2>
                    <p class="text-muted f-14 f-w-600">Enter your email to reset your password.</p>
                    <div class="form-group">
                        <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="email" name="email" placeholder="Email">
                    </div>
                    <div class="form-group text-center">
                                    <input type="hidden" name="submit" value="forgot" />
                        
                        <button class="btn btn-primary waves-effect waves-light" type="submit">Submit</button>
                        <button  type="button" class="btn bg-gray waves-effect waves-light cancel-forgot-pwd" style="padding:6px 15px; margin: 0;" onclick="loginpagedisplay()">Cancel</button>
                    </div>
                </div>
                </form>
            </div>
            <div class="p-4" style="padding-top:0px !important;">
                <div class="width-60">
                    <ul class="text-start p-t-5" style="">
                        <li class="d-inline-block"><a class="text-primary f-w-600" href="#">Employee Login</a></li>
                    </ul>
                    <ul class="text-start p-t-5 mt-2" style="">
                        <li class="d-inline-block"><a class="text-primary f-w-600" href="https://infisuite.com/">View Infisuite Privacy Policies</a></li>
                    </ul>
                    <ul class="text-start p-t-5 mt-2" style="">
                        <li class="d-inline-block"><a class="text-primary f-w-600" href="https://infisuite.com/">Website Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>

            <div class="" style="bottom: 10px; left: 20px; right: 20px;">
                <b class="f-w-600 f-13">For any issues (login credentials, registration, technical assistance, etc.) in accessing the portal <span class="text-primary cursor-pointer"  onclick="loginemailpopup()" >click here</span></b>
            </div>
        </div>
        <div class="col-lg-6 col-xl-6 col-md-6 col-sm-12 text-center desktop-view-bg">
            <div class="p-4">
                <!-- <div class="text-center d-block">
                    <img class="img-100 mt-3" src="assets/images/sign-in/<?php // echo LOGIN_INFO['logo']; ?>">
                </div> -->
                <!-- <img class="mt-5 mb-5" src="assets/images/sign-in/auth-screens.png" style=""> -->
                <div class="mt-4 text-center text-dark">
                    <h2 class="f-30 f-w-600">Welcome to Vendor Registration Portal<?php // echo LOGIN_INFO['heding']; ?></h2>
                    <!-- <p class="f-14 text-white p-2 m-l-30 m-r-30"><?php // echo LOGIN_INFO['text']; ?></p> -->
                    <p class="text-start mt-4 mb-2">Please enter your login credentials to access your secure vendor portal account.</p>
                    <ul class="text-start m-l-10" style="list-style: disc; list-style-position: inside;">
                        <li class=""><b class="f-w-600">Username/Email:</b> Enter the email address associated with your registration.</li>
                        <li class=""><b class="f-w-600">Password:</b> Enter your password. Password fields are case-sensitive.</li>
                        <!-- <li class="p-2"><b class="f-w-600">Remember Me:</b> (Optional) Select this option if you are using a personal device and would like your credentials remembered for future logins. Security Reminder: We strongly recommend against using this function on public or shared computers.</li> -->
                    </ul>
                    <b class="f-w-600 d-block text-start mt-3">Having trouble accessing your account?</b>
                    <ul class="text-start mt-2 m-l-10 login-ul" style="list-style: disc; list-style-position: inside;">
                        <li class="">Click the "Forgot Password" link below to initiate a password reset process.</li>
                    </ul>
                    <b class="f-w-600 text-start d-block mt-3">Forgot Password?</b>
                    <ul class="text-start mt-2 login-ul m-l-10" style="list-style: disc; list-style-position: inside;">
                        <li class="">Click here to reset your password: <span class="text-primary f-w-600 cursor-pointer" onclick="fergotpassworddisplay()">Forgot Password</span></li>
                    </ul>
                    <b class="f-w-600 text-start d-block mt-3">New Vendor Registration</b>
                    <ul class="text-start mt-2 m-l-10" style="list-style: disc; list-style-position: inside;">
                        <li class="">If you are a new vendor seeking to establish a business relationship with us, please <span class="text-primary cursor-pointer"  onclick="loginemailpopup()" >click here</span></li>
                    </ul>
                    <b class="f-w-600 text-start d-block mt-3">For Your Security</b> 
                    <p class="text-start mt-2 mb-2">We prioritize the security of your account information. Please be mindful of the following:</p>
                    <ul class="text-start mt-2 m-l-10" style="list-style: disc; list-style-position: inside;">
                        <li class="">Do not share your login credentials with anyone.</li>
                        <li class="">We will never request your password via email or phone.</li>
                        <li class="">Consider using a strong, unique password for your vendor portal account.</li>
                    </ul>
                    <b class="f-w-600 text-start d-block mt-3">Sign in Button</b>
                    <ul class="text-start mt-2 m-l-10" style="list-style: disc; list-style-position: inside;">
                        <li class="">Click the "Sign in" button to securely access your vendor portal account.</li>
                    </ul>
                    <!-- <b class="f-w-600 text-start d-block mt-3">Additional Information</b>
                    <ul class="text-start mt-2" style="list-style: disc; list-style-position: inside;">
                        <li class="p-2">For detailed information regarding our privacy practices, please refer to our Privacy Policy: link to your Privacy Policy.</li>
                        <li class="p-2">Our comprehensive Terms and Conditions outline the rights and responsibilities of using the vendor portal. Please review them at your convenience: Terms and Conditions: link to your Terms and Conditions.</li>
                        <li class="p-2">Should you require assistance with login or encounter any technical difficulties, please contact our Vendor Support team at [phone number] or [email address].</li>
                    </ul> -->
                </div>
            </div>
        </div>
    </div>

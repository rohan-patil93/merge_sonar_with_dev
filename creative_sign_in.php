<body class="" style="background-image:url('assets/images/sign-in/bg4.jpg'); background-size: cover;">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="p-5 mob-pd">
                    <div class="text-left m-l-50 p-l-50 margin-0 mob-pd">
                        <img class="img-200 mt-3" src="<?php echo $image; ?>">
                        <h3 class="text-white mt-3 f-24"><?php echo LOGIN_INFO['heding']; ?></h3>
						<p class="f-14 text-white"><?php echo LOGIN_INFO['text']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center creative pt-2">
                    <div class="width-70 mt-5">
                        <div class="card" style="min-height: 580px;">
                            <div class="card-body">
                                <div class="p-5 custom-login-on">
                                    <div class="text-center mb-5">
                                        <img class="img-200" src="assets/images/sign-in/HRstaq.png">   
                                    </div>
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
                                   <!--- <p class="text-muted f-14 f-w-600">Your Social Campaigns</p>
                                      <div class="d-block mt-5 text-center">
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/google-icon.svg"> Sign in with Google</button>
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/apple-black.svg"> Sign in with Apple</button>
                                    </div>
                                    <div class="d-block mt-5">
                                        <h3 class="text-muted f-14">Or with email</h3>
                                    </div>    -->
										<form class="md-float-material form-material" action="" method="post" novalidate> 
                           
                                    <div class="text-left mt-5">
							
                                        <div class="form-group"> 
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text"name="chrUserName" id="chrUserName" required="" placeholder="User Name">
                                        </div>
                                        <div class="form-group mb-1">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="chrPassword" required="" placeholder="password">
                                        </div>
                                        <a class="float-right f-w-600 text-primary forgot-pwd-open" onclick="fergotpassworddisplay()">Forgot Password?</a>
                                        <div class="form-group">
										<input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="Login" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4 ">Sign In</button>
                                        </div>
                                        <p class="text-muted f-w-600">Not a Member yet? <a class="text-primary" onclick="registerform()">Sign up</a></p>
                                    </div>
									</form>
                                </div>
								
								<div class="p-5 regsterform" style="display:none">
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
                           
                                    <div class="text-left mt-4">
                                        <div class="form-group">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" name="name" id="chrUserName" required="" placeholder="User Name">
                                        </div>
                                        <div class="form-group mb-3">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray"  type="text" name="email" id="chrUserName" required="" placeholder="Email Id">
                                        </div>
                                        <div class="form-group mb-1">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="password"   required="" placeholder="Password">
                                             <span class="text-muted f-12 text-left ">Use 8 or more characters with a mix of letters, numbers & symbols.</span>
                   
										</div>
                                    <!---    <div class="form-group mb-1 text-left mt-2">
                                            <input type="checkbox" name="" id="term">
                                            <label for="term">I Accept the <a class="text-primary" href="">Terms</a></label>
                                        </div>  -->
                                        <div class="form-group">
										  <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="signup" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-3">Sign Up</button>
                                        </div>
                                        <p class="text-muted f-w-600 text-center sign-in-click">Already A Member <a class="text-primary" onclick="loginpagedisplay()">Sign In</a></p>
                                    </div>
									</form>
                                </div>
                                <div class="p-5 mt-5 forgotpasswordon" style="display:none">
									<form class="md-float-material form-material" action="" method="post" novalidate>
                           
                                    <div class="">
                                        <h2 class="f-26 f-w-600 mt-2">Forgot Password</h2>
                                        <p class="text-muted f-14 f-w-600">Enter your email to reset your password.</p>
                                        <div class="form-group">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="email" name="" placeholder="Email">
                                        </div>
                                        <div class="form-group text-center">
											<input type="hidden" name="submit" value="forgot" />
											<button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                            <button type="button" class="btn bg-gray waves-effect waves-light" onclick="loginpagedisplay()">Cancel</button>
                                        </div>
                                    </div>
									</form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </div>


<body class="bg-white" style="background: url('assets/images/sign-in/bg.jpg'); background-size: cover;">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    
    <div class="" style="">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center">
                    <div class="ps-5 mob-pd">
					   <!--  <div class="text-center d-block">
						  <img class="img-100 mt-3" src="<?php echo $image; ?>">
					    </div> -->
                        <img class=" img-vector"  src="assets/images/sign-in/seating_image.png" style="width: 460px;">
                        <div class="mt-2 text-center">
                          <!--   <h2 class="text-dark f-30 f-w-600"><?php echo $image; ?></h2> -->
                            <p class="f-14 text-muted p-2 m-l-30 m-r-30 margin-0">Cubicle management system streamlines workspace allocation by enabling administrators to assign cubicles to employees efficiently. It also allows for the assignment of necessary equipment, ensuring each workspace is fully equipped and ready for use. This system enhances organization and maximizes resource utilization.</p>
                        </div>
                    </div>          
                </div>
                <div class="col-lg-6 text-center">
                    <div class="width-70">
                        <div class="card" style="min-height: auto;">
                            <div class="card-body pw-5">
                                <div class="p-3 custom-login-on mob-pd">
                                    <img class="img-100 mt-3" src="<?php echo $image; ?>">
                                    <h2 class="f-26 f-w-600 mt-2">Sign In</h2>
									  <?php 
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
                                    <!---   <p class="text-muted f-14 f-w-600">Your Social Campaigns</p>
                                   <div class="d-block mt-5">
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/google-icon.svg"> Sign in with Google</button>
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/apple-black.svg"> Sign in with Apple</button>
                                    </div>
                                    <div class="d-block mt-5">
                                        <h3 class="text-muted f-14">Or with email</h3>
                                    </div>   -->
								<form class="md-float-material form-material" action="" method="post" >
                                    
                                    <div class="text-left mt-5"> 
                                        <div class="form-group">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="chrUserName" id="chrUserName" required="" placeholder="User Name">
                                        </div>
                                        <div class="form-group mb-1">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="chrPassword" required="" placeholder="Password">
                                        </div>
                                        <?php if(CAPTCHA_YES_NO == 'YES'){ ?> 
                                  <center><img style="height: 50px;" src="captcha/captcha.php" /></center>
                                            </br>
                                <div class="form-group form-primary login-mean-class" >
                                    <input type="text" class="form-control" name="captcha" placeholder="Enter Captcha" required="required"/>
                                </div>
                            <?php } ?>
										<div class="row text-right">
                                            <a class="float-right f-w-600 text-primary forgot-pwd-open" href="#" onclick="fergotpassworddisplay()" >Forgot Password?</a>
                                        </div>
                                        <div class="form-group">
										<input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="Login" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4">Sign In</button>
                                        </div>
                                       <!--  <p class="text-muted f-w-600">Not a Member yet? <a class="text-primary" href="#" onclick="registerform()">Sign up</a></p> -->
                                    </div>
								</form>	
                                </div>
								<div class="p-5 regsterform" style="display:none">
								<form class="md-float-material form-material" action="" method="post" >
                                    <img class="img-100 mt-0 rentalji" src="<?php echo $image; ?>">
									 <h2 class="f-26 f-w-600 mt-4">Sign Up</h2>
                                  <!---   <p class="text-muted f-14 f-w-600">Your Social Campaigns</p>
                                   <div class="d-block mt-5">
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/google-icon.svg"> Sign in with Google</button>
                                        <button class="btn border f-w-600 text-muted f-14 b-radius-5 p-2"><img class="img-20 m-r-10" src="assets/images/sign-in/apple-black.svg"> Sign in with Apple</button>
                                    </div>
                                    <div class="d-block mt-4 text-center">
                                        <h3 class="text-muted f-14">Or with email</h3>
                                    </div>  ---> 
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
                                     <!---   <div class="form-group mb-1 text-left mt-2">
                                            <input type="checkbox" name="" id="term">
                                            <label for="trem">I Accept the <a class="text-primary" href="">Terms</a></label>
                                        </div>  -->
                                        <div class="form-group">
										  <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="signup" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-3">Sign Up</button>
                                        </div>
                                        <p class="text-muted f-w-600 text-center sign-in-click">Already A Member <a class="text-primary" href="#" onclick="loginpagedisplay()">Sign In</a></p>
                                    </div>
									</form>
                                </div>
                                <div class="p-5 forgotpasswordon mt-5" style="display:none">
								<form class="md-float-material form-material" action="" method="post" >
                                    <img class="img-100 mt-3" src="<?php echo $image; ?>">
                                    <div class="">
                                        <h2 class="f-26 f-w-600 mt-2">Forgot Password</h2>
                                        <p class="text-muted f-14 f-w-600">Enter your email to reset your password.</p>
                                        <div class="form-group">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="email" name="email" placeholder="Email" required="">
                                        </div>
                                        <div class="form-group text-center">
										<input type="hidden" name="submit" value="forgot" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                                            <button type="button" class="btn bg-gray waves-effect waves-light cancel-forgot-pwd" style="padding: 6px 15px;margin: 0;" onclick="loginpagedisplay()">Cancel</button>
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
    </div>

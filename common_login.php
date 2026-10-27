<body themebg-pattern="theme1" class="ligin-bg-white">
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
  <!-- Pre-loader end -->
    <section class="login-block bg-login-image">
        <!-- Container-fluid starts -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Authentication card start -->
                   
<!--begin:Sign In Form-->


                        <div class="text-center mt-2 mb-0">
                            <img src="assets/images/logo.png" alt="logo.png" style="width:150px"  >
                        </div>

                        <div class="auth-box custom-auth-box custom-login-on animated animate__animated animate__backInUp">
                                        <?php 
                            $error = isset($_GET['error']) && $_GET['error'] !== '' ? $_GET['error'] : '';
                            $dis_error['6001'] = "Thank you very much for confirming your registration, </br> please login using email ID.";
                            $dis_error['6002'] = "Incorrect username or password.  Please try again.";
                            $dis_error['6003'] = "We have sent a verification email, </br> please login into your email and verify.";
                            $dis_error['6004']  = "Your email verification is pending,</br>  kindly verify your email to access the system.";
                            $dis_error['6005']  = "The email is not registered with us,</br>  please register to access the system";
                            $dis_error['6006']  = "Details are sent to your registered email,</br>  please check";
                            $dis_error['6007']  = "The Captcha code does not match!";

                            if($error != '') { 
                                $errorMessage = isset($dis_error[$error]) ? $dis_error[$error] : $dis_error['6002'];
                            ?>
                                <div class="alert alert-warning alert-dismissible">
                                    <strong class="f-14"><?php echo $errorMessage; ?></strong>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php } ?>

                           <form class="md-float-material form-material" action="" method="post">
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12 mb-2">
                                        <h3 class="text-center txt-primary">Sign In</h3>
                                      <p class="text-center text-muted">Enter your details to login to your account</p>
                                    </div>
                                </div>
                                <div class="form-group form-primary login-mean-class">
                                    <input type="text" name="chrUserName" id="chrUserName" class="form-control" required="" placeholder="User Name">
                               </div>
                                <div class="form-group form-primary login-mean-class">
                                    <input type="password" name="chrPassword" class="form-control" required="" placeholder="Password">
                                </div>
                                <div class="row m-t-25 text-start">
                                    <div class="col-12">
                                        <div class="checkbox-fade fade-in-primary">
                                           <!-- <label class="form-label ">
                                                <input type="checkbox" value="">
                                                <span class="cr"><i class="cr-icon icofont icofont-ui-check txt-primary"></i></span>
                                                <span class="text-inverse text-muted">Remember me</span>
                                            </label>-->
                                        </div>
                                        <div class="forgot-phone text-end float-end">
                                            <a onclick="fergotpassworddisplay()" class="text-end text-muted cursor-pointer" > Forgot Password?</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12 text-center">
                                        <div class="">
                                            <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="Login" />
                                            <button type="submit" class="btn btn-primary btn-md waves-effect text-center b-radius m-b-20 fw-bold">LOGIN</button>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-inverse text-start">Don't have an account? <a onclick="registerform()" class="text-primary cursor-pointer"> <b> &nbsp; &nbsp; Register Here </b></a></p>
                            </div>
                          </form>
                        </div>
                        <div class="auth-box  custom-auth-box regsterform animated animate__animated animate__backInUp" style="display: none">
                           <form class="md-float-material form-material" action="" method="post" novalidate>
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12 mb-2">
                                        <h3 class="text-center txt-primary">Register</h3>
                                    </div>
                                </div>
                                <div class="form-group form-primary login-mean-class">
                                    <input type="text" name="name" id="chrUserName"  class="form-control" required="" placeholder="User Name">
                                   
                                </div>
                                 <div class="form-group form-primary login-mean-class">
                                    <input type="text" name="email" id="chrUserName" class="form-control" required="" placeholder="Email Id">
                                  
                                </div>
                                <div class="form-group form-primary login-mean-class">
                                    <input type="password" name="password" class="form-control" required="" placeholder="Password">
                              
                                </div>
                             
                                <div class="row m-t-30">
                                    <div class="col-md-12 text-center">
                                        <div class="">
                                            <input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                      <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                      <input type="hidden" name="submit" value="signup" />
                                            <button type="submit" class="btn btn-primary btn-md waves-effect b-radius text-center m-b-20 fw-bold">Register</button>
                                             <button type="button" onclick="loginpagedisplay()" class="btn btn-info btn-md b-radius waves-effect text-center m-b-20 fw-bold">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                           
                            </div>
                          </form>
                        </div>
                        <div class="auth-box custom-auth-box  forgotpasswordon animated animate__animated animate__backInUp" style="display: none">
                             <form class="md-float-material form-material" action="" method="post" novalidate>
                            <div class="card-block">
                                <div class="row m-b-20">
                                    <div class="col-md-12 mb-2">
                                        <h3 class="text-center txt-primary">Forgotten Password ?</h3>
                                    </div>
                                </div>
                                <div class="form-group form-primary login-mean-class">
                                    <input type="text" name="email"  class="form-control" required="" placeholder="Email Id">
                                  
                                </div>
                                <div class="row m-t-30">
                                    <div class="col-md-12 text-center">
                                        <div class="">
										<input type="hidden" name="submit" value="forgot" />
                                            <button type="button" class="btn btn-primary b-radius btn-md waves-effect text-center m-b-20 fw-bold">Request</button>
                                            <button type="button" onclick="loginpagedisplay()" class="btn btn-info btn-md b-radius waves-effect text-center m-b-20 fw-bold">Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          </form>
                        </div>
  
                        <!-- end of form -->
                    </div>
                    <!-- Authentication card end -->
                </div>
                <!-- end of col-sm-12 -->
            </div>
            <!-- end of row -->
        </div>
        <!-- end of container-fluid -->
    </section>
<!-- Required Jquery -->
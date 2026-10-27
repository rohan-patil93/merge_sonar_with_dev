
<body class="bg-white" style="background-image:linear-gradient(rgb(255 255 255 / 70%) 240px, #ffffff 0%);">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    <!-- <div class="mobile-view-logo bg-primary text-center">
        <img class="img-100 m-3" src="assets/images/sign-in/HRstaq-1.png">   
    </div> -->
    <div class="row fancy-sign-in">
        <div class="col-lg-5 bg-white">
            <div class="m-l-20 p-l-20 margin-0 mob-pd">
		        <div class="logo m-l-30"> <img src="<?php echo $image; ?>" style="width: :150px !important;" >  
                </div>
                <div class="p-5 custom-login-on pt-0 pb-0">
					<!-- <p class="text-muted f-w-600 text-center f-18 text-muted">Not a Member yet? <a class="text-primary" href="#" onclick="registerform()">Sign up</a></p> -->
           
                    <h2 class="f-26 f-w-600 mt-4">Sign In</h2>
					    <p class="text-muted f-14 f-w-600"><?php echo LOGIN_INFO['heding']; ?></p>
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
                        <form class="md-float-material form-material" action="" method="post">
                           
                        <div class="text-left mt-5 m-r-50">
                            <div class="form-group">
                                <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="chrUserName" id="chrUserName" required="" placeholder="User Name">
                            </div>
                            <div class="form-group mb-1">
                                <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password"  name="chrPassword" required="" placeholder="Password">
                            </div>
                            <?php if(CAPTCHA_YES_NO == 'YES'){ ?> 
                                  <center><img style="height: 50px;" src="captcha/captcha.php" /></center>
                                            </br>
                                <div class="form-group form-primary login-mean-class" >
                                    <input type="text" class="form-control" name="captcha" placeholder="Enter Captcha" required="required"/>
                                </div>
                            <!-- <div class="form-group mb-1">
                                <img src="captcha.php?rand=<?php echo rand();?>" id='captchaimg'><br>
                                <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray"  id="captcha_code" name="captcha_code" type="text" placeholder="Enter Captcha">
                                <br>
                                Can't read the image? click <a href='javascript: refreshCaptcha();' class="text-primary">here</a> to refresh.
                            </div> -->
                            <?php } ?>
                            <div class="row text-right">
                                <a class="float-right f-w-600 text-primary forgot-pwd-open" href="#" onclick="fergotpassworddisplay()" >Forgot Password?</a>
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <div class="form-group mb-0">
    								<input class="form-control" type="hidden"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                          <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                          <input type="hidden" name="submit" value="Login" />
                                        <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4 mob-btn">Sign In</button>
                                    </div>
                                </div>
                             <!--   <div class="col-lg-6 text-right mt-4">
                                    <b class="m-r-15">Or</b>
                                    <a class="m-r-10" href=""><img class="img-20" src="assets/images/sign-in/google-icon.svg"></a>
                                    <a class="m-r-10" href=""><i class="fa fa-facebook-official f-20 mt-2"></i></a>
                                    <a class="" href=""><img class="img-20" src="assets/images/sign-in/apple-black.svg"></a>
                                </div> -->
                            </div>
                           <!--  <div class="row mt-5">
                                <div class="col-lg-6">
                                    <select class="form-control border">
                                        <option>English</option>
                                        <option>Spanish</option>
                                        <option>German</option>
                                        <option>Japanese</option>
                                        <option>French</option>
                                    </select>
                                </div>
                            </div>  -->
                        </div>
					</form>
                </div>
				 <div class="p-5 regsterform" style="display:none">
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
                           <!-- <span class="text-muted f-12 text-left">Use 8 or more characters with a mix of letters, numbers &amp; symbols.</span>  -->
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
                          <!--   <div class="col-lg-6 text-right mt-4">
                                <b class="m-r-15">Or</b>
                                <a class="m-r-10" href=""><img class="img-20" src="assets/images/sign-in/google-icon.svg"></a>
                                <a class="m-r-10" href=""><i class="fa fa-facebook-official f-20 mt-2"></i></a>
                                <a class="" href=""><img class="img-20" src="assets/images/sign-in/apple-black.svg"></a>
                            </div> -->
                        </div>
                    </div>
					</form>
                </div>
                <div class="p-5 forgotpasswordon" style="display:none">
				 <form class="md-float-material form-material" action="" method="post" novalidate>
                            
                    <div class="">
                        <h2 class="f-26 f-w-600 mt-2">Forgot Password</h2>
                        <p class="text-muted f-14 f-w-600">Enter your email to reset your password.</p>
                        <div class="form-group">
                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="email" name="email" placeholder="Email">
                        </div>
                        <div class="form-group text-center">
						<input type="hidden" name="submit" value="forgot" />
                                        
                            <button type="submit" class="btn btn-primary waves-effect waves-light">Submit</button>
                            <button  type="button" class="btn bg-gray waves-effect waves-light cancel-forgot-pwd" onclick="loginpagedisplay()" >Cancel</button>
                        </div>
                    </div>
					</form>
                </div>
                
            </div>
        </div>
        <div class="col-lg-7 desktop-view-bg why-choose" style="">
            <!-- <img class="w-100 fancy-bg" src="assets/images/sign-in/recruitstaq_side_image.png" style=""> -->
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="why-box hrstaq-box float-end wow fadeInLeft">
                        <h6 class="text-custom-blue">Purchasing</h6>
                        <p class="m-0 mt-2">Streamlines procurement processes, supplier management, and purchase orders</p>
                        <strong>1</strong>
                    </div>
                    <div class="why-box VendorStaq-box float-start wow fadeInLeft">
                        <h6 class="text-custom-blue">Inventory</h6>
                        <p class="m-0 mt-2">Tracks stock levels, orders, and manages warehouse operations efficiently</p>
                        <strong>2</strong>
                    </div>
                    <div class="why-box RecruitStaq-box float-end wow fadeInLeft">
                        <h6 class="text-custom-blue">Sales</h6>
                        <p class="m-0 mt-2">Automates sales processes, manages leads, and tracks customer interactions<!--experience--></p>
                        <strong>3</strong>
                    </div>
                    <div class="why-box AssesStaq-box float-end wow fadeInLeft">
                        <h6 class="text-custom-blue">Quality Management</h6>
                        <p class="m-0 mt-2">Ensures product and service quality through inspections and compliance measures</p>
                        <strong>4</strong>
                    </div>
                </div>
                <div class="col-lg-3 why-choose-img text-center">
                    <img src="assets/images/why.jpg">
                </div>
                <div class="col-lg-4">
                    <div class="why-box AppreXA-box why-box-2 float-start text-end wow fadeInRight">
                        <h6 class="text-custom-blue">Finance</h6>
                        <p class="m-0 mt-2"> Manages financial transactions, budgets, and reporting for better financial control</p>
                        <strong>5</strong>
                    </div>
                    <div class="why-box BillsStaq-box float-end text-end wow fadeInRight">
                        <h6 class="text-custom-blue">Asset Management</h6>
                        <p class="m-0 mt-2">Tracks and manages company assets throughout their lifecycle for optimal utilization</p>
                        <strong>6</strong>
                    </div>
                    <div class="why-box SurverStaq-box float-start text-end wow fadeInRight">
                        <h6 class="text-custom-blue">Human Resources</h6>
                        <p class="m-0 mt-2">Handles employee data, payroll, benefits, and recruitment processes</p>
                        <strong>7</strong>
                    </div>
                    <div class="why-box TickDay-box float-start text-end wow fadeInRight">
                        <h6 class="text-custom-blue">Reporting and Analytics</h6>
                        <p class="m-0 mt-2">Generates real-time insights through customizable reports and data visualization</p>
                        <strong>8</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>


<style type="text/css">
.fancy-sign-in{/*margin-top: 120px;*/ min-height: 100vh; /*display: flex; flex-direction: column;*/ align-items: center; justify-content: center;}
.why-box{width: 95%; border: 2px solid #3772ff; border-radius: 10px; position: relative; padding: 10px; transition: 0.5s ease-in-out;
  box-shadow: 0px 5px 15px #cccccc; z-index: 1;}
.why-box h5{font-size: 21px; margin: 0;}
.why-box p{font-size: 13px; color: #333333; min-height: 59px;}
.why-box strong{ color: #ffffff; position: absolute; right: 0px; top: 0px; background: #3772ff; font-size: 23px; 
    border-radius: 0 0 0 5px;  padding: 0px 12px; font-weight: bold; display: none;}
.why-box.why-box-2 strong{right: auto; left: -40px; border-radius: 60px 60px 0px 60px;}
.why-box:hover{box-shadow: 0px 20px 45px #cccccc; margin-top: 40px !important;}


.why-choose-img img {
    width: 100%;
    border-radius: 50%;
    border: 20px solid #eeeeee;
    box-shadow: 0px 0px 0px 30px #cccccc;
    position: relative;
    -webkit-transition: all .5s linear;
    -moz-transition: all .5s linear;
    -o-transition: all .5s linear;
    transition: all .5s linear;
}
.why-choose-img img:hover {
    -webkit-transform: scale(1.1, 1.1);
    -moz-transform: scale(1.1, 1.1);
    -o-transform: scale(1.1, 1.1);
    -ms-transform: scale(1.1, 1.1);
    transform: scale(1.1, 1.1);
}
.hrstaq-box{
    margin-right: -80px !important;
/*    margin-top: 20px !important;*/
}
.AppreXA-box{
    margin-left: -80px;
}
.AppreXA-box strong{
    left: -40px;
    right: auto;
    border-radius: 60px 60px 0px
}
.AssesStaq-box{
    margin-right: -80px;
    margin-top: 20px !important;
}
.TickDay-box{
    margin-left: -80px;
    margin-top: 20px !important;
}
.TickDay-box strong{
    left: -40px;
    right: auto;
    border-radius: 60px 60px 0px
}
.VendorStaq-box{
    margin-top: 20px;
}
.BillsStaq-box{
    margin-top: 20px;
}
.BillsStaq-box strong{
    left: -40px;
    right: auto;
    border-radius: 60px 60px 0px
}
.SurverStaq-box{
    margin-top: 20px;
    margin-left: 15px;
}
.SurverStaq-box strong{
    left: -40px;
    right: auto;
    border-radius: 60px 60px 0px
}
.RecruitStaq-box{
    margin-top: 20px;
    margin-right: 15px;
}

.logo{margin-left: 45px;}
.logo img{width: 85px;}
.custom-login-on{margin-top: -15px;}


@media (max-width:991px){
    .fancy-sign-in {margin-top: 10px;}
    .m-l-20.p-l-20.margin-0.mob-pd {margin: 0; padding: 0;}
    .why-box{width:96%; display: block; margin: 0 auto 20px !important; float: none !important; }
    .why-box p{min-height: auto;}
    .why-choose-img img{width: 75%; margin: 30px 0px 50px;}
    .logo{margin: 0 !important;}
    .fancy-sign-in .custom-login-on{padding: 0 13px !important;}
    .mob-btn{width: 120px !important; margin-top: 0 !important;}
}

@media (max-width:768px){
    .logo{text-align: center;}
    .desktop-view-bg.why-choose{display: block;}
}


</style>
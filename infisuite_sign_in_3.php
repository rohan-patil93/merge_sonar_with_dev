
<style>
    .login_container{
        height: 100vh;
        display: flex;
    }
    .login_left, .login_right{
        
    }    
    .logo_width{
        width: 100px;
    }
    .login_left{
        width: 60%;
        float: right;
        vertical-align: middle;
        background-image: url('assets/images/sign-in/login-banner-3.jpg');
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        min-height: 100vh;
    }
    .login_right{
        width: 40%;
        float: left;
        vertical-align: middle;
    }
    .login_container .width-70 {
        width: 70%;
        margin: 0 auto;
        float: none;
    }
    .password-wrapper {
        position: relative;
    }

    .password-wrapper input {
        padding-right: 40px;
    }

    .toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #666;
    }

    .toggle-password:hover {
        color: #000;
    }
    @media (max-width:1440px){
        .login_left{  background-position: center;}
        
    }

    @media (max-width:1299px){
        /*.login_container{display: block;}  */
        .login_container .width-70{width: 90%;}
        /*.width-70 .card{min-height: 100vh;}*/
        .login_left{background-size: contain;}
    }

    @media (max-width:991px){
        .custom-login-on{width: 100%; margin: 0;}
    }
    @media (max-width: 850px){
        .login_container{display: block;}
        .login_right{width: 100%; margin-top: 20px;}
        .login_left{ width:100%; margin-top: 20px;}
    }
    @media (max-width: 576px){
        .login_left{min-height: 50vh;}
    }
    @media (max-width: 480px){
        .login_left{min-height: 45vh;}
    }
    @media (max-width: 380px){
        .login_left{min-height: 30vh;}
    }
    @media (max-width: 340px){
        .login_left{min-height: 25vh;}
    }

</style>
<script type="text/javascript">
    function togglePassword() {
        var password = document.getElementById("chrPassword");
        var eyeIcon = document.getElementById("eyeIcon");

        if (password.type === "password") {
            password.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            password.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>


<body class="bg-white" style="background: url('assets/images/sign-in/bg.jpg'); background-size: cover;">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-bar"></div>
    </div>
    
    <div class="" style="">
        <div class="login_container align-items-center">
            <div class="login_right text-center">
                <div class="width-70">
                    <div class="card mb-0" style="min-height: auto;">
                        <div class="card-body pw-5">
                            <div class="p-3 custom-login-on mob-pd">
                                <img class="mt-2 mb-3" src="<?php echo $image; ?>">
                                <h2 class="f-26 f-w-600">Welcome Back</h2>
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
                                
							    <form class="md-float-material form-material" action="" method="post" >
                                
                                    <div class="text-left mt-4"> 
                                        <div class="form-group">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="text" name="chrUserName" id="chrUserName" required="" placeholder="User Name">
                                        </div>
                                        <!-- <div class="form-group mb-1">
                                            <input class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" type="password" name="chrPassword" required="" placeholder="Password">
                                        </div> -->
                                        <div class="password-wrapper">
                                            <input type="password" id="chrPassword" class="form-control p-3 b-radius-5 f-w-600 text-muted bg-gray" required="" placeholder="Enter Password" name="chrPassword">
                                            <span class="toggle-password" onclick="togglePassword()">
                                                <i class="fa fa-eye" id="eyeIcon"></i>
                                            </span>
                                        </div>
                                        <?php if(CAPTCHA_YES_NO == 'YES' && $subdomain !='uat' ){ ?> 
                                        <center><img style="height: 50px;" src="captcha/captcha.php" /></center>
                                        <div class="form-group form-primary login-mean-class" >
                                            <input type="text" class="form-control" name="captcha" placeholder="Enter Captcha" required="required"/>
                                        </div>
                                        <?php } ?>
										<div class="row text-right">
                                            <a class="float-right f-w-600 text-primary forgot-pwd-open" href="#" onclick="fergotpassworddisplay()" >Forgot Password?</a>
                                        </div>
                                        <div class="form-group">
									        <input class="form-control" type="hidden" id="<?php echo $subdomain; ?>"  name="ip" autocomplete="off" value="<?php echo getIPAddress();?>" >
                                            <input class="form-control" type="hidden"  name="browser" autocomplete="off" value="<?php echo getBrowser();?>" >
                                            <input type="hidden" name="submit" value="Login" />
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100 mt-4">Sign In</button>
                                        </div>
                                    </div>
							    </form>	
                            </div>
							<div class="p-5 regsterform" style="display:none">
							    <form class="md-float-material form-material" action="" method="post" >
                                    <img class="img-100 mt-0 rentalji" src="<?php echo $image; ?>">
								    <h2 class="f-26 f-w-600 mt-4">Sign Up</h2>
                              
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
                                    <img class="mt-3 mb-3" src="<?php echo $image; ?>">
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
            <div class="login_left text-center" style="">
                <div class="ps-5 mob-pd">
                    
                </div>          
            </div>
        </div>
    </div>
</body>
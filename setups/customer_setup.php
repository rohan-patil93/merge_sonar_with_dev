<div id="setup<?php echo $spage['seq'][0]; ?>" class="setuphide" style="display:none;">
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">Customer Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button" onclick="nextsetupview(<?php echo $spage['seq'][1]; ?>)"><i class="ti-arrow-right"></i>Start Customer Setup</button>
                    </div>
                </div>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $previousseqnumber; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview(<?php echo $spage['seq'][1]; ?>)"><i class="ti-arrow-right"></i>  Start Customer Setup</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 text-center position-relative">
            <div class="top-logo p-2">
                <a href="">
                    <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                </a>
            </div>
            <div class="text-position text-start">
                <span class="f-30 f-w-600">Setup</span>
                <h2 class="f-50 f-w-600">Customer</br> Setup</h2>
                <p class="f-14 f-w-600"><?php echo $system_message['welcome_tab']['description']; ?></p>
                <button class="btn btn-primary">infistaq.com</button>
            </div>
            <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
        </div>
    </div>
</div>
<div id="setup<?php echo $spage['seq'][1]; ?>" class="setuphide" style="display:none;" >
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Customer</h3>
                    <div class="row">
                        <div class="col-lg-3">
                            <span class="form-label">First Name</span>
                            <input aria-label="Location Name[]" type="text" class="form-control" name="location_name[]">
                        </div>
                        <div class="col-lg-3">
                            <span class="form-label">Last Name</span>
                            <input aria-label="Short Name[]" type="text" class="form-control" name="short_name[]">
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Email Id</span>
                            <input aria-label="Short Name[]" type="text" class="form-control" name="short_name[]">
                        </div>
                    </div>
                    <div id="newaddmorelocation"></div>
                    <div class="mt-3 mb-5">
                        <button type="button"  class="btn btn-primary" onclick="addMoreLocation()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $spage['seq'][0]; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview(<?php echo $nextseqstartnumber; ?>)"><i class="ti-arrow-right"></i>  Next Step</button>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center position-relative">
                <div class="top-logo p-2">
                    <a href="">
                        <img class="img-100" src="assets/images/sign-in/<?php echo LOGIN_INFO['logo']; ?>" alt="Logo">
                    </a>
                </div>
                <div class="text-position text-start">
                    <span class="f-30 f-w-600">Setup</span>
                    <h2 class="f-50 f-w-600"><?php echo $system_message['locations']['header']; ?></h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>

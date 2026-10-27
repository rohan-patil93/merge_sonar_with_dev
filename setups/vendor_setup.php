<?php
// }
//     }
// }
?>
<script type="text/javascript">
    var maxvendors = 100;
    function addMoreVendors(){
        maxvendors = maxvendors+1;
        var id = 'rowvendor_'+maxvendors;

        var errorvendorrowid = 'errorrowvendor_'+maxvendors;
        var vendorrowid = 'rowvendor_'+maxvendors;
        var email_id = "email_id";
        var html = "";
        html += '<div class="row mt-3" id="'+id+'"><div class="col-lg-3"><input aria-label="First Name[]" type="text" class="form-control" name="first_name[]"><input type="hidden" name="user_type[]" value="3"></div>';

        html += '<div class="col-lg-3"><input aria-label="Last Name[]" type="text" class="form-control" name="last_name[]"></div>';

        html += '<div class="col-lg-5"><input aria-label="User Email[]" type="text" class="form-control allemails" name="user_email[]" data-errorcls="'+errorvendorrowid+'" id="'+vendorrowid+'"  onblur="checkdomainname(\''+vendorrowid+'\',\''+email_id+'\',\''+errorvendorrowid+'\')"><input type="hidden" class="form-control " name="role_id[]" value="<?php echo VENDOR_ROLE; ?>"><span class="text-danger f-12" id="'+errorvendorrowid+'"  style="display:none">Email Id Already Register</span></div>';

        html += '<div class="col-lg-1 p-t-25"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup(\''+id+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';
        $("#newaddmorevendors").append(html);
    }

    function checkdomainname(id,field,type){
        var value = $("#"+id).val();
        $(".allemailshide").hide();
        $(".submituserbtn").removeClass("disabled");
          $.ajax({
              url: "ajax.php",
              data:'page=checkdomainavailability&domain='+value+'&field='+field,
              type: "POST",
              success:function(data){
                  console.log(data);
                var res = JSON.parse(data);
                $("#"+type).hide();
                if(res.res == 'true'){
                  $("#"+type).show();
                   $(".submituserbtn").addClass("disabled");
                }else{
                    var errerdata = [];
                     $(".allemails").each(function(index) {
                        var arrarowid = $(this).attr("data-errorcls");
                        if(type !=arrarowid){
                            errerdata.push($(this).val());
                           }
                    });
                    if($.inArray(value, errerdata)>=0){
                          $("#"+type).show();
                           $(".submituserbtn").addClass("disabled");
                       }
                }
              },
              error:function (){}
          });
    }

</script>
<div id="setup<?php echo $pagedetail[0]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[0]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">Vendors Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button" onclick="nextsetupview(<?php echo $pagedetail[1]['sequence']; ?>)"><i class="ti-arrow-right"></i>Start Vendors Setup</button>
                    </div>
                </div>
                <form id="submit_form_id_<?php echo $pagedetail[0]['sequence']; ?>" enctype='multipart/form-data'>
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['0']['sequence']; ?>">
                </form>
                <div class="footer_btn ">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $previousseqnumber; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>  Start Vendors Setup</button>
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
                <h2 class="f-50 f-w-600">Vendors</br> Setup</h2>
                <p class="f-14 f-w-600"><?php echo $system_message['welcome_tab']['description']; ?></p>
                <button class="btn btn-primary">infistaq.com</button>
            </div>
            <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[1]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[1]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Vendors</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[1]['sequence']; ?>" enctype='multipart/form-data'>
                        <div class="row ">
                            <div class="col-lg-3">
                                <span class="form-label">First Name</span>
                            </div>
                            <div class="col-lg-3">
                                <span class="form-label">Last Name</span>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-label">Email Id</span>
                            </div>
                        </div>
                        <div id="newaddmorevendors">
                            <?php $im=1;

                        $vendors[] = array();

                        foreach($vendors as $vend){
                        $rowid = 'rowdivision_'.$im;
                        $errorvendorrowid = 'errorrowvendor_'.$im;
                        $vendorrowid = 'rowvendor_'.$im;
                        ?>
                            <div class="row" id="<?php echo $rowid; ?>">
                                <div class="col-lg-3"><input aria-label="First Name[]" type="text" class="form-control" name="first_name[]"><input type="hidden" name="user_type[]" value="3"></div>

                                <div class="col-lg-3"><input aria-label="Last Name[]" type="text" class="form-control" name="last_name[]"></div>

                                <div class="col-lg-5">
                                    <input aria-label="User Email[]" type="text" class="form-control allemails" name="user_email[]" data-errorcls="<?php echo $errorvendorrowid; ?>" id="<?php echo $vendorrowid; ?>" onblur="checkdomainname('<?php echo $vendorrowid; ?>','email_id','<?php echo $errorvendorrowid; ?>')">
                                    <input type="hidden" class="form-control " name="role_id[]" value="<?php echo VENDOR_ROLE; ?>">
                                    <span class="text-danger f-12" id="<?php echo $errorvendorrowid; ?>"  style="display:none">Email Id Already Register</span>
                                </div>
                                <div class="col-lg-1 p-t-25">
                                    <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                                </div>
                            </div>
                    <?php } ?>
                        </div>
                        <input type="hidden" name="transaction_table" value="vendors">
                        <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['1']['sequence']; ?>">
                    </form>
                    <div class="mt-3 mb-5 ">
                        <button type="button"  class="btn btn-primary" onclick="addMoreVendors()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail['0']['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary submituserbtn" onclick="nextsetupview('<?php echo $nextseqstartnumber; ?>','<?php echo $pagedetail['1']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>

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
                    <h2 class="f-50 f-w-600">Vendor Setups</h2>
                    <p class="f-14 f-w-600"></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>

<?php
    $divisions =  $result['divisions'];
    $departments =  $result['departments'];
    $cost_centers =  $result['cost_centers'];
    $locations =  $result['locations'];
    $companies = $result['companies'];
    $config = $result['config'];
?>
<script type="text/javascript">
    var amdivision = 100;
    function addMoreDivisions(){
        amdivision = amdivision+1;
        var id = 'rowdivision_'+amdivision;
        html = '<div class="row mt-3" id="'+id+'"><div class="col-lg-6"><input aria-label="Division Name[]" type="text" class="form-control" name="division_name[]" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></div><div class="col-lg-5"><textarea aria-label="Division Description[]" type="text" class="form-control" name="division_description[]"></textarea></div><div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup(\''+id+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';
        $("#newaddmoredivision").append(html);
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    var maxdepartment = 100;
    function addMoreDepartments(){
        $(".department_show").show();
        $(".department_show_empty").hide();
        maxdepartment = maxdepartment+1;
        var rowid = 'rowdepartment_'+maxdepartment;
        $.ajax({
            url: "ajax.php",
            data:'page=getaddmoresetupdepartment&rowid='+rowid,
            type: "POST",
            success: function(response) {
                $("#newaddmoredepartments").append(response);
                 $(".js-example-basic-single").select2();
            }
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    var maxcostcenter = 100;
    function addMoreCostCenters(){
        $(".cost_center_show").show();
        $(".cost_center_show_empty").hide();
        maxcostcenter = maxcostcenter+1;
        var rowid = 'rowcostcenter_'+maxcostcenter;
        $.ajax({
            url: "ajax.php",
            data:'page=getaddmoresetupcostcenter&rowid='+rowid,
            type: "POST",
            success: function(response){
                $("#newaddmorecostcenter").append(response);
                $(".js-example-basic-single").select2();
            }
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
    var maxlocations = 100;
    function addMoreLocation(){
        maxlocations = maxlocations+1;
        var id = 'rowlocations_'+maxlocations;
        html = '<div class="row mt-3" id="'+id+'"><div class="col-lg-6"><input aria-label="Location Name[]" type="text" class="form-control" name="location_name[]" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></div><div class="col-lg-5"><input aria-label="Short Name[]" type="text" class="form-control" name="short_name[]"></div><div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''+id+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div></div>';
        $("#newaddmorelocation").append(html);
        $('[data-bs-toggle="tooltip"]').tooltip();
    }
</script>
<div id="setup<?php echo $pagedetail[0]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[0]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">Orgnization Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>Start Orgnization Setup</button>
                    </div>
                </div>
                <form id="submit_form_id_<?php echo $pagedetail[0]['sequence']; ?>" enctype='multipart/form-data'>
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['0']['sequence']; ?>">
                </form>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $previousseqnumber; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>','<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-right"></i>  Start Orgnization Setup</button>
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
                <h2 class="f-50 f-w-600">Orgnization</br> Setup</h2>
                <p class="f-14 f-w-600"><?php echo $system_message['welcome_tab']['description']; ?></p>
                <button class="btn btn-primary">infistaq.com</button>
            </div>
            <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[1]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[1]['sequence'])?'':'style="display:none;"'; ?> >
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Division</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[1]['sequence']; ?>" enctype='multipart/form-data'>
                        <?php $im=1;
                        if(count((array)$divisions) ==0){
                            $divisions[] = array();
                        }
                         foreach($divisions as $divis){ ?>

                        <?php $rowid = 'rowdivision_'.$im;  ?>
                    <div class="row mt-2" id="<?php echo $rowid; ?>">
                        <div class="col-lg-6">
                            <span class="form-label">Division Name</span>
                            <input type="hidden" class="form-control" name="old_division_id[]" value="<?php echo $divis['id']; ?>">
                            <input aria-label="Division Name[]" type="text" class="form-control" name="division_name[]" value="<?php echo $divis['division_name']; ?>">
                        </div>
                        <div class="col-lg-5">
                            <span class="form-label">Description</span>
                            <textarea aria-label="Division Description[]" type="text" class="form-control" name="division_description[]"><?php echo $divis['division_description']; ?></textarea>
                        </div>
                        <div class="col-lg-1">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>

                    </div>
                <?php $im++;
} ?>
                    <div id="newaddmoredivision"></div>
                    <div class="mt-3 mb-5">
                        <button type="button"  class="btn btn-primary" onclick="addMoreDivisions()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="divisions">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['1']['sequence']; ?>">
                </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail[0]['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[2]['sequence']; ?>','<?php echo $pagedetail['1']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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
                    <span class="f-30 f-w-600">Orgnization Setup</span>
                    <h2 class="f-50 f-w-600">Division </br>Information</h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[2]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[2]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Department</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[2]['sequence']; ?>" enctype='multipart/form-data'>
                    <div class="row department_show" <?php if(count((array)$departments) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <div class="col-lg-6">
                            <span class="form-label">Department Name</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Division</span>
                        </div>
                    </div>
                    <div id="newaddmoredepartments">
                            <?php $im=1;
                        if(count((array)$departments) !='0'){
                             foreach($departments as $depart){
                                 $rowid = 'rowdepartment_'.$im;  ?>
                    <div class="row mt-2" id="<?php echo $rowid; ?>">
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" name="old_department_id[]" value="<?php echo $depart['id']; ?>">
                            <input aria-label="Department Name[]" type="text" class="form-control" name="department_name[]" value="<?php echo $depart['department_name']; ?>">
                        </div>
                        <div class="col-lg-5">
                            <select aria-label="Division []" class="js-example-basic-single" name="division_id[]">
                                <?php  foreach($divisions as $divis){ ?>
                                    <option value='<?php echo $divis['id']; ?>' <?php echo $divis['id']==$depart['division_id']?"selected":""; ?>><?php echo $divis['division_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>

                    </div>
                <?php $im++;
}
}else{ ?>

                        <div class="card-px text-center department_show_empty">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Department</p>
                            <button type="button" onclick="addMoreDepartments()" class="btn btn-primary"><i class="icofont icofont-plus icon-white "></i> Add More Department</button>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="mt-3 mb-5 department_show"  <?php if(count((array)$departments) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <button type="button"  class="btn btn-primary" onclick="addMoreDepartments()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="departments">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['2']['sequence']; ?>">
                </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail[1]['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[3]['sequence']; ?>','<?php echo $pagedetail['2']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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
                    <span class="f-30 f-w-600">Orgnization Setup</span>
                    <h2 class="f-50 f-w-600">Department </br>Information</h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[3]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[3]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Cost Center</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[3]['sequence']; ?>" enctype='multipart/form-data'>
                    <div class="row cost_center_show" <?php if(count((array)$cost_centers) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <div class="col-lg-6">
                            <span class="form-label">Cost Center Name</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Department</span>
                        </div>
                    </div>
                    <div id="newaddmorecostcenter">
                            <?php $im=1;
                        if(count((array)$cost_centers) !='0'){
                             foreach($cost_centers as $costcent){
                                 $rowid = 'rowcostcenter_'.$im;  ?>
                    <div class="row mt-2" id="<?php echo $rowid; ?>">
                        <div class="col-lg-6">
                            <input type="hidden" class="form-control" name="old_cost_center_id[]" value="<?php echo $costcent['id']; ?>">
                            <input aria-label="Cost Center Name[]" type="text" class="form-control" name="cost_center_name[]" value="<?php echo $costcent['cost_center_name']; ?>">
                        </div>
                        <div class="col-lg-5">
                            <select aria-label="Department []" class="js-example-basic-single" name="department_id[]">
                                <?php  foreach($departments as $deprt){ ?>
                                    <option value='<?php echo $deprt['id']; ?>' <?php echo $deprt['id']==$costcent['department_id']?"selected":""; ?>><?php echo $deprt['department_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>

                    </div>
                <?php $im++;
}
}else{ ?>

                        <div class="card-px text-center cost_center_show_empty">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Cost Center</p>
                            <button type="button" onclick="addMoreCostCenters()" class="btn btn-primary"><i class="icofont icofont-plus icon-white "></i> Add More Cost Center</button>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="mt-3 mb-5 cost_center_show"  <?php if(count((array)$cost_centers) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <button type="button"  class="btn btn-primary" onclick="addMoreCostCenters()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="cost_centers">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['3']['sequence']; ?>">
                </form>

                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail[2]['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail[4]['sequence']; ?>','<?php echo $pagedetail['3']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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
                    <span class="f-30 f-w-600">Orgnization Setup</span>
                    <h2 class="f-50 f-w-600">Cost Center </br>Information</h2>
                    <p class="f-14 f-w-600"><?php echo $system_message['locations']['description']; ?></p>
                    <button class="btn btn-primary">infistaq.com</button>
                </div>
                <img class="" src="assets/images/setup_welcome_compliance.png" alt="Banner">
            </div>
        </div>
    </div>
</div>
<div id="setup<?php echo $pagedetail[4]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[4]['sequence'])?'':'style="display:none;"'; ?> >
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Locations</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[4]['sequence']; ?>" enctype='multipart/form-data'>
                        <?php
                            if(count((array)$locations) ==0){
                                $locations[] = array();
                            }
                        ?>
                    <div class="row">
                        <div class="col-lg-6">
                            <span class="form-label">Locations Name</span>
                        </div>
                        <div class="col-lg-6">
                            <span class="form-label">Short Name</span>
                        </div>
                    </div>
                    <div id="newaddmorelocation">
                        <?php $im = 1;
                            foreach($locations as $loction){
                                 $rowid = 'rowlocations_'.$im;
                         ?>
                        <div class="row mt-2" id="<?php echo $rowid; ?>">
                            <div class="col-lg-6">
                                <input type="hidden" class="form-control" name="old_cost_center_id[]" value="<?php echo $loction['id']; ?>">
                                <input aria-label="Location Name[]" type="text" class="form-control" name="location_name[]" value="<?php echo $loction['location_name']; ?>">
                            </div>
                            <div class="col-lg-5">
                                <input aria-label="Short Name[]" type="text" class="form-control" name="short_name[]" value="<?php echo $loction['short_name']; ?>">
                            </div>
                            <div class="col-lg-1">
                                <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                            </div>
                        </div>
                        <?php $im++;
} ?>

                    </div>
                    <input type="hidden" name="transaction_table" value="locations">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['4']['sequence']; ?>">
                </form>
                    <div class="mt-3 mb-5">
                        <button type="button"  class="btn btn-primary" onclick="addMoreLocation()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $pagedetail[3]['sequence']; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $nextseqstartnumber; ?>','<?php echo $pagedetail['4']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>

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

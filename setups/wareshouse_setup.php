<?php
$locations =  $result['locations'];
$warehouses =  $result['warehouses'];
$stock_adjustment_reasons =  $result['stock_adjustment_reasons'];
$stock_adjustment_reasons_condition = array('1'=>'ADD','0'=>'MINS');
?>
<script type="text/javascript">
    var maxwarehouse = 100;
    function addMoreWarehouse(){
        $(".warehouse_show").show();
        $(".warehouse_show_empty").hide();
        maxwarehouse = maxwarehouse+1;
        var rowid = 'rowwarehouse_'+maxwarehouse;
        $.ajax({
            url: "ajax.php",
            data:'page=getaddmoresetupwarehouse&rowid='+rowid,
            type: "POST",
            success: function(response){
                $("#newaddmorewarehouse").append(response);
                $(".js-example-basic-single").select2();
            }
        });
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

    var maxsar = 100;
    function addMoreStockAdjustmentReason(){
        maxsar = maxsar+1;
        $(".stock_adjustment_reasons_show").show();
        $(".stock_adjustment_reasons_show_empty").hide();
        var id = 'stock_adjustment_reasons_'+maxsar;
        var html ="";
        html += '<div class="row mt-3" id="'+id+'">';
        html += '<div class="col-lg-6"><input aria-label="Reason[]" type="text" class="form-control" name="reason[]" value=""></div>';

        html += '<div class="col-lg-5"><select aria-label="Upordown[]" class="js-example-basic-single" name="upordown[]"><?php foreach($stock_adjustment_reasons_condition as $kyval=>$val){ ?><option value="<?php echo $kyval; ?>"><?php echo $val; ?></option><?php
} ?></select></div>';
        html += '<div class="col-lg-1"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeOrganizationSetup(\''+id+ '\')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></div>';
        html += '</div>';

        $("#newaddmorestockadjustmentreason").append(html);
        $(".js-example-basic-single").select2();
        $('[data-bs-toggle="tooltip"]').tooltip();
    }

</script>
<div id="setup<?php echo $pagedetail[0]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[0]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="row align-items-center">
        <div class="col-lg-6 bg-white" style="">
            <div class="center-sec" style="">
                <div class="" style="vertical-align: middle;align-items: center;">
                    <div class="p-5">
                        <h2 class="f-w-600">Wareshouse Setup</h2>
                        <p class="f-w-500 mt-4">
                            <span class="d-block"><span class="f-w-600 f-18 text-primary">Congratulations</span> on taking the first step towards streamlining your business operations and maximizing efficiency. With Infistaq, you're in for a game-changing experience.</span>

                            <span class="d-block mt-3">Get ready to unlock a world of possibilities where managing your enterprise becomes seamless and productive. Our intuitive interface, comprehensive features, and robust tools are designed to elevate your business to new heights.    </span>

                            <span class="d-block mt-3"> From finance to HR, inventory to analytics, Infistaq is your all-in-one solution, tailored to meet your specific needs. We're excited to embark on this journey with you and help your business thrive.</span>

                            <span class="d-block mt-3">Feel free to explore our platform, and don't hesitate to reach out if you have any questions. Welcome aboard, and here's to a successful partnership with Infistaq! </span>
                        </p>
                        <button class="btn btn-primary mt-3" type="button" onclick="nextsetupview('<?php echo $pagedetail['1']['sequence']; ?>','<?php echo $pagedetail['0']['sequence']; ?>')"><i class="ti-arrow-right"></i>Start Wareshouse Setup</button>
                    </div>
                </div>
                <form id="submit_form_id_<?php echo $pagedetail['0']['sequence']; ?>" enctype='multipart/form-data'>
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['0']['sequence']; ?>">
                </form>
                <div class="footer_btn">
                    <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview('<?php echo $previousseqnumber; ?>')"><i class="ti-arrow-left"></i> Previous Step</button>
                    <button type="button"  class="btn btn-primary"  onclick="nextsetupview('<?php echo $pagedetail['1']['sequence']; ?>','<?php echo $pagedetail['0']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Start Wareshouse Setup</button>

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
                <h2 class="f-50 f-w-600">Wareshouse</br> Setup</h2>
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
                    <h3 class="f-w-600 mb-5">Wareshouse</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[1]['sequence']; ?>" enctype='multipart/form-data'>
                    <div class="row warehouse_show" <?php  if(count((array)$warehouses) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <div class="col-lg-5">
                            <span class="form-label">warehouse Name</span>
                        </div>
                        <div class="col-lg-3">
                            <span class="form-label">Short Name</span>
                        </div>
                        <div class="col-lg-4">
                            <span class="form-label">location</span>
                        </div>

                    </div>
                    <div id="newaddmorewarehouse">
                            <?php $im=1;
                        if(count((array)$warehouses) !='0'){
                             foreach($warehouses as $wareh){
                                 $rowid = 'rowwarehouse_'.$im;  ?>
                    <div class="row mt-3" id="<?php echo $rowid; ?>">
                        <div class="col-lg-5">
                            <input type="hidden" class="form-control" name="warehouse_id[]" value="<?php echo $wareh['id']; ?>">
                            <input aria-label="Warehouse Name[]" type="text" class="form-control" name="warehouse_name[]" value="<?php echo $wareh['warehouse_name']; ?>">
                        </div>
                        <div class="col-lg-2">
                            <input aria-label="Warehouse Short Name[]" type="text" class="form-control" name="warehouse_short_name[]" value="<?php echo $wareh['warehouse_short_name']; ?>">
                        </div>
                        <div class="col-lg-3">
                            <select aria-label="Location []" class="js-example-basic-single" name="location_id[]">
                                <?php  foreach($locations as $loc){ ?>
                                    <option value='<?php echo $loc['id']; ?>' <?php echo $loc['id']==$wareh['location_id']?"selected":""; ?>><?php echo $loc['location_name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-1 ">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>

                    </div>
                <?php $im++;
}
}else{ ?>

                        <div class="card-px text-center warehouse_show_empty">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Warehouse</p>
                            <button type="button" onclick="addMoreWarehouse()" class="btn btn-primary"><i class="icofont icofont-plus icon-white "></i> Add More Warehouse</button>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="mt-3 mb-5 warehouse_show"  <?php if(count((array)$warehouses) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <button type="button"  class="btn btn-primary" onclick="addMoreWarehouse()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="warehouses">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['1']['sequence']; ?>">
                </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $pagedetail['0']['sequence']; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview('<?php echo $pagedetail['2']['sequence']; ?>','<?php echo $pagedetail['1']['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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
<div id="setup<?php echo $pagedetail[2]['sequence']; ?>" class="setuphide" <?php echo ($lastsetup_page_id == $pagedetail[2]['sequence'])?'':'style="display:none;"'; ?>>
    <div class="taskda-bg">
        <div class="row align-items-center">
            <div class="col-lg-6 p-l-50 p-r-50 bg-white">
                <div class="m-20">
                    <h3 class="f-w-600 mb-5">Stock Adjustment Reason</h3>
                    <form id="submit_form_id_<?php echo $pagedetail[2]['sequence']; ?>" enctype='multipart/form-data'>
                    <div class="row stock_adjustment_reasons_show" <?php  if(count((array)$stock_adjustment_reasons) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <div class="col-lg-6">
                            <span class="form-label">Reason Name</span>
                        </div>
                        <div class="col-lg-5">
                            <span class="form-label">Condition</span>
                        </div>

                    </div>
                    <div id="newaddmorestockadjustmentreason">
                            <?php $im=1;
                        if(count((array)$stock_adjustment_reasons) !='0'){
                             foreach($stock_adjustment_reasons as $wareh){
                                 $rowid = 'stock_adjustment_reasons_'.$im;  ?>
                    <div class="row mt-3" id="<?php echo $rowid; ?>">
                        <div class="col-lg-6">
                            <input aria-label="Reason[]" type="text" class="form-control" name="reason[]" value="<?php echo $wareh['reason']; ?>">
                        </div>
                        <div class="col-lg-5">
                            <select aria-label="Upordown[]" class="js-example-basic-single" name="upordown[]">
                                <?php  foreach($stock_adjustment_reasons_condition as $kyval=>$val){ ?>
                                    <option value='<?php echo $kyval; ?>' <?php echo $kyval==$wareh['upordown']?"selected":""; ?>><?php echo $val; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-1 ">
                            <i class="ti-trash text-danger" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" onclick="removeorganizationSetup('<?php echo $rowid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></div>

                    </div>
                <?php $im++;
}
}else{ ?>

                        <div class="card-px text-center stock_adjustment_reasons_show_empty">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Stock Adjustment Reason</p>
                            <button type="button" onclick="addMoreStockAdjustmentReason()" class="btn btn-primary"><i class="icofont icofont-plus icon-white "></i> Add More Stock Adjustment Reason</button>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                    </div>
                    <div class="mt-3 mb-5 stock_adjustment_reasons_show"  <?php if(count((array)$stock_adjustment_reasons) =='0'){ ?>style="display:none;" <?php
} ?>>
                        <button type="button"  class="btn btn-primary" onclick="addMoreStockAdjustmentReason()"><i class="ti-plus"></i>  Add More</button>
                    </div>
                    <input type="hidden" name="transaction_table" value="stock_adjustment_reasons">
                    <input type="hidden" name="setup_page_id" value="<?php echo $pagedetail['2']['sequence']; ?>">
                </form>
                    <div class="footer_btn">
                        <button type="button"  class="btn btn-success m-r-50" onclick="nextsetupview(<?php echo $pagedetail[1]['sequence']; ?>)"><i class="ti-arrow-left"></i> Previous Step</button>
                        <button type="button"  class="btn btn-primary" onclick="nextsetupview(<?php echo $nextseqstartnumber; ?>,'<?php echo $pagedetail[2]['sequence']; ?>')"><i class="ti-arrow-right"></i>  Next Step</button>
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

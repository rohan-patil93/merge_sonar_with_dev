<?php
    $_SESSION['apply_js']['multiselest'] = 1;

    if(isset($_POST['submit'])){
       $_POST['company_id'] = $_SESSION['company_id'];
       $_POST['is_active'] = 0;
       $_POST['created_by'] = $_SESSION['userid'];
       $_POST['created_date'] = nowDate();
       $url = API."?mode=addsalarycontractemployeepay&method=post";
       $response = file_post_contents($url, $_POST, 'insert');
       $response3 = json_decode($response,1);
       if($response){
            header("location:index.php?option=COM_LIST_CONTRACT_EMPLOYEE_PAY");
       }
    }
    $main_ids = encodeId($_GET['Rid']);
    $main_id = explode(":",$main_ids);
    $rid = $main_id['1'];
    $stpurl = API."?mode=togetcontractemployeepaydetails&cep_id=".$rid;
    $salarytmpdata1 = callAPI($stpurl);
    $data = json_decode($salarytmpdata1,1);
    $salarydata = $data['row'];
?>
<script type="text/javascript">
    function changejoiningboun(){
        var is_joining_bouns_val =  $("#is_joining_bouns_val").val();
         console.log("is_joining_bouns_val"+is_joining_bouns_val);
        if(is_joining_bouns_val==1){
            $("#is_joining_bouns_val").val(0);
            $("#empty_text").hide();
            $("#joining_bouns_text").show();
        }else{
            $("#is_joining_bouns_val").val(1);
            $("#joining_bouns_text").hide()
            $("#empty_text").show();
        }
    }
    function changeyeartomonth(val){
        $('#ctc_monthly').val((val/12).toFixed(2));
    }
    function shoehidetdssection() {

        var is_tds_applicable_val =  $("#is_tds_applicable_val").val();
         console.log("is_tds_applicable_val"+is_tds_applicable_val);
        if(is_tds_applicable_val==1){
            $("#is_tds_applicable_val").val(0);

            $("#tds_section_dis").show();
        }else{
            $("#is_tds_applicable_val").val(1);
            $("#tds_section_dis").hide()
        }
    }
    function gesttyype(val){
        if(val==0){
            $("#dis_gst_no").show();
        }else{
            $("#dis_gst_no").hide()
        }

    }
</script>
 <div class="card card-custom " id="kt_page_sticky_card">
    <div class="card-header">
        <h5> Contract Pay Salary </h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i> Back
            </a>
        </div>
    </div>
    <div class="card-body">
        <form class="form" method="post" action="">
        <div class="row">
            <div class="form-group row">
                <div class="form-group col-lg-4">
                    <span class="form-label">Employee </span>
                    <select aria-label="Employee" class="form-select js-example-basic-single" id="employee_id" name='employee_id'>
                        <option> Select Any One</option>
                        <?php foreach ($data['emp_data'] as $employe) { ?>
                           <option <?php if( $salarydata['employee_id'] == $employe['id'] ){ echo "selected";
} ?> value="<?php echo $employe['id']; ?>"> <?php echo $employe['display_name']; ?></option>
                       <?php } ?>
                   </select>
                </div>

                <div class="form-group col-lg-4">
                    <span class="form-label">Pay Type </span>
                    <select aria-label="Pay Type" class="form-select js-example-basic-single" id="pay_type" name='pay_type'>
                        <option value="0"> Select Any One</option>
                        <?php foreach ($data['pay_type_data'] as $pay_type) { ?>
                           <option <?php if( $salarydata['pay_type'] == $pay_type['genc_detail_code'] ){ echo "selected";
} ?> value="<?php echo $pay_type['genc_detail_code']; ?>"> <?php echo $pay_type['genc_detail_name']; ?></option>
                       <?php } ?>
                   </select>
                </div>

                <div class="form-group  col-lg-4 ">
                    <span class="form-label">
                       Pay Rate
                     </span>
                    <div>
                        <input aria-label="Pay Rate" class="form-control text-right" id="pay_rate" type="text" name="pay_rate" value="<?php echo $salarydata['pay_rate']!=""? $salarydata['pay_rate']: '0.00'; ?>" >
                    </div>
                </div>

                <div class="form-group  col-lg-4 " <?php echo $stylecss; ?>>
                    <span class="form-label">Effective Date </span>
                    <input aria-label="Affected Date" class="form-control datepicker" type="text" name="affected_date" value="<?php echo fromDate($salarydata['affected_date']); ?>">
                </div>

                <div class="form-group col-lg-4">
                    <span class="form-label">Pay Frequency</span>
                    <select aria-label="Pay Frequency" class="form-select js-example-basic-single" id="pay_frequency" name='pay_frequency'>
                        <option value="0"> Select Any One</option>
                        <?php
                        $frequency_array = array('D'=>'Daily', 'W'=> 'Weekly', 'B'=> 'Biweekly', 'M'=> 'Monthly');
                        foreach ($frequency_array as $fkey=>$frequency) { ?>
                           <option <?php if( $salarydata['pay_frequency'] == $fkey){ echo "selected";
} ?> value="<?php echo $fkey; ?>"> <?php echo $frequency; ?></option>
                       <?php } ?>
                   </select>
                </div>

                <div class="form-group col-lg-4">
                    <span class="form-label">GST Applicable</span>
                    <select aria-label="Gst Type" class="form-select js-example-basic-single" onchange="gesttyype(this.value)" id="gst_type" name='gst_type'>
                        <option value="0"> Select Any One</option>
                        <?php foreach ($data['gst_type_data'] as $gst_type) { ?>
                           <option <?php if( $salarydata['gst_type'] == $gst_type['genc_detail_code']){ echo "selected";
} ?> value="<?php echo $gst_type['genc_detail_code']; ?>"> <?php echo $gst_type['genc_detail_name']; ?></option>
                       <?php } ?>
                   </select>
                </div>

                <div class="form-group col-lg-4" id="dis_gst_no" <?php if($salarydata['gst_type']!=0){ ?> style="display:none; "; <?php
} ?> >
                    <span class="form-label">GST No.</span>
                    <input aria-label="Gst No" class="form-control" type="text" name="gst_no" id="gst_no" value="<?php echo $salarydata['gst_no']; ?>">
                </div>

                <div class="form-group  col-lg-4">
                    <span class="form-label">
                       Joining Bonus Applicable
                     </span>
                    <div>
                         <button type="button" onclick="changejoiningboun()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent">
                        <?php $is_joining_bouns = $salarydata['joining_bonus_applicable']!=""?$salarydata['joining_bonus_applicable']: 1; ?>
                        <input aria-label="Checkbox" type="checkbox"  class="js-switch" value="<?php echo $sen; ?>"  name="is_joining_bonus" <?php if($is_joining_bouns== 0 ){ ?> checked <?php
} ?>  />
                         <input type="hidden"   value="<?php echo $is_joining_bouns; ?>"  name="joining_bonus_applicable" id="is_joining_bouns_val"  />
                         </button>
                    </div>
                </div>

                <div class="form-group  col-lg-4" id="joining_bouns_text"
                    <?php if($is_joining_bouns ==1){ ?> style="display:none; "; <?php
} ?> >
                    <span class="form-label">
                       Please Enter Joining Bonus
                     </span>
                    <div>
                        <input aria-label="Joining Bonus Amount" class="form-control text-right" id="joining_bouns" type="text" name="joining_bonus_amount" value="<?php echo $salarydata['joining_bonus_amount']!=""? $salarydata['joining_bonus_amount']: '0.00'; ?>">
                    </div>
                </div>

                <div class="form-group  col-lg-4" id="empty_text" <?php if($is_joining_bouns ==0){ ?> style="display:none; "; <?php
} ?>>
                </div>


                <div class="form-group  col-lg-4">
                    <span class="form-label">Notes</span>
                    <textarea aria-label="Max 500 characters" class="form-control" id="kt_autosize_2" name="note" placeholder="Max 500 characters" ><?php echo $salarydata['note']; ?></textarea>
                </div>


                <div class="form-group  col-lg-4">
                    <span class="form-label">
                       TDS Applicable
                     </span>
                    <div>
                         <button type="button" onclick="shoehidetdssection()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" class="btn p-0 border-0 bg-transparent">
                        <?php $is_tds_applicable = $salarydata['tds_applicable']!=""?$salarydata['tds_applicable']: 1; ?>
                        <input aria-label="Checkbox" type="checkbox"  class="js-switch" value="<?php echo $sen; ?>"  name="is_joining_bonus" <?php if($is_tds_applicable== 0 ){ ?> checked <?php
} ?>  />
                         <input type="hidden"   value="<?php echo $is_tds_applicable; ?>"  name="tds_applicable" id="is_tds_applicable_val"  />
                         </button>
                    </div>
                </div>

                <div class="form-group col-lg-4"  id="tds_section_dis"  <?php if($is_tds_applicable ==1){ ?> style="display:none; "; <?php
} ?>>
                    <span class="form-label">TDS Section </span>
                    <select aria-label="Tds Section" class="form-select js-example-basic-single" id="tds_section" name='tds_section'>
                        <option value="0"> Select Any One</option>
                        <?php foreach ($data['tax_deducted_sources_data'] as $gst_type) { ?>
                           <option <?php if( $salarydata['tds_section'] == $gst_type['id']){ echo "selected";
} ?> value="<?php echo $gst_type['id']; ?>"> <?php echo $gst_type['tds_description']; ?></option>
                       <?php } ?>
                   </select>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-9">
                <input type="hidden" name="submit" value="Add">
                 <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10 btn_mobile" name="submit_value_exit" value="exit"><i class="la la-check"></i>Save &amp; Exit</button>

                <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2 btn_mobile"><i class="la la-close"></i>Cancel</button>

            </div>
            <div class="col-lg-3 text-right">
                <button type="reset" disabled="disabled" class="btn btn-danger btn_mobile"><i class="la la-trash"></i>Delete</button>
            </div>
        </div>
        </form>
    </div>
</div>

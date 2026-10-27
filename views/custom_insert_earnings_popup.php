<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$_SESSION['apply_js']['multiselest'] = '1';
$table_name = "earnings";
if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $statusurl = API."?mode=singlerowtabledata&fid=id&rid=".$_GET['Rid']."&table=earnings";
    $response = callAPI($statusurl);
    $role = json_decode($response,1);
}
    $vdata1["where"] = array('is_active'=> '0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=earnings&data=".$vdata;
    $configesponse = callAPI($vurl);
    $codes = json_decode($configesponse,1);
    $operators = array('+','-','*','/');
    $vurl = API."?mode=gettabledetailswithconditions&table=salary_categories&data=".$vdata;
    $configesponse = callAPI($vurl);
    $salary_categories = json_decode($configesponse,1);
    foreach($codes as  $code){
        $list_code[]=$code['code'];
    }
    $fixed_code = array('GS'=> 'GS (Gross Salary)', 'BASIC'=>'BASIC (Basic)' ,'HRA'=>'HRA (House Rent Allowance)', 'DA'=>'DA (Dearness Allowance)', 'OA'=> 'OA (Other Allowance)', 'PF'=>'PF (Provident Fund)', 'PT'=>'PT (Professional Tax)','ESI'=>'ESI (Employees State Insurance Corporation)', 'EMPLOYERPF'=>'EMPLOYERPF (Employer PF Contribution)', 'EMPLOYERESI'=>'EMPLOYERESI (Employer State Insurance Corporation)', 'TDS'=> 'TDS (Tax Deducted at Source)');
    foreach($fixed_code as $keyfix=>$fixed){
        $fixedcd [] = $keyfix;
        if(!in_array($keyfix,(array)$list_code)){
            $show_fixed[$keyfix] = $fixed;
        }
    }
    $show_select = 'Y'; $show_text = 'N';
    if(count((array)$show_fixed)>0 && !isset($_GET['Rid']) ){
        $show_select = 'Y'; $show_text = 'N';
    }
?>
<script type="text/javascript">
    function submitrom(){
        var formvalue =  $("#submitnextform").serialize();
        console.log(formvalue);
        jQuery.ajax({
            url: "ajax.php",
            data: "page=toaddupdateearningdata&"+formvalue,
            type: "POST",
            dataType: "json",
            success:function(data){
                console.log(data);
                console.log("PPP"+pp);
                console.log(data.name);
                $('#kt_modal_dynamic_popup2').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Earnings Saved Successfully','');
                if(data.rid!=""){
                    $("#"+data.ptype+"_name_"+data.prow).val(data.name);
                    $("#"+data.ptype+"_code_"+data.prow).val(data.code);
                }else{
                    window.location.reload();
                }

            },
            error:function (){}
        });
    }
    function calculationtype(val){
        if(val=="A" || val=="P" || val=="N"){
            $("#CTTAPN").show();
            $("#CTTF").hide();
            $("#CTTL").hide();
        }
        if(val=="F"){
            $("#CTTAPN").hide();
            $("#CTTL").hide();
            $("#CTTF").show();
        }
        if(val=="L"){
            $("#CTTAPN").show();
            $("#CTTL").show();
            $("#CTTF").hide();
        }
    }
    function chekother(val,id){
        console.log("check"+val+"iddds"+id);
        if(val=='123'){
             $("#other_"+id).html('<input aria-label="Lcode[]" type="text" class="form-control fitemRow" name= "lcode[]" id="lcode_'+id+'">');
        }
        if(val=='321'){
             $("#rother_"+id).html('<input aria-label="Rcode[]" type="text" class="form-control" name= "rcode[]" id="rcode_'+id+'">');
        }
    }
    function deletestatickRows(val){
        $("#qur_div_"+val).css("display", "none");
        $("#delete_value_"+val).val('1');
    }
    function createFormula(){
        var count = $(".fitemRow").length;
        console.log(count);

        var str ="";
        if(count > 0){
            for (i = 1; i < count+1 ; i++) {
                str += '('+$('#lcode_'+i).val()+$('#loperand_'+i).val()+''+$('#rcode_'+i).val()+')'+$('#roperand_'+i).val();
            }
        }
        str = str.slice(0, -1);
        $('#dis_formula').val(str);
    }
    function addRowsearning(){
        var count = $(".fitemRow").length;
        count++;
        var htmlRows = '';
        htmlRows += '<tr id="qur_div_'+count+'" >';
        htmlRows += '<td id="other_'+count+'"><select aria-label="Lcode[]" class="form-control fitemRow js-example-basic-single" id="lcode__2"+count+'" name= "lcode[]"  onchange="chekother(this.value,'+count+')"><option value=""> Select </option><?php foreach ($codes as  $code) { ?><option value="<?php echo $code['code']; ?>"><?php echo $code['code']; ?></option><?php
} ?> <option value="123">Other</option> </select></td>';
        htmlRows += '<td><select aria-label="Loperand[]" class="form-control  js-example-basic-single" id="loperand_'+count+'" name= "loperand[]" ><option value=""> Select </option><?php foreach ($operators as  $operator) { ?><option value="<?php echo $operator; ?>"><?php echo $operator; ?></option><?php
} ?></select></td>';
        htmlRows += '<td id="rother_'+count+'"> <select aria-label="Rcode[]" class="form-control  js-example-basic-single" id="rcode__2"+count+'" name= "rcode[]" onchange="chekother(this.value,'+count+')" ><option value=""> Select </option><?php foreach ($codes as  $code) { ?><option value="<?php echo $code['code']; ?>"><?php echo $code['code']; ?></option><?php
} ?> <option value="321">Other</option> </select></td>';
        htmlRows += '<td><select aria-label="Roperand[]" class="form-control  js-example-basic-single" id="roperand_'+count+'" name= "roperand[]" ><option value=""> Select </option><?php foreach ($operators as  $operator) { ?><option value="<?php echo $operator; ?>"><?php echo $operator; ?></option><?php
} ?></select></td>';

        htmlRows += '<td><input type="hidden" id="delete_value_'+count+'" value="0"><div  id="'+count+'"  class="close" onclick="deletestatickRows(this.id)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-trash"></i></div>';
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
             $(".js-example-basic-single").select2();
        $(".js-example-disabled-results").select2();
    }
    function othervalchange(val){
        console.log(val);
        if(val=='OT123'){
            $("#code_textpop").show();
            $("#code_selectpop").hide();
        }
    }
</script>

    <div class="">
        <div class="modal-header">
            <h5>  Earnings</h5>
            <div class="card-header-right table-card-header ">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
        </div>
        <div class=" card-custom card-sticky">
            <div class="card-body">
                <div class="form-group row ml-20 mr-20">
                    <form class="kt-form" id="submitnextform" method="post" action=""  name="myForm" enctype="multipart/form-data">
                        <div class="row j-content">
                            <div class="form-group col-lg-6">
                                <span>Category Check</span>
                                <select aria-label="Category" class="form-select js-example-basic-single" id="kt_select2_41" name="category_id">
                                    <option value="0"> Select Any one Category</option>
                                    <?php foreach($salary_categories as $salary_categorie){ ?>
                                        <option value="<?php echo $salary_categorie['id']; ?>" <?php if($role['category_id']==$salary_categorie['id'] ){ echo "selected";
} ?>> <?php echo $salary_categorie['name']; ?> </option>
                                    <?php } ?>
                                </select>
                                <div style="display: none;color: red;" id="category_id_err"> Please Select Category </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="">Earnings Name</span>
                                <input aria-label="Name" class="form-control" type="text" id="name" name="name" value="<?php echo $role['name']; ?>">
                                <div style="display: none;color: red;" id="name_err"> Please enter Earnings Name </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="">Earnings System Code</span>
                                <div id="code_textpop" <?php if($show_text == 'N'){ ?> style="display: none;" <?php
} ?>>
                                <input aria-label="Input field" class="form-control" <?php if(in_array($role['code'],(array)$fixedcd)){ echo "readonly";
} ?> type="text" id="code" name="code"  value="<?php echo $role['code']; ?>">
                            </div>
                            <div id="code_selectpop" <?php if($show_select == 'N'){ ?> style="display: none;" <?php
} ?> >
                                <select aria-label="Code Select" class="form-control js-example-basic-single" name="code_select" id="code_2" onchange="othervalchange(this.value)" >
                                    <option value=""> Select Any</option>
                                    <?php foreach($show_fixed as $keyfix=>$fixed){ ?>
                                        <option value="<?php echo $keyfix; ?>"><?php echo $fixed; ?></option>
                                    <?php } ?>
                                    <option value="OT123">Other (Add New Text Label)</option>
                                </select>
                                </div>

                                <div style="display: none;color: red;" id="code_err"> Please enter Earnings System Code </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="">Printed on Payslip as </span>
                                <input aria-label="Payslip Name" class="form-control" type="text" id="payslip_name" name="payslip_name" value="<?php echo $role['payslip_name']; ?>">
                                <div style="display: none;color: red;" id="payslip_name_err"> Please enter Printed on Payslip as  </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="">Display CTC</span>
                                <select aria-label="Part Of Ctc" class="form-select js-example-basic-single" name="part_of_ctc" id="part_of_ctc">
                                    <?php $disslip = array('0'=>'Yes', '1'=>'No');
                                        foreach($disslip as $dkey=>$dissl){
                                     ?>
                                    <option  <?php if($role['part_of_ctc']==$dkey ){ echo "selected";
} ?> value="<?php echo $dkey; ?>"> <?php echo $dissl; ?></option>
                                <?php } ?>
                                </select>
                                <div style="display: none;color: red;" id="name_err_2"> Please enter Earnings Name </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span class="">Display Playslip</span>
                                <select aria-label="Display In Payslip" class="form-select js-example-basic-single" name="display_in_payslip" id="display_in_payslip">
                                    <?php $disslip = array('0'=>'Yes', '1'=>'No', '2'=>'Based on value');
                                        foreach($disslip as $dkey=>$dissl){
                                     ?>
                                    <option  <?php if($role['display_in_payslip']==$dkey ){ echo "selected";
} ?> value="<?php echo $dkey; ?>"> <?php echo $dissl; ?></option>
                                <?php } ?>
                                </select>
                                <div style="display: none;color: red;" id="name_err_3"> Please enter Earnings Name </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span>Calculation Type</span>
                                <div class="radio radio-outline  mt-1">
                                    <?php $ctct =  array('A' => 'Amount', 'P'=>'Percentage', 'F'=>'Formula', 'L'=>'Link', 'N'=>'None');
                                        foreach ($ctct as $ckey => $ct) { ?>
                                            <label class="radio radio-inline"> <input aria-label="Calculation Type" type="radio" onclick="calculationtype(this.value)" name="calculation_type" <?php if($role['calculation_type']==$ckey){ echo "checked";
} ?> value="<?php echo $ckey; ?>" onkeydown="">    <span></span><?php echo $ct; ?> </label>

                                        <?php } ?>


                                </div>
                                <div style="display: none;color: red;" id="calculation_type_err"> Please Select Calculation Type </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span>Calculation Mode</span>
                                <div class="radio radio-outline  mt-1">

                                    <label class="radio radio-inline"> <input aria-label="Calculation Mode" type="radio" checked="" name="calculation_mode" value="ADD" <?php if($role['calculation_mode']=='ADD'){ echo "checked";
} ?>>    <span></span>ADD </label>
                                    <label class="radio radio-inline"> <input aria-label="Calculation Mode" type="radio" name="calculation_mode" value="MINUS" <?php if($role['calculation_mode']=='MINUS'){ echo "checked";
} ?>>    <span></span>MINUS </label>
                                    <label class="radio radio-inline"> <input aria-label="Calculation Mode" type="radio" name="calculation_mode" value="STATIC" <?php if($role['calculation_mode']=='STATIC'){ echo "checked";
} ?>>    <span></span>Static </label>
                                </div>
                                <div style="display: none;color: red;" id="calculation_mode_err"> Please Select Calculation Mode </div>
                            </div>
                            <div id="CTTAPN" class="col-lg-12 row" <?php if($role['calculation_type']=="F" ){ ?> style="display: none;" <?php
} ?>>
                                <div class="form-group col-lg-6">
                                    <span class="">Value</span>
                                    <input aria-label="Calculation Number" class="form-control" type="text" id="calculation_number" name="calculation_number" value="<?php echo $role['calculation_number']; ?>">
                                    <div style="display: none;color: red;" id="calculation_number_err"> Please enter Value </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <span>Calculation Base</span>
                                    <select aria-label="Calculation Category" class="form-select js-example-basic-single" id="kt_select2_42" name="calculation_category">
                                        <option value="0"> Select Any one Calculation Base</option>
                                        <?php foreach($codes as $code){ ?>
                                            <option <?php if($role['calculation_category']==$code['code']){ echo "selected";
} ?> value="<?php echo $code['code']; ?>"><?php echo $code['code']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <div style="display: none;color: red;" id="calculation_category_err"> Please Select Calculation Base </div>
                                </div>
                            </div>
                            <div class="col-lg-12" id="CTTL"  <?php if($role['calculation_type']!="L"){ ?> style="display: none;" <?php
} ?>>
                                <div class="form-group col-lg-6">
                                    <span>Calculation Link</span>
                                    <?php $links = array('PF'=>'Provident Fund', 'ESI'=>'ESI', 'PTX'=>'Professional Tax', 'GU'=>'Gratuity','EMPLOYERPF'=>'Employer Provident Fund','EMPLOYERESI'=>'Employer ESI'); ?>
                                    <select aria-label="Calculation Link" class="form-select js-example-basic-single" id="kt_select2_42_2" name="calculation_link">
                                        <option value="0"> Select Any one Calculation Link</option>
                                        <?php foreach($links as $clink=>$link){ ?>
                                            <option <?php if($role['calculation_link']==$clink){ echo "selected";
} ?> value="<?php echo $clink; ?>"><?php echo $link; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-12" id="CTTF" <?php if($role['calculation_type']!="F"){ ?> style="display: none;" <?php
} ?> >
                                <span>Formula</span>
                                <div class="row align-items-center" style="margin-top: 10px;padding: 3px;">
                                <div class="col-lg-8">
                                    <table class="table table-striped- table-bordered table-hover table-checkable  no-footer mb-3" style="color: #646c9a;" id="invoiceItem">
                                            <tr>
                                                <th scope="col" class="" style="width: 35%;">System Code</th>
                                                <th scope="col" class="thheader" style="width: 5%;">Operator</th>
                                                <th scope="col" class="thheader" style="width: 35%;">System Code</th>
                                                <th scope="col" class="thheader" style="width: 5%;">Operator</th>
                                                <th scope="col"></th>
                                            </tr>
                                            <?php  $tr_val = json_decode($role['formula_details'],1);
                                                   $tr = $tr_val['details'];

                                            if(count((array)$tr)>0){
                                                $count = 1;
                                                foreach ($tr as $td) { //print_r($td); ?>


                                                <tr id="qur_div_<?php echo $count; ?>" >
                                                    <td id='other_<?php echo $count; ?>'>
                                                        <?php if(is_numeric($td['lcode'])){ ?>
                                                            <input aria-label="Lcode[]" type="text" class="form-control fitemRow" name= "lcode[]"  id="lcode_<?php echo $count; ?>" value="<?php echo $td['lcode']; ?>" >
                                                         <?php }else{ ?>
                                                        <select aria-label="Select option" class="form-control fitemRow form-select js-example-basic-single" id="lcode_<?php echo $count; ?>" name= "lcode[]" onchange="chekother(this.value,<?php echo $count; ?>)" >
                                                            <option value=""> Select </option>
                                                            <?php foreach ($codes as  $code) { ?>
                                                                   <option value="<?php echo $code['code']; ?>" <?php if($td['lcode'] == $code['code']){ echo "selected";
} ?> ><?php echo $code['code']; ?></option>
                                                               <?php } ?>
                                                               <option value="123">Other</option>
                                                        </select>
                                                        <?php } ?>
                                                    </td>
                                                    <td>

                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single" id="loperand_<?php echo $count; ?>" name= "loperand[]"  >
                                                            <option value=""> Select </option>
                                                        <?php foreach ($operators as $dd) {  ?>
                                                            <option  <?php if($td['loperand'] == $dd){ echo "selected";
} ?>  value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php
} ?>
                                                        </select>

                                                    </td>
                                                    <td id='rother_<?php echo $count; ?>'>
                                                        <?php if(is_numeric($td['rcode'])){ ?>
                                                            <input aria-label="Rcode[]" type="text" class="form-control" name= "rcode[]"  id="rcode_<?php echo $count; ?>" value="<?php echo $td['rcode']; ?>" >
                                                         <?php }else{ ?>
                                                        <select aria-label="Select option" class="form-control  form-select js-example-basic-single" id="rcode_<?php echo $count; ?>" name= "rcode[]" onchange="chekother(this.value,<?php echo $count; ?>)"  >
                                                            <option value=""> Select </option>
                                                            <?php foreach ($codes as  $code) { ?>
                                                                   <option value="<?php echo $code['code']; ?>" <?php if($td['rcode'] == $code['code']){ echo "selected";
} ?> ><?php echo $code['code']; ?></option>
                                                               <?php } ?>
                                                                   <option value="321">Other</option>
                                                        </select>
                                                        <?php } ?>
                                                    </td>
                                                    <td>
                                                        <select aria-label="Select option" class="form-control form-select js-example-basic-single" id="roperand_<?php echo $count; ?>" name= "roperand[]"  >
                                                            <option value=""> Select </option>
                                                            <?php foreach ($operators as $dd) {  ?>
                                                            <option  <?php if($td['roperand'] == $dd){ echo "selected";
} ?>  value = "<?php echo $dd; ?>"> <?php echo $dd; ?> </option> <?php
} ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="hidden" value="0" id="delete_value_<?php echo $count; ?>" >
                                                        <div  id="<?php echo $count; ?>" class="close" onclick="deletestatickRows(this.id)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"><i class="ti-trash"></i></div>

                                                    </td>
                                                </tr>
                                            <?php $count++; ?>
                                            <?php } ?>
                                            <?php } ?>
                                    </table>
                                    <button class="btn btn-success col-lg-4" id="addRows" type="button" onclick="addRowsearning()">+ Add More</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-primary col-lg-4" onclick="createFormula()">Bulid Formula</button>
                                </div>
                                <div class="col-lg-4">
                                        <span>Show Formula</span>
                                        <div class="kt-input-icon">
                                            <textarea aria-label="Calculation Formula" class="form-control"  type="text" id="dis_formula" name="calculation_formula" style="height: 80px;" value=""> <?php echo $role['calculation_formula']; ?></textarea>
                                        </div>
                                </div>
                            </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <span>Display Seq</span>
                                <div>
                                    <input aria-label="Display Seq" class="form-control text-right" id="" type="text" name="display_seq" value="<?php echo $role['display_seq']; ?>">

                                </div>
                            </div>
                            <div class="form-group col-lg-3">
                                <span>Taxable</span>
                                <div class="radio radio-outline  mt-1">

                                    <label class="radio radio-inline"> <input aria-label="Taxable" type="radio" checked="" name="taxable" value="0" <?php if($role['taxable']=='0'){ echo "checked";
} ?>>    <span></span>Yes </label>

                                    <label class="radio radio-inline"> <input aria-label="Taxable" type="radio" name="taxable" value="1" <?php if($role['taxable']=='1'){ echo "checked";
} ?>>    <span></span>No </label>
                                </div>
                                <div style="display: none;color: red;" id="taxable_err"> Please Select Taxable </div>
                            </div>
                            <div class="form-group col-lg-3">
                                <span>Taxable Limit</span>
                                <div>
                                    <input aria-label="Taxable Limit" class="form-control text-right" id="" type="text" name="taxable_limit" value="<?php echo $role['taxable_limit']; ?>">

                                </div>
                            </div>
                            <div class="form-group col-lg-3">
                                <span>Min Range</span>
                                <div>
                                    <input aria-label="Min Range" class="form-control text-right" id="" type="text" name="min_range" value="<?php echo $role['min_range']; ?>">

                                </div>
                            </div>
                            <div class="form-group col-lg-3">
                                <span>Max Range</span>
                                <div>
                                    <input aria-label="Max Range" class="form-control text-right" id="" type="text" name="max_range" value="<?php echo $role['max_range']; ?>">

                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span>Notes</span>
                                <div class="kt-radio-inline">
                                    <textarea aria-label="Note" type="text" class="form-control " rows="5" cols="5" id="note" name="note"><?php echo $role['note']; ?></textarea>
                                </div>
                                <div style="display: none;color: red;" id="note_err"> Please Select Notes </div>
                            </div>
                            <input type="hidden" name="in_date_pic" value="">
                            <input type="hidden" name="in_currency_fileds" value="">
                            <input type="hidden" name="in_phone_fileds" value="">
                            <input type="hidden" name="calculation_mode_type" value="<?php echo $_GET['calculation_mode_type']; ?>">
                            <input type="hidden" name="mandatory_str" value="category_id,name,code,payslip_name,calculation_type,calculation_mode,calculation_category,calculation_number,display_seq,taxable,taxable_limit,min_range,max_range,note,calculation_formula,calculation_link,display_in_payslip,part_of_ctc,calculation_mode_type,payroll_calculation_type">
                            <input type="hidden" name="add_mandatory_str" value="">
                            <input type="hidden" name="notification_users" value="3">
                            <input type="hidden" name="notification_message" value="New   earnings page has been created">
                            <input type="hidden" name="insert_page_names" value="COM_INSERT_EARNINGS">
                            <input type="hidden" name="list_page_names" value="COM_LIST_EARNINGS">
                            <input type="hidden" name="option" value="">
                        </div>
                        <?php $from = fromDate($role['from_date']);
                                  $to= toDate($role['to_date']);?>
                        <div class="row ml- mr-20  j-datebutoon ">
                            <div class="col-lg-6  col-xs-12 form-group">
                                <span>From Date</span>
                                <input aria-label="From Date" class="form-control" type="date" name="from_date" value="<?php echo $from; ?>" readonly="">
                            </div>
                            <div class="col-lg-6  col-xs-12 form-group">
                                <span>To Date</span>

                                <input aria-label="To Date" class="form-control" type="date" name="to_date" value="<?php echo $to; ?>">
                            </div>
                        </div>
                        <div class="card-footer j-datebutoon">
                            <div class="row">
                                <div class="col-lg-9">
                                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>

                                    <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                        <input type="hidden" name="submit" value="Update" />
                                    <button type="button" onclick="submitrom()" class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>
                                <?php }else{ ?>
                                    <input type="hidden" name="submit" value="Add">

                                    <button  type="button" onclick="submitrom()" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save</button>

                                <?php } ?>
                                <input type="hidden" name="ptype" value="<?php echo $_GET['ptype']; ?>">
                                <input type="hidden" name="prow" value="<?php echo $_GET['prow']; ?>">
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['option']!=""){
    $_REQUEST['option'] = $_POST['option'];
    if(isset($_POST['Rid'])){ $_REQUEST['Rid'] = $_POST['Rid'];
}
}
$slabs = array(array('start_range'=>'0.00'));
$mlabs = array(array('start_range'=>'0.00'));
$flabs = array(array('start_range'=>'0.00'));
if(isset($_REQUEST['Rid']) && $_REQUEST['Rid']!=""){

    $dataipf["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>0,'id'=>$_REQUEST['Rid']);
    $datapff = urlencode(json_encode($dataipf));
    $csudatapff = API."?mode=gettabledetailswithconditions&table=professional_tax&data=".$datapff;
    $csudatapff = callAPI($csudatapff);
    $pfftdata = json_decode($csudatapff,1);
    $pfftdata = $pfftdata['0'];
    $slabs = json_decode($pfftdata['slabs'],1);
    $mslabs = json_decode($pfftdata['male_slabs'],1);
    $fslabs = json_decode($pfftdata['female_slabs'],1);
    $taxexemptions = json_decode($pfftdata['personal_tax_exemptions'],1);
    foreach((array)$taxexemptions as $taxexempti){
        $chek_ex[$taxexempti['id']] = $taxexempti;
        $chek_ex_id[] = $taxexempti['id'];
    }
}
$chk["where"] = array('is_active'=>0);
$chks = urlencode(json_encode($chk));
$locationurl = API."?mode=gettabledetailswithconditions&table=locations&data=".$chks;
$locationdata =  callAPI($locationurl);
$locationdata = json_decode($locationdata,1);

$sateurl = API."?mode=gettabledetailswithconditions&table=states&data=".$chks;
$satedata =  callAPI($sateurl);
$satedata = json_decode($satedata,1);

$sateurl = API."?mode=togetprofessionaltaxexemptionsdetails";
$satedatas =  callAPI($sateurl);
$extaxdata = json_decode($satedatas,1);
$exdatas = $extaxdata['exdata'];
$exfieds = $extaxdata['emp_fileds'];
$range_type = array('yes_no'=>'Yes OR No', 'percentage'=> 'Percentage', 'age'=>'Age');
$months = array(
    '4'=>'April',
    '5'=>'May',
    '6'=>'June',
    '7'=>'July ',
    '8'=>'August',
    '9'=>'September',
    '10'=>'October',
    '11'=>'November',
    '12'=>'December',
    '1'=>'January',
    '2'=>'February',
    '3'=>'March',
);

?>

<script type="text/javascript">

    var fcount = $(".fitemRow").length;
    function addfemalerows(){
        fcount++;
        var htmlRows = '';
        htmlRows += '<tr class="rowclass'+fcount+'"><td><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" onclick="deleterowpt('+fcount+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td>';
        htmlRows += '<td><input aria-label="Fstart Range[]" type="text" name="fstart_range[]" class="form-control fitemRow text-right" value="0.00" style="width: 130px;"></td> ';
        htmlRows += '<td><input aria-label="Fend Range[]"  type="text" name="fend_range[]" class="form-control text-right" value="0.00" style="width: 130px;"></td>';
        htmlRows += '<td><input aria-label="Ftax Amount[]"  type="text" name="ftax_amount[]"   onblur="monthamontcalculation('+fcount+')" class="form-control taxamountcheck tax_amountpt'+fcount+'  text-right"  data-checkid="'+fcount+'" value="0.00" style="width: 130px;"></td>';
            <?php foreach($months as $keyy=>$value3){ ?>
        htmlRows += '<td ><input aria-label="Input field"  type="text" name="fmonthvalue[<?php echo $keyy; ?>][]" class="form-control text-right monthamount'+fcount+'" onblur="monthamontcalculation('+fcount+')" value="0.00" style="width: 110px;"></td>';
                    <?php } ?>
        htmlRows += '</tr>';
        $('#finvoiceItem').append(htmlRows);
    }
     var count = $(".itemRow").length;
    function addrows(){
         count++;
        var htmlRows = '';
        htmlRows += '<tr class="rowclass'+count+'"><td><button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger" onclick="deleterowpt('+count+')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></td>';
        htmlRows += '<td><input aria-label="Start Range[]" type="text" name="start_range[]" class="form-control itemRow text-right" value="0.00" style="width: 130px;"></td> ';
        htmlRows += '<td><input aria-label="End Range[]"  type="text" name="end_range[]" class="form-control text-right" value="0.00" style="width: 130px;"></td>';
        htmlRows += '<td><input aria-label="Tax Amount[]"  type="text" name="tax_amount[]"   onblur="monthamontcalculation('+count+')" class="form-control taxamountcheck tax_amountpt'+count+'  text-right"  data-checkid="'+count+'" value="0.00" style="width: 130px;"></td>';
            <?php foreach($months as $keyy=>$value3){ ?>
        htmlRows += '<td ><input aria-label="Input field"  type="text" name="monthvalue[<?php echo $keyy; ?>][]" class="form-control text-right monthamount'+count+'" onblur="monthamontcalculation('+count+')" value="0.00" style="width: 110px;"></td>';
                    <?php } ?>
        htmlRows += '</tr>';
        $('#invoiceItem').append(htmlRows);
    }

function locationselection(vall){
  var optionval =  vall.split("||");

$.post("ajax.php",{'page':'getstatvaluewithlist','state_id':optionval[1]},function(data){
   $('.selectstatedrop').html(data);
})

}

function monthamontcalculation(id){
    var total = 0;
    var tax_amountpt = $(".tax_amountpt"+id).val();
    $(".monthamount"+id).each(function(index){
            total += parseInt($(this).val());
        });
    if(tax_amountpt < total){
       Swal.fire({
  icon: 'error',
  title: 'Oops...',
  text: 'Error message',
})
    }
    console.log(total);
}
function deleterowpt(id){
    $(".rowclass"+id).remove();
}

function taxamountcheckss(){
    var error = '0';
    $(".taxamountcheck").each(function(index){
        var total= 0;
            var value = $(this).val();
            var id =$(this).data("checkid");
            $(".monthamount"+id).each(function(index){
                total += parseInt($(this).val());
            });

            if(value != total){
                $(".tax_amountpt"+id).css("background-color","red");
                console.log("errer");
            }else{
                $(".tax_amountpt"+id).css("background-color","white");
                console.log("no  errer");
            }

    });

}
function showslabs(val) {
    $("#maledivtab").removeClass("btn-primary");
    $("#femaledivtab").removeClass("btn-primary");
    $("#"+val+"tab").addClass("btn-primary");

    $('#malediv').hide();
    $('#femalediv').hide();
    $('#'+val).show();
}
function sameaschange(){
    console.log("Checkkkk");
    var val = $("#sameasval").val();
    console.log("val"+val);
    if(val == 1){
        $("#sameasval").val('0');
        $('#divsameaschange').hide();
    }
    if(val == 0){
        $("#sameasval").val('1');
        $('#divsameaschange').show();
    }
}
function viewexemption(){
    console.log("viewexemption");
    var val = $("#viewexem").val();
    console.log("val"+val);
    if(val == 1){
        $("#viewexem").val('0');
        $('#exemid').hide();
        $('#exemptionicon').html('+');
    }
    if(val == 0){
        $("#viewexem").val('1');
        $('#exemid').show();
        $('#exemptionicon').html('-');
    }
}
</script>

<div class="modal-content" style="overflow-y: scroll;">
    <div class="j-pro">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Professional Tax</h5><br />
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="" class=" ">
        <div class="modal-body" >
            <div class="form-group row">
                <div class="form-group col-lg-4">
                    <span class="form-label">PT Number:</span>
                    <input aria-label="Pt Number" type="text" class="form-control" name="pt_number" placeholder="" value="<?php echo $pfftdata['pt_number']; ?>">
                </div>
                <div class="col-lg-4">
                    <span class="form-label">Work Location:</span>
                    <select aria-label="Wrok Location" class="form-control form-select js-example-basic-single" name="wrok_location_id" onchange="locationselection(this.value)" id="kt_select2_41">
                        <option value="0"> Select Any One </option>
                        <?php foreach ($locationdata as $location) { ?>
                            <option  <?php if($location['id'] == $pfftdata['work_location_id']) { echo "selected";
} ?> value="<?php echo $location['id']."||".$location['location_state'];  ?>"><?php echo $location['location_name'];  ?></option>
                       <?php  } ?>
                    </select>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">State :</span>
                    <select aria-label="State" class="form-control  form-select js-example-basic-single selectstatedrop" name="state" id="kt_select2_42">
                        <option value="0"> Select Any One </option>
                        <?php foreach ($satedata as $state) { ?>
                            <option  <?php if($state['id'] == $pfftdata['state_id']) { echo "selected";
} ?> value="<?php echo $state['id'];  ?>"><?php echo $state['state_name'];  ?></option>
                       <?php  } ?>

                    </select>
                </div>
            </div>
            <div>
                <div class="row col-lg-8 p-2 text-white" onclick="viewexemption()" style="background-color: #4099ff;" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                    <div class="col-lg-11">
                        Professional Tax Exemptions
                    </div>
                    <div class=" col-lg-1 text-right f-w-600 f-16 cursor-pointer"  id="exemptionicon">
                        +
                    </div>
                    <input type="hidden" name="" id="viewexem" value="0">
                </div>
            </div>
            <div style="display: none;" id="exemid">
            <?php if(count((array)$exdatas)>0){
                    foreach($exdatas as $exdata){
                        $kid = $exdata['id'];
                ?>

                <div class="row col-lg-8 mt-2" id="<?php echo "old".$kid; ?>">
                     <div class="col-lg-1 f-w-600 mt-1">
                        <div class="checkbox-zoom zoom-primary mt-1">
                            <label class="form-label">
                                <input aria-label="Checkbox" type="checkbox" <?php if(in_array($exdata['id'], (array)$chek_ex_id)){ ?> checked <?php
} ?>  name="ptexemption[<?php echo $exdata['id']; ?>]" value= "1" >
                                <span class="cr">
                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                            </label>
                        </div>
                    </div>
                    <div class="col-lg-7 f-w-600">
                      <input aria-label="Input field" type="text" class="form-control" readonly value="<?php echo $exdata['exemption_name']." (".$exdata['exemption_code'].")"; ?>">
                      <input type="hidden" name="ptexemptionmin[<?php echo $exdata['id']; ?>][id]" value="<?php echo $exdata['id']; ?>">
                      <input type="hidden" name="ptexemptionmin[<?php echo $exdata['id']; ?>][exemption_name]" value="<?php echo $exdata['exemption_name']; ?>">
                      <input type="hidden" name="ptexemptionmin[<?php echo $exdata['id']; ?>][exemption_code]" value="<?php echo $exdata['exemption_code']; ?>">
                      <input type="hidden" name="ptexemptionmin[<?php echo $exdata['id']; ?>][range_type]" value="<?php echo $exdata['range_type']; ?>">
                    </div>
                   <?php
                        $minmum_range = $chek_ex[$exdata['id']]['minmum_range'] !="" ? $chek_ex[$exdata['id']]['minmum_range'] : $exdata['minmum_range'];
                    ?>
                    <div class="col-lg-2 f-w-600">
                        <input aria-label="Input field" type="text" <?php if($exdata['range_type']=='yes_no'){ echo "readonly";
} ?>  class="form-control" name="ptexemptionmin[<?php echo $exdata['id']; ?>][minmum_range]" value="<?php echo $minmum_range; ?>">
                    </div>
                    <div class="col-lg-2 f-w-600 mt-2">
                        <?php echo $range_type[$exdata['range_type']]; ?>
                    </div>
                </div>
                <?php }
            } ?>
            </div>
            <div class="form-group">
                <div class="example mb-10 mt-5">
                    <div class="btn waves-effect waves-light btn-primary p-2 m-r-5" style="border-radius: 5px;" id="maledivtab" onclick="showslabs('malediv')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <i class="fa fa-male" ></i> Male
                    </div>
                    <div class="btn waves-effect waves-light  p-2  m-r-5"  id="femaledivtab" style="border-radius: 5px;" onclick="showslabs('femalediv')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                        <i class="fa fa-female" ></i> Female
                    </div>
                </div>
                <div class="mt-5" id="maindiv">
                    <div class="" id="malediv" >
                        <div class="dt-responsive table-responsive mt-2">
                            <table class="table table-striped table-bordered nowrap" id="invoiceItem">
                                <thead>
                                    <tr>
                                        <th scope="col" ></th>
                                        <th scope="col" class="text-right">Start Range</th>
                                        <th scope="col" class="text-right">End Range</th>
                                        <th scope="col" class="text-right">Tax Amount</th>
                                         <?php foreach($months as $key=>$value){?>
                                                <th scope="col" class="text-right"><?php echo $value;?></th>
                                        <?php } ?>
                                    </tr>
                                </thead>
                                <?php $i=1; foreach ($mslabs as $slabs_value){ ?>
                                    <tr class="rowclass<?php echo $i; ?>">
                                        <td><i class="ti-trash text-danger" onclick="deleterowpt(<?php echo $i; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td>
                                        <td scope="row">
                                            <input aria-label="Start Range[]" type="text" name="start_range[]" class="form-control itemRow text-right" placeholder="" value="<?php echo $slabs_value['start_range']; ?>" style="width: 130px;">
                                        </td>
                                        <td><input aria-label="End Range[]"  type="text" name="end_range[]" class="form-control text-right" placeholder="" value="<?php echo $slabs_value['end_range']; ?>" style="width: 130px;"></td>
                                        <td><input aria-label="Tax Amount[]" type="text" name="tax_amount[]" class="form-control text-right taxamountcheck tax_amountpt<?php echo $i; ?>" placeholder="" data-checkid="<?php echo $i; ?>" value="<?php echo $slabs_value['tax_amount']; ?>" onblur="monthamontcalculation(<?php echo $i; ?>)" style="width: 130px;"></td>
                                    <?php foreach($months as $keys=>$value2){ ?>
                                    <td >
                                        <input aria-label="Input field"  type="text" name="monthvalue[<?php echo $keys; ?>][]" class="form-control  text-right monthamount<?php echo $i; ?>" onblur="monthamontcalculation(<?php echo $i; ?>)" value="<?php echo $slabs_value['months'][$keys]; ?>" style="width: 110px;">
                                    </td>
                                <?php } ?>
                                    </tr>
                                <?php $i++;
} ?>
                            </table>
                        </div>
                        <div class="row">
                                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

                                    <button class="btn btn-success"  onclick="addrows()" id="addRows_male" type="button">+ Add More</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="" id="femalediv" style="display: none;">
                        <div onclick="sameaschange()" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                            <span> Same as Male slabs </span>
                            <input aria-label="Checkbox" type="checkbox" <?php if($pfftdata['femaie_check']==0 ){ echo 'checked';
}  ?> class="js-switch" value="1"  name="sameasfemale"/>
                            <?php $sameasval = isset($pfftdata['femaie_check']) ? $pfftdata['femaie_check'] : 1;  ?>
                            <input type="hidden"  value="<?php echo $sameasval; ?>" name="sameasval"  id="sameasval" />
                        </div>
                        <div id="divsameaschange" <?php if($pfftdata['femaie_check']==0 ){ ?> style="display: none;" <?php
}  ?> >
                            <div class="dt-responsive table-responsive mt-2" >
                                <table class="table table-striped table-bordered nowrap" id="finvoiceItem">
                                    <thead>
                                        <tr>
                                            <th scope="col" ></th>
                                            <th scope="col" class="text-right">Start Range</th>
                                            <th scope="col" class="text-right">End Range</th>
                                            <th scope="col" class="text-right">Tax Amount</th>
                                             <?php foreach($months as $key=>$value){?>
                                                    <th scope="col" class="text-right"><?php echo $value;?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <?php $i=1; foreach ($fslabs as $slabs_value){ ?>
                                        <tr class="rowclass<?php echo $i; ?>">
                                            <td><i class="ti-trash text-danger" onclick="deleterowpt(<?php echo $i; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td>
                                            <td scope="row">
                                                <input aria-label="Fstart Range[]" type="text" name="fstart_range[]" class="form-control fitemRow text-right" placeholder="" value="<?php echo $slabs_value['start_range']; ?>" style="width: 130px;">
                                            </td>
                                            <td><input aria-label="Fend Range[]"  type="text" name="fend_range[]" class="form-control text-right" placeholder="" value="<?php echo $slabs_value['end_range']; ?>" style="width: 130px;"></td>
                                            <td><input aria-label="Ftax Amount[]" type="text" name="ftax_amount[]" class="form-control text-right taxamountcheck tax_amountpt<?php echo $i; ?>" placeholder="" data-checkid="<?php echo $i; ?>" value="<?php echo $slabs_value['tax_amount']; ?>" onblur="monthamontcalculation(<?php echo $i; ?>)" style="width: 130px;"></td>
                                        <?php foreach($months as $keys=>$value2){ ?>
                                        <td >
                                            <input aria-label="Input field"  type="text" name="fmonthvalue[<?php echo $keys; ?>][]" class="form-control  text-right monthamount<?php echo $i; ?>" onblur="monthamontcalculation(<?php echo $i; ?>)" value="<?php echo $slabs_value['months'][$keys]; ?>" style="width: 110px;">
                                        </td>
                                    <?php } ?>
                                        </tr>
                                    <?php $i++;
} ?>
                                </table>
                            </div>
                            <div class="row">
                                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">

                                        <button class="btn btn-success"  onclick="addfemalerows()" id="addRows_female" type="button">+ Add More</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <?php if($_REQUEST['Rid']!=''){ ?>
                        <input type="hidden" name="submit_pt" value="Update">
                         <input type="hidden" name="rid" value="<?php echo $_REQUEST['Rid']; ?>">
                   <?php  } else{?>
                    <input type="hidden" name="submit_pt" value="submit">
                     <?php } ?>

                    <button  class="btn btn-primary font-weight-bold">Save</button>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script type="text/javascript" src="assets/bower_components/switchery/js/switchery.min.js"></script>
    <script type="text/javascript" src="assets/bower_components/bootstrap-maxlength/js/bootstrap-maxlength.js"></script>
    <script type="text/javascript" src="assets/pages/advance-elements/swithces.js"></script>

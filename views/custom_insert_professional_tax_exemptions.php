<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['option']!=""){
    $_REQUEST['option'] = $_POST['option'];
    if(isset($_POST['Rid'])){ $_REQUEST['Rid'] = $_POST['Rid'];
}
}
$sateurl = API."?mode=togetprofessionaltaxexemptionsdetails";
$satedata =  callAPI($sateurl);
$extaxdata = json_decode($satedata,1);
$exdatas = $extaxdata['exdata'];
$exfieds = $extaxdata['emp_fileds'];
$nodisarray =  array('id', 'company_id', 'is_active', 'current_status', 'status_date', 'from_date', 'to_date', 'created_date', 'created_by', 'created_by', 'updated_date', 'first_name', 'middle_name', 'last_name', 'emp_code' );
$range_type = array('yes_no'=>'Yes OR No', 'percentage'=> 'Percentage', 'age'=>'Age');
?>

<script type="text/javascript">
    var count = $(".itemrown").length;
    function addexrows(){
        count++;
        var htmlRows = '<div class="row col-lg-12 mt-2 itemrown" id="'+count+'"><div class="col-lg-4 f-w-600"> <input aria-label="Newexemptions[]" type="text" class="form-control" name="newexemptions[]"></div>';
        htmlRows += ' <div class="col-lg-2 f-w-600"> <input aria-label="Newexemption Code[]" type="text" class="form-control" name="newexemption_code[]"></div>';
        htmlRows += '<div class="col-lg-2 f-w-600"> <select aria-label="Newemployee Field Name[]" class="form-select js-example-basic-single" name="newemployee_field_name[]"><option value="0"> Select Any One</option> <?php foreach ($exfieds as $filed) { if(!in_array($filed, (array)$nodisarray)){ ?><option value="<?php echo $filed; ?>"><?php echo viewTitle($filed); ?></option><?php
}
} ?></select></div>';
        htmlRows +='<div class="col-lg-2"><select aria-label="Newrange Type[]" class="js-example-basic-single" name="newrange_type[]" onchange="rangetype('+count+',this.value)"><option value="0"> Select Any One </option> <?php foreach((array)$range_type as $rkey=>$range_ty){ ?><option value="<?php echo $rkey; ?>"><?php echo $range_ty; ?></option><?php
} ?></select></div>';
        htmlRows += '<div class="col-lg-1 f-w-600 ">  <input aria-label="Newminmum Range[]" type="text" class="form-control" id="rr'+count+'"  name="newminmum_range[]" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></div><div class="col-lg-1 f-w-600 mt-3"> <button type="button" class="btn p-0 border-0 bg-transparent ti-trash text-danger cursor-pointer" onclick="deleteroe('+count+')" tabindex="0" onkeypress="if(event.key===\'Enter\'||event.key===\' \')this.click()"></button></div></div>';
        $('#adddatahtml').append(htmlRows);
        $(".js-example-basic-single").select2();
    }
    function savedata(){
        var post = $("#savedataa").serialize();
        console.log(post);
        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=savedataforprofessionaltaxexemptions&'+post,
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Professional Tax Exemption is Saved Successfully','');
                $('#kt_modal_KTDatatable_remote1').modal("hide");
            }
        });
    }
    function deleteroe(val){
        $("#" + val).remove();
    }
    function rangetype(val_id,val){
        if(val=='yes_no'){
            $('#rr'+val_id).prop('readonly', true);
        }else{

            $('#rr'+val_id).removeAttr('readonly', true);
        }
    }
</script>

<div class="modal-content">
    <div class="">
        <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Professional Tax Exemptions</h5><br />
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="" class=" " id="savedataa">
        <div class="modal-body" >
            <div class="row col-lg-12">
                <div class="col-lg-4 f-w-600"> Exemption Name</div>
                <div class="col-lg-2 f-w-600"> Code</div>
                <div class="col-lg-2 f-w-600"> Field </div>
                <div class="col-lg-2 f-w-600"> Range Type</div>
                <div class="col-lg-1 f-w-600"> Minmum Range</div>
                <div class="col-lg-1 f-w-600"> </div>
            </div>
            <div id="adddatahtml">
                <?php if(count((array)$exdatas)>0){
                    foreach($exdatas as $exdata){
                        $kid = $exdata['id'];
                ?>
                <div class="row col-lg-12 mt-2" id="<?php echo "old".$kid; ?>">
                    <div class="col-lg-4 f-w-600">
                      <input aria-label="Input field" type="text" class="form-control" name="exemptions[<?php echo $kid; ?>]" value="<?php echo $exdata['exemption_name']; ?>">
                    </div>
                    <div class="col-lg-2 f-w-600">
                        <input aria-label="Input field" type="text" class="form-control" name="exemption_code[<?php echo $kid; ?>]" value="<?php echo $exdata['exemption_code']; ?>">
                    </div>
                    <div class="col-lg-2 f-w-600">
                        <select aria-label="Select option" class="form-select js-example-basic-single" name="employee_field_name[<?php echo $kid; ?>]">
                            <option value="0"> Select Any One</option>
                            <?php foreach ($exfieds as $filed) {
                                    if(!in_array($filed, (array)$nodisarray)){ ?>
                                <option <?php if($filed== $exdata['employee_field_name']){ echo "selected";
} ?> value="<?php echo $filed; ?>"><?php echo viewTitle($filed); ?></option>
                            <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-2">
                        <select aria-label="Select option" class="js-example-basic-single" name="range_type[<?php echo $kid; ?>]" onchange="rangetype('new<?php echo $exdata['id']; ?>',this.value)">
                            <option value="0"> Select Any One </option>
                            <?php foreach((array)$range_type as $rkey=>$range_ty){ ?>
                                <option <?php if($rkey== $exdata['range_type']){ echo "selected";
} ?>  value="<?php echo $rkey; ?>"><?php echo $range_ty; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-lg-1 f-w-600">

                        <input aria-label="Input field" type="text"   <?php if($exdata['range_type']=='yes_no'){ echo "readonly";
} ?>  id="rrnew<?php echo $exdata['id']; ?>" class="form-control" name="minmum_range[<?php echo $kid; ?>]" value="<?php echo $exdata['minmum_range']; ?>">
                    </div>
                    <div class="col-lg-1 f-w-600 mt-3">
                        <i class="ti-trash text-danger cursor-pointer" onclick="deleteroe('<?php echo "old".$kid; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
                    </div>
                </div>
                <?php }
                } ?>
            </div>
            <div class="row mt-3">
                    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
                        <button class="btn btn-success"  onclick="addexrows()" id="addRows" type="button">+ Add More</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button onclick="savedata()" type="button"  class="btn btn-primary font-weight-bold">Save</button>
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

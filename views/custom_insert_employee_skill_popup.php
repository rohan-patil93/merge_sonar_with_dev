<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$emp_skills =array(); $skill = array(); $esk = array(); $emp_skills_data = array();
$data1["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0');
$data = urlencode(json_encode($data1));
$curl = API."?mode=gettabledetailswithconditions&table=skills&data=".$data;
$cudata = callAPI($curl);
$emp_skills = json_decode($cudata,1);

$data1["where"] = array('company_id'=>$_SESSION['company_id'],'emp_id'=>$_POST['emp_id'],'is_active'=>'0');
$data1["field"] = "id,emp_id,skill_id,years_of_exp,rating,comments";
$data = urlencode(json_encode($data1));
$curl = API."?mode=gettabledetailswithconditions&table=emp_skills&data=".$data;
$cudata = callAPI($curl);
$setemp_skills = json_decode($cudata,1);
foreach($setemp_skills as $setskil){
    if($setskil['id'] == $_POST['rid']){
        $emp_skills_data = $setskil;
    }else{
        $esk[$setskil['skill_id']] = $setskil['skill_id'];
    }
}
?>
<script type="text/javascript">
    function confirmsubmit(){
        var validations = ajaxFormSubmitValidation();
        if(validations == 0){
            var id = '<?php echo $_POST['rid']; ?>';
            var company_id =document.getElementById("company_id").value;
            var skill_id =document.getElementById("skill_id").value;
            var emp_id =document.getElementById("emp_id").value;
            var years_of_exp =document.getElementById("years_of_exp").value;
            var rating =document.getElementById("rating").value;
            var comments =document.getElementById("comments").value;
            var pass_data = "&skill_id="+skill_id+"&emp_id="+emp_id+"&years_of_exp="+years_of_exp+"&rating="+rating+"&comments="+comments+"&table_name=emp_skills&company_id="+company_id+"&id="+id;
            console.log(pass_data);
            $.ajax({
                url: "ajax.php",
                data:'page=setupcomponents'+pass_data,
                type: "POST",
                success: function(response) {
                    $('#kt_modal_KTDatatable_remote').modal("hide");
                    if(typeof reloadEmployeeSkills == 'function'){
                        reloadEmployeeSkills(emp_id);
                        if(id !=""){
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                        }else{
                            tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3000']; ?>','');
                        }
                    }else{
                        tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');

                        window.location.reload();
                    }

                }
            });
        }

    }
    function selectSkills(skill_id){
        if(skill_id !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=getselectskills&skill_id='+skill_id,
                type: "POST",
                success: function(response) {
                    $("#rating").html(response);
                    $(".removeevent").removeClass('event_disabled');
                }
            });
        }else{
            $(".removeevent").addClass('event_disabled');
        }
    }
</script>
<div class="modal-header">
    <h4 class="modal-title">Add Employee Skils</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <?php $event_disabled =""; if($emp_skills_data['id'] ==""){ $event_disabled = "event_disabled";
}?>
    <div class="row col-lg-12">
        <div class="form-group col-lg-6">
            <span class="form-label">Skills <span class="text-danger">*</span></span>
            <span class="d-block error-message text-danger"></span>
             <select aria-label="Skill" class="form-select js-example-basic-single requried_field" name="skill_id" id="skill_id" onchange="selectSkills(this.value)">
                <option value="">Select Any One</option>
                <?php if(count((array)$emp_skills)>0){ foreach ($emp_skills as $skill) { ?>
                    <option <?php if(in_array($skill['id'],$esk)){ ?> disabled="disabled" <?php
} ?> <?php echo $emp_skills_data['skill_id'] == $skill['id']?"selected":""; ?> value="<?php echo $skill['id']; ?>"><?php echo $skill['skill']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
            <input type="hidden" name="emp_id" value="<?php echo $_POST['emp_id']; ?>" id="emp_id">
            <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>" id="company_id">
        </div>
        <div class="form-group col-lg-6 <?php echo $event_disabled; ?> removeevent">
            <span class="form-label">Years Of Exp <span class="text-danger">*</span></span>
            <span class="d-block error-message text-danger"></span>
            <input aria-label="Years Of Exp" type="text" class="form-control text-right requried_field" id="years_of_exp"  name="years_of_exp" value="<?php echo $emp_skills_data['years_of_exp']; ?>">
        </div>
        <div class="form-group col-lg-6 <?php echo $event_disabled; ?> removeevent">
            <span class="form-label">Rating<span class="text-danger">*</span></span>
            <span class="d-block error-message text-danger"></span>
            <select aria-label="Rating"  id="rating"  name="rating" class="form-select js-example-basic-single requried_field">
                <option value="1" <?php echo $emp_skills_data['rating'] == 1?"selected":""; ?>>1</option>
                <option value="2" <?php echo $emp_skills_data['rating'] == 2?"selected":""; ?>>2</option>
                <option value="3" <?php echo $emp_skills_data['rating'] == 3?"selected":""; ?>>3</option>
                <option value="4" <?php echo $emp_skills_data['rating'] == 4?"selected":""; ?>>4</option>
                <option value="5" <?php echo $emp_skills_data['rating'] == 5?"selected":""; ?>>5</option>
                <option value="6" <?php echo $emp_skills_data['rating'] == 6?"selected":""; ?>>6</option>
                <option value="7" <?php echo $emp_skills_data['rating'] == 7?"selected":""; ?>>7</option>
                <option value="8" <?php echo $emp_skills_data['rating'] == 8?"selected":""; ?>>8</option>
                <option value="9" <?php echo $emp_skills_data['rating'] == 9?"selected":""; ?>>9</option>
                <option value="10" <?php echo $emp_skills_data['rating'] == 10?"selected":""; ?>>10</option>
            </select>
        </div>
        <div class="form-group col-lg-6 <?php echo $event_disabled; ?> removeevent">
            <span>Comments</span>
            <textarea aria-label="Comments" class="form-control" id="comments"  name="comments"><?php echo $emp_skills_data['comments']; ?> </textarea>
        </div>
    </div>
</div>
<div class="modal-footer">

    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 removeevent <?php echo $event_disabled; ?>" onclick="confirmsubmit();" name="submitform" value="exit"><i class="la la-check"></i>Save</button>
</div>
<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/moment-with-locales.min.js"></script>

<script type="text/javascript" src="assets/bower_components/jquery-bar-rating/js/jquery.barrating.js"></script>
<script type="text/javascript" src="assets/js/rating.js"></script>
<script>
    $(document).ready(function() {
        $('[data-bs-toggle="popover"]').popover({
            html: true,
            content: function() {
                return $('#primary-popover-content').html();
            }
        });
    });
</script>
<script type="text/javascript" src="assets/js/script.js"></script>

<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['rid'];
$hedcnt = $_POST['hedcnt'];
$header_id = $_POST['header_id'];
$loginuser_type = $_POST['loginuser_type'];
$epahurl = API."?mode=getemployeeperformanceappraisals&rid=".$rid."&ajax=Y&header_id=".$header_id;
$epasresult = callAPI($epahurl);
$result = json_decode($epasresult,1);
$paheaders  = $result['performance_appraisal_headers'];
$header_display_setting = json_decode($paheaders['header_display_setting'],1);
$sub_header_name = json_decode($paheaders['sub_header_name'],1);
?>
<tr id="deleterow_<?php echo $header_id.$hedcnt; ?>" >

    <?php $fieldcnt = 1; foreach($sub_header_name as $sub_headerres){
        $fieldname = "filed[".$header_id."][".$fieldcnt."][]";
        ?>
        <td>
            <div class="">
                <input aria-label="<?php echo __('Input field'); ?>" type="text" class="form-control" name="<?php echo $fieldname; ?>"  <?php echo $sub_headerres[$loginuser_type] == "N"?"readonly":""; ?> >
            </div>
        </td>
    <?php $fieldcnt++;
    } ?>
    <?php if($paheaders['sub_header_addmore'] == '0'){ //delete button visible 0 or 1 is not ?>
        <td>
                <button type="button" class="btn btn-link p-0 border-0" onclick="appraiserDeleteRow(<?php echo $header_id.$hedcnt; ?>)" aria-label="<?php echo __('Delete'); ?>"><i class="fa fa-trash text-danger"></i></button>

        </td>
    <?php } ?>
</tr>

<?php
$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$appraisal_id  = $erid['1'];
$url = API."?mode=getperformanceappraisalsettings&appraisal_id=".$appraisal_id;
$response = callAPI($url);
$pasetting_res = json_decode($response,1);
$performance_area_res = $pasetting_res['performance_appraisal_settings'];
foreach($pasetting_res['check_performance_appraisal_settings'] as $cpas){
    $checklist[$cpas['appraisal_setting_id']] = $cpas['appraisal_setting_id'];
}
?>
<script type="text/javascript">
    function checkperformanace(id) {
        if($('#optionget'+id).is(":checked")){
            var value = '0';
            $("#menu_show_"+id).show();
        }else{
            var value = '1';
            $("#menu_show_"+id).hide();
        }
        var pasappraisal_id = $("#pasappraisal_id").val();
        jQuery.ajax({
            url: "ajax.php",
            data:'page=updatepormanace&applicable='+value+'&id='+id+'&appraisal_id='+pasappraisal_id,
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][3001]; ?>','');
            },
            error:function (){}
        });
    }
</script>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header">
        <h5>Performance Appraisal Settings</h5>
        <div class="card-header-right">
            <?php if($_GET['option'] != "COM_VIEW_PERFORMANCE_APPRAISAL_SETTINGS"){ ?>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                    <i class="icofont icofont-reply"></i>  Back
                </a>
            <?php } ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <input type="hidden" id="pasappraisal_id" value="<?php echo $appraisal_id; ?>">
            <?php    foreach ($performance_area_res as $key => $value) { ?>
                <div class="col-lg-4">
                    <div class="card social-card">
                        <div class="inputGroup text-center">
                            <input id="optionget<?php echo $value['id']; ?>" name="applicable" type="checkbox" <?php echo ($checklist[$value['id']] != "")?'checked':''; ?> value="0" onchange="checkperformanace('<?php echo $value['id']; ?>')"  />
                            <label for="optionget<?php echo $value['id']; ?>" class="text-center">
                                <div class="text-facebook m-b-20"><i class="feather icon-home"></i></div>
                                <h3 class="text-facebook f-w-700"><?php echo $value['performance_area']?></h3>
                                <p><?php echo $value['performance_area_name']?></p>
                            </label>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

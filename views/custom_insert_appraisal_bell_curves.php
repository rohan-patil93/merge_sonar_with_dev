<?php
if(isset($_POST['submitbellcurves'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertappraisalbellcurves&method=post";
    $response1 = file_post_contents($url, $_POST, 'update');
    $response2 = json_decode($response1,1);
    $response =$response2['insert'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            $getid = $response["LASTID"];
            $Rrid = deocdeId("id:".$getid);
            header("location:index.php?option=COM_LIST_APPRAISAL_BELL_CURVES&Rid=".$Rrid); exit;
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        //($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }
}

$drid = encodeId($_GET['Rid']);
$erid = explode(":",$drid);
$rid = $erid['1'];
$apiurl = API."?mode=getappraisalbellcurves&rid=".$rid;
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
$appraisal_rating_slabs = $result['appraisal_rating_slabs'];
$appraisal_bell_curves = $result['appraisal_bell_curves'];
$slabsval = json_decode($appraisal_bell_curves['slabs'],1);
?>
<script type="text/javascript">
    function getTotalBellCurve(){
        var sumcnt = 0;
        $('.bell_curve_sum').each(function(){
            if($(this).val() !="" && $(this).val() !="0"){
                sumcnt += parseFloat($(this).val());
            }
        });
        $("#bell_curve_sum_text").text(sumcnt+"%");
        if(sumcnt ==100){
            $('#bell_curve_btn').prop('disabled', false);
        }else{
            $('#bell_curve_btn').prop('disabled', true);
        }
    }
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
        </div>
<?php } ?>
<div class="card custom_card">
    <div class="card-header">
        <h5><?= __('Performance Bell Curves'); ?> </h5>
        <div class="card-header-right table-card-header m-r-50">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply" aria-hidden="true"></i>
                <?= __('Back'); ?>
            </a>
        </div>
    </div>
    <div class="card-body ">
        <form method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="form-group col-lg-4">
                    <label class="form-label" for="bell_curve_name"><?= __('Bell Curve Name') ?><span class="text-danger">*</span></label>
                    <input type="text" name="bell_curve_name" id="bell_curve_name" class="form-control" value="<?php echo $appraisal_bell_curves['bell_curve_name']; ?>" required aria-label="<?= htmlspecialchars(__('Bell Curve Name'), ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="form-group col-lg-8">
                    <div class="row">
                        <div class=" col-lg-6">
                             <span class="fw-bold font-weight-bold"><?= __('Slab Name'); ?><span class="text-danger">*</span></span>
                        </div>
                         <div class="col-lg-6 text-right">
                             <span class="fw-bold font-weight-bold"><?= __('Percentage (%)'); ?><span class="text-danger">*</span></span>
                        </div>
                    </div>
                    <?php $sum = 0; foreach($appraisal_rating_slabs as $aprslab){
                        $slab11 = $slabsval[$aprslab['id']];
                        $sum += $slab11['percentage'];
                        $slab_id = $aprslab['id'];
                        ?>
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <input type="hidden" name="slabs[<?php echo $slab_id; ?>][appraisal_rating_slab_id]" value="<?php echo $slab_id; ?>">
                            <input type="text" class="form-control" id="slab_name_<?php echo $slab_id; ?>" value="<?php echo $aprslab['slabs_name']; ?>" style="background-color:<?php echo $aprslab['background_color']; ?>; color: <?php echo $aprslab['font_color']; ?> " readonly aria-label="<?php echo htmlspecialchars($aprslab['slabs_name']); ?>">
                        </div>
                        <div class="form-group col-lg-6">
                            <div class="input-group input-group-button">
                                <input type="number" id="slab_percentage_<?php echo $slab_id; ?>" name="slabs[<?php echo $slab_id; ?>][percentage]" class="form-control bell_curve_sum text-right width1" value="<?php echo $slab11['percentage']; ?>" onblur="getTotalBellCurve()" required aria-label="Percentage for <?php echo htmlspecialchars($aprslab['slabs_name']); ?>">
                                <span class="input-group-text addon-style">
                                    <span class="">%</span>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <div class="row">
                        <div class="form-group col-lg-6 text-right">
                             <span class="form-label fw-bold font-weight-bold"><?= __('Total'); ?><span class="text-danger">*</span></span>
                        </div>
                        <div class="form-group col-lg-6 text-right">
                            <span class="form-label " id="bell_curve_sum_text"><?php echo $sum; ?>%</span>
                        </div>
                    </div>
                 </div>
            </div>
            <input type="hidden" name="rid" value="<?php echo $rid; ?>">
            <button type="submit" class="btn btn-primary waves-effect waves-light mt-5" id="bell_curve_btn" name="submitbellcurves" <?php echo $sum ==100?"":"disabled";?>><i class="fa fa-check m-r-10" aria-hidden="true"></i> <?php echo $rid ==""?__("Submit"):__("Update"); ?></button>
        </form>
    </div>

</div>

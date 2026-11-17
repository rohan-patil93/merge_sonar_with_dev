include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=VERIFYASSETS&assetid=".$_POST['assetid'];
$data_details = callAPI($url);
$details = json_decode($data_details,1);
$assetcategory = $details['asset_categories'];
$list_assets = $details['list_assets'];
$locations = $details['locations'];
$users = $details['users'];
$asset_verifications = $details['asset_verifications'];
$alredyverfyinpro = 0;
foreach($list_assets as $list_asset){
    if(isset($asset_verifications[$list_asset['id']])){
        $alredyverfyinpro++;
    }
}

?>
<script type="text/javascript">

function verificationSubmitdetails(){
    var notemtyarry=['user_id','start_date_time','end_date_time'];
    var validation = 0;
    $.each(notemtyarry, function(key, fieldid) {
        if($("#"+fieldid).val() ==""){
            $("#errormessage_"+fieldid).text(<?php echo json_encode(__('Field Is Requerd'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>);
            validation = 1;
        }else{
            $("#errormessage_"+fieldid).text("");
        }
    });
    if(validation == 0){
           var dataarray = $("#assetverificationform").serialize();
        jQuery.ajax({
            url: "ajax.php",
            data: "page=insertassetverification&"+dataarray,
            type: "POST",
            success:function(data){
                $('#kt_modal_dynamic_popup2').modal("hide");
               tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo  $_SESSION['error_message']['3000']; ?>",'');
            },
            error:function (){}
        });
    }else{
        $("#kt_modal_dynamic_popup2").animate({ scrollTop: 0 }, 500);
    }
}
</script>
<div class="modal-header">
    <h4 class="modal-title"><?= __('Verify Asset'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <?php if($alredyverfyinpro != 0){ $btndisable = "disabled";?>
    <div class="alert alert-danger background-danger alert-dismissible">
        <strong class="f-16"><?php echo $alredyverfyinpro; ?> - <?= __('Assets Already Verification In Process'); ?></strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
    </div>
    <?php } ?>
    <form id="assetverificationform" method="post">
        <input type="hidden" name="asset_id" value="<?php echo $_POST['assetid']; ?>">
        <div class="row">
            <div class="col-lg-4 from-group w-full">
                <span class="form-label d-block"><?= __('Verify Asset User'); ?></span>
                <select aria-label="<?= htmlspecialchars(__('Verification User'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single " name="verification_user_id" id="user_id" >
                    <option value=""><?= __('Select Any One') ?></option>
                    <?php foreach ($users as $userlist) { ?>
                        <option value="<?php echo $userlist['id']; ?>" ><?php echo ucwords($userlist['display_name']); ?></option>
                    <?php } ?>
                </select>
                <span id="errormessage_user_id" class="text-danger emtmsg"></span>
            </div>
            <div class="col-lg-4 from-group">
                <span class="form-label"><?= __('Start Date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('Start Date Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="datetime-local" name="start_date_time" id="start_date_time">
                <span id="errormessage_start_date_time" class="text-danger emtmsg"></span>
            </div>
            <div class="col-lg-4 from-group">
                <span class="form-label"><?= __('End Date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('End Date Time'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="datetime-local" name="end_date_time" id="end_date_time">
                <span id="errormessage_end_date_time" class="text-danger emtmsg"></span>
            </div>
        </div>
    </form>
    <div class="row mt-3">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="example-1">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?= __('Asset Code') ?></th>
                        <th scope="col"><?= __('Asset Name') ?></th>
                        <th scope="col"><?= __('Location') ?></th>
                        <th scope="col"><?= __('Asset Custodian') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $mm=1; foreach($list_assets as $list_asset){ ?>
                    <input type="hidden" name="asset_id[]" value="<?php echo $list_asset['id']; ?>">
                    <tr class="<?php echo isset($asset_verifications[$list_asset['id']])?"bg-danger text-white":"";?>">
                        <td scope="row"><?php echo $mm; ?></td>
                        <td class="tabledit-view-mode" style="pointer-events: none;"><span class="tabledit-span"><?php echo $list_asset['asset_code']; ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Asset Code'), ENT_QUOTES, 'UTF-8'); ?>[]" class="tabledit-input form-control input-sm" type="text" name="asset_code[]" value="<?php echo $list_asset['asset_code']; ?>" style="display:none;">
                        </td>
                        <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $list_asset['asset_description']; ?></span>
                            <input aria-label="<?= htmlspecialchars(__('Asset Description'), ENT_QUOTES, 'UTF-8'); ?>[]" class="tabledit-input form-control input-sm" type="text" name="asset_description[]" value="<?php echo $list_asset['asset_description']; ?>" style="display:none;">
                        </td>
                        <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $locations[$list_asset['asset_location']]; ?></span>
                        </td>
                        <td class="tabledit-view-mode"><span class="tabledit-span"><?php echo $_SESSION['users'][$list_asset['asset_custodian']]; ?></span>
                        </td>

                    </tr>
                    <?php $mm++;
} ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close') ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="verificationSubmitdetails()" <?php echo $btndisable; ?>><?= __('Submit') ?></button>
</div>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
</script>

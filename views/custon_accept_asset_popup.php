<?php print_r($_POST);
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<script>
function submitdetails(allocation_type){

      var formData = new FormData();
    formData.append("page", "assetacceptreectvms");
    formData.append("allocation_type", allocation_type);
    var dataArray = $("#assetsallocationform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value);
    });
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                $('#kt_modal_dynamic_popup2').modal("hide");
                console.log(data);
                location.reload()

            }
        });
}
</script>
<div class="modal-header">
    <h4 class="modal-title"><?= __('Asset Allocation'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body j-pro">
    <div class="j-content">
    <form id="assetsallocationform" method="post" >
        <div class="row ">
    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
    <input type="hidden" name="user_id" value="<?php echo $_SESSION['userid']; ?>">
    <input type="hidden" name="admin_buddy" value="<?php echo $_POST['admin_budddy']; ?>">
    <input type="hidden" name="asset_id" value="<?php echo $_POST['assetid']; ?>">
        <input type="hidden" name="emp_asset_id" value="<?php echo $_POST['id']; ?>">
    <div class="card p-2 mt-2">
        <div class="row">
            <div class="form-group col-lg-3">
                <span><?= __('Received date'); ?></span>
                <input aria-label="<?= htmlspecialchars(__('Received Date'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control datepicker" id="received_date"  name="received_date" value="">
            </div>
            <div class="form-group col-lg-9">
                <span><?= __('Note'); ?></span>
                <textarea aria-label="<?= htmlspecialchars(__('Received Note'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="received_note" rows="4" cols="50" name="received_note" ></textarea>
            </div>
        </div>
    </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails('1')"><?= __('Accept'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails('2')"><?= __('Reject'); ?></button>
</div>

<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
    $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "<i class='ti-calendar'></i>",
        dateFormat: date_format,
        changeMonth: true,
        changeYear: true
    });
</script>

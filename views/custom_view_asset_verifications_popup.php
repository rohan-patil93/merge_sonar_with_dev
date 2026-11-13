<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$File_Uploads_Path = "uploads/assets/";
$url = API."?mode=getassetverifications&verification_id=".$_POST['verification_id'];
$data_details = callAPI($url);
$result = json_decode($data_details,1);
$asset_verifications = $result['asset_verifications'];
$asset_condition_type = $result['asset_condition_type'];
$locations= $result['locations'];
?>
<style>
    .drop-zone {
        height: 300px;
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-family: "Quicksand", sans-serif;
        font-weight: 500;
        font-size: 20px;
        cursor: pointer;
        color: #cccccc;
        border: 4px dashed #009578;
        border-radius: 10px;
    }

    .drop-zone--over {
        border-style: solid;
    }

    .drop-zone__input {
        display: none;
    }

    .drop-zone__thumb {
        width: 100%;
        height: 160px;
        border-radius: 10px;
        overflow: hidden;
        background-color: #cccccc;
        background-size: cover;
        position: relative;
    }

    .drop-zone__thumb::after {
        content: attr(data-label);
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 5px 0;
        color: #ffffff;
        background: rgba(0, 0, 0, 0.75);
        font-size: 14px;
        text-align: center;
    }
</style>
<script type="text/javascript">
    document.querySelectorAll(".drop-zone__input").forEach((inputElement) => {
        const dropZoneElement = inputElement.closest(".drop-zone");

        dropZoneElement.addEventListener("click", (e) => {
            inputElement.click();
        });

        inputElement.addEventListener("change", (e) => {
            if (inputElement.files.length) {
                updateThumbnail(dropZoneElement, inputElement.files[0]);
            }
        });

        dropZoneElement.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZoneElement.classList.add("drop-zone--over");
        });

        ["dragleave", "dragend"].forEach((type) => {
            dropZoneElement.addEventListener(type, (e) => {
                dropZoneElement.classList.remove("drop-zone--over");
            });
        });

        dropZoneElement.addEventListener("drop", (e) => {
            e.preventDefault();

            if (e.dataTransfer.files.length) {
                inputElement.files = e.dataTransfer.files;
                updateThumbnail(dropZoneElement, e.dataTransfer.files[0]);
            }
            dropZoneElement.classList.remove("drop-zone--over");
        });
    });
    function updateThumbnail(dropZoneElement, file) {
        let thumbnailElement = dropZoneElement.querySelector(".drop-zone__thumb");
// First time - remove the prompt
if (dropZoneElement.querySelector(".drop-zone__prompt")) {
    dropZoneElement.querySelector(".drop-zone__prompt").remove();
}
if (!thumbnailElement) {
    thumbnailElement = document.createElement("div");
    thumbnailElement.classList.add("drop-zone__thumb");
    dropZoneElement.appendChild(thumbnailElement);
}
thumbnailElement.dataset.label = file.name;
if (file.type.startsWith("image/")) {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => {
        thumbnailElement.style.backgroundImage = `url('${reader.result}')`;
    };
} else {
    thumbnailElement.style.backgroundImage = null;
}
}

function submitVerification(){
      var formData = new FormData();
    formData.append("page", "updateassetverificationstatus");
    var dataArray = $("#assetverificationform").serializeArray(),
    dataObj = {};
    $(dataArray).each(function(i, field){
      formData.append(field.name, field.value);
    });
    var popattachment = document.getElementById('popattachment').files[0];
   formData.append("file",popattachment);
        jQuery.ajax({
            url: "ajax.php",
            data: formData,
            type: "POST",
            contentType: false,
            processData: false,
            success:function(data){
                $('#kt_modal_dynamic_popup2').modal("hide");
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX',"<?php echo $_SESSION['error_message']['9073']; ?>",'');
                getVerifyList('ALL','')
            },
            error:function (){}
        });
}
</script>
<div class="modal-header">
    <h4 class="modal-title"><?= __('Verify Asset'); ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body">
    <div class="row mb-2">
        <div class="col-lg-6">
            <table style="width: 100%;">
                <tr><th scope="col"><?= __('Asset Code'); ?></th><td class="w-b-w-s"><?php echo $asset_verifications['asset_code']; ?></td></tr>
                <tr><th scope="col"><?= __('Asset Name'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['asset_description']; ?></td></tr>
                <tr><th scope="col"><?= __('Serial No'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['serial_no']; ?></td></tr>
                <tr><th scope="col"><?= __('Category_name'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['category_name']; ?></td></tr>
                <tr><th scope="col"><?= __('Bar Code'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['bar_code']; ?></td></tr>
                <tr><th scope="col"><?= __('Asset Location'); ?></th><td  class="w-b-w-s"><?php echo $locations[$asset_verifications['asset_location']]['location_name']; ?></td></tr>
                <tr><th scope="col"><?= __('Brand'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['brand']; ?></td></tr>
                <tr><th scope="col"><?= __('Model'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['model']; ?></td></tr>
                <tr><th scope="col"><?= __('Description'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['long_description']; ?></td></tr>
            </table>
        </div>
        <div class="col-lg-6" >
            <table style="width: 100%;">

                <tr><th scope="col"><?= __('Conditions'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['condition_name']; ?></td></tr>
                <tr><th scope="col"><?= __('Serial_no'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['serial_no']; ?></td></tr>
                <tr><th scope="col"><?= __('Warranty Expiry Date'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['warranty_expiry_date']; ?></td></tr>
                <tr><th scope="col"><?= __('End Of Life'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['end_of_life']; ?></td></tr>
                <tr><th scope="col"><?= __('Asset Owner'); ?></th><td  class="w-b-w-s"><?php echo $asset_verifications['asset_owner']; ?></td></tr>
                <tr><th scope="col"><?= __('Asset Custodian'); ?></th><td  class="w-b-w-s"><?php echo $_SESSION['users'][$asset_verifications['asset_custodian']]; ?></td></tr>

            </table>
        </div>
    </div>
    <form id="assetverificationform" method="post">
        <input type="hidden" name="verification_id" value="<?php echo $_POST['verification_id']; ?>">
        <input type="hidden" name="asset_id" value="<?php echo $asset_verifications['id']; ?>">
        <div class="row">
            <div class="col-lg-4 from-group w-full">
                <span class="form-label d-block"><?= __('Condition'); ?></span>
                <select aria-label="<?= htmlspecialchars(__('Conditions'), ENT_QUOTES, 'UTF-8'); ?>"  class="form-select js-example-basic-single " name="conditions" id="conditions" >
                    <option value=""><?= __('Select Any One'); ?></option>
                    <?php foreach ($asset_condition_type as $keyid=>$condition) { ?>
                        <option value="<?php echo $keyid; ?>" <?php echo $asset_verifications['conditions']==$keyid?"selected":""; ?>><?php echo $condition; ?></option>
                    <?php } ?>
                </select>
                <span id="errormessage_conditions" class="text-danger"></span>
            </div>
            <div class="col-lg-4 from-group">
                <span class="form-label"><?= __('Note'); ?></span>
                <textarea aria-label="<?= htmlspecialchars(__('User Note'), ENT_QUOTES, 'UTF-8'); ?>" type="text" class="form-control" id="user_note" rows="8" cols="50" name="user_note"  ></textarea>
                <span id="errormessage_user_note" class="text-danger"></span>
            </div>
            <div class="col-lg-4 from-group">
                <span class="form-label"><?= __('Asset Image'); ?></span>
                <div class="card-block">
                    <input type="hidden" id="oldattachment" name="oldattachment" value='<?php echo $asset_verifications['asset_img']; ?>'>
                    <div class="drop-zone">
                        <?php if($asset_verifications['asset_img'] !=""){ ?>
                            <div class="drop-zone__thumb"  style="background-image: url(<?php echo $File_Uploads_Path.$asset_verifications['asset_img']; ?>)"></div>
                        <?php  }else{ ?>
                            <span class="drop-zone__prompt"><?= __('Drop file here or click to upload'); ?></span>
                        <?php } ?>
                        <input aria-label="<?= htmlspecialchars(__('Asset Img'), ENT_QUOTES, 'UTF-8'); ?>" type="file" name="asset_img" class="drop-zone__input" id="popattachment" >
                    </div>
                </div>
                <span id="errormessage_end_date_time" class="text-danger emtmsg"></span>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitVerification()"><?= __('Submit'); ?></button>
</div>
<script type="text/javascript">
    $(".js-example-basic-single").select2();
    $(".js-example-disabled-results").select2();
</script>

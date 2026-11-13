<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$url = API."?mode=insertvendorandvendorregisterssynchapi";
$api = callAPI($url);
$result = json_decode($api,1);
echo "<pre>";  print_r($result); echo "</pre>";

// ----------------------------------------------
// ----------------------------------------------

// ----------------------------------------------
// ----------------------------------------------

?>
<div class="modal-header">
    <h5 class="modal-title"> <?= __('Vendor Summary'); ?> </h5>

    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>

<div class="modal-body" id="printDiv_popup">
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
</div>

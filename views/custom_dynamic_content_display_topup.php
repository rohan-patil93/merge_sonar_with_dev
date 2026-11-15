<?php
include_once('../includes/session.php');
include_once('../dbconfig.php');
include_once('../comm.php');
$header = $_POST['header'];
$data= $_POST['data'];
?>
<div class="modal-header">
	<h5 class="modal-title"><?php  echo $header; ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= __('Close') ?>"></button>
</div>
<div class="modal-body" >
<?php  echo $data; ?>
</div>

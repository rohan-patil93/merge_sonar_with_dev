<?php 
include('../includes/session.php');
include('../dbconfig.php');
include('../comm.php');
$header = $_POST['header'];
$data= $_POST['data'];
?>
<div class="modal-header">
	<h5 class="modal-title"><?php  echo $header; ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" > 
<?php  echo $data; ?>
</div>

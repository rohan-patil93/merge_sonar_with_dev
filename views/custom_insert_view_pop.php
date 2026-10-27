<?php
if(isset($_GET['popup']) && $_GET['popup']!=""){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$saidnav = $_SESSION['application_menu_LN'];
}
include_once "../includes/controller.php";
$dispage = str_replace("views/", '', $disptemp);
?>
<?php include_once "../partials/_head.php" ?>
<div class="modal-header modal-header text-white p-25 header-bg" style="border-top-right-radius: 20px; border-top-left-radius: 20px;">
    <h4 class="modal-title"><?php echo $_GET['title']; ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="custom-scroll" style="height: 600px;">
    <div class="p-20">
<?php include_once $dispage; ?>
<?php include_once "fooer_js.php"; ?>

</div>
</div>

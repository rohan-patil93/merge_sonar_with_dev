<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$path = $_POST['path'];
$path_parts = pathinfo($path);
$file_extension = $path_parts['extension'];
$urlfilepath = SITEURL.$path;
$googledocsfile = array('docx','pdf');
?>
<div class="modal-header">
      <h5 class="modal-title">
        Preview Docs
      </h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body  text-center" >

<div class="text-center" id="loader_popup"><img src="assets/images/spiner/spiner3.gif" style="width: 100px;" alt="Preview"></div>

    <?php if(in_array($file_extension, $googledocsfile)){ ?>

        <iframe src="<?php echo $path; ?>" width="600" height="400" frameborder="0" id="myIframe_2" title="File Preview"></iframe>
    <?php }else{  ?>
        <iframe src="<?php echo $path; ?>" width="600" height="400" frameborder="0" id="myIframe_3" title="File Preview"></iframe>
    <?php } ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-bs-dismiss="modal">Close</button>
</div>
<script>
var loader = document.getElementById("loader_popup");
var iframe = document.getElementById("myIframe");
iframe.addEventListener("load", function() {
  loader.style.display = "none";
});
iframe.addEventListener("DOMContentLoaded", function() {
  loader.style.display = "block";
});
</script>

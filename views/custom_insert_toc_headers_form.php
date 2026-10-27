<?php
$tocheaderssingle=array();
if(isset($_POST['editform']) && $_POST['editform'] != ""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $toc_id = $_POST['toc_id'];
    $url1 = API."?mode=gettocheader&id=".$_POST['id']."&EID=".$toc_id;
    $jsondecode = callAPI($url1);
    $result = json_decode($jsondecode,1);
    $toc_headers = $result['toc_headers'];
    $tocheaderssingle = $result['toc_headers_single'];
    $reportgroupid = ($_POST['pargrp'] !="0" && $_POST['pargrp'] !="")?$_POST['pargrp']:$tocheaderssingle['parent_toc_header_id'];

}

?>
  <div class="card">
    <div class="card-header">
        <h5>Header </h5>
        <div class="card-header-right"></div>
    </div>
    <div class="card-body">
        <form id="formtocheaders">
        <div class="row">

                <div class="form-group col-lg-4">
                    <span class="form-label">Header Name <span class="text-danger">*</span></span>
                    <input aria-label="Header Name" type="text"  class="form-control text-capitalize"  name="header_name" value="<?php echo $tocheaderssingle['header_name']; ?>" />
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Display Priority <span class="text-danger">*</span></span>
                    <input aria-label="Display Priority" type="text"  class="form-control text-capitalize"  name="display_priority" value="<?php echo $tocheaderssingle['display_priority']; ?>" />
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Parent Header<span class="text-danger">*</span></span>
                    <select aria-label="Parent Toc Header" class="form-control border" name="parent_toc_header_id" style="-webkit-appearance:auto;" >
                      <option  value="0">Parent</option>
                      <?php
                      foreach($toc_headers as $toc_header){ ?>

                       <option <?php if($toc_header['id']== $reportgroupid){ echo "selected";
} ?> value="<?php echo $toc_header['id']; ?>"><?php echo $toc_header['header_name']; ?></option>
                     <?php } ?>
                   </select>
                </div>

            <div class="form-group col-lg-12">
                <span class="form-label">Text<span class="text-danger">*</span></span>
                <textarea aria-label="Header Contain" id="editor1" class="summernote email_content_1" name="header_contain"  > <?php echo urldecode(json_decode($tocheaderssingle['header_contain'],1)); ?></textarea>
            </div>
            <div class="col-lg-12 form-group">
                <input type="hidden" name="Rid" id="Rid" value="<?php echo $tocheaderssingle['id']; ?>">
                <input type="hidden" name="toc_id" id="toc_id" value="<?php echo $toc_id; ?>">
                <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mt-2" onclick="addtocheader()" >
                 <?php if($tocheaderssingle['id'] !="" && $tocheaderssingle['id'] !="0"){ echo "Update Header";
}else{ echo "<i class='fa fa-plus'></i> Add Header";
} ?></button>
             </div>
         </div>
     </form>
     </div>
 </div>
  <?php
if(isset($_POST['editform']) && $_POST['editform'] != ""){?>
     <script src="assets/pages/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="assets/pages/ckeditor/ckeditor-custom.js"></script>
    <?php } ?>

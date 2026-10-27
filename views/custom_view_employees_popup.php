<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$urlemp = urldecode($_POST['empids']);
$empids = json_decode($urlemp,1);
$emp_ids = implode(',',(array)$empids);
$post['empids'] = $emp_ids;
$vurl = API."?mode=togetemployeedetaila&method=post";
$empdata = file_post_contents($vurl,$post);
$bulkdata = json_decode($empdata,1);
foreach($bulkdata as $bulkd){
    $checkd = strtolower($bulkd['display_name']);
    if(in_array($checkd['0'],array('a','b','c','d','e','f'))){
        $one[]=$bulkd;
    }
    if(in_array($checkd['0'],array('g','h','i','j','k','l'))){
        $two[]=$bulkd;
    }
    if(in_array($checkd['0'],array('m','n','o','p','q','r'))){
        $tree[]=$bulkd;
    }
    if(in_array($checkd['0'],array('s','t','u','v','w','x','y','z'))){
        $four[]=$bulkd;
    }
}
?>
<script type="text/javascript">
    function  getBasicInfoPopUp(emp_id) {
        jQuery.ajax({
              url: "views/custom_insert_onboarding_employees.php",
              data:'emp_id='+emp_id,
              type: "POST",
              success:function(data){
                $('#dynamic_popupxxl').html(data); //popup1
               $('#kt_modal_dynamic_popupxxl').modal("show");
                $(".js-example-basic-single").select2();
                $(".js-example-disabled-results").select2();
              },
              error:function (){}
        });
    }
</script>
<div class="modal-header">
    <h5 class="modal-title text-center">
        Employees List
    </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

</div>
<div class="modal-body" data-scroll="true" data-height="500" style="height: 500px;">
    <div class="form-group row" >
        <div class="">
            <div class="card-body">

        <div class="row">
          <div class="col-lg-9"></div>
          <div class="col-lg-3">
                  <div class="input-group input-group-button mb-3">
                    <input aria-label="Search The Name" type="text" id="filter1111" class="form-control form-control-sm width1"  placeholder="Search The Name" data-bs-toggle="tooltip" data-placement="bottom" title="Search The Name">
                    <span class="input-group-text addon-style">
                        <span class=""><i class="ti ti-search"></i></span>
                    </span>
                </div>
          </div>
        </div>
                <div class="col-lg-12 row custom-scroll" style="height: 400px;">
                    <div class="col-lg-3 virticalline " >
                        <div class="f-w-600 m-b-10 text-center ">A to F </div>
            <div id="results">
                        <?php foreach ($one as $key => $value) { ?>
                      <div class=" card-sub ui-sortable-handle p-2 results">
                          <div class="align-middle p-1">
                              <?php if($value['image']!="") { ?>
                                  <img src="uploads/all/<?php echo $value['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                              <?php } else { ?>
                                  <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $value['display_name']; ?>"><?php echo initials($value['display_name']); ?></span>
                              <?php } ?>
                              <div class="d-inline-block width73">
                                  <button type="button" !" class="text-dark text-capitalize" onclick="getBasicInfoPopUp(<?php echo $value['id']; ?>)" ><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></button>
                                  <p class="text-muted m-b-0"><?php echo $value['email_id']!="" ? $value['email_id'] : "&nbsp;"; ?></p>
                              </div>
                          </div>
                      </div>
                  <?php } ?>
                  </div>
              </div>
              <div class="col-lg-3 virticalline ">
                  <div class="f-w-600 m-b-10 text-center">G to L </div>
                <div id="results_2">
                  <?php foreach ($two as $key => $value) { ?>
                      <div class=" card-sub ui-sortable-handle p-2">
                          <div class="align-middle p-1">
                              <?php if($value['image']!="") { ?>
                                  <img src="uploads/all/<?php echo $value['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                              <?php } else { ?>
                                  <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $value['display_name']; ?>"><?php echo initials($value['display_name']); ?></span>
                              <?php } ?>
                              <div class="d-inline-block width73">
                                  <button type="button" !" onclick="getBasicInfoPopUp(<?php echo $value['id']; ?>)"  class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></button>
                                  <p class="text-muted m-b-0"><?php echo $value['email_id']!="" ? $value['email_id'] : "&nbsp;"; ?></p>

                              </div>
                          </div>
                      </div>
                  <?php } ?>
              </div></div>
              <div class="col-lg-3 virticalline ">
                  <div class="f-w-600 m-b-10 text-center">M to R </div>
                <div id="results_3">
                  <?php foreach ($tree as $key => $value) { ?>
                      <div class=" card-sub ui-sortable-handle p-2">
                          <div class="align-middle p-1">
                              <?php if($value['image']!="") { ?>
                                  <img src="uploads/all/<?php echo $value['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                              <?php } else { ?>
                                  <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $value['display_name']; ?>"><?php echo initials($value['display_name']); ?></span>
                              <?php } ?>
                              <div class="d-inline-block width73">
                                  <button type="button" !" onclick="getBasicInfoPopUp(<?php echo $value['id']; ?>)"  class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></button>
                                  <p class="text-muted m-b-0"><?php echo $value['email_id']!="" ? $value['email_id'] : "&nbsp;"; ?></p>
                              </div>
                          </div>
                      </div>
                  <?php } ?>
              </div></div>
              <div class="col-lg-3 ">
                  <div class="f-w-600 m-b-10 text-center">S to Z </div>
                <div id="results_4">
                  <?php foreach ($four as $key => $value) { ?>
                      <div class=" card-sub ui-sortable-handle p-2">
                          <div class="align-middle p-1">
                              <?php if($value['image']!="") { ?>
                                  <img src="uploads/all/<?php echo $value['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                              <?php } else { ?>
                                  <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $value['display_name']; ?>"><?php echo initials($value['display_name']); ?></span>
                              <?php } ?>
                              <div class="d-inline-block width73">
                                  <button type="button" !" onclick="getBasicInfoPopUp(<?php echo $value['id']; ?>)"  class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></button>
                                  <p class="text-muted m-b-0"><?php echo $value['email_id']!="" ? $value['email_id'] : "&nbsp;"; ?></p>

                              </div>
                          </div>
                      </div>
                  <?php } ?>
              </div></div>
          </div>

      </div>
  </div>
</div>
</div>

<div class="modal-footer">
    <button name="" type="button" class="btn btn-primary mr-2"  data-bs-dismiss="modal" >OK</button>
</div>

<?php /**/ ?>

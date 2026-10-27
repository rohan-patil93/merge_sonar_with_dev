<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$urlemp = urldecode($_POST['empids']);
$empids = json_decode($urlemp,1);
$emp_ids = implode(',',(array)$empids);
$post['empids'] = $emp_ids;
$url = API."?mode=getsubscriptionusers&application_users=Y";
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$bulkdata = $subscriptusers['application_user'];
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
    function assignedUserlicens(id){
        var checktype ='N';
        var company_subscr_id = "<?php echo $_POST['company_subscr_id'];?>";
        if($("#getuserid_"+id).is(':checked')){
            var checktype ='Y';
        }
         jQuery.ajax({
            url: "ajax.php",
            data: 'page=assigneduserlicens&company_subscr_id='+company_subscr_id+"&checktype="+checktype+"&userid="+id,
            type: "POST",
            success:function(data){
                $('#popupm').html(data);
                $('#kt_modal_KTDatatable_remotem').modal("show");
            }
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
                        <span class=""><i class="ti-search"></i></span>
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

                        <div class="align-middle p-1 position-relative">

                            <?php if($value['image']!="") { ?>
                                <img src="uploads/all/<?php echo $value['image']; ?>" class="img-radius img-40 align-top m-r-15" alt="Attachment Preview">
                            <?php } else { ?>
                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center" data-toggle="tooltip" title="" data-original-title="<?php echo $value['display_name']; ?>"><?php echo initials($value['display_name']); ?></span>
                            <?php } ?>
                            <div class="d-inline-block width73">
                                <a href="#!" class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6>
                                </a>
                                <p class="text-muted m-b-0"><?php echo $value['user_email']!="" ? $value['user_email'] : "&nbsp;"; ?></p>

                            </div>

 <div class="checkbox-fade fade-in-primary float-right">
                                      <label class="form-label">
                                        <input aria-label="Checkbox" type="checkbox" value="<?php echo $value['id']; ?>" id="getuserid_<?php echo $value['id']; ?>" onchange="assignedUserlicens('<?php echo $value['id']; ?>')">
                                        <span class="cr position-absolute top-0">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span></span>
                                      </label>
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
                                <a href="#!" class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></a>
                                 <div class="checkbox-fade fade-in-primary float-right">
                                      <label class="form-label">
                                        <input aria-label="Checkbox" type="checkbox" value="">
                                        <span class="cr">
                                          <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                        </span>
                                        <span></span>
                                      </label>
                                    </div>
                                <p class="text-muted m-b-0"><?php echo $value['user_email']!="" ? $value['user_email'] : "&nbsp;"; ?></p>

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
                                <a href="#!" class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></a>
                                <p class="text-muted m-b-0"><?php echo $value['user_email']!="" ? $value['user_email'] : "&nbsp;"; ?></p>
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
                                <a href="#!" class="text-dark text-capitalize"><h6 class="mb-1"><?php echo $value['display_name']; ?></h6></a>
                                <p class="text-muted m-b-0"><?php echo $value['user_email']!="" ? $value['user_email'] : "&nbsp;"; ?></p>

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


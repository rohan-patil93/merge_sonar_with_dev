<?php
$_SESSION['apply_js']['crop_image']='1';
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['building_floor_id'];
$url = API."?mode=getbuildingfloors&rid=".$rid;
$response = callAPI($url);
$result = json_decode($response,1);
$buildings = $result['buildings'];
$building_floors = $result['building_floors'];
$space_types = $result['space_types'];
$building_floor_spaces = $result['building_floor_spaces'];
$all_building_floors=$result['all_building_floors'];
$employee_res = $result['employees'];
foreach($employee_res as $empr){
  $employees[$empr['id']] = $empr;
}
$building_floor_space_allocations = $result['building_floor_space_allocations'];
foreach($building_floor_space_allocations as $bfsa){
  $spacewithallocation[$bfsa['building_floor_space_id']] = $bfsa;
}
$seating_pools  =$result['seating_pools'];
?>
<style type="text/css">
   #floor_cabin{
        position: relative;

    }
    #disable_crop_floor_image{
        width: 100%;
    }
    .cropper-crop-box{
        pointer-events: all !important;
    }
    .cropper-face{
        cursor: pointer !important;
    }
</style>
<script type="text/javascript">
  function toCubicleSpaceAllocateEmployeePopup(bfs_id){
    closeToAllocateEmployee();
    $(".to_allocate_employee_popup_"+bfs_id).show();
    var htmloader = '<div class="text-center"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $(".to_allocate_employee_popup_"+bfs_id).html(htmloader);
    jQuery.ajax({
        url: "views/custom_view_equipment_allocation_ajax.php",
        data: "pagetype=CSA_ALLOCATION_EMP&getajax=Y&ajaxpagecall=Y&building_floor_space_id="+bfs_id,
        type: "POST",
        success:function(data){
            $(".to_allocate_employee_popup_"+bfs_id).html(data);
            $('.tooltip').remove();
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
  }
  function closeViewCubicleSpace(){
        $('#dynamic_popup11').html('');
        var urlParams = new URLSearchParams(window.location.search);
        var param_value = "B";
        if (urlParams.has('TAB1')) {
            param_value = urlParams.get('TAB1');
            param_value = (param_value !=null || param_value !="")?param_value:"B";
        }
        getBuildingDynamicPage(param_value);
    }

    <?php if($_POST['building_floor_space_allocation_id'] !=""){ ?>
          toCubicleSpaceAllocateEmployeePopup('<?php echo $_POST['building_floor_space_allocation_id']; ?>')
    <?php  } ?>

</script>
<div class="modal-header">
    <h4 class="modal-title">Cubicle Spaces</h4>
    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close" onclick="closeViewCubicleSpace()"> </button>
</div>
<div class="modal-body m-l-50 m-r-50">
  <?php
  $empallocatedcnt = $reservecnt = $notallocatedcnt =$labspacedcnt=  0;
  foreach($building_floor_spaces as $bfspace){
    $bfsa_details =  $spacewithallocation[$bfspace['id']];
    if($bfsa_details['emp_id'] !=""){
      $empallocatedcnt++;
    }elseif($bfspace['space_type_id'] =="8") {
          $labspacedcnt++;
    }elseif($bfsa_details['reserve_pool_id'] !=""){
      $reservecnt++;
    }else{
      $notallocatedcnt++;
    }
  }
  ?>
   <div class="row">
        <div class="col-xl-4 col-md-6 text-center">
            <h5>Building Name</h5>
            <h6 class="f-w-700 mb-0 mt-2"><?php echo $buildings['building_name']; ?></h6>
            <p><i class="ti-location-pin"></i> <?php echo $buildings['building_address']; ?></p>
        </div>
        <div class="col-xl-3 col-md-6 text-center">
            <h5>Floor Name</h5>
            <h6 class="m-b-30 f-w-700 mt-2"><?php echo $building_floors['floor_name']; ?></h6>
        </div>
        <div class="col-lg-5 text-right">
          <div class="d-inline-block m-l-10">
              <span class="form-label label label-lg label-info"><?php echo $labspacedcnt; ?></span>
              <span class="f-12">Lab Space</span>
            </div>
          <div class="d-inline-block m-l-10">
              <span class="form-label label label-lg label-warning"><?php echo $reservecnt; ?></span>
              <span class="f-12">Reserved</span>
            </div>
            <div class="d-inline-block m-l-10">
              <span class="form-label label label-lg label-primary text-dark" style="background-color: #e4e4e4"><?php echo $empallocatedcnt; ?></span>
              <span class="f-12 " >Allocated</span>
            </div>
            <div class="d-inline-block m-l-10 m-l-20">
                <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo $notallocatedcnt; ?></span>
              <span class="f-12">Free</span>
            </div>
        </div>
  </div>
  <div class="row">
    <div class="row mt-5">
      <div id="floor_cabin">
          <img id="disable_crop_floor_image" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Attachment Preview">
<?php
            $cabincnt = 0;
            foreach($building_floor_spaces as $ealloc){
             $cabincnt++;
              $allocationdetail = $spacewithallocation[$ealloc['id']];
              $empreserved = $poopreserved = $freeallocation = "";
              $dynmicclass = "";
                if($allocationdetail['emp_id'] !=""){
                  $dynmicclass = "emp_reserved";
                }elseif($ealloc['space_type_id'] =="8") {
                  $dynmicclass = "lab_spaces";
                }elseif($allocationdetail['reserve_pool_id'] !="") {
                  $dynmicclass = "poop_reserved";
                }else{
                  $dynmicclass = "free_allocation";
                }
                if($_POST['building_floor_space_allocation_id'] == $ealloc['id']){
                  $dynmicclass .= " active_employee_space";
                }

                ?>
              <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon     cursor-pointer <?php echo $dynmicclass; ?> " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $ealloc['cabin_name']; ?>" style="top: <?php echo $ealloc['y_size']; ?>%; left: <?php echo $ealloc['x_size']; ?>%;">
                    <div onclick="toCubicleSpaceAllocateEmployeePopup('<?php echo $ealloc['id']; ?>')" class="cubicle_line_space" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                      <?php if($allocationdetail['emp_id'] !=""){ ?>
                        <?php if($employees[$allocationdetail['emp_id']]['image'] !="") { ?>
                            <img src="uploads/all/<?php echo $employees[$allocationdetail['emp_id']]['image']; ?>" class="img-radius img-30 align-top" alt="Attachment Preview">
                        <?php } else { ?>
                            <span  alt="user" class=" align-top f-12 text-center" ><?php echo initials($employees[$allocationdetail['emp_id']]['display_name']); ?></span>
                      <?php }  ?>
                      <?php }elseif($allocationdetail['reserve_pool_id'] !="") { ?>
                        <span  alt="user" class="align-top f-12 text-center text-white" ><?php echo initials($seating_pools[$allocationdetail['reserve_pool_id']]['pool_name']); ?></span>
                      <?php  }else{ ?>
                            <i class="ti-location-pin"></i>
                      <?php } ?>
                    </div>
                        <div class="custom-popup" id="custom-popup_<?php echo $cabincnt; ?>">
                            <div class="empty_popup_template custom-popup-open to_allocate_employee_popup_<?php echo $ealloc['id']; ?> text-start" style="right: 34px; top: -160px; width: 340px;">

                              </div>

                        </div>
                    </div>
            <?php } ?>
        </div>
    </div>
  </div>
</div>
<div class="modal-footer">
    <input type="hidden" id="space_allocation_popup" value="Y">
    <input type="hidden" id="space_allocation_popup_building_floor_id" value="<?php echo $rid; ?>">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal" >Close</button>
</div>

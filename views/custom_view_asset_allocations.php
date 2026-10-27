<?php
$_SESSION['apply_js']['crop_image']='1';
$_SESSION['apply_js']['multiselest'] = "1";
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$rid = $_POST['building_floor_id'];
$url = API."?mode=getequipmentallocations&rid=".$rid."&pagetype=viewassetallocations";
$response = callAPI($url);
$result = json_decode($response,1);
$buildings = $result['buildings'];
$building_floors = $result['building_floors'];
$asset_allocations = $result['asset_allocations'];
$assets = $result['assets'];

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
  //       url: "views/custom_view_equipment_allocation_ajax.php",
  //       data: "pagetype=CSA_ALLOCATION_EMP&getajax=Y&ajaxpagecall=Y&building_floor_space_id="+bfs_id,
  //       type: "POST",
</script>
<div class="modal-header">
    <h4 class="modal-title">Assets Allocations</h4>

    <button type="button" class="btn-close btn_white" data-bs-dismiss="modal" aria-label="Close" > </button>
</div>
<div class="modal-body m-l-50 m-r-50">
  <?php
  $empallocatedcnt = $reservecnt = $notallocatedcnt = 0;
  foreach($building_floor_spaces as $bfspace){
    $bfsa_details =  $spacewithallocation[$bfspace['id']];
    if($bfsa_details['emp_id'] !=""){
      $empallocatedcnt++;
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
            <div class="d-inline-block m-l-10 m-l-20">
                <span class="form-label label label-lg text-dark" style="border: 1px black solid;"><?php echo count((array)$asset_allocations); ?></span>
              <span class="f-12">Assets</span>
            </div>
        </div>
  </div>
  <div class="row">
    <div class="row mt-5">
      <div id="floor_cabin">
          <img id="disable_crop_floor_image" src="uploads/building_floors/<?php echo $building_floors['floor_image']; ?>" alt="Attachment Preview">
<?php
            $cabincnt = 0;
            foreach($asset_allocations as $ealloc){
             $cabincnt++;
              $allocationdetail = $assets[$ealloc['asset_id']];
              $empreserved = $poopreserved = $freeallocation = "";
              $dynmicclass = "free_allocation";

                if($_POST['asset_allocations_id'] == $ealloc['id']){
                  $dynmicclass .= " active_employee_space";
                }
                ?>
              <div id="position_<?php echo $cabincnt; ?>" class="equipment-allocation-icon     cursor-pointer <?php echo $dynmicclass; ?> " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?php echo $allocationdetail['asset_description']; ?>" style="top: <?php echo $ealloc['top_size']; ?>%; left: <?php echo $ealloc['left_size']; ?>%;">
                    <div class="cubicle_line_space">
                       <i class="ti-location-pin"></i>
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

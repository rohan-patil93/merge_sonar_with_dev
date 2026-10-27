<?php
if($_POST['getajax'] == 'Y'  || $_POST['ajaxpage'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=getequipmentallocations&gettype=buildings_list";
    $response = callAPI($url);
    $result = json_decode($response,1);
    $buildings = $result['buildings'];
    $building_with_floors = $result['building_with_floors'];
}
?>
<script type="text/javascript">
    function showPopup(carddiv){
        $(".custom-popup-open").hide();
        $("."+carddiv).show('slow');
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Building</h5>
        <div class="card-header-right">
        </div>
    </div>
    <div class="card-body m-l-50 m-r-50">
        <div class="row">
            <?php foreach($buildings as $building){ ?>
                <div class="col-xl-3 col-md-6" id="custom-popup" >
                    <div class="card sale-card " >
                        <div class="card-block text-center pb-2">
                            <div data-label="100%" class="radial-bar radial-bar-100 radial-bar-lg radial-bar-danger mb-0">
                            <?php if($building['building_image'] !=""){ ?>
                                <img src="uploads/all/<?php echo $building['building_image']; ?>" alt="User">
                            <?php }else{  ?>
                                <img src="uploads/all/no-image.jpg" alt="User-">
                            <?php } ?>
                            </div>
                            <div class="row" >
                                <h7 class="f-w-700 m-b-0"> <?php echo $building['building_name']; ?></h7>
                                <p><i class="ti-location-pin"></i> <?php echo $building['building_address']; ?></p>
                            </div>
                        </div>
                        <div class="card-footer pt-0">
                            <a href="index.php?option=COM_INSERT_BUILDINGS&Rid=<?php echo deocdeId("id:".$building['id']); ?>" class="btn btn-primary desktop-view mt-2 float-left" title="Edit Details" target="_blank"><i class="fa fa-edit"></i> Edit Details</a>
                            <button  type="button" onclick="showPopup('bulding_floors_<?php echo $building['id']; ?>')"class="btn btn-info desktop-view mt-2 float-right" title="View Details"><i class="fa fa-eye"></i> View Details</a>
                        </div>
                    </div>
                    <div class="custom-popup-open bulding_floors_<?php echo $building['id']; ?> text-start" style="top: 0px; display: none;">
                          <div class="card">
                            <div class="card-body">
                                <?php foreach($building_with_floors[$building['id']] as $bfloor){ ?>
                                    <a href="index.php?option=COM_INSERT_BUILDING_FLOORS&Rid=<?php echo deocdeId("id:".$bfloor['id']); ?>" target="_blank">
                                        <div class="card" onclick="ShowBuildingFloors('<?php echo $bfloor['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                            <div class="card-body">
                                                <img src="uploads/building_floors/<?php echo $bfloor['floor_image']; ?>" class="img-100" alt="Attachment Preview">
                                                <span><?php echo $bfloor['floor_name']; ?></span>
                                            </div>
                                        </div>
                                    </a>
                                <?php } ?>
                             </div>
                         </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

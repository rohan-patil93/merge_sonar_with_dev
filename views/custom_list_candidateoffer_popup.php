<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$datacat["where"] = array('is_active'=> '0','id'=>$_POST['id']);
  $catdata = urlencode(json_encode($datacat));
  $caturl = API."?mode=gettabledetailswithconditions&table=offer_details&data=".$catdata;
  $salarycatdata = callAPI($caturl);
  $salarycatdata = json_decode($salarycatdata,1);
  $data = $salarycatdata['0'];
?>

<div class="modal-header">
        <h5 class="modal-title">
            <?php echo $data['header'] ; ?>
            <small>&nbsp; <?php echo $data['sub_header'] ; ?> </small>
        </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

    </div>
    <div class="modal-body" >
        <?php echo $data['content'] ; ?>
    </div>

<div class="modal-footer">
         <button type="button" class="btn btn-primary waves-effect waves-light " data-bs-dismiss="modal" aria-label="Close">Close</button>

</div>

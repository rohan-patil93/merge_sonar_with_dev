<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$toc_id = $_POST['toc_id'];
$url1 = API."?mode=gettocheader&popup=Y&EID=".$toc_id;
$jsondecode = callAPI($url1);
$result = json_decode($jsondecode,1);
$toc_headers = $result['toc_headers'];
foreach($toc_headers as $tocheaders){
    if($tocheaders['header_type'] == '2'){
            $hearedfooter['header'] = $tocheaders;
        }elseif($tocheaders['header_type'] == '3'){
            $hearedfooter['footer'] = $tocheaders;
        }else{
    $tocheadersparentchield[$tocheaders['parent_toc_header_id']][$tocheaders['id']] = $tocheaders;
}
    $tocheaders[$tocheaders['id']] = $tocheaders;
}

?>
<div class="modal-content">
<div class="modal-header">
    <h5 class="f-24">Preview </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <?php $i = 'A'; foreach($tocheadersparentchield[0] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
            <h3><?php echo $i.'. '.$level1['header_name']; ?></h3>
            <div class="w-b-w-s"><?php echo $decodetext ?></p>
        <?php $j = 'b'; foreach($tocheadersparentchield[$level1['id']] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
            <h4 class="p-l-20"><?php echo $j.'. '.$level1['header_name']; ?></h4>
            <div class="p-l-20 w-b-w-s"><?php echo $decodetext ?></div>
                <?php $k = 1; foreach($tocheadersparentchield[$level1['id']] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
                <h5 class="p-l-20"><?php echo $k.'. '.$level1['header_name']; ?></h5>
                <div class="p-l-20 w-b-w-s"><?php echo $decodetext ?></div>
                    <?php $l = $k.'.1'; foreach($tocheadersparentchield[$level1['id']] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
                    <h5 class="p-l-20"><?php echo $l.'. '.$level1['header_name']; ?></h5>
                    <div class="p-l-20 w-b-w-s"><?php echo $decodetext ?></div>
                        <?php $m = $l.'.1'; foreach($tocheadersparentchield[$level1['id']] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
                        <h5 class="p-l-20"><?php echo $m.'. '.$level1['header_name']; ?></h5>
                        <div class="p-l-20 w-b-w-s"><?php echo $decodetext ?></div>
                            <?php $n = $m.'.1'; foreach($tocheadersparentchield[$level1['id']] as $level1){  $decodetext =  urldecode(json_decode($level1['header_contain'],1)); ?>
                            <h5 class="p-l-20"><?php echo $n.'. '.$level1['header_name']; ?></h5>
                            <div class="p-l-20 w-b-w-s"><?php echo $decodetext ?></div>

                        <?php $n = $n+0.1;
} ?>
                    <?php $m = $m+0.1;
} ?>
                <?php $l = $l+0.1;
} ?>
            <?php $k++;
} ?>
        <?php $j++;
} ?>
    <?php $i++;
} ?>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default " data-bs-dismiss="modal">Close</button>
</div>
</div>

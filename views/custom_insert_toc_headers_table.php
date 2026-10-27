<?php
if(isset($_POST['tableshow']) && $_POST['tableshow'] != ""){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $toc_id = $_POST['toc_id'];
    $url1 = API."?mode=gettocheader&EID=".$toc_id;
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
}
?>
<div class="card " style="position: sticky;top: 10px; ">
    <div class="card-header">
        <h5>View</h5>
        <div class="card-header-right">
           <button target="_blank" class="btn btn-info font-weight-bold m-r-5" onclick="editFinanaceGrp('<?php echo $hearedfooter['header']['id']; ?>')"><i class="fa fa-edit cursor-pointer text-white"></i> Header </button>
            <button target="_blank" class="btn btn-info font-weight-bold " onclick="editFinanaceGrp('<?php echo $hearedfooter['footer']['id']; ?>')"><i class="fa fa-edit cursor-pointer text-white"></i> Footer </button>
        </div>
    </div>
    <div class="card-body custom-scroll" style="height: 500px;">
        <table class="table table-bordered table-hover table-xs">
            <thead>
                <tr>

                    <th scope="col" style="width: 90%;">Header Name</th>
                    <th scope="col" style="width: 10%;"><button type="button" class="btn p-0 border-0 bg-transparent fa fa-plus-square text-primary mr-10 f-24" onclick="editFinanaceGrp('0')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button></th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($tocheadersparentchield[0] as $level1){  ?>
                    <tr><td style="padding-left:5px"><?php echo $i.'. '.$level1['header_name']; ?> </td>
                        <td class="p-1 text-center"><i class="fa fa-plus-square mr-10 text-primary cursor-pointer " onclick="editFinanaceGrp('0','<?php echo $level1['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i><i class="fa fa-edit cursor-pointer " onclick="editFinanaceGrp('<?php echo $level1['id']; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td></tr>
                    <?php $j = 'A'; foreach($tocheadersparentchield[$level1['id']] as $level1){  ?>
                            <tr><td style="padding-left:20px"><?php echo $j.'. '.$level1['header_name']; ?> </td><td class="p-1 text-center"><i class="fa fa-plus-square mr-10 text-primary cursor-pointer " onclick="editFinanaceGrp('0','<?php echo $level1['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i><i class="fa fa-edit cursor-pointer " onclick="editFinanaceGrp('<?php echo $level1['id']; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td></tr>
                        <?php $k = 'a'; foreach($tocheadersparentchield[$level1['id']] as $level1){  ?>
                            <tr><td style="padding-left:40px"><?php echo $k.'. '.$level1['header_name']; ?> </td><td class="p-1 text-center"> <i class="fa fa-plus-square mr-10 text-primary cursor-pointer " onclick="editFinanaceGrp('0',<?php echo $level1['id']; ?>)" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i><i class="fa fa-edit cursor-pointer " onclick="editFinanaceGrp('<?php echo $level1['id']; ?>>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i></td></tr>

                            <?php $k++;
} ?>
                    <?php $j++;
} ?>
                <?php $i++;
} ?>

            </tbody>
        </table>

    </div>
</div>

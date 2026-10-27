<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$empurl = API."?mode=getitemqualitychecknewdetails&quality_type=GRN&table=".$_POST['table']."&trnid=".$_POST['tranid']."&item_id=".$_POST['item_id'];
$empres = callAPI($empurl);
$data = json_decode($empres,1);
$type = array('1'=>'Serial','2'=>'Lot');
$status = "Active";
$spfulljson = json_decode($data['quality_parameters_mapping']['specifications'],1);
$listdata = array();
if($data['item']['tracking_type']==1){
    $listdata = $data['item_serial_nos'];
}elseif($data['item']['tracking_type']==2){
    foreach($data['item_lot_nos'] as $item_lot_nos){
            $lot_no = $item_lot_nos;
            $lot_no['serial_no'] = $item_lot_nos['lot_no'];
            $lot_nos[] = $lot_no;
    }
    $listdata = $lot_nos;
}else{
    $listdata[] = array('id'=>$_POST['item_id'],'serial_no'=>$data['item']['item_name']);
}

$attributes = $data['quality_parameters'];
$groups = $data['quality_parameters_groups'];
$listparameters = $data['quality_parameters'];
foreach( $groups as $group){
$groupsbaseid[$group['id']] = $group['group_name'];
}
$reschk = $data['item_quality_check'];
foreach($reschk as $resc){
    $resdata[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['specification_result'];
    $resremarkdata[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['remarks'];
  $resquality[$resc['item_serial_lot_id']][$resc['item_attribute_id']] =$resc['quality_check'];
}
$spjsonlist = $spfulljson['parameters'];
foreach($spjsonlist as $paramlist){
    $spj_ids[] = $paramlist['id'];
    $spchkp[$paramlist['id']] = $paramlist;
    $reslutlist[$paramlist['id']] =  $paramlist['res'];
}
$listgroup = $spfulljson['group'];

?>
 <link rel="stylesheet" type="text/css" href="assets/bower_components/seiyria-bootstrap-slider/css/bootstrap-slider.css">
<style type="text/css">
    td, th {
    white-space: normal;
}
/* TOGGLE STYLING */
.toggle {
  margin: 0 0 1.5rem;
  box-sizing: border-box;
  font-size: 0;
  display: flex;
  flex-flow: row nowrap;
  justify-content: flex-start;
  align-items: stretch;
}
.toggle input {
  width: 0;
  height: 0;
  position: absolute;
  left: -9999px;
}
.toggle input + label {
  margin: 0;
  padding: 0.25rem 1rem;
  box-sizing: border-box;
  position: relative;
  display: inline-block;
  border: solid 1px #DDD;
  background-color: #FFF;
  font-size: 1rem;
  line-height: 140%;
  font-weight: 600;
  text-align: center;
  box-shadow: 0 0 0 rgba(255, 255, 255, 0);
  transition: border-color 0.15s ease-out, color 0.25s ease-out, background-color 0.15s ease-out, box-shadow 0.15s ease-out;


  /* ----- */
}
.toggle input + label:first-of-type {
  border-radius: 6px 0 0 6px;
  border-right: none;
}
.toggle input + label:last-of-type {
  border-radius: 0 6px 6px 0;
  border-left: none;
}
.toggle input:hover + label {
  border-color: #213140;
}
.toggle input:checked + label {
  background-color: #4B9DEA;
  color: #FFF;
  box-shadow: 0 0 10px rgba(102, 179, 251, 0.5);
  border-color: #4B9DEA;
  z-index: 1;
}
.toggle input:focus + label {
  outline: dotted 1px #CCC;
  outline-offset: 0.45rem;
}
@media (max-width: 800px) {
  .toggle input + label {
    padding: 0.75rem 0.25rem;
    flex: 0 0 50%;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>
<script type="text/javascript">
    function submitrom(){
        var formvalue =  $("#submitnextform").serialize();
        console.log(formvalue);
        jQuery.ajax({
            url: "ajax.php",
            data: "page=toinsertthedataonitemquality&"+formvalue,
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Asset Allocation Successfully','');
            },
            error:function (){}
        });
    }
    function viewstatuspfr(val, valid, disid, type){

        var disicon = '<i class="fa fa-times text-danger"></i>';
        var ranval = $("#"+valid).val();
        var res = 1;
        console.log(ranval);
        if(type == 'R'){
            if(val>= ranval){
                disicon = '<i class="fa fa-check text-success"></i>';
                res =0;
            }
        }
        if(type == 'E'){
            if(val == ranval){
                disicon = '<i class="fa fa-check text-success"></i>';
                res =0;
            }
        }
        if(type == 'C'){
            var min = $("#"+valid+"_min").val();
            var max = $("#"+valid+"_max").val();
            console.log('rrr:'+min+'--'+max);
            if(ranval==0){
                console.log('Check1:'+min+'--'+max+"--"+val);
                if(min<=val && val<=max){
                    console.log('work:'+min+'--'+max);
                    disicon = '<i class="fa fa-check text-success"></i>';
                    res =0;
                }
            }
            if(ranval==1){
                console.log('Check2:'+min+'--'+max);
                if(max<val || val< min){
                    disicon = '<i class="fa fa-check text-success"></i>';
                    res =0;
                }
            }
        }
        $("#"+disid).html(disicon);
        $("#"+disid+'_val').val(res);
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Quality Check</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form  action="" id="submitnextform" method="post">
<div class="modal-body p-5">
    <div class="row">
        <div class="col-lg-2"><span class="f-w-600">Product:</span></div>
        <div class="col-lg-2 "><?php echo $data['item']['item_name']; ?></div>
        <div class="col-lg-4">

        </div>
        <div class="col-lg-2"><span class="f-w-600">Date:</span></div>
        <div class="col-lg-2 float-right">
            <span class="float-right"> <?php echo fromDate(); ?> </span>
        </div>
        <div class="col-lg-2"><span class="f-w-600">SKU:</span></div>
        <div class="col-lg-2"><?php echo $data['item']['sku']; ?></div>
        <div class="col-lg-4"></div>
        <div class="col-lg-2"><span class="f-w-600">QC Inspector:</span></div>
        <div class="col-lg-2">
            <span style="float:right !important;"><?php echo ucwords($_SESSION['user']['display_name']); ?></span>
        </div>
        <div class="col-lg-12 mt-1">
            <div class="float-right">
                <button class="btn btn-primary"> Save </button>
                <button class="btn btn-primary"> Submit Quality Check </button>
            </div>
        </div>
    </div>
    <div class="col-lg-12 mt-5">
        <div>
            <ul class="nav md-tabs infisuite_tab">
                <?php $k=1; foreach($listdata as $tabs){ ?>
                    <li class="nav-item">
                        <a class="nav-link f-w-600 <?php if($k==1){ echo "active";
} ?>" data-bs-toggle="tab" href="#tab<?php echo $k; ?>" role="tab" aria-selected="true"><?php echo $tabs['serial_no']; ?></a>
                        <div class="slide"></div>
                    </li>
                <?php $k++;
} ?>
            </ul>
        </div>
        <?php if(count((array)$listgroup)>0){ ?>
        <div class="tab-content tabs card-body">
            <?php $k=1; foreach($listdata as $tabs){
                 $listval = $tabs; $slid = $tabs['id'];  ?>
                <div class="tab-pane <?php if($k==1){  echo "active";
} ?> " id="tab<?php echo $k; ?>" role="tabpanel">
                    <div class="col-lg-12">
                        <table class="table table-hover m-b-0" style="width: 100%;">
                            <tr>
                                <th scope="col" style="width: 5%;">#</th>
                                <th scope="col" style="width: 48%;"> Name <?php echo $tabs['serial_no']; ?> </th>
                                <th scope="col" class="text-center" style="width: 20%;">Type</th>

                                <th scope="col" style="width: 20%;">Remarks</th>
                                <th scope="col" style="width: 1%;"></th>
                            </tr>
                            <?php $m=1; foreach ($listgroup as $lisgroup) { ?>
                                <tr class="bg-info text-white">
                                    <td style="width: 5%;"><?php echo $m; ?></td>
                                    <td style="width: 48%;"> <span class="f-w-600"><?php echo $groupsbaseid[$lisgroup]; ?> </span></td>
                                    <td style="width: 20%;"></td>

                                    <td style="width: 20%;"></td>
                                    <td style="width: 1%;"></td>
                                </tr>
                                <?php $n=1; foreach($listparameters[$lisgroup] as $param){
                                    if(in_array($param['id'],$spj_ids)){
                                        $att = $param; $spj_id = $param['id'];
                                ?>
                                    <tr>
                                        <td class="m-l-10" style="width: 5%;"> &nbsp;&nbsp; <?php echo $m.".".$n; ?></td>
                                        <td style="width: 48%;"><?php echo $param['name']; ?></td>
                                        <td style="width: 20%;">
<?php
    $valid = "ran_".$slid."_".$spj_id."_val";
    $displayval = "dis_".$slid."_".$spj_id."_val";
    if($att['display_type']=="1" || $att['display_type']=="2"){
    $selectdata = json_decode($att['quality_parameters'],1);
?>
<div class="toggle mt-1 float-right">
        <input type="hidden" name="" id="<?php echo $valid; ?>" value="<?php echo $reslutlist[$spj_id] ?>">
    <?php $mn=1; foreach ($selectdata as $selectkey => $selectvalue) {
            $id = "chk"."_".$slid."_".$spj_id."_".$mn;
     ?>
        <input aria-label="Input field" onclick="viewstatuspfr(<?php echo $selectkey.",'".$valid."','".$displayval."','E'"; ?>)" type="radio" <?php if($resdata[$slid][$spj_id]==$selectkey){ echo "checked";
} ?> name="item_attribute_res[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]" value='<?php echo $selectkey; ?>' id='<?php echo $id; ?>' onkeydown="" />
    <label for="<?php echo $id; ?>"><?php echo $selectvalue; ?></label>
    <?php  $mn++;
} ?>
    </div>
<?php }?>
<?php if($att['display_type']=="3"){
    $selectdata = json_decode($att['quality_parameters'],1);
?>

    <div class="toggle mt-1 float-right" >
        <input type="hidden" name="" id="<?php echo $valid; ?>" value="<?php echo $reslutlist[$spj_id] ?>">
    <?php $mn=1; for($i=0;$i<=5;$i++){
            $id = "ran"."_".$slid."_".$spj_id."_".$mn;
     ?>

        <input aria-label="Input field" onclick="viewstatuspfr(<?php echo $i.",'".$valid."','".$displayval."','R'"; ?>)" type="radio" <?php if($resdata[$slid][$spj_id]==$i){ echo "checked";
} ?> name="item_attribute_res[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]" value='<?php echo $i; ?>' id='<?php echo $id; ?>' onkeydown="" />
    <label for="<?php echo $id; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="<?php echo $selectdata[$i-1]; ?>"><?php echo $i+1; ?></label>
    <?php  $mn++;
} ?>
    </div>
<?php }?>
<?php if($att['display_type']=="4"){
    $selectdata = json_decode($att['quality_parameters'],1); ?>
    <div class="toggle mt-1 float-right" >
    <input type="hidden" name="" id="<?php echo $valid; ?>" value="<?php echo $reslutlist[$spj_id] ?>">
    <?php $mn=1; for($i=1;$i<=5;$i++){
            $id = "ran"."_".$slid."_".$spj_id."_".$mn;
     ?>

        <input aria-label="Input field" onclick="viewstatuspfr(<?php echo $i.",'".$valid."','".$displayval."','R'"; ?>)" type="radio" <?php if($resdata[$slid][$spj_id]==$i){ echo "checked";
} ?> name="item_attribute_res[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]" value='<?php echo $i; ?>' id='<?php echo $id; ?>' onkeydown="" />
    <label for="<?php echo $id; ?>"><?php echo $i; ?></label>
    <?php  $mn++;
} ?>
    </div>
<?php }?>
<?php if($att['display_type']=="5"){
    $min_id = $valid."_min";
    $max_id = $valid."_max";
    $selectdata = json_decode($att['quality_parameters'],1);
?>
    <input type="hidden" name="" id="<?php echo $valid; ?>" value="<?php echo $reslutlist[$spj_id]; ?>">
    <input type="hidden" name="" id="<?php echo $min_id; ?>" value="<?php echo $selectdata['min']; ?>">
    <input type="hidden" name="" id="<?php echo $max_id; ?>" value="<?php echo $selectdata['max']; ?>">

    <div class="w-full">
        <input aria-label="Enter number" type="number" class="form-control" onblur="viewstatuspfr(this.value,<?php echo "'".$valid."','".$displayval."','C'"; ?>)" value="<?php $resdata[$slid][$spj_id]; ?>"  name="item_attribute_res[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]">
    </div>
    <span>Range <?php echo $selectdata['min']." - ".$selectdata['max']; ?></span>
<?php } ?>
                                        </td>

                                        <td style="width: 20%;">
                                            <textarea aria-label="Remarks" class="form-control" placeholder="Remarks" name="item_attribute_remarks[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]"  ><?php echo $resremarkdata[$slid][$spj_id]; ?></textarea>
                                        </td>
                                        <td style="width: 1%;">
                                            <input type="hidden" name="item_attribute_pass[<?php echo $listval['id']; ?>][<?php echo $att['id']; ?>]" value="0" id="<?php echo $displayval."_val"; ?>">
                                            <div id="<?php echo $displayval; ?>" class="mt-2">
                                                <?php if(isset($resquality[$slid][$spj_id]) && $resquality[$slid][$spj_id]==0){ ?>
                            <i class="fa fa fa-check text-success f-18"></i>
                        <?php }elseif($resquality[$slid][$spj_id]==1){?>
                            <i class="fa  fa fa-times text-danger f-18"></i>
                        <?php } ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php $n++; ?>
                                <?php } ?>
                                <?php } ?>
                            <?php $m++;
}?>
                        </table>
                    </div>
                </div>
            <?php $k++;
} ?>
        </div>
            <?php }else{?>
                <div class="text-danger mt-5">
                            <?php echo $_SESSION['error_message']['9065']; ?>
                </div>
            <?php } ?>
    </div>
</div>
<div class="modal-footer">
    <input type="hidden" name="table" id="table" value="<?php echo $_POST['table']; ?>">

    <input type="hidden" name="tranid" id="tranid" value="<?php echo $_POST['tranid']; ?>">    <input type="hidden" name="item_id" id="item_id" value="<?php echo $_POST['item_id']; ?>">
    <button type="button" class="btn btn-default waves-effect" data-bs-dismiss="modal">Cancel</button>
    <button type="button" class="btn btn-primary" onclick="submitrom()" >Save </button>
    <button type="button" class="btn btn-primary" onclick="submitromquality()" >Submit Quality Check </button>
</div>
</form>
<script type="text/javascript" src="assets/bower_components/select2/js/select2.full.min.js"></script>

<script type="text/javascript" src="assets/bower_components/bootstrap-multiselect/js/bootstrap-multiselect.js">
</script>
<script type="text/javascript" src="assets/bower_components/multiselect/js/jquery.multi-select.js"></script>
<script type="text/javascript" src="assets/js/jquery.quicksearch.js"></script>
<!-- Custom js -->
<script type="text/javascript" src="assets/pages/advance-elements/select2-custom.js"></script>

<script type="text/javascript" src="assets/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="assets/bower_components/modernizr/js/css-scrollbars.js"></script>

<script type="text/javascript" src="assets/bower_components/seiyria-bootstrap-slider/js/bootstrap-slider.js"></script>
        <script type="text/javascript" src="assets/js/script.js"></script>
        <script src="assets/js/pcoded.min.js"></script>
        <script src="assets/js/vertical/vertical-layout.min.js"></script>
        <script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <!-- Custom js -->

<script type="text/javascript" src="assets/pages/range-slider.js"></script>

<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$appraisal_id = $_POST['appraisal_id'];
$assessment_year = $_POST['assessment_year'];
$loc_id = array();
$dep_id= array();
$des_id = array();
if(isset($_POST['locatdepdesignatio'])){
    $postarray = $_POST['locatdepdesignatio'];
    foreach($_POST['locatdepdesignatio'] as $locid =>$loclist){
        $loc_id[$locid] = $locid;
        foreach($loclist as $dipid =>$deplist){
            $dep_id[$dipid] = $dipid;
            foreach($deplist as $desid =>$deslist){
                $des_id[$desid] = $desid;
            }
        }
    }
    $url = API."?mode=getkratagging&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id;
}else{
    $loc_id = array($_POST['loc_id']);
    $dep_id= array($_POST['dept_id']);
    $des_id = array($_POST['desg_id']);
    $postarray = array($_POST['loc_id'] => array($_POST['dept_id'] =>array($_POST['desg_id']=>'0')));
    $url = API."?mode=getkratagging&assessment_year=".$assessment_year."&appraisal_id=".$appraisal_id."&loc_id=".$_POST['loc_id']."&dep_id=".$_POST['dept_id']."&des_id=".$_POST['desg_id']."&edit=Y";
}
$response = callAPI($url);
$kipandkradetails = json_decode($response,1);
$department_list = $kipandkradetails['department_list'];
$designation_list = $kipandkradetails['designation_list'];
$location_list = $kipandkradetails['location_list'];
$kralist  = $kipandkradetails['kralist'];
$krakpiid = array();
$kpiderails = array();
foreach($kipandkradetails['kralid'] as $kralist0){
    $krakpiid[$kralist0['id']] = $kralist0['id'];
    $organization_goalsidd[$kralist0['ogid']] = $kralist0['ogid'];
    $kpiderails[$kralist0['key_performance_id']] = $kralist0;
}
?>
<script type="text/javascript">
    function updateKraKpiSubmited(){
        var fomsub = $("#formsubmitkrakpitagging").serialize();
        var appraisal_id = '<?php echo $appraisal_id; ?>';
        var assessment_year     = '<?php echo $assessment_year; ?>';
    $.ajax({
            url: "ajax.php",
            data: "page=updatekrakpitagging&"+fomsub+"&appraisal_id="+appraisal_id+"&assessment_year="+assessment_year+"&inserttype=OLD",
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
                $("#getandshowlocationdesdep").show('slow');
                $("#div_kraandkpi").hide('slow');
                $("#div_kraandkpi").html("");
            }
    });
}
function getnamelist(emplist){
        $('#kt_modal_dynamic_popup').modal('hide');
        $.ajax({
            url: "ajax.php",
            data:'page=filternamelist&filterdata='+emplist,
            type: "POST",
            success:function(response){
                console.log("response ="+response);
                document.getElementById('filternames').innerHTML =  response;
            },
            error:function (){}
        });
    }
</script>
<form method="post" id="formsubmitkrakpitagging">
<div class="row">
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-header">
                <h5>Location</h5>
            </div>
            <div class="card-body">
                <?php foreach($loc_id as $locid){
                    echo '<span class="form-label label label-md label-primary">'.$location_list[$locid]['location_name'].'</span>';
                }?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-header">
                <h5>Department</h5>
            </div>
            <div class="card-body">
                <?php foreach($dep_id as $depid){
                    echo '<span class="form-label label label-md label-primary">'.$department_list[$depid]['department_name'].'</span>';
                }?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card table-card">
            <div class="card-header">
                <h5>Designation</h5>
            </div>
            <div class="card-body">
                <?php foreach($des_id as $desid){
                    echo '<span class="form-label label label-md label-primary">'.$designation_list[$desid]['designation_name'].'</span>';
                }?>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12 row  mb-3"><div class="col-lg-6"></div><div class="col-lg-6 text-right"><button type="button"  onclick="setLocationDesignationDep()"  class="btn btn-primary" ><i class="icofont icofont-reply"></i>  Go To Set Criteria</button> </div></div>
    <div class="col-xl-3 col-md-3">
        <div class="card table-card">
            <div class="card-header">
                <h5>Select KRA</h5>
            </div>
            <div class="card-block p-b-0">
                <div class="table-responsive">
                    <table class="table table-hover m-b-0 without-header">
                        <tbody>
                            <?php foreach($kralist as $kralist){ ?>
                                <tr>
                                    <th scope="col" class="p-2">
                                        <div class="checkbox-fade fade-in-primary m-l-20">
                                            <label class="form-label">
                                                <input aria-label="Checkbox" class="form-control itemRow kraselected" type="checkbox" value="<?php echo $kralist['id']; ?>" name="kra_ids[]"  onchange="selectKPA()" <?php echo in_array($kralist['id'], (array)$krakpiid)?"checked":""; ?>>
                                                <span class="cr">
                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                </span>
                                            </label>
                                        </div>
                                    </th>
                                    <th scope="col" class="p-2">
                                        <span><?php echo $kralist['area_name']; ?></span>
                                    </th>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div id="newhpirowtagging" >
            <?php if(isset($_POST['locatdepdesignatio'])){
}else{
                $url22 = API."?mode=getorganizationgoalsdetails&kra_list=Y&kpaidlist=".implode(',',(array)$krakpiid);
                $response22 = callAPI($url22);
                $kipandkradetails22 = json_decode($response22,1);
                $modellist  = $kipandkradetails22['modellist'];
                $upmllist = $kipandkradetails22['uomllist'];
                $kralist = $kipandkradetails22['kralist'];
                $kpilistres = $kipandkradetails22['kpilist'];
                $frequencyllist = $kipandkradetails22['frequencylist'];
                foreach($kpilistres as $kpilistre){
                    $kpilist[$kpilistre['key_reslut_id']][] = $kpilistre;
                }
                include_once "custom_insert_organization_goal_ajax.php";
}
?>
        </div>
    </div>
    <div class="col-lg-4">
        <input type="hidden" id="table_name" name="table_name" value=<?php echo $table_name; ?> />
        <input type="hidden" id="user_id" name="user_id" value=<?php echo $_SESSION['userid']; ?> />
        <input type="hidden" id="now_date" name="now_date" value="<?php echo date("Y-m-d H:i:s"); ?>" />
        <input type="hidden" name="rid" id="rid" value="<?php echo $rid;?>">
        <input type="hidden" name="locatdepdesignatio"  value='<?php echo json_encode($postarray);?>'>
        <?php  $disable = "disabled";     if($total == 100) { $disable = "";
} ?>
        <input type="hidden" name="kra_mapping"  value="insertgoal">
        <input type="hidden" name="location_id"  value='<?php echo json_encode($loc_id);?>'>
        <input type="hidden" name="department_id"  value='<?php echo json_encode($dep_id);?>'>
        <input type="hidden" name="designation_id"  value='<?php echo json_encode($des_id);?>'>
        <input type="hidden"  name="assessment_year"  value='<?php echo $assessment_year;?>'>
        <input type="hidden"  name="organization_goalsidd"  id="organization_goalsidd" value='<?php echo implode(',',(array)$organization_goalsidd);?>'>
        <input type="hidden"  name="inserttype"  value='<?php echo $_POST['inserttype'];?>'>
        <input type="hidden" name="parent_tab" value="kra">
        <input type="hidden" name="child_tab" value="setkratagging">

    <?php if($_POST['inserttype']=="OLD"){ ?>
        <button type="button"  class="btn btn-primary mr-2"  onclick="updateKraKpiSubmited()" ><i class="la la-check"></i>Update</button>
    <?php }else{ ?>
        <button type="submit"  class="btn btn-primary mr-2"   ><i class="la la-check"></i>Submit</button>
    <?php } ?>
    </div>
</div>
</form>

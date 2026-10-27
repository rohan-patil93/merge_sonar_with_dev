<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$modulesetupwh["where"] = array('company_id'=>$_SESSION['company_id']);
$modlueencode = urlencode(json_encode($modulesetupwh));
$moduleurl = API."?mode=gettabledetailswithconditions&table=module_setup&data=".$modlueencode;
$moduleapi = callAPI($moduleurl);
$modulelists = json_decode($moduleapi,1);
foreach($modulelists as $modulelist){
    if($modulelist['id'] != '1'){
        $allmodules[$modulelist['module_id']] = $modulelist;
    }
    if($modulelist['is_active'] == '0'){
        $activemodule[$modulelist['module_id']] = $modulelist['module_id'];
    }

}
$modulesetuplinkwh["where"] = array('company_id'=>$_SESSION['company_id'],'is_active'=>'0','parent_module_id'=>$_POST['module_id']);
$modulesetuplinkwh["field"] = "parent_module_id,module_id";
$modluelinkencode = urlencode(json_encode($modulesetuplinkwh));
$modulelinkurl = API."?mode=gettabledetailswithconditions&table=module_setup_linkings&data=".$modluelinkencode;
$modulelinkapi = callAPI($modulelinkurl);
$modulelinklists = json_decode($modulelinkapi,1);

?>
<script type="text/javascript">

</script>
<form method="post" enctype="multipart/form-data" id="formsubmit">
    <div class="card">
        <div class="card-header">
            <h5>Additional Module Setup</h5>
            <div class="card-header-right">
                <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closeModuleSelection()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                    <?php foreach($modulelinklists as $modulelink){
                        $allmodule = $allmodules[$modulelink['module_id']];
                     ?>
                        <div class="col-lg-4" >
                            <div class="card social-card">
                                <div class="inputGroup text-center ">
                                    <input id="optionget<?php echo $allmodule['module_id']; ?>" name="module_id[]" type="checkbox" <?php echo isset($activemodule[$allmodule['module_id']])?"checked":""; ?> value="<?php echo $allmodule['module_id']; ?>" onclick="selectModule('<?php echo $allmodule['module_id']; ?>')" onkeydown="">
                                    <label for="optionget<?php echo $allmodule['module_id']; ?>" class="text-center fixheght">
                                        <span class="text-facebook">
                                            <?php if($allmodule['module_logo'] !=""){?>
                                                <img src='uploads/module/<?php echo $allmodule['module_logo']; ?>' class="img-60" style="height: auto;" alt="Logo">
                                            <?php }else{ ?>

                                            <?php  } ?>
                                        </span>
                                        <h3 class="text-facebook f-w-700 f-16 mb-0 mt-2"><?php echo $allmodule['module_name']; ?></h3>
                                        <p class="f-10 mb-0"><?php echo $allmodule['display_name']; ?></p>
                                    </label>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    </div>
        </div>
    </div>
</form>

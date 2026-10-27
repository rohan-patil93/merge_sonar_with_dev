<?php
$_SESSION['apply_js']['multiselest'] = '1';
$table_name = "quality_parameters";
if (isset($_POST['submit'])){
    $data['company_id'] = $_SESSION['company_id'];
    $data['quality_parameter_group_id'] = $_POST['quality_parameter_group_id'];
    $data['name'] = $_POST['name'];
    $data['display_type'] = $_POST['display_type'];
    if($_POST['display_type']==4){
        $data['quality_parameters'] = $_POST['quality_parameters'];
    }elseif($_POST['display_type']=='5'){
        $spe['min'] = $_POST['min'];
        $spe['max'] = $_POST['max'];
        $data['quality_parameters'] = json_encode($spe);
    }else{

        foreach($_POST['field'] as $fieldkey=>$field){
            $spe[$field] = $_POST['fieldvalue'][$fieldkey];
        }
        $data['quality_parameters'] = json_encode($spe);
    }
    if($_POST['submit'] == 'Add'){
        $data['current_status'] =  '0';
        $data['is_active'] =  '0';
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
         $url = API."?mode=adddata&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data, 'insert');
    }
    if($_POST['submit'] == 'Update'){
        $_SESSION['check'] = 1;
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['hid']."&table=".$table_name."&method=post";
        $response = file_post_contents($url, $data, 'update');
    }
     $response = json_decode($response,1);
     if($response['status'] == 'fail'){
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }else{
        header("location:index.php?option=COM_LIST_QUALITY_PARAMETERS");
    }
}
$dispay = array('1'=>'Pass Fail','2'=>'Pass Fail N/A','3'=>'Likert Sacle','4'=>'Ratting','5'=>'Range');
$vdata1["where"] = array('company_id'=>$_SESSION['company_id']);
$vdata1["field"] =  "id,group_name,is_active";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=quality_parameters_groups&data=".$vdata;
$vdata_details = callAPI($vurl);
$groupdetails = json_decode($vdata_details,1);

if(isset($_GET['Rid']) && $_GET['Rid'] !=''){
    $vdata_details = array();
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $url = API."?mode=togetdatabasepaimairy&rid=".$rid."&table=".$table_name;
    $response = callAPI($url);
    $role = json_decode($response,1);
}
?>
<script type="text/javascript">
    function dschangepart(val){
        var count =5;
        console.log('var:'+val);
        for(var i=1; i<=count;i++){
            console.log('loop:'+i);
            $("#type_"+i).hide();
        }
        $("#type_"+val).show();
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <?php if($error_message !=''){ ?>
            <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php } ?>
            <div class="card-header">
                <h5>Quality Parameters</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-custom">
                <div class="card-body">
                    <form class="" method="post" action="" >
                        <div class="row p-4">
                            <div class="form-group col-lg-6">
                                <span>Group Name</span>
                                <div class="w-full">
                                    <select aria-label="Quality Parameter Group" class="form-select js-example-basic-single" name="quality_parameter_group_id" >
                                        <option>Select Any</option>

                                        <?php foreach($groupdetails as $group){ ?>
                                            <option <?php if($role['quality_parameter_group_id']==$group['id']){ echo "selected";
} ?> value="<?php echo $group['id']; ?>"><?php echo $group['group_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" form-group col-lg-6">
                                <span>Display Type</span>
                                <div class="w-full">
                                    <select aria-label="Display Type" class="form-select js-example-basic-single" onchange="dschangepart(this.value)" name="display_type" >
                                        <option>Select Any</option>
                                        <?php foreach($dispay as $diskey=>$dis){ ?>
                                            <option <?php if($role['display_type']==$diskey){ echo "selected";
} ?> value="<?php echo $diskey; ?>"><?php echo $dis; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-lg-6">
                                <span>Name</span>
                                <textarea aria-label="Name" class="form-control" name="name"><?php echo $role['name'] ?></textarea>
                            </div>


                            <div class="col-lg-12 row" id="type_1" <?php if($role['display_type']!=1){ ?> style="display:none;" <?php
} ?> >
                                <?php
                                    if($role['display_type']==1){
                                    $jdataon = json_decode($role['quality_parameters'],1);
                                    }
                                    if(count((array)$jdataon)==0){
                                        $jdataon = array("0"=>"Pass","1"=>"Fail");
                                    }
                                 ?>
                                <div class="col-lg-12 row f-w-600">
                                    <div class="col-lg-5">
                                        Key
                                    </div>
                                    <div class="col-lg-6">
                                        Value
                                    </div>

                                </div>
                                <?php foreach($jdataon as $jkey=>$jsd){ ?>
                                <div class="col-lg-12 row mt-2" id="jsn_1">
                                    <div class="col-lg-5">
                                        <input aria-label="Field[]" type="text" name="field[]" class="form-control" value="<?php echo $jkey; ?>">
                                    </div>
                                    <div class="col-lg-6">
                                        <input aria-label="Fieldvalue[]" type="text" name="fieldvalue[]" class="form-control" value="<?php echo $jsd; ?>">
                                    </div>

                                </div>
                                <?php } ?>
                            </div>

                            <div class="col-lg-12 row" id="type_2" <?php if($role['display_type']!=2){ ?> style="display:none;" <?php
} ?> >
                                <?php
                                    if($role['display_type']==2){
                                    $jdatato = json_decode($role['quality_parameters'],1);
                                    }
                                    if(count((array)$jdatato)==0){
                                        $jdatato = array("0"=>"Pass","1"=>"Fail","3"=>"N/A");
                                    }
                                 ?>
                                <div class="col-lg-12 row f-w-600">
                                    <div class="col-lg-5">
                                        Key
                                    </div>
                                    <div class="col-lg-6">
                                        Value
                                    </div>
                                </div>
                                <?php foreach($jdatato as $jkey=>$jsd){ ?>
                                <div class="col-lg-12 row mt-2" id="jsn_1_2">
                                    <div class="col-lg-5">
                                        <input aria-label="Field[]" type="text" name="field[]" class="form-control" value="<?php echo $jkey; ?>">
                                    </div>
                                    <div class="col-lg-6">
                                        <input aria-label="Fieldvalue[]" type="text" name="fieldvalue[]" class="form-control" value="<?php echo $jsd; ?>">
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <div class="col-lg-12 row" id="type_3" <?php if($role['display_type']!=3){ ?> style="display:none;" <?php
} ?> >
                                <?php
                                    if($role['display_type']==3){
                                        $jdatathr = json_decode($role['quality_parameters'],1);
                                    }
                                    if(count((array)$jdatathr)==0){
                                        $jdatathr = array("0"=>"Strongly Disagree","1"=>"Disagree","2"=>"Neither Agree or Disagree","3"=>"Agree", "4"=>"Strongly Agree");
                                    }
                                 ?>
                                <div class="col-lg-12 row f-w-600">
                                    <div class="col-lg-5">
                                        Key
                                    </div>
                                    <div class="col-lg-6">
                                        Value
                                    </div>
                                </div>
                                <?php foreach($jdatathr as $jkey=>$jsd){ ?>
                                <div class="col-lg-12 row mt-2" id="jsn_1_3">
                                    <div class="col-lg-5">
                                        <input aria-label="Field[]" type="text" name="field[]" class="form-control" value="<?php echo $jkey; ?>">
                                    </div>
                                    <div class="col-lg-6">
                                        <input aria-label="Fieldvalue[]" type="text" name="fieldvalue[]" class="form-control" value="<?php echo $jsd; ?>">
                                    </div>
                                </div>
                                <?php } ?>
                            </div>

                            <div class="form-group col-lg-6" id="type_4" <?php if($role['display_type']!=4){ ?> style="display:none;" <?php
} ?> >
                                <?php
                                if($role['display_type']==4){
                                $jdatafov = $role['quality_parameters'];
                                } ?>
                                <span>Ratting out of</span>
                                <input aria-label="Input field" type="text" class="form-control" value="<?php echo $jdatafov; ?>" name="quality_parameters">
                            </div>

                            <div class="col-lg-6 row" id="type_5" <?php if($role['display_type']!=5){ ?> style="display:none;" <?php
} ?> >
                                <?php
                                if($role['display_type']==5){
                                $jdatafiv = json_decode($role['quality_parameters'],1);
                                } ?>
                                <div class="form-group col-lg-6" >
                                    <span>Min Range </span>
                                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo $jdatafiv['min'] ?>" name="min">
                                </div>
                                <div class="form-group col-lg-6" >
                                    <span>Max Range </span>
                                    <input aria-label="Input field" type="text" class="form-control" value="<?php echo $jdatafiv['max'] ?>" name="max">
                                </div>
                            </div>

                        </div>
                        <div class="card-footer mt-5 p-4">
                            <div class="row">
                                <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1'){ ?>
                                    <div class="col-lg-9 col-md-6 col-sm-6 col-6">
                                        <input type="hidden" name="hid" value=<?php echo $rid; ?> />
                                        <input type="hidden" name="submit" value="Update" />
                                        <button id="kt_llogin_signup_submit" <?php if($status_res == '1'){ ?> disabled="disabled" <?php
} ?> class="btn btn-primary mr-2" ><i class="la la-check"></i>Upadate</button>

                                    <?php } else{ ?>
                                        <div class="col-lg-9">
                                            <input type="hidden" name="submit" value="Add" />
                                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10"   name="submit_value_exit" value="exit"><i class="la la-check"></i>Save & exit</button>

                                            <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
                                        <?php } ?>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 col-6 text-right">
                                        <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

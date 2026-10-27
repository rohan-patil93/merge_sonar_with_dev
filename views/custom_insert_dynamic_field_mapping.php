<?php
$_SESSION['apply_js']['multiselest'] = 1;
    if(isset($_POST['submit_value'])){
        foreach ($_POST['filed'] as $key=>$filed) {
            if($_POST['select'][$key] != '0'){
                $infiels[$filed] = $_POST['select'][$key];
            }
        }
        $datacv['file_name'] = $_POST['file_name'];
        $datacv['application_table'] = $_POST['table'];
        $datacv['filed_mapping'] = json_encode($infiels);
        $datacv['is_active'] =0;
        $datacv['current_status']    = 0;
        $datacv['created_date'] = nowDate();
        $datacv['created_by'] = $_SESSION['userid'];
        $datacv['company_id'] = $_SESSION['company_id'];
        $url = API."?mode=adddata&table=import_mappings&method=post";
        $response = file_post_contents($url, $datacv, 'insert');
        if ($response) {
            header("location:index.php?option=COM_INSERT_DYNAMIC_DATA_IMPORT&file_name=".$_POST['file_name']."&table=".$_POST['table']);
        }
    }

    $vdata1["where"] = array('name'=> $_GET['file_name']);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=edi_dump&data=".$vdata;
    $configesponse = callAPI($vurl);
    $edidumps = json_decode($configesponse,1);
    $edidumpsdata = $edidumps[0];
    $application_table_name = $edidumpsdata['application_table_name'];
    $array = array('id','company_id','name','application_table_name','is_active','current_status','status_date','from_date','to_date','created_date','created_by','updated_by','updated_date');

     $edidumpsdata = json_decode($edidumpsdata['field_data'],1);

    $vdata1["where"] = array('is_active'=> '0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=listofTableFieldsonly&table=".$_GET['table'];
    $configesponse = callAPI($vurl);
    $selectdata = json_decode($configesponse,1);
?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row workflow-step text-center mt-5 mb-5">
            <div class="col-lg-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon  bg-primary"></div>
                <div class="font-size-lg mt-1 ">Step 1</div>

            </div>
            <div class="col-lg-4">
                <div class="work-box bg-primary">
                    <i class="ti-settings"></i>
                </div>
                <div class="step-icon bg-secondary"></div>
                <div class="font-size-lg mt-1">Step 2</div>

            </div>
            <div class="col-lg-4">
                <div class="work-box bg-secondary">
                    <i class="ti-settings"></i>
                </div>

                <div class="font-size-lg mt-1">Step 3</div>

            </div>

        </div>
        <div class="row">
            <div class="col-lg-12">
            <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
            <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                <div class="card-header table-card-header">
                      <h5><?php echo "Data Mapping";  ?></h5>
                      <div class="card-header-right table-card-header ">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                  <i class="icofont icofont-reply"></i>
                                  Back
                            </a>
                        </div>
                </div>
                <div class="card-body">
                    <div class="">
                        <?php foreach ($edidumpsdata as $filed=>$edidump) {
                                if($edidump!=""){
                            ?>

                        <div class="form-group row col-lg-12">
                            <label class="col-4">
                                <input aria-label="Input field" class="form-control form-control-solid" type="text" value="<?php echo $edidump; ?>" />
                                <input class="form-control form-control-solid" type="hidden" name="filed[]" value="<?php echo $filed; ?>" />
                            </label>
                                <div class="col-4">
                                    <select aria-label="Ect[]" class="form-select js-example-basic-single" name="select[]">
                                        <option value="0">Select Any one</option>
                                        <?php foreach ($selectdata as  $select) {
                                            if($select != 'id'){
                                        ?>
                                        <option  <?php if(strtolower($edidump) ==strtolower($select)){ ?> selected <?php
} ?> value="<?php echo $select;  ?>"><?php echo viewTitle($select);  ?></option>

                                    <?php } ?>
                                    <?php } ?>
                                    </select>
                                </div>
                        </div>
                    <?php } ?>
                    <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-lg-6">
                    <input type="hidden" name="submit_map" value="Add">
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i>Save &amp; exit</button>
                    <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="continue"><i class="la la-check-circle"></i>Save &amp; continue</button>
                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2"><i class="la la-close"></i>Cancel</button>
                </div>
                <div class="col-lg-3">        </div>
                <div class="col-lg-3 text-right">
                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
                </div>
            </div>
        </div>
        <input type="hidden" name="table" value="<?php echo $_GET['table']; ?>">
        <input type="hidden" name="file_name" value="<?php echo $_GET['file_name']; ?>">
    </form>
    </div>
</div>

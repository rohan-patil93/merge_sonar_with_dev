<?php
$_SESSION['apply_js']['multiselest'] = "1";
$File_Uploads_Path =  "uploads/all/";
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','parent_category_id'=>'0');
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=expense_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$expense_categories = json_decode($configesponse,1);
foreach($expense_categories as $catlist){
    $expense_catlist[$catlist['id']] = $catlist['category_name'];
}

$caturl = API."?mode=gettabledetails&table=expense_categories";
$catdata = callAPI($caturl);
$catdata = json_decode($catdata,1);
foreach($catdata as $catdat){
    if($catdat['parent_category_id']==0){
        $datacat[$catdat['id']]=$catdat;
    }else{
        $datacat[$catdat['parent_category_id']]['data'][]=$catdat;
    }
}

$caturl = API."?mode=gettabledetails&table=designations";
$catdata = callAPI($caturl);
$desgdata = json_decode($catdata,1);

$expense_catlist = array_unique($expense_catlist);

if($_GET['Rid']!=""){
    $drid = encodeId($_GET['Rid']);
    $erid = explode(":",$drid);
    $rid = $erid['1'];

    $vurl = API."?mode=singlerowtabledata&table=expense_categories&rid=".$rid;
    $configesponse = callAPI($vurl);
    $expense_row = json_decode($configesponse,1);
}

if(isset($_POST['submit_save'])){
    savecategory($_POST);
    header("location:index.php?option=COM_LIST_EXPENSE_CATEGORIES");
}elseif(isset($_POST['submit_continue'])){
    $rid = savecategory($_POST);
    header("location:index.php?option=COM_INSERT_EXPENSE_CATEGORIES&Rid=".$rid);

}
function savecategory($postarry){
    $docvalue = $docvalue ?? '';
    $File_Uploads_Path =  "uploads/all/";
    $data = array();
    $data['company_id'] = $_SESSION['company_id'];
    $data['parent_category_id'] = $postarry['parent_category_id'];
    $data['category_name'] = $postarry['category_name'];
    $data['account_code'] = $postarry['account_code'];
    $data['description'] = $postarry['description'];
    $data['policy_document'] = $postarry['policy_document'];
    $data['max_expense_limit'] = $postarry['max_expense_limit'];
    $data['receipt_required_limit'] = $postarry['receipt_required_limit'];
    if($_FILES["image_url".$docvalue]["name"]!=''){
        $temp = explode(".", $_FILES["image_url"]["name"]);
        $Filename=  "file_".time()."_".uniqid();
        $newfilename = $Filename.'.' .end($temp);
        $data['image_url'] = $newfilename;
        move_uploaded_file($_FILES["image_url"]["tmp_name"], $File_Uploads_Path.$newfilename);
    }
    else{
        $data['image_url'] = $postarry['hidden_image_url'];
    }
    if($_FILES["policy_document".$docvalue]["name"]!=''){
        $temp = explode(".", $_FILES["policy_document"]["name"]);
        $Filename=  "file_".time()."_".uniqid();
        $newfilename = $Filename.'.' .end($temp);
        $data['policy_document']= $newfilename;
        move_uploaded_file($_FILES["policy_document"]["tmp_name"], $File_Uploads_Path.$newfilename);
    }
    else{
        $data['policy_document'] = $postarry['hidden_policy_document'];
    }

    if($postarry['rid']!=""){
        $data['updated_date'] = nowDate();
        $data['updated_by'] = $_SESSION['userid'];
        $url = API."?mode=updaterowtabledata&rid=".$postarry['rid']."&table=expense_categories&method=post";
        $response = file_post_contents($url, $data, 'update');
        $pid = deocdeId("id:".$postarry["rid"]);
        return $pid;
    }else{
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
        $data['is_active'] = '0';
        $data['current_status'] ='0';
        $url = API."?mode=adddata&table=expense_categories&method=post";
        $response = file_post_contents($url, $data, 'insert');
        $res = json_decode($response,1);
        $pid = deocdeId("id:".$res["LASTID"]);
        return $pid;
    }
}
if(isset($_POST['submit'])){
    $data = array();

    if($_POST['submit']=='saveexpmileage'){

        foreach($_POST['exp_rowid'] as $rkey=>$rid){
            $data['company_id'] = $_SESSION['company_id'];
            $rowid = $_POST['exp_rowid'][$rkey];
            $data['name'] = $_POST['name'][$rkey];
            $data['expense_type'] = '0';
            $data['default_unit'] = $_POST['default_unit'];
            $data['expenses_category_id'] = $_POST['parent_category_id'];
            $data['designation_id'] = $_POST['designation_id'];
            $data['amount'] = $_POST['amount'][$rkey];
            $data['expense_start_date'] = date("Y-m-d",$_POST['expense_start_date'][$rkey]);
            if($rowid !=""){
                $data['updated_date'] = nowDate();
                $data['updated_by'] = $_SESSION['userid'];
                $url = API."?mode=updaterowtabledata&rid=".$rowid."&table=expenses_mileages_perdiem&method=post";
                $response = file_post_contents($url, $data, 'update');
            }else{
                $data['created_date'] = nowDate();
                $data['created_by'] = $_SESSION['userid'];
                $data['is_active'] = '0';
                $data['current_status'] ='0';
                $url = API."?mode=adddata&table=expenses_mileages_perdiem&method=post";
                $response = file_post_contents($url, $data, 'insert');
            }
        }
    }

    if($_POST['submit']=='saveexpperdiem'){
        $default_unit = $_POST['default_unit'];
        foreach($_POST['perdiem_rowid'] as $rkey=>$rid){
            $data['company_id'] = $_SESSION['company_id'];
            $rowid = $_POST['perdiem_rowid'][$rkey];
            $data['name'] = $_POST['perdiem_name'][$rkey];
            $data['expense_type'] = '1';
            $data['default_unit'] = $default_unit;
            $data['expenses_category_id'] = $_POST['parent_category_id'];
            $data['designation_id'] = $_POST['designation_id'];
            $data['amount'] = $_POST['perdiem_amount'][$rkey];


            if($rowid !=""){
                $data['updated_date'] = nowDate();
                $data['updated_by'] = $_SESSION['userid'];
                $url = API."?mode=updaterowtabledata&rid=".$rowid."&table=expenses_mileages_perdiem&method=post";
                $response = file_post_contents($url, $data, 'update');

            }else{
                $data['created_date'] = nowDate();
                $data['created_by'] = $_SESSION['userid'];
                $data['is_active'] = '0';
                $data['current_status'] ='0';
                $url = API."?mode=adddata&table=expenses_mileages_perdiem&method=post";
                $response = file_post_contents($url, $data, 'insert');
            }
        }
    }

}
$type = $_GET['TYPE']!=""?$_GET['TYPE']:"1";
?>

<div class="d-flex flex-column-fluid ">

    <div class="container-fluid ">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5>Expenses</h5>

                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
                <div class="card-body">
                    <div class="row col-lg-12">
                        <div class="d-flex mt-2 h-55px">
                            <ul class="nav md-tabs nav-bold nav-tabs-line nav-line-tabs-3x border-transparent fs-5 fw-bolder flex-nowrap responsive-tab My_Informarion_Tab infisuite_tab">
                                <li class="nav-item">
                                    <a class="nav-link <?php if($type==1){ echo "active";
} ?>" id="profile-tab-1" data-bs-toggle="tab" href="#people-1" aria-controls="HOME">
                                        <span class="nav-icon">
                                            <i class="flaticon2-layers-1"></i>
                                        </span>
                                        <span class="nav-text">Accommodation & Ticketing</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="">
                        <div class="tab-content mb-5 " id="myTabContent1">
                            <div class="tab-pane fade <?php if($type==1){ echo "show active";
} ?>" id="people-1" role="tabpanel" aria-labelledby="home-tab-1">
                                    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                                        <div class="card-body">

                                            <div class="row">

                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Category Name<span class="text-danger">*</span></span>
                                                    <input aria-label="Category Name" type="text" name="category_name" class="form-control" value="<?php echo $expense_row['category_name']; ?>">
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Parent Category<span class="text-danger">*</span></span>

                                                    <select aria-label="Parent Category" class="form-control form-select js-example-basic-single"  name="parent_category_id" id="parent_category_id_2">
                                                        <option value="0"> Select Any One </option>
                                                        <?php foreach($expense_catlist as $ekey => $expense) { ?>
                                                            <option <?php if($expense_row['parent_category_id']==$ekey){ echo "selected";
}  ?> value="<?php echo $ekey;?>" ><?php echo $expense; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Policy Document</span>
                                                    <div></div>
                                                    <div class="custom-file">
                                                        <input aria-label="Custom File" type="file" class="form-control" id="customFile" value="<?php echo $expense_row['policy_document']; ?>" name="policy_document">
                                                    </div>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Description</span>
                                                    <textarea aria-label="Description" name="description" class="form-control" > <?php echo $expense_row['description']; ?></textarea>
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Expense Amount Limit<span class="text-danger">*</span></span>
                                                    <input aria-label="Max Expense Limit" type="number" name="max_expense_limit" class="form-control text-right" value="<?php echo $expense_row['max_expense_limit']; ?>">
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Receipt Required Limit<span class="text-danger">*</span></span>
                                                    <input aria-label="Receipt Required Limit" type="number" name="receipt_required_limit" class="form-control text-right" value="<?php echo $expense_row['receipt_required_limit']; ?>">
                                                </div>
                                                <div class="form-group col-lg-6">
                                                    <span class="font-weight-bolder">Account Code<span class="text-danger">*</span></span>
                                                    <input aria-label="Account Code" type="text" name="account_code" class="form-control" value="<?php echo $expense_row['account_code']; ?>">
                                                </div>

                                                <div class="form-group col-lg-6">
                                                    <span>Image Url</span>
                                                    <div></div>
                                                    <div class="profile-pic">
                                                        <?php $logo = $expense_row['image_url']==''?  $File_Uploads_Path.'no-image.jpg':$File_Uploads_Path.$expense_row['image_url']; ?>
                                                        <label class="-label" for="logo_image">

                                                            <span> <i class="fa fa-camera"></i> &nbsp; Change Image</span>
                                                        </label>
                                                        <input  type="file" name="image_url" id="logo_image" accept=".png, .jpg, .jpeg" onchange="loadFile(event,this.id)"/>
                                                        <input type="hidden" name="logo_remove" >

                                                        <img src="<?php echo $logo;?>" id="outputlogo_image" width="200"   alt="Logo" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="rid" value="<?php echo $rid; ?>" />
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_save" value="exit"><i class="la la-check"></i>Save & Exit</button>

                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_continue" value="exit"><i class="la la-check"></i>Save & Continue</button>
                                    </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

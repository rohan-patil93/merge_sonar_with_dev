<?php
$_SESSION['apply_js']['multiselest'] = 1;
$_SESSION['apply_js']['textediter'] = '1';
$_SESSION['apply_js']['jobposting'] = '1';
$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=job_descrption_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$job_categories = json_decode($configesponse,1);
foreach ($job_categories as $catee) {
    $catees[$catee['id']] = $catee;
}
$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=job_descrptions&data=".$vdata;
$configesponse = callAPI($vurl);
$job_descrptions = json_decode($configesponse,1);
foreach ($job_descrptions as $job_descrption) {
    $jobdescrptions[$job_descrption['job_description_category_id']][] = $job_descrption;
}
$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=job_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$categories = json_decode($configesponse,1);
$table_name = "job_titles";
if(isset($_POST['submit'])){
        $jobcheckval = array_keys($_POST['desc']);
        foreach($_POST['cat'] as $postcat=>$postcatdata){
            $descrp = '';
            $descrp.= '<p style="font-size:16px;font-weight: bold;">'.$catees[$postcat]['name'].' </p>';
            foreach ($jobdescrptions[$postcat] as $descvalue) {
                if(in_array($descvalue['id'],(array)$jobcheckval)){
                    $descrp.= '<p style="font-size:14px; margin-left:20px;">'.$descvalue['description'].' </p>';
                }
            }
        }
        $checkk['categ'][]= array_keys($_POST['cat']);
        $checkk['descr'][]= array_keys($_POST['desc']);
        $data['company_id'] = $_SESSION['company_id'];
        $data['job_title'] = $_POST['job_title'];
        $data['job_category_id'] = $_POST['job_category_id'];
        $data['note'] = $_POST['note'];
        $data['job_description'] = $descrp;
        $data['list_check'] = json_encode($checkk,1);
        $data['current_status'] = '0';
        $data['is_active'] = '0';
        if($_POST['submit']=='Adds'){
            $data['status_date'] = nowDate();
            $data['created_date'] = nowDate();
            $data['created_by'] = $_SESSION['userid'];
            $url = API."?mode=adddata&table=job_titles&method=post";
            $response = file_post_contents($url, $data, 'insert');
            header("location:index.php?option=COM_LIST_JOB_TITLES");
        }
        if($_POST['submit']=='updatess'){
            $data['updated_date'] = nowDate();
            $data['updated_by'] = $_SESSION['userid'];
            $url = API."?mode=updaterowbasepimarydata&rid=".$_POST['rid']."&table=".$table_name."&method=post";
            $response = file_post_contents($url, $data, 'update');
            header("location:index.php?option=COM_LIST_JOB_TITLES");
        }

    }
    if($_GET['Rid']!=""){
        $rid =  encodeId($_GET['Rid']);
        $rid = urldecode($rid);
        $url = API."?mode=togetdatabasepaimairy&rid=$rid&table=".$table_name;
        $response = callAPI($url);
        $role = json_decode($response,1);
    }
    ?>
    <script type="text/javascript">
        function jobcate(catid){
            var job_title = document.getElementById('job_title').value;
            var job_cate = document.getElementById('kt_select2_41').value;
            if(job_title==""){
           document.getElementById('job_title_err').style.display = "block";
           $("#cat_"+catid).prop("checked", false);
            if(job_cate==""){
                document.getElementById('job_category_err').style.display = "block";
            }
            return false;
        }else{
            document.getElementById('job_title_err').style.display = "none";
        }
        if(job_cate==""){
            document.getElementById('job_category_err').style.display = "block";
            $("#cat_"+catid).prop("checked", false);
            return false;
        }else{
            document.getElementById('job_category_err').style.display = "none";
        }

        if($("#cat_"+catid ).prop('checked')) {
            $( "#cate_"+catid ).show("slow");
$("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).show('slow');

}else{
$("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).hide();

$( "#cate_"+catid ).hide();
        }

    }
    function jobdesc(id,catid){
        if($("#jobdes_"+id ).prop('checked')) {
            $("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).find("#id_"+id).show();
            $("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).find("#id_"+id).css('margin-left','30px');
            $("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).find("#id_"+id).css('margin-top','5px');

        }else{
            $("#description_ifr").contents().find('#tinymce').find(".checktxetarea").find("#discate_"+catid).find("#id_"+id).hide();
        }
    }
</script>
<style>
    .line-height24{
        line-height: 24px !important;
    }
    #sticky {
        position: sticky;
        position: -webkit-sticky;
        background: #f83d23;
        width: 100px;
        height: 100px;
        top: 70px;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 0 6px #000;
        color: #fff;
    }
</style>
<div class="d-flex flex-column-fluid ">
    <div class="container-fluid ">
        <div class="card card-custom card-sticky " id="kt_page_sticky_card">
            <div class="card-header">
                <h5>Job Title</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
                </div>
            </div>
            <div >
                <form method="post" >
                    <div class="card card-custom">
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="form-group col-lg-12">
                                    <div class="form-group col-lg-3 p-1 float-left">
                                        <span class="form-label ">Job Title&nbsp;<span class="requried">*</span></span>
                                        <div class="input-group input-icon-right">
                                            <input aria-label="Job Title" class="form-control"  type="text"  id="job_title" name="job_title" id="job_title_2"  value="<?php echo $role['job_title']; ?>">

                                        </div>
                                        <div style="display: none;color: red;" id="job_title_err"> Please Enter Job Tittle Name </div>
                                    </div>
                                    <div class="form-group col-lg-3 p-1 float-left">
                                        <span class="form-label ">Job Category</span>
                                        <select aria-label="Job Category" class="form-control form-select js-example-basic-single" id="kt_select2_41"  name="job_category_id">
                                            <option value=""> Select Any one Job Category </option>
                                            <?php foreach($categories as $categ){ ?>
                                                <option  <?php if($role['job_category_id'] == $categ['id'] ){ echo "selected";
} ?> value="<?php echo $categ['id'];  ?>"><?php echo $categ['category_name'];  ?></option>
                                            <?php } ?>
                                        </select>
                                        <div style="display: none;color: red;" id="job_category_err"> Please Select Job Category </div>
                                    </div>
                                    <div class="form-group col-lg-6 p-1 float-left">
                                        <span class="form-label ">Note</span>
                                        <div class="kt-radio-inline">
                                            <textarea aria-label="Note" type="text" class="form-control" id="note" name="note" ><?php echo $role['note']; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php $role_js = json_decode($role['list_check'],1);
                                $role_cat = $role_js['categ']['0'];
                                $role_des = $role_js['descr']['0'];
                                ?>
                                <div class="col-lg-6">
                                    <h3 class="card-label">Description</h3>
                                    <div class="col-lg-12">
                                        <div class="checkbox-list">
                                            <?php foreach ($job_categories as $job_categorie) { ?>
                                                <div class="mt-5">
                                                    <div class="checkbox-fade fade-in-primary">
                                                        <label class="form-label line-height24">
                                                            <input aria-label="Checkbox" type="checkbox" <?php if(in_array($job_categorie['id'],(array)$role_cat)){ echo "checked";
} ?>  name="cat[<?php echo $job_categorie['id']; ?>]" id="cat_<?php echo $job_categorie['id']; ?>" onclick="jobcate(<?php echo $job_categorie['id']; ?>)" onkeydown="" />
                                                            <span class="cr float-left">
                                                                <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                            </span>
                                                            <?php echo $job_categorie['name'];  ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="ml-10 mt-5" id="cate_<?php echo $job_categorie['id']; ?>" <?php if(!in_array($job_categorie['id'],(array)$role_cat)){ ?> style="display: none;" <?php
} ?>>
                                                    <?php foreach ($jobdescrptions[$job_categorie['id']] as $jobdescrption) { ?>
                                                        <div class="checkbox-fade fade-in-primary mt-2 p-l-20">
                                                            <label class="form-label text-auto-hight line-height24">
                                                                <input aria-label="Checkbox" type="checkbox" <?php if(in_array($jobdescrption['id'],(array)$role_des)){ echo "checked";
} ?>  name="desc[<?php echo $jobdescrption['id']; ?>]" id="jobdes_<?php echo $jobdescrption['id']; ?>" onclick="jobdesc(<?php echo $jobdescrption['id'];  ?>,'<?php echo $job_categorie['id']; ?>')" onkeydown="" />

                                                                <span class="cr float-left">
                                                                    <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                                                </span>
                                                                <?php echo $jobdescrption['description'];  ?>
                                                            </label></div></br>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6  card card-custom card-sticky">
                                        <div class=" card-custom  " id="kt_page_stretched_card">
                                            <div class="card-header">
                                                <div class="card-title">
                                                    <h3 class="card-label">Description</h3>
                                                </div>
                                            </div>
                                            <textarea aria-label="Description" class="textediters" name="description" rows="4" cols="250" id="description">
                                                <div class="p-10 checktxetarea">

                                                    <?php foreach ($job_categories as $job_categorie) { ?>
                                                        <div  id="discate_<?php echo $job_categorie['id']; ?>" <?php if(in_array($job_categorie['id'],(array)$role_cat)){ echo "";
}else{ ?> style="display: none;" <?php
} ?> >
                                                            <div class="mt-5 font-weight-bolder font h6" style="margin-bottom:15px;margin-top:10px;">
                                                                <b><?php echo $job_categorie['name'];  ?></b>
                                                            </div>
                                                            <?php foreach ($jobdescrptions[$job_categorie['id']] as $jobdescrption) { ?>
                                                                <div class="ml-10 mt-5 mlmt30" id="id_<?php echo $jobdescrption['id']; ?>" <?php if(in_array($jobdescrption['id'],(array)$role_des)){ echo "checked";
}else{ ?> style="display: none;" <?php
} ?> style="margin-left:30px; margin-top: 5px;">
                                                                    <?php echo $jobdescrption['description'];  ?>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <div class="row">
                                <div class="col-lg-9">
                                    <?php if(isset($_GET['Rid']) && $_GET['Rid'] !='' && $_GET['copy'] != '1' ){ ?>
                                        <input type="hidden" name="submit" value="updatess" />
                                        <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                                        <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_update" value="exit"><i class="la la-check"></i>Update</button>
                                    <?php }else{ ?>
                                        <input type="hidden" name="submit" value="Adds" />
                                        <button id="kt_login_signin_submit_continue" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_values" value="exit"><i class="la la-check"></i>Save</button>
                                    <?php } ?>

                                    <button type="reset" class="btn btn-light-primary font-weight-bolder mr-2" ><i class="la la-close"></i>Cancel</button>
                                </div>
                                <div class="col-lg-3 text-right">
                                    <button type="reset" disabled="disabled" class="btn btn-danger"><i class="la la-trash"></i>Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div></div></div>

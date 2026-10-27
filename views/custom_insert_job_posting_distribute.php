<?php
$_SESSION['apply_js']['multiselest'] = 1;
    if($_POST['submit1'] == 'Add'){
         $_POST['created_by'] = $_SESSION['userid'];
        $_POST['created_date'] = nowDate();
        $url = API."?mode=addjobpostingdistribute&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        if ($response) {
            header("location:index.php?option=COM_INSERT_JOB_POSTING_DISTRIBUTE&POST=".$_POST['job_posting_id']);
        }
    }
    $vdata1["where"] = array('is_active'=> '0');
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=job_postings&data=".$vdata;
    $configesponse = callAPI($vurl);
    $job_postings = json_decode($configesponse,1);

    $vurl = API."?mode=gettabledetailswithconditions&table=vendors&data=".$vdata;
    $configesponse = callAPI($vurl);
    $vendors = json_decode($configesponse,1);

    if($_GET['POST']!=""){
        foreach($job_postings as $job_posting){
            if($job_posting['id'] == $_GET['POST']){
                $sejobpost = $job_posting;
            }
        }
        $vdata1["where"] = array('is_active'=> '0','job_posting_id'=>$_GET['POST']);
        $vdata = urlencode(json_encode($vdata1));
        $vurl = API."?mode=gettabledetailswithconditions&table=job_posting_distribute&data=".$vdata;
        $configesponse = callAPI($vurl);
        $job_posting_distributes = json_decode($configesponse,1);
        $mulcheck= array();
        foreach($job_posting_distributes as $job_posting_distribute){
            $mulcheck[] = $job_posting_distribute['vendor_id'];
        }
    }
?>
<script type="text/javascript">
    function jobposting(val) {
        window.location.href = 'index.php?option=COM_INSERT_JOB_POSTING_DISTRIBUTE&POST='+val;
    }
    function distributetype(val){
        if(val==2){
            $("#multiple1").show();
        }else{
            $("#multiple1").hide();
        }
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky j-content" id="kt_page_sticky_card">


<div class="card-header">
    <h5>Job Posting</h5>
    <div class="card-header-right">
             <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
    </div>
</div>

                <div class="card-body">
                    <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                        <div class="form-group row">
                            <div class="form-group col-lg-6">
                                <span class="form-label">Job Posting&nbsp;<span class="requried">*</span></span>
                                <div id="frist">
                                    <select aria-label="Job Posting" class="form-control form-select js-example-basic-single"  onchange="jobposting(this.value)" name="job_posting_id" >
                                        <option value=""> Select Any one </option>
                                        <?php foreach ($job_postings as $key => $job_posting) { ?>
                                        <option <?php if($job_posting['id'] == $_GET['POST']){ echo"selected";
} ?> value="<?php echo $job_posting['id']; ?>"> <?php echo $job_posting['job_number']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if($_GET['POST']!=""){ ?>
                            <div class="form-group col-lg-6">
                                <span class="form-label">Distribute Type&nbsp;</span>
                                    <?php $type_lists = array('1'=>'All Vendor','2'=>'Distribute Vendor','3'=> 'Inner') ?>
                                    <select aria-label="Distribute" class="form-control form-select js-example-basic-single "  onchange="distributetype(this.value)" name="distribute" >
                                        <option value="0"> Select Any one </option>
                                        <?php foreach ($type_lists as $typekey => $type_list) {  ?>
                                            <option <?php if($typekey == $sejobpost['job_posting_distribute_type']){ echo"selected";
} ?> value="<?php echo $typekey; ?>"> <?php echo $type_list; ?> </option>
                                        <?php } ?>
                                    </select>

                            </div>
                            <div class="form-group col-lg-6" id="multiple1" <?php if($sejobpost['job_posting_distribute_type']!="2"){ ?> style="display:none" <?php
} ?> >
                                <span class="form-label">Vendors&nbsp;</span>
                                <select aria-label="Vendor[]" class="form-control form-select js-example-basic-multiple" id="kt_select2_31" name="vendor[]" multiple="multiple">
                                    <option value=""> Select Any one </option>
                                    <?php foreach ($vendors as $selectkey => $vendor) {  ?>
                                            <option <?php if(in_array($vendor['id'], (array)$mulcheck)){ ?> selected <?php
} ?> value="<?php echo $vendor['id']; ?>"><?php echo $vendor['first_name']; ?></option>
                                    <?php  } ?>
                                </select>
                            </div>

                        </div>
                        <div class="card-footer">
                            <div class="col-lg-4">
                                <input type="hidden" name="submit1" value="Add" />
                                <button class="btn btn-primary mr-2"  id="activebutton" <?php echo $disable; ?>  name="submit_value" value="exit"><i class="la la-check"></i>Save </button>
                                <button type="reset" class="btn btn-secondary" ><i class="la la-close"></i>Cancel</button>
                            </div>
                             <?php } ?>
                        </div>

                    </form>
                </div>

        </div>
    </div>
</div>


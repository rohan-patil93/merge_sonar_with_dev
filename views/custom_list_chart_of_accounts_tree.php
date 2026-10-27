<?php
    $wh1["where"] = array('is_active'=>0,'company_id'=>$_SESSION['company_id']);
    $wh1["field"] = "id,account_name";
    $data1 = urlencode(json_encode($wh1));
    $url1 = API."?mode=gettabledetailswithconditions&table=chart_of_accounts&data=".$data1;
    $jsondecode = callAPI($url1);
    $jsondecode = json_decode($jsondecode,1);
?>
<div class="page-body">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header table-card-header mb-3">
                    <h5> Chart OF Account Lists </h5>
                    <div class="card-header-right table-card-header mb-2">
                        <span class="form-label  col-form-label m-r-20">Chart OF Account List</span>
                        <select aria-label="Hid Userid" class="form-select js-example-basic-single " id="hid_userid" onchange="emplyeetree(this.value)">
                            <?php foreach($jsondecode as $dt){ ?>
                                <option value="<?php echo $dt['id']; ?>" ><?php echo $dt['account_name']; ?></option>

                            <?php } ?>
                        </select>
                        <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                            <i class="icofont icofont-reply"></i>
                            Back
                        </a>
                    </div>
                </div>
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-body">
                        <div class="form-group row ml-20 mr-20 j-wrapper  ">

                            <div id="tree"/></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

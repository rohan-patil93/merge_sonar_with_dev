<?php
    $dm = date('Y');
    $mon = date('m');
    if($mon < 4 ){
        $dn = $dm-1;
    }else{
        $dn = $dm;
        $dm = date('Y');
        $dm = $dm + 1;
    }
    $dc = $dn."-".$dm;
    $emp_id = $_SESSION['emp_id'];
    $assessment_year = $_GET['assessment_year'] !='' ? $_GET['assessment_year'] : $dc;
    $vdata1["where"] = array('is_active'=> '0','assessment_year'=>$assessment_year,'emp_id'=>$emp_id);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=emp_it_declaration&data=".$vdata;
    $configesponse = callAPI($vurl);
    $itds = json_decode($configesponse,1);
    $alpha=array('0','A','B','C','D','E','F','G','H','I','J','K');
    $stausresult = ViewStatusData();
?>
<script type="text/javascript">
    function rechangeassement(val){
         window.location.href = 'index.php?option=COM_INSERT_IT_PLANS&assessment_year='+val;
    }
 function statuspopup(status,id){
    console.log('check');
     var table_name = 'emp_it_declaration';
      jQuery.ajax({
            url: "views/custom_insert_status_popup.php",
            data:'table='+table_name+'&status='+status+'&rid='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
   }
</script>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom">
                <div class="card-header table-card-header">
                    <h5>IT Savings (Declaration)*</h5>
                <div class="card-header-right table-card-header ">
                <select aria-label="Select option" class="form-select" tabindex="" onchange="rechangeassement(this.value)">
                    <?php
                        for($i=1; $i<4; $i++){
                            $dc = $dm-1;
                            $cc = $dc."-".$dm; ?>
                        <option  <?php if($cc==$assessment_year){ echo "selected";
} ?> value="<?php echo $cc; ?>"><?php echo $cc; ?></option>
                    <?php $dm=$dm-$i;
                    } ?>
                </select>
                </div>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <?php $k=1; foreach ($itds as $itd) {
                            $version_no = $itd['version_no']!="" ? $itd['version_no'] : $k;
                        ?>
                    <div class="col-lg-4">
                        <div class="card card-custom">
                            <div class="card-header">
                                <div class="card-title">
                                    <h5 class="card-label">
                                        Plan <?php echo $alpha[$k]; ?>
                                        <small>Updated on <?php echo date('d M Y',strtotime($itd['updated_date'])); ?></small>
                                    </h5>
                                </div>
                                <div class="card-header-right table-card-header">
                                    <a href="index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=<?php echo $assessment_year; ?>&version_no=<?php echo $version_no; ?>&IT_PLAN=<?php echo $itd['id']; ?>" class="btn btn-xs btn-icon btn-danger mr-2">
                                        <i class="far fa-edit text-white"></i>
                                    </a>
                        <a href="index.php?option=COM_VIEW_REGIME&assessment_year=<?php echo $assessment_year; ?>&version_no=<?php echo $version_no; ?>&IT_PLAN=<?php echo $itd['id']; ?>" class="btn btn-xs btn-icon btn-info mr-2">
                                        <i class="ti-printer text-white"></i>
                                    </a>
                                    <a href="#" class="btn btn-xs btn-icon btn-success mr-2">
                                        <i class="ti-trash text-white"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="mt-1">
                                <div class="col-lg-12 row text-center m-0">
                                    <div class="col-lg-4 border bg-light p-2" >
                                        <h6>Potential</h6>
                                        <span><?php echo customeummarmonyformate($itd['potential_amount']); ?></span>
                                    </div>
                                    <div class="col-lg-4 border bg-light" style="padding-bottom: 10px; padding-top: 10px;">
                                        <h6>Actual</h6>
                                        <span><?php echo customeummarmonyformate($itd['declaration_tax']); ?></span>
                                    </div>
                                    <div class="col-lg-4 border bg-light" style="padding-bottom: 10px; padding-top: 10px;">
                                        <h6>GAP</h6>
                                        <span><?php echo customeummarmonyformate($itd['gap_tax']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div>
                            <div class="d-flex align-items-center justify-content-between p-4 flex-lg-wrap flex-xl-nowrap">
                                    <div class="d-flex flex-column mr-5">
                                        <span class="text-dark text-hover-primary mb-2">Total Income &nbsp;: &nbsp; &nbsp;&nbsp; &nbsp; <?php echo  customeummarmonyformate($itd['taxable_income']); ?></span>
                                        <span class="text-dark text-hover-primary mb-2 ">Annual Income &nbsp;: &nbsp; &nbsp;  <?php echo customeummarmonyformate($itd['annual_tax']); ?></span>
                                    </div>
                                    <div class="ml-6 ml-lg-0 ml-xxl-6 flex-shrink-0">
                                        <div>
                                        <?php for($i=0;$i<5;$i++){
                                               if($i< $itd['star_rate']){?>
                                                <i class="fas fa-star text-warning"></i>
                                            <?php }else{?>
                                                <i class="fas fa-star"></i>
                                        <?php } ?>
                                        <?php } ?>
                                        </div>
                                        <div>
                                        <?php if($itd['current_status']==0){ ?>
                                        <button type="button" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2 m-b-10"   name="submit_value" value="continue" onclick="statuspopup(10,<?php echo $itd['id']; ?>)">Send Approval</button>
                                        <?php }else{ ?>
                                           <span> <?php echo $stausresult[$itd['current_status']]['name']; ?> </span>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php $k++;
} ?>
                    <div class="col-lg-4">
                        <div class="card card-custom bg-primary" style="padding: 70px 0;">
                            <div class="card-body text-center ">
                                <a class="h3 text-white" href="index.php?option=COM_INSERT_IT_DECLARATION&assessment_year=<?php echo $assessment_year; ?>&version_no=<?php echo count((array)$itds); ?>"><i class="fas fa-plus"></i> Create New Plan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

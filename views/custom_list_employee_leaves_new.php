<?php
$vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=leave_categories&data=".$vdata;
$leavetype= callAPI($vurl);
$leavetype = json_decode($leavetype,1);

$result = date("Y",strtotime(date('Y-m-d')));

$vdata3["where"] = array('is_active'=>'0','employee_id'=>$_SESSION['emp_id']);
$vdata_3 = urlencode(json_encode($vdata3));
$vurl_3 = API."?mode=gettabledetailswithconditions&table=emp_leave_balances&data=".$vdata_3;
$configesponse = callAPI($vurl_3);
$emp_leave_balances = json_decode($configesponse,1);

foreach ($emp_leave_balances as $value) {
    $applicable_leaves[$value['leave_type']] = $value['applicable_leaves'];
    $consume_leaves[$value['leave_type']] = $value['consumed_leaves'];
}
?>

<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header table-card-header">
              <h5><?php echo "Leaves";  ?></h5>
                    <div class="card-header-right table-card-header">
                        <?php if(count((array)$res_team)>0){ ?>
        <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>" class="nav-link ml-3 float-left" <?php if($_GET['TEAM']!=1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon2-user"></i></span>&nbsp;Leaves</a>
        <a href="index.php?option=COM_LIST_NEW_EMPLOYEE&NI=<?php echo $_GET['NI']; ?>&TEAM=1"  class="nav-link ml-3 float-left"  <?php if($_GET['TEAM']==1){  ?> style="border-bottom: 1px solid #3699FF;" <?php
} ?>> <span class="nav-icon"><i class="flaticon-network"></i></span>&nbsp;Team Leaves</a>
      <?php } ?>
                        <a href="index.php?option=COM_INSERT_LEAVE_REQUEST_NEW"  class="btn btn-primary font-weight-bold ml-3 float-left">
                            <i class="ki ki-plus icon-md mr-2"></i> Apply Leave </a>

                    </div>
                </div>

        <?php if($_GET['TEAM']==1){ ?>
          <input type="hidden" name="calendar_cat" id="calendar_cat" value="56">
      <?php }else{ ?>
        <input type="hidden" name="calendar_cat" id="calendar_cat_2" value="6">
       <?php } ?>
                <div class="card-body">
                    <div class=" d-flex flex-column mb-6">
                        <div class="row col-lg-12 deep">
                            <?php
                            $count = count($leavetype);
                            $div_count_array = array('0'=>'0','1'=>'12','2'=>'6','3'=>'4','4'=>'3','5'=>'2', '6'=>'2','7'=>'3','8'=>'3','9'=>'2','10'=>'2','11'=>'2','12'=>'2');
                             ?>
                        </div>
                    </div>

                     <div id='calendar'></div>
                </div>
            </div>
        </div>
    </div>
</div>

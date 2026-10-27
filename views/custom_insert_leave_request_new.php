<?php
$_SESSION['apply_js']['multiselest'] = 1;
$table_name = "employee_leaves";

if(isset($_POST['submit'])){
    echo "RRRR<pre>SSSS"; print_r($_POST); echo "</pre>";
    $data['leave_from'] = date("Y-m-d",strtotime($_POST['leave_from']));
    $data['start_half_day'] = $_POST['start_half_day'];
    $data['leave_to'] = date("Y-m-d",strtotime($_POST['leave_to']));
    $data['end_half_day'] = $_POST['end_half_day'];
    $data['leave_type_id'] = $_POST['leave_type_id'];
    $data['special_request'] = $_POST['special_request'];
    $data['leave_days'] = $_POST['days'];
    $data['employee_id'] = $_SESSION['emp_id'];
    $data['company_id'] = $_SESSION['company_id'];
    $data['applying_late'] = ((strtotime(date("d-m-Y")) - strtotime($_POST['leave_from'])) / (60 * 60 * 24));
    $data['current_status'] = '0';
    $data['is_active'] = '0';
    if($_POST['submit']=='Add'){
        $data['status_date'] = nowDate();
        $data['created_date'] = nowDate();
        $data['created_by'] = $_SESSION['userid'];
            $url = API."?mode=adddata&table=".$table_name."&method=post";
            $response = file_post_contents($url, $data, 'insert');
            $res = json_decode($response,1);
            header("location:index.php?option=COM_INSERT_LEAVE_REQUEST_NEW");
    }


}

$vdata1["where"] = array('is_active'=> 0);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=leave_categories&data=".$vdata;
$configesponse = callAPI($vurl);
$cate = json_decode($configesponse,1);

$vdataee["where"] = array('is_active'=> 0,'employee_id'=>$_SESSION['emp_id']);
$vdata = urlencode(json_encode($vdataee));
$vurl = API."?mode=gettabledetailswithconditions&table=emp_leave_balances&data=".$vdata;
$empbalances = callAPI($vurl);
$empbalances = json_decode($empbalances,1);

foreach ($empbalances as $empbalance) {
    $balance[$empbalance['leave_type']] = $empbalance;
}

$empbalanceurl = API."?mode=checkleavebalanceorhalfday&emp_id=".$_SESSION['emp_id'];
$empbalancesd = callAPI($empbalanceurl);
$empbalancesd = json_decode($empbalancesd,1);

$vdata1["where"] = array('is_active'=> 0,);
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
$configesponse = callAPI($vurl);
$emplos = json_decode($configesponse,1);
foreach ($emplos as  $value) {
    $empdata[$value['id']] =$value;
}
$leavedate = date('Y-m-d');
$otstdate = date('Y-m-d', strtotime('-20 day', strtotime($leavedate)));
$ottodate = date('Y-m-d', strtotime('+20 day', strtotime($leavedate)));
$evurl = API."?mode=tosearchempleavesbydate&start=".$otstdate."&end=".$ottodate;
$otheremp = callAPI($evurl);
$otheremp = json_decode($otheremp,1);
?>
<script type="text/javascript">
    function tocheckdays(){
        var date1 =  $('#kt_datepicker_31').val();
        var date2 =  $('#kt_datepicker_32').val();
        var leave_type_id =  $('.leave_type_id').val();
        var start_half_day =  $('#start_half_day').val();
        var end_half_day =  $('#end_half_day').val();
        console.log("Date:"+date1+":Date2:"+date2);
        if(date1!="" && date2!=""){
            $.ajax({
                url: "ajax.php",
                data:'page=datediffval&date1='+date1+'&date2='+date2+'&start_half_day='+start_half_day+'&end_half_day='+end_half_day,
                type: "POST",
                success:function(data){
                    console.log(data);
                    $('#leave_days').val(data);
                },
                error:function (){}
            });

        }
    }
    function validateForm(){
        var date1 =  $('#kt_datepicker_31').val();
        var date2 =  $('#kt_datepicker_32').val();
        var leave_cat =  $('#kt_select2_41').val();
        if(date1 !="" && date2 !=""){
            $.ajax({
                url: "ajax.php",
                data:'page=leavedatecheck&date1='+date1+'&date2='+date2+'&leave_cat='+leave_cat,
                type: "POST",
                success:function(data){
                    if(data !=""){
                        return false;
                    }
                },
                error:function (){}
            });

        }

    }


</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row j-content">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom " id="kt_page_sticky_card">
                        <div class="card-header">
                            <h5>Leave Request</h5>
                            <div class="card-header-right">
                                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                    <i class="icofont icofont-reply"></i>
                                    Back
                                </a>
                            </div>
                        </div>
                        <div class="card-body">


                            <div class="form-group col-lg-6 p-2" style="float: left;">                            <div class="form-group col-lg-6">
                                    <span class="form-label">Leave Category</span>
                                    <div>
                                    <select aria-label="Leave Type" class="col-lg-12 form-control form-select js-example-basic-single leave_type_id" id="kt_select2_41" name="leave_type_id" data-val="<?php echo $role['half_day']; ?>" onchange="selectLeaveType(this.value)">
                                        <option value=""> Select Any one</option>
                                        <?php foreach ($cate as $cat) {  ?>
                                            <option <?php if($cat['id'] == $role['leave_type'] ){ ?> selected <?php
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                        <?php  } ?>
                                    </select>
                                    </div>
                                </div>
                                <div class=" col-lg-12">
                                    <span class="form-label">Start Date(s)</span>
                                    <div class="w-100">

                                        <div class="row">
                                            <div class=" col-lg-6">
                                                <input aria-label="Select date" type="date" onblur="tocheckdays()" class="form-control"  value="<?php echo  $date_pic; ?>"  name="leave_from"  id="kt_datepicker_31" />
                                            </div>
                                            <div class="col-lg-6">
                                                <select aria-label="Start Half Day" class="form-control form-select js-example-basic-single halfdayselect" id="start_half_day"  name="start_half_day">
                                                    <option value="0"> Full Day</option>
                                                    <option value="1"> Start Half</option>
                                                    <option value="2"> End Half</option>
                                                </select>
                                            </div>
                                        </div>
                                        <span class="form-label mb-1 mt-3">End Date(s)</span>
                                        <div class="row">

                                            <div class="col-lg-6">
                                                <div class="input-group date ">
                                                    <input aria-label="Select date" type="date" onblur="tocheckdays()" class="form-control"  value="<?php echo  $date_pic; ?>"  name="leave_to"  id="kt_datepicker_32" />

                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <select aria-label="End Half Day" class="form-control form-select js-example-basic-single halfdayselect" id="end_half_day" name="end_half_day">
                                                    <option value="0"> Full Day</option>
                                                    <option value="1"> Start Half</option>
                                                    <option value="2"> End Half</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">

                                    <div class="form-group col-lg-6 ">
                                        <span class="form-label">Leave Days</span>
                                        <input aria-label="Days" type="text" class="form-control" name="days" value="" id="leave_days" readonly="">
                                    </div>
                                    <div class="col-lg-6 hilidayorweekend mt-4">

                                    </div>
                                </div>
                                <div class="row">
                                </div>
                                <div class="form-group col-lg-12">
                                    <span class="form-label">Special Request</span>
                                    <textarea aria-label="Special Request" type="text" class="form-control" name="special_request" ><?php echo $role['details'];?></textarea>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <input type="hidden" name="submit" value="Add" />
                                            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"   name="submit_value" value="exit"><i class="la la-check"></i>Save & exit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if($_SESSION['config']['show_leave_member']){ ?>
                                <div class="col-lg-4" style="float: left;">
                                    <div class="card card-custom card-stretch gutter-b">
                                        <div class="card-header border-0">
                                            <h3 class="card-title font-weight-bolder text-dark">Who else wil be out</h3>
                                        </div>
                                        <?php foreach ($otheremp as $othemp) {
                                            if($othemp['employee_id'] !=$empl['employee_id']){
                                                ?>
                                                <div class="card-body pt-2">
                                                    <div class="d-flex align-items-center">
                                                        <?php if($empdata[$othemp['employee_id']]['image'] !=''){ ?>
                                                            <div class="symbol symbol-60 symbol-sm flex-shrink-0 ">
                                                                <img class="img-80" src="uploads/all/<?php echo $empdata[$othemp['employee_id']]['image']; ?>" alt="Details">
                                                            </div>
                                                        <?php }else{ ?>
                                                            <div class="mr-3 img-radius p-15">
                                                                <span class="symbol-label font-size-h5 img-80"><?php echo initials($empdata[$othemp['employee_id']]['display_name']); ?></span>
                                                            </div>
                                                        <?php } ?>
                                                        <div class="ml-4">
                                                            <div class="text-dark-75 font-weight-bolder font-size-lg mb-0"><?php echo $empdata[$othemp['employee_id']]['display_name']; ?></div>
                                                            <a href="#" class="text-muted font-weight-bold text-hover-primary"><?php echo date('dS M Y',strtotime($othemp['leave_from'])) ; ?> TO <?php echo date('dS M Y',strtotime($othemp['leave_to'])) ; ?> </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-group col-lg-6" style="float: left;">
                                    <div class="card card-custom">
                                        <div class="card-header">
                                            <div class="card-title">
                                                <h3 class="card-label">My Leaves</h3>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input type="hidden" name="calendar_cat" id="calendar_cat" value="6">
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

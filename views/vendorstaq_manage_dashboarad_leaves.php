<?php
$from_cur_year = date('Y');
$from_cur_month = date('m');
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
     $url = API."?mode=togetretrivemanageremployeeleaves&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&year_month=".$_POST['fromdate']."&emp_id=".$_SESSION['emp_id']."&check=1";
    $data_details = callAPI($url);
    $listdata = json_decode($data_details,1);
    $summarydetails = $listdata['data'];
    $from_cur_year = date('Y', strtotime($_POST['fromdate']));
    $from_cur_month = date('m', strtotime($_POST['fromdate']));

}else{
    $summarydetails = $requestData['leave_details'];
}

?>
<script type="text/javascript">
    function selectanyoneleavedate(val){
        $('#listleave').html('<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner2.gif" style="width: 300px;" alt="Preview"></div>');
        jQuery.ajax({
            url: "views/vendorstaq_manage_dashboarad_leaves.php",
            data:'ajaxpagecall=Y&fromdate='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#listleave').html(data);

            },
            error:function (){}
        });
    }

</script>
<input type="hidden" name="calendar_cat" id="calendar_cat" value="25">
<div class="card card-custom " id="kt_page_sticky_card">
    <div class="card-header table-card-header mb-2">
        <h5><?php echo "Employee Leaves";  ?></h5>
        <div class="card-header-right table-card-header ">

        </div>
    </div>
    <div id='calendar' class="p-2"></div>
</div>


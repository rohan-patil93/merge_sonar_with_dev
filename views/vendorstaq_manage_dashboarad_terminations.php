<?php
$from_cur_year = date('Y');
$from_cur_month = date('m');
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
     $url = API."?mode=togetretrivemanageremployetermination&admin_type=".$_SESSION['admin_type']."&user_type=".$_SESSION['user_type']."&user_email=".$_SESSION['user_email']."&year_month=".$_POST['fromdate']."&emp_id=".$_SESSION['emp_id']."&check=1";
    $data_details = callAPI($url);
    $listdata = json_decode($data_details,1);
    $summarydetails = $listdata['data'];
    $from_cur_year = date('Y', strtotime($_POST['fromdate']));
    $from_cur_month = date('m', strtotime($_POST['fromdate']));

}else{
    $summarydetails = $requestData['termination_details'];
}

?>
<script type="text/javascript">
    function selectanyonterminationsdate(val){
        $('#listtermination').html('<div style="display: block;  margin-left: auto;  margin-right: auto;  width: 40%;"><img src="assets/images/spiner/spiner2.gif" style="width: 300px;" alt="Preview"></div>');
        jQuery.ajax({
            url: "views/vendorstaq_manage_dashboarad_terminations.php",
            data:'ajaxpagecall=Y&fromdate='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                $('#listtermination').html(data);

            },
            error:function (){}
        });
    }

</script>

<div class="card card-custom " id="kt_page_sticky_card">
    <div class="card-header table-card-header mb-2">
        <h5><?php echo "Employee Terminations";  ?></h5>
        <div class="card-header-right table-card-header ">

            <input type="hidden" name="emp_id" value="<?php echo $empp_id; ?>">

            <div class="input-group input-group-button">
                <input aria-label="Fromdate" type="month" name="fromdate" onchange="selectanyonterminationsdate(this.value)" value="<?php echo $from_cur_year."-".$from_cur_month?>" id="example-date-input-from"  class="form-control form-control-sm width1" style="font-size: 14px;height: 40px !important;padding: 10px 15px;">

            </div>

        </div>
    </div>
    <div class="table-responsive custom-scroll" style="height:540px;">
    <table class="table table table-separate table-head-custom table-checkable bordered dataTable px-6" id="kt_datatable1" style="">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Employee  </th>
                <th scope="col">Reason</th>
                <th scope="col">Leaving Date </th>
            </tr>
        </thead>
        <tbody>
            <?php $sno= 0; $checkyy =0;
              if(count((array)$summarydetails)>0){
                foreach($summarydetails as $loc) {

                    $sno++;  $checkyy =1; ?>
                    <tr>
                        <td> <?php echo $sno; ?> </td>
                        <td>

                            <div class="align-middle">
                                <?php if($loc['image']!=""){
                                    $loc_img_src = !empty($loc['image']) ? 'uploads/all/' . $loc['image'] : 'uploads/all/no-image.jpg'; ?>
                                    <img src="<?php echo $loc_img_src; ?>" class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($loc['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h6><?php echo $loc['display_name']; ?></h6>
                                    <span class="form-label label label-primary"><?php echo $loc['emp_code']; ?></span>
                                </div>
                            </div>

                        </td>

                        <td>
                            <?php echo ucwords($loc['termination_reason']); ?>
                        </td>
                        <td>
                            <?php echo fromDate($loc['employee_leaving_date']); ?>
                        </td>

                </tr>

            <?php  }
        }else{ $checkyy =1; ?>
            <tr>
                <td colspan="4">
                    <div class="text-center mt-3">
                               <h6 class="text-center mt-3"> No Data</h6>
                                 <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                             </div>
                </td>
            </tr>
        <?php } ?>
        <?php if( $checkyy ==0){ ?>
            <tr>
                <td colspan="4">
                    <div class="text-center mt-3">
                               <h6 class="text-center mt-3"> No Data</h6>
                                 <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                             </div>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    </div>
</div>


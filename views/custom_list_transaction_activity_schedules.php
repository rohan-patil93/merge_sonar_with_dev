<?php
function paggination($records,$pageno ="",$limit="",$functionname=""){
    $tlrecord = sizeof($records);
    $number_of_page = ceil ($tlrecord / $limit);
    $btnpageno = $pageno;
    $pageno = $pageno*$limit;
    $array=array_slice($records, $pageno, $limit);
    $pageui = "";
    $j=0;
    $pageui .='<tr class="datatable-row"><td colspan="100%"><nav aria-label="Page navigation example" style="float:right"><ul class="pagination my-2 mb-sm-0">';
    for($i = 0; $i< $number_of_page; $i++) {
       $classactive ="";
       if($btnpageno == $i){
               $classactive = "Active";
       }
       $pageui .= '<li class="page-item '.$classactive.'"><a class="page-link" href="'.$functionname.'&page='.$j.'">'.$j.'</a></li>';
  $j++;
}
      $pageui .='</ul></td></td></nav>';
    return array('array'=>$array,'pageui'=>$pageui,'pagenumber'=>$pageno);
}
function cal_percentage($num_amount, $num_total) {
  $count1 = $num_amount / $num_total;
  $count2 = $count1 * 100;
  $count = number_format($count2, 0);
  return $count;
}
$urll = API."?mode=candidateactivityreports";
$response = callAPI($urll);
$result = json_decode($response,1);
$vdata1["field"] = "id,display_name,user_photo";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=application_users&data=".$vdata;
$vdata_details = callAPI($vurl);
$userslist = json_decode($vdata_details,1);
$paegs = paggination($result,$_GET['page'],"10","index.php?option=com_list_transaction_activity_schedules");
?>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
  <style>
        tr{}
        td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
        table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
        table.data-table .custom-check{position: relative;}
        table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
        table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}
        span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
        span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
        table.data-table .dropdown-toggle:after{display: none;}
        table.data-table a.edit{display: inline-block; vertical-align: middle;}
    </style>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="card-label">&nbsp;<?php echo "Candidate Activity List";  ?> </h3>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                            <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                                <i class="ki ki-long-arrow-back icon-xs"></i>
                                Back
                            </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
            <div class="card-body">
    <div class="row">

     <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
         <tr>
                <th scope="col" style="width: 3%;"></th>
                <th scope="col" style="width: 5%;">SL.NO.</th>
                <th scope="col" style="width: 13%;">Image</th>
                <th scope="col" style="width: 10%;">Name</th>
                <th scope="col" style="width: 12%;">Email</th>

                <th scope="col" style="width: 10%;">Contact</th>
                 <th scope="col" style="width: 10%;">Status</th>

            </tr>
            <?php $k=1; foreach($paegs['array'] as  $result){ ?>
            <tr class="main">
                <td>
                    <a href="javascript:void(0)" class="main">
                        <svg xmlns="http:
                          <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                    </a>
                </td>
                <td><?php echo $k; ?></td>
                 <td>
                     <?php if($result['data']['image']!="") { $dispath = "uploads/".$_SESSION['DB_NAME']."/".$_SESSION['company_id']."/candidates/".$result['data']['id']."/".$result['data']['image']; ?>
                      <img src="<?php echo $dispath; ?>" style="width: 50px;border-radius: 50%;" alt="Details" />
                    <?php }else{ ?>
                        <div class="symbol symbol-50 symbol-light mr-2" >
                            <span class="symbol-label" style="border-radius: 50%;"><?php echo initials($result['data']['display_name']); ?>
                            </span>
                          </div>
                   <?php } ?>
                   </td>
                <td ><?php echo  $result['data']['display_name']; ?>    </td>
                <td ><?php echo  $result['data']['email_id']; ?></td>
                <td ><?php echo  $result['data']['phone']; ?></td>
                <td >

<?php
$tskcomplate = 0;
$totaltask= 0;
foreach($result['checklist'] as $dataaa){
    $tasssk_list = json_decode($dataaa['tran_check_list'],1);
    foreach($tasssk_list as $tasks){
    $totaltask++;
       if($tasks['check_value']!=""){
        $tskcomplate++;
    }
}
}
$percentage = cal_percentage($tskcomplate,$totaltask);
?>

                    <div class="d-flex flex-column w-100 mr-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted mr-2 font-size-sm font-weight-bold"><?php echo $percentage!="nan"?$percentage:"0"; ?>%</span>
                        <span class="text-muted font-size-sm font-weight-bold">Progress</span>
                    </div>
                    <div class="progress progress-xs w-100">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $percentage!="nan"?$percentage:"0"; ?>%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div></td>

            </tr>
            <tr class="data">
                <td colspan="7">
                    <table class="mb-3" style="width: 100%">
                        <tr>
                            <th scope="col" style="width: 1%;"></th>
                            <th scope="col" style="width: 3%;">SL.NO</th>
                            <th scope="col" style="width: 7%;">Task</th>
                            <th scope="col" style="width: 10%;">Date</th>
                            <th scope="col" style="width: 10%;">Assign To</th>
                            <th scope="col" style="width: 10%;">Status</th>
                        </tr>
                        <?php $l=1; foreach($result['checklist'] as $dataa){  $task_list = json_decode($dataa['tran_check_list'],1); ?>
                        <tr>
                            <td></td>
                            <td><?php  echo $l; ?></td>
                            <td><?php echo $dataa['task_name']; ?></td>
                            <td ><?php echo explode(" ",$dataa['tran_revised_action_date'])[0]; ?></td>
                            <td >
<?php     $user = filterArrayValue($userslist,'id',$dataa['assigned_to']);
if($user[0]['user_photo']!="") {
     ?>
                      <img src="<?php echo "uploads/all/".$user[0]['user_photo'];?>" style="width: 50px;border-radius: 50%;" alt="User Profile" />
                    <?php }else{ ?>
                        <div class="symbol symbol-50 symbol-light mr-2" >
                            <span class="symbol-label" style="border-radius: 50%;"><?php echo initials($user[0]['display_name']); ?>
                            </span>
                          </div>
                   <?php }

echo $user[0]['display_name']; ?>

                            </td>
                           <td>

                               <?php
                                   foreach($task_list as $task){
                                   if($task['check_value']!=""){
                                    echo '<span class="label label-success  label-pill label-inline mr-2 mt-2" style="width: max-content"><i class="icon-sm far fa-check-square text-white"></i>  &nbsp;'.$task['name'].'</span>';
                                }else{
                                    echo '<span class="label label-warning label-pill label-inline mr-2 mt-2" style="width: max-content"><i class="icon-sm far fa-square text-white"></i>  &nbsp;'.$task['name'].'</span>';
                                }
                                   }
?>
                          </td>

                        </tr>
                        <?php $l++;
} ?>
                    </table>
                </td>
            </tr>
            <?php $k++;
} ?>
        </table>
        <?php echo $paegs['pageui']; ?>
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>

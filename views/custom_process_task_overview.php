<?php

$table = "candidates";
$vurl = API."?mode=processtaskschecduleoverview&table_name=".$table.'&admintype='.$_SESSION['admin_type'];
$configesponse = callAPI($vurl);
$tasklist = json_decode($configesponse,1);
$cntcomplte = 0;
$yasttostart = 0;
$cntinproress = 0;
foreach($tasklist as $row){
    $proceslist = json_decode($row['tran_check_list'],1);
    $complete =  0;
    foreach($proceslist as $fef){
        if($fef['check_value']==""){
            $complete = $complete+1;
        }
    }
    if(count((array)$proceslist) == $complete){
            $yasttostart = $yasttostart+1;
    }elseif($complete == '0'){
        $cntcomplte = $cntcomplte+1;
    }else{
        $cntinproress = $cntinproress+1;
    }
}
function cal_percentage($num_amount, $num_total) {
    $count = 0;
    if($num_amount != "" && $num_amount != 0 && $num_total != "" && $num_total != 0){
      $count1 = $num_amount / $num_total;
      $count2 = $count1 * 100;
      $count = number_format($count2, 0);
    }
      return $count;
}

?>
<script type="text/javascript">
    function displaystausdrop(id,table,status_id){
   $.ajax({
   url: "ajax.php",
   data:'page=current_status_drop&id='+id+'&current_status='+status_id+'&table='+table,
   type: "POST",
   success:function(data){
     console.log(data);
     $("#dropdownlist").html(data);
   },
   error:function (){}
   });

   }

   function statuspopup(status,id){
   var table_name = 'vendor_registers';
   jQuery.ajax({
   url: "views/custom_insert_status_popup.php",
   data:'table='+table_name+'&status='+status+'&rid='+id,
   type: "POST",
   success:function(data){
   $('#popup1').html(data);
   $('#kt_modal_KTDatatable_remote').modal({ show: true});
   },
   error:function (){}
   });
   }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
                    <div class="card card-custom " id="kt_page_sticky_card">

                        <div class="card-header">
    <h5>Process Task Overview</h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>
                        <div class="card-body mt-2">
                            <div class=" card-custom gutter-b card-stretch">
                                <div class=" d-flex flex-column">
                                    <div class="row col-lg-12">
                                        <div class="col-lg-9">
<div class="row col-lg-12">
        <button type="button" class="btn circle-circle-xl border-primary m-l-50 text-dark">
            <h3 class="text-center font-size-h3"><?php echo count((array)$tasklist); ?></h3>
            <div class="text-center font-size-h6"><?php echo "Total Tasks";?></div>
        </button>
        <button type="button" class="btn circle-circle-xl border-secondary  m-l-10 text-dark ">
            <h3 class="text-center font-size-h3"><?php echo $cntcomplte; ?></h3>
            <div class="text-center font-size-h6"><?php echo "Completed";?></div>
        </button>
        <button type="button" class="btn circle-circle-xl border-success   m-l-10 text-dark">
            <h3 class="text-center font-size-h3"><?php echo $cntinproress; ?></h3>
            <div class="text-center font-size-h6"><?php echo "InProgress";?></div>
        </button>
        <button type="button" class="btn circle-circle-xl border-warning   m-l-10 text-dark">
            <h3 class="text-center font-size-h3"><?php echo $yasttostart; ?></h3>
            <div class="text-center font-size-h6"><?php echo "Yet to start";?></div>
        </button>
</div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 dt-responsive table-responsive mt-5 p-3">

                                    <table class="table table-separate table-head-custom table-checkable bordered dataTable" id="kt_datatable1">

                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 20%;">Task Name </th>
                                                    <th scope="col">Created Date </th>
                                                    <th scope="col">Expiry Date </th>
                                                    <th scope="col">Check List </th>

                                                    <th scope="col">Progress</th><th scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serialno = 0;
                                                foreach ($tasklist as $key => $value) {
    $proceslist = json_decode($value['tran_check_list'],1);

                                                    $serialno++;     ?>
                                                    <tr>
                                                        <td  > <?php echo $serialno; ?> </td>
                                                        <td style="width: 20%;"> <?php echo ucwords($value['task_name']); ?> </td>

<td> <?php $datere = explode(" ",$value['tran_action_date'])[0]; echo $datere; ?> </td>

<td> <?php $datere = explode(" ",$value['tran_revised_action_date'])[0]; echo $datere; ?> </td>

<td> <?php echo count((array)$proceslist); ?> </td>
<td><?php
$tskcomplate = 0;
$totaltask= 0;
foreach($proceslist as $tasks){
        $totaltask++;
       if($tasks['check_value']!=""){
        $tskcomplate++;
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
        </div>
</td>

<td>
    <a class="btn btn-success" href="index.php?option=COM_INSERT_TRANSACTION_ACTIVITY_SCHEDULE&Rid=<?php echo deocdeId("id:".$value['id']); ?>">  View   </a>
</td>
                                                    </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>


                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


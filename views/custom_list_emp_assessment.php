<?php
if ($_GET['CAT']=="" && $_GET['option']="COM_LIST_EMP_ASSESSMENT") {
      $_GET['CAT'] =1;
   }
$category = array('1' =>'Assessment');
$display_tag = $category[$_GET['CAT']];
$table_list = array(TBL_ASSESSMENT_SETUP,TBL_ASSESSMENT_SETUP_QUESTIONS,TBL_ASSESSMENT_SETUP_QUESTION_OPTIONS);
$vurl = API."?mode=getempassessment&company_id=".$_SESSION['company_id']."&is_active=0&emp_id=".$_SESSION['emp_id']."&category=".$_GET['CAT'];
$configesponse = callAPI($vurl);
$assessmentData = json_decode($configesponse,1);

$assessment_details = $assessmentData['summary'];
$assessment_data = $assessmentData['assessment_details'];
$assessment_qtypes = implode('|',(array)$assessmentData['assessment_qtypes']);
$assessment_qtype_names = implode('|',array_keys((array)$assessmentData['assessment_qtypes']));
$stausresult = ViewStatusData();

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
                            <div class="card-title">
                                <h3 class="card-label">&nbsp;<?php echo $display_tag; ?> Overview  </h3>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class=" card-custom gutter-b card-stretch">
                                <div class=" d-flex flex-column">
                                    <div class="row col-lg-12">
                                        <div class="col-lg-9">
                                            <div class="row col-lg-12">
                                                <?php foreach($assessment_details as $assessment) { ?>
                                                    <button type="button" class="btn circle-circle-xl border-<?php echo $assessment['bgcolor']; ?> ml-4 "><div class="text-center font-size-h3"><?php echo $assessment['data']; ?></div> <div class="text-center font-size-h6"><?php echo $assessment['name'];?></div></button>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="card-body d-flex flex-column">
                                                <div class="d-flex align-items-center justify-content-between flex-grow-1">
                                                    <div class="mr-2">
                                                        <h6><?php echo $display_tag; ?> Completion Rate</h6>
                                                    </div>
                                                </div>
                                                <div class="pt-8">
                                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                                        <div class="text-muted font-weight-bold mr-2">Average</div>
                                                        <div class="text-muted font-weight-bold"><?php echo $assessmentData['rate']; ?></div>
                                                    </div>
                                                    <div class="progress bg-light-warning progress-xs">
                                                        <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $assessmentData['rate']; ?>%;" aria-valuenow="60" aria-valuemin="60" aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($assessment_data)) { ?>
                                <div class="row">
                            <div class="col-lg-8 form-group dataTables_wrapper ">

                                    <table class="table table-separate table-head-custom table-checkable bordered dataTable" id="kt_datatable1">

                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 20%;">Name </th>
                                                    <th scope="col">Created Date </th>
                                                    <th scope="col">Expiry Date </th>
                                                    <th scope="col">Questions </th>
                                                    <th scope="col">Score</th>
                                                    <th scope="col">Status </th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $serialno = 0;
                                                foreach ($assessment_data as $empkey => $empvalue) {
                                                        $current_status =  $empvalue['current_status']!=""?$empvalue['current_status']:"0";

                                                    $assessment_id = deocdeId("id:".$empvalue['assessment_id']);
                                                    $serialno++;     ?>
                                                    <tr>
                                                        <td style="width: 3%;"> <?php echo $serialno; ?> </td>
                                                        <td style="width: 40%;"> <?php echo $empvalue['assessment_name']; ?> </td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['assessment_start_date']; ?> </td>
                                                        <td style="width: 15%;"> <?php echo  $empvalue['assessment_end_date']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['totalquestion']; ?> </td>
                                                        <td class="text-right" style="width: 14%;"> <?php echo $empvalue['total_score']; ?> </td>

                                                        <td style="width: 14%;">
                                                            <button class="btn btn-primary" style="color: <?php echo "#". $stausresult[$current_status]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$current_status]['background_color'] ); ?>; border-color: <?php echo "#".strval($stausresult[$current_status]['background_color']); ?>;" type="button" ><?php echo $stausresult[$current_status]['name']; ?> </button>
                                                        </td>

                                                    <td class="text-right" style="width: 14%;">
                                                        <?php if($empvalue['completionrate']== ST_INACTIVE) { ?>
                                                            <button type="button" class="btn btn-<?php echo $empvalue['completionratecolor']; ?>">Expired</button>
                                                        <?php }
                                                        elseif($empvalue['completionrate']== ST_COMPLETE) {
                                                        $emp_id = deocdeId("emp_id:".$_SESSION['emp_id']);
                                                         ?>
                                                            <a class="btn btn-<?php echo $empvalue['completionratecolor']; ?>" href="index.php?option=COM_VIEW_ASSESSMENT_RESULT&Pid=<?php echo $assessment_id;?>&Eid=<?php echo $emp_id;?>&CAT=<?php echo $_GET['CAT'];?>">  View   </a>
                                                        <?php }
                                                        else { ?>
                                                            <a class="btn btn-<?php echo $empvalue['completionratecolor']; ?>" href="index.php?option=COM_INSERT_ASSESSMENT_RESPONSE&Rid=<?php echo $assessment_id;?>&CAT=<?php echo $_GET['CAT'];?>">Start  </a>
                                                        <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>

                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="form-group col-lg-4" >
                                        <input type="hidden" id="assessment_qtypes" value="<?php echo $assessment_qtypes; ?>">
                                        <input type="hidden" id="assessment_qtype_names" value="<?php echo $assessment_qtype_names; ?>">
                                        <div id="assessment_emp_chart" class="d-flex justify-content-center"></div>
                                        <h3 class="font-weight-bolder text-center"> <?php echo $display_tag; ?> Types</h3>
                                    </div>
                                <?php }else{ ?>

<div class="card-px text-center py-20 m-t-30">
                                                <p class="text-muted f-16 fw-bold m-b-30"><i class='ti-alert text-warning'></i> No Assessments </p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                                </div>
                                            </div>

                            <?php    } ?>
                            </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


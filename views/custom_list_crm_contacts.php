<?php
if($_POST['getlistpage']=="Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
}
$from_date = $_POST['from_date'] !=""?$_POST['from_date']:date("Y-m-01");
$to_date =$_POST['to_date'] !=""?$_POST['to_date']:date("Y-m-t");
$transaction_table =$_POST['transaction_table'];
$menu = API."?mode=getcrmleads&list=Y&transaction_table_list=".$transaction_table."&from_date=".$from_date."&to_date=".$to_date."&admin_type=".$_SESSION['admin_type'];
$page_list = callAPI($menu);
$result = json_decode($page_list,1);
$crm_contacts = $result['crm_contacts'];
$crm_histories =$result['crm_histories'];
foreach($crm_histories as $crmhistry){
    $lastactivity[$crmhistry['transaction_table']][$crmhistry['transaction_id']]=$crmhistry;
}
$crm_general_codes = $result['crm_general_codes'];
$crm_source_types = array();
foreach($crm_general_codes as $key=>$crmsource1){
    foreach($crmsource1 as $crmsource){
        $crm_source_types[$key][$crmsource['value']] = $crmsource['label'];
    }
}
$project_task_status = $result['project_task_status'];
//Start graph crm leads
$crm_contacts_graph = $result['crm_contacts_graph'];
foreach($crm_contacts_graph as $crmstat){
    $crm_contacts_chart[] = array($project_task_status['crm_contacts'][$crmstat['crm_status']]['name'],(int)$crmstat['cnt']);
}
$feasibility_requirements = $result['feasibility_requirements'];
//End graph crm leads
?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Contacts <small class="f-12 text-info">(<?php echo fromDate($from_date)." To ".fromDate($to_date); ?>)</small></h5>
                <div class="card-header-right">
                    <button type="button" class="btn btn-success mb-2 desktop-view leadsidebar "  onclick="leadPageCall('CONTACT')"><i class="icofont icofont-plus icon-white "></i> Contact </button>
                </div>
            </div>
            <div class="card-body">
                <?php if(count((array)$crm_contacts) !=0){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                    <thead>
                        <tr><th scope="col">Name</th>
                            <th scope="col">Number</th>
                            <th scope="col">Originator</th>

                            <th scope="col">Last Action</th>
                            <th scope="col">Feasibility Check</th>
                            <th scope="col">Feasibility Stause</th>

                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($crm_contacts as $crmleads){
                            $crmld = $lastactivity['crm_contacts'][$crmleads['id']];
                            $feasibility_requirement = $feasibility_requirements[$crmleads['id']];
                            ?>
                        <tr>
                            <td  class="p-2">
                                <div class="align-middle">
                                  <?php if($crmleads['image']) { ?>
                                      <img src="uploads/all/<?php echo $crmleads['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                  <?php } else { ?>
                                      <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($crmleads['display_name']); ?></span>
                                  <?php } ?>
                                  <div class="d-inline-block width73">
                                      <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $crmleads['id']; ?>')"><h6><?php echo $crmleads['display_name']; ?></h6></button>
                                      <p class="text-muted m-b-0"><?php echo $crmleads['phone']; ?></p>
                                  </div>
                              </div>

                            </td>
                            <td  class="p-2"><?php echo $crmleads['phone']; ?></td>
                            <td  class="p-2"><?php echo $_SESSION['users'][$crmleads['originator']]; ?></td>

                            <td class="w-b-w-s p-2"><?php echo  crmLastActivity($crmld,$crmleads['created_date']); ?></td>
                            <td  class="p-2"><?php echo $crmleads['feasibility_check'] =="Y"?"Yes":"-"; ?></td>
                            <td  class="p-2">
                                <?php
                                if($crmleads['feasibility_check'] =="Y"){
                                    if($feasibility_requirement['current_status'] =="199"){
                                        if($feasibility_requirement['sales_order_id'] !=""){
                                            echo "Closed/SO Created";
                                        }else{
                                            echo "Closed/SO Pending";
                                        }
                                    }else{
                                        echo "Open";
                                    }
                                }else{
                                    echo "-";
                                  }  ?>
                                </td>

                            <td  class="p-2"><?php echo $project_task_status['crm_contacts'][$crmleads['crm_status']]['name']; ?></td>

                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            <?php }else{ ?>
                <div class="card-px text-center ">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Contacts</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Status</h5>
                <div class="card-header-right"></div>
            </div>
            <div class="card-body">
                <input type="hidden" name="chart_data_contact_chart2" id="chart_data_contact_chart2" value='<?php echo json_encode((array)$crm_contacts_chart); ?>' >

            </div>
        </div>
        <ul class="nav md-tabs infisuite_tab mt-2 mb-2">
          <?php $statseq = 1; foreach($crm_contacts_graph as $allstatuswithlist){ ?>
            <li class="nav-item">
                <a class="nav-link f-w-600 f-12 <?php echo $statseq ==1?'active':''; ?>" data-bs-toggle="tab" href="#statuswithlist_<?php echo $statseq; ?>" role="tab" aria-selected="true"><?php echo $project_task_status['crm_contacts'][$allstatuswithlist['crm_status']]['name']; ?><span class="form-label label label-xs label-success m-l-10"><?php echo $allstatuswithlist['cnt']; ?></span></a>
            </li>
          <?php $statseq++;
} ?>
        </ul>
        <div class="tab-content tabs ">
          <?php $statseq = 1; foreach($crm_contacts_graph as $allstatuswithlist){ ?>
            <div class="tab-pane card-body <?php echo $statseq ==1?'active':''; ?> p-0" id="statuswithlist_<?php echo $statseq; ?>" role="tabpanel">
                <div class="card mb-2">
                    <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                      <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                        <thead>
                          <tr>
                            <th scope="col">Name</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                          $allreocrdconverttoarray = explode(",",$allstatuswithlist['group_values']);
                          foreach($allreocrdconverttoarray as $crmleadsval){
                             $reocrdarray = explode("|",$crmleadsval);
                          ?>
                            <tr>
                               <td  class="p-2">
                                <div class="align-middle">
                                  <?php  if($reocrdarray['3']) { ?>
                                      <img src="uploads/all/<?php echo $reocrdarray['3']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                  <?php } else { ?>
                                      <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($reocrdarray['1']); ?></span>
                                  <?php } ?>
                                  <div class="d-inline-block width73">
                                      <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('LEAD','<?php echo $reocrdarray['0']; ?>')"><h6><?php echo $reocrdarray['1']; ?></h6></button>
                                      <p class="text-muted m-b-0"><?php echo $reocrdarray['2']!=""?$reocrdarray['2']:"-"; ?></p>
                                  </div>
                                  </div>
                              </td>
                            </tr>
                          <?php } ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
            <?php $statseq++;
} ?>
        </div>
    </div>
</div>
<!-- jquery CRM js -->
<script type="text/javascript" src="assets/js/lead_script.js"></script>
<!-- jquery CRM js -->
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js" integrity="sha384-Q4nTc23a1YNtnl17XDjJkYn/j5Ksb7rsGG1NTcIxbz6sTGfGXZJ8WdvzALeeuafr" crossorigin="anonymous"></script>
<script type="text/javascript" src="assets/js/custom_view_crm_dashboards.js"></script>

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
$crm_projects = $result['crm_projects'];
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

?>  <div class="card">
    <div class="card-header">
        <h5>Projects <small class="f-12 text-info">(<?php echo fromDate($from_date)." To ".fromDate($to_date); ?>)</small></h5>
        <div class="card-header-right">
            <button type="button" class="btn btn-success mb-2 desktop-view leadsidebar "  onclick="leadPageCall('PROJECT')"><i class="icofont icofont-plus icon-white "></i> Project </button>
        </div>
    </div>
    <div class="card-body">
        <?php if(count((array)$crm_projects) !=0){ ?>
            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                <thead>
                    <tr><th scope="col">Project Name</th>
                        <th scope="col">Originator</th>
                        <th scope="col">Project Start</th>
                        <th scope="col">Project End</th>
                        <th scope="col">Total Budget</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($crm_projects as $prjct){
                        ?>
                    <tr>
                        <td  class="p-2">
                            <div class="align-middle">
                              <?php if($prjct['project_logo']) { ?>
                                  <img src="uploads/all/<?php echo $prjct['project_logo']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Logo">
                              <?php } else { ?>
                                  <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($prjct['project_name']); ?></span>
                              <?php } ?>
                              <div class="d-inline-block width73">
                                  <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('PROJECT','<?php echo $prjct['id']; ?>')"><h6><?php echo $prjct['project_name']; ?></h6></button>
                                </div>
                          </div>

                        </td>
                        <td  class="p-2"><?php echo $_SESSION['users'][$prjct['assigner_id']]; ?></td>
                         <td  class="p-2"><?php echo fromDate($prjct['planned_start']); ?></td>
                        <td  class="p-2"><?php echo fromDate($prjct['planned_finish']); ?></td>
                        <td  class="p-2"><?php echo $prjct['project_cost']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <div class="card-px text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Projects</p>
                <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!-- jquery CRM js -->
<script type="text/javascript" src="assets/js/lead_script.js"></script>
<!-- jquery CRM js -->
<script src="assets/js/jquery.mCustomScrollbar.concat.min.js"></script>

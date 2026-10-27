<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
    $listcompliancesurl = API."?mode=getcompliancesdashbord&type=popup&id=".$_POST['id']."&date=".$_POST['date'];
    $listcompliancesres = callAPI($listcompliancesurl);
    $listcompliances = json_decode($listcompliancesres, 1);
    $compliancetaskhistory = $listcompliances['compliance_task_histories'];
$alldone = 0;
foreach($compliancetaskhistory as $list_compl){
    if($list_compl['current_status']=="199"){
        $alldone++;
    }
}

?>
<div class="modal-header">
    <h4 class="modal-title">View Employee details</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body" >

<div class="form-group row ml-20 mr-20">
    <div class="form-group col-lg-4 text-center">
        <span class="f-w-700">Task Name</span></br>
        <span><?php echo $compliancetaskhistory[0]['name']; ?></span>
    </div>
    <div class="form-group col-lg-4 text-center">
        <span  class="f-w-700">Date</span></br>
        <span><?php echo $_POST['date']; ?></span>
    </div>
    <div class="form-group col-lg-4 text-center">
        <span  class="f-w-700">Status</span></br>
        <span><?php echo (count((array)$compliancetaskhistory) == $alldone)?'Completed':'Pending'; ?></span>
    </div>
</div>

<div class="dt-responsive table-responsive">
    <table id="new-cons" class="table table-striped table-bordered nowrap">
        <thead>
            <tr><th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Task</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach($compliancetaskhistory as $list_compliance){
            $check_list = json_decode($list_compliance['check_list'], 1);
            $taskstatus = 0;
            ?>
                    <tr>
                    <td><?php echo $i; ?></td>
                        <td><?php echo $list_compliance['display_name']; ?></td>
                        <td><?php foreach($check_list as $list){ if($list['check_value']!=""){ $taskstatus++;
} ?>
                            <div class="label-main">
                                <span class="form-label label label-primary"><?php echo $list['name'];?> <?php echo $list['check_value']!=""?'<i class="fa fa-thumbs-up "></i>':'<i class="fa fa-hourglass-2"></i>'; ?></span>
                            </div>
                        <?php }  ?>
                    </td>
                    <td>
                    <?php echo count((array)$check_list) == $taskstatus?'<span class="form-label label label-success">Completed</span>':'<span class="form-label label label-warning">Pending</span>'; ?>
                    </td>
                </tr>
            <?php $i++;
} ?>

            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
</div>
    <!-- data-table js -->
    <script src="assets/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/js/pdfmake.min.js"></script>
    <script src="assets/pages/data-table/js/vfs_fonts.js"></script>

    <script src="assets/pages/data-table/extensions/buttons/js/dataTables.buttons.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.flash.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/jszip.min.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/vfs_fonts.js"></script>
    <script src="assets/pages/data-table/extensions/buttons/js/buttons.colVis.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="assets/bower_components/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="assets/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="assets/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

<!-- nw adde table -->
<script src="assets/pages/data-table/extensions/responsive/js/responsive-custom.js"></script>

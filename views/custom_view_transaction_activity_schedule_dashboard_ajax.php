<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$ajax_transaction_table = $_POST['transaction_table'];
$url = API."?mode=gettransactionactivityschedulesdashboartd&transaction_table=".$ajax_transaction_table;
$response = callAPI($url);
$result = json_decode($response,1);
$transactions = $result['transactions'];
$transaction_activity_schedule = $result['transaction_activity_schedule'];
?>
<script type="text/javascript">
    function getTaskPerticulerUser(transaction_table,transaction_id){

        $("#listdivid").removeClass("col-lg-12");
        $("#resultdivid").removeClass("col-lg-12");
        $("#listdivid").addClass("col-lg-2");
        $("#resultdivid").addClass("col-lg-10");
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
        $("#resultdivid").html(loader);
        $.ajax({
            url: "views/custom_insert_transaction_activity_schedules.php",
            data:'ajaxpage=Y&transaction_table='+transaction_table+'&transaction_id='+transaction_id,
            type: "POST",
            success:function(data){
                $('#resultdivid').html(data);
                $('[data-bs-toggle="tooltip"]').tooltip();
            },
            error:function (){}
        });
    }
</script>
<div class="row">
    <div class="col-lg-12" id="listdivid">
        <div class="card" >
            <div class="card-header">
                <h5><?php echo ucwords(str_replace("_"," ",($ajax_transaction_table))) ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 ">
                    </div>
                    <div class="col-md-4">
                        <div class="input-group input-group-button mb-0">
                            <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="Enter Name" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Enter Name" aria-label="Enter Name">
                            <span class="input-group-text addon-style">
                                <span class=""><span class="ti-search text-white mt-2"></span></span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover m-b-0" style="font-size:13px;" >
                        <thead>
                            <tr><th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col" class="text-right">Total Task</th>
                                <th scope="col" class="text-right">Yet To Start</th>
                                <th scope="col" class="text-right">Review Pending</th>
                                <th scope="col" class="text-right">In-Progress</th>
                                <th scope="col" class="text-right">Approved</th>
                                <th scope="col">Progress</th>
                            </tr>
                        </thead>
                        <tbody id="categoryfilter">
                            <?php $i=1; foreach($transactions as $tansres){
                                $transaction_activity = $transaction_activity_schedule[$tansres['id']];
                                ?>
                            <tr onclick="getTaskPerticulerUser('<?php echo $ajax_transaction_table; ?>','<?php echo $tansres['id']; ?>')" class="cursor-pointer" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                                <td class="p-2"><?php echo $i; ?></td>
                                <td class="p-2 f-w-600"><?php echo ucwords($tansres['display_name']); ?></td>
                                <td class="text-right p-2"><?php echo customNanAndEmptyValidation($transaction_activity['total_task_cnt']); ?></td>
                                <td class="text-right p-2"><?php echo customNanAndEmptyValidation($transaction_activity['yet_to_start_cnt']); ?></td>
                                <td class="text-right p-2"><?php echo customNanAndEmptyValidation($transaction_activity['review_cnt']); ?></td>
                                <td class="text-right p-2"><?php echo customNanAndEmptyValidation($transaction_activity['inprogress_cnt']); ?></td>
                                <td class="text-right p-2 "><?php echo customNanAndEmptyValidation($transaction_activity['approve_cnt']); ?></td>
                                <td class="p-2">
                                    <?php
                                        $complettaskper = $transaction_activity['total_task_cnt'] !=0?($transaction_activity['approve_cnt']/$transaction_activity['total_task_cnt'])*100:0;
                                    ?>
                                    <div class="row align-items-center m-b-5">
                                        <div class="col-auto">
                                            <p class="m-b-0">Completion</p>
                                        </div>
                                        <div class="col text-end">
                                            <p class="m-b-0 text-c-green"><?php echo round($complettaskper); ?>%</p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 0.4rem;">
                                        <div class="progress-bar bg-c-green" style="width:<?php echo $complettaskper; ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-12" id="resultdivid" style="zoom: 0.9;">
    </div>
</div>

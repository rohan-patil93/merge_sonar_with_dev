<?php
if($dashboardcall != 'Y'){
    $url1 = API."?mode=getexpensepettycash&admin_type=".$_SESSION['admin_type']."&startdate=".$startdate."&enddate=".$enddate."&petty_cash_box_id=".$petty_cash_box_id;
    $resultapi = callAPI($url1);
    $result = json_decode($resultapi,1);
    $petty_cashres = $result;
}else{
    $petty_cashres = $result['expense'];
}

$petty_cash = $petty_cashres['petty_cash'];
$status_progress = $petty_cashres['status_progress'];
$statuse = ViewStatusData();
$topuprequest  = array();
foreach($petty_cash as $pcbtr){
    if($pcbtr['current_status'] == ST_ACTIVE){
        $topuprequest[ST_ACTIVE][] = $pcbtr;
    }elseif($pcbtr['current_status'] == ST_APPROVED){
        $topuprequest[ST_APPROVED][] = $pcbtr;
    }else{
        $topuprequest['inprocess'][] = $pcbtr;
    }
}
?>
<div class="card">
    <div class="card-header  row">
        <div class="col-lg-2">
            <h5>Expense</h5>
        </div>
        <div class="col-lg-10 text-right">
            <a href="index.php?option=COM_INSERT_PETTY_CASH" class="btn btn-success mb-2 desktop-view mt-2"  target="_blank"> <i class="icofont icofont-plus icon-white "></i> Expense</a>
            <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3 float-right">
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#expense1" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Active Expenses <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$topuprequest[ST_ACTIVE]);?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expense2" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Inprocess Expenses<span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$topuprequest['inprocess']);?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expense3" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> Approved Expenses <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$topuprequest[ST_APPROVED]);?></span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body custom-scroll" id="biilingrent" >
        <div class="tab-content tabs">
            <div class="tab-pane card-body active p-0" id="expense1" role="tabpanel">
                <?php if(count((array)$topuprequest[ST_ACTIVE]) !=0){ ?>
                    <table class="table table-striped table-bordered nowrap table-hover" style="font-size:13px;" id="basic-btn3">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Transaction No</th>
                                <th scope="col">Requester Name</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col">Due Date</th>
                                <th scope="col" class="text-right">Amount</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $m=1; foreach($topuprequest[ST_ACTIVE] as $ptb){ ?>
                                <tr>
                                    <td  class="p-2"><?php echo $m; ?></td>
                                    <td class="p-2"><?php echo $ptb['transaction_number']; ?></td>
                                    <td class="p-2"><?php echo $_SESSION['users'][$ptb['originator']]; ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['transaction_date']); ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['due_date']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['total_amount']); ?></td>

                                    <td class="p-2 text-center">
                                        <a href="index.php?option=COM_INSERT_PETTY_CASH&Rid=<?php echo deocdeId("id:".$ptb['id']);?>" target="_blank" class="btn btn-sm btn-clean btn-icon btn-icon-sm m-l-5" data-bs-toggle="tooltip" data-bs-original-title="Edit Details"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                                <?php  $m++;
} ?>
                            </tbody>
                        </table>
                    <?php }else{?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Expense</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="tab-pane card-body p-0" id="expense2" role="tabpanel">
                <?php if(count((array)$topuprequest['inprocess']) !=0){ ?>
                    <table class="table table-striped table-bordered nowrap table-hover" style="font-size:13px;" id="basic-btn4">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Transaction No</th>
                                <th scope="col">Requester Name</th>
                                <th scope="col">Transaction Date</th>
                                <th scope="col">Due Date</th>
                                <th scope="col" class="text-right">Amount</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $m=1; foreach($topuprequest['inprocess'] as $ptb){  ?>
                            <tr>
                                    <td  class="p-2"><?php echo $m; ?></td>
                                    <td class="p-2"><?php echo $ptb['transaction_number']; ?></td>
                                    <td class="p-2"><?php echo $_SESSION['users'][$ptb['originator']]; ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['transaction_date']); ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['due_date']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['total_amount']); ?></td>

                                    <td class="p-2 text-center">
                                        <a href="index.php?option=COM_INSERT_PETTY_CASH&Rid=<?php echo deocdeId("id:".$ptb['id']);?>" target="_blank" class="btn btn-sm btn-clean btn-icon btn-icon-sm m-l-5" data-bs-toggle="tooltip" data-bs-original-title="Edit Details"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            <?php  $m++;
} ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Expense</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="tab-pane card-body p-0" id="expense3" role="tabpanel">
                <?php if(count((array)$topuprequest[ST_APPROVED]) !=0){ ?>
                <table class="table table-striped table-bordered nowrap table-hover" style="font-size:13px;" id="basic-btn5">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Transaction No</th>
                            <th scope="col">Requester Name</th>
                            <th scope="col">Transaction Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col" class="text-right">Amount</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $m=1; foreach($topuprequest[ST_APPROVED] as $ptb){  ?>
                            <tr>
                                    <td  class="p-2"><?php echo $m; ?></td>
                                    <td class="p-2"><?php echo $ptb['transaction_number']; ?></td>
                                    <td class="p-2"><?php echo $_SESSION['users'][$ptb['originator']]; ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['transaction_date']); ?></td>
                                    <td class="p-2"><?php echo fromDate($ptb['due_date']); ?></td>
                                    <td class="p-2 text-right"><?php echo customeummarmonyformate($ptb['total_amount']); ?></td>

                                    <td class="p-2 text-center">
                                        <a href="index.php?option=COM_INSERT_PETTY_CASH&Rid=<?php echo deocdeId("id:".$ptb['id']);?>" target="_blank" class="btn btn-sm btn-clean btn-icon btn-icon-sm m-l-5" data-bs-toggle="tooltip" data-bs-original-title="Edit Details"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            <?php  $m++;
} ?>
                        </tbody>
                    </table>
                <?php }else{ ?>
                    <div class="card-px text-center py-20 m-t-30">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Expense</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

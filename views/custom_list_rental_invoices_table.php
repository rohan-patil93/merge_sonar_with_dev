<?php
$_SESSION['apply_js']['table']= 1;
if((count((array)$admindashboardd['purchase_invoice_list'])!='0' || !isset($admindashboardd['purchase_invoice_list'])) && $_GET['option'] != ""){
    $admindashboardurl = API."?mode=getrentaladmindashboard&vend_limit=3&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
    $admindashboardres = callAPI($admindashboardurl);
    $admindashboardd = json_decode($admindashboardres,1);
    $customers = $admindashboardd['customers'];
    $approvedinvoices = $unapprovedinvoices = array();
    foreach($admindashboardd['rental_invoices_list'] as $pending_list){
        if($pending_list['pending_amount'] <='0'){
            $paidinvoicse[] = $pending_list;
        }elseif($pending_list['shared']=='0'){
            $submittedinvoice[] = $pending_list;
        }elseif($pending_list['generated_invoice']=='0'){
            $generatedinvoice[] = $pending_list;
        }
    }
}
?>
<ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12 <?php echo $_GET['type']==""?"active":"";?>" data-bs-toggle="tab" href="#generatedinvoice" role="tab" aria-selected="true"> <i class="fa fa-check-circle f-20"></i>&nbsp; Generated Invoices <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$generatedinvoice); ?></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#submittedinvoice" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-envelope f-20"></i>&nbsp; Submitted Invoices <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$submittedinvoice); ?></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12 <?php echo $_GET['type']=="P"?"active":"";?>" data-bs-toggle="tab" href="#paidinvoicse" role="tab" aria-selected="true"> <i class="fa fa-shopping-basket f-20"></i>&nbsp; Paid Invoices<span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$paidinvoicse); ?></span></a>
    </li>
</ul>
<div class="tab-content tabs">
    <div class="tab-pane card-body <?php echo $_GET['type']==""?"active":"";?> p-0" id="generatedinvoice" role="tabpanel">
        <div class="card table-card">
            <div class="card-header">
                <h5>Generated Rental Invoices</h5>
            </div>
            <div class="card-body">
                  <div class="card-block table-responsive" >
                    <table id="basic-btn"  class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tenant</th>
                                <th scope="col">Invoices Number</th>
                                <th scope="col">Generated Date</th>
                                <th scope="col" class="text-right">Price</th>

                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $mq = 1; if(count((array)$generatedinvoice)!='0'){ foreach($generatedinvoice as $pending_list){
                                $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                ?>
                                <tr>
                                    <td><?php echo $mq; ?></td>
                                    <td><?php echo $customers[$pending_list['party_id']]['display_name']; ?></td>
                                    <td><?php echo $pending_list['transaction_number']; ?></td>
                                    <td><?php echo fromDate($pending_list['transaction_date']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateifine($pending_list['amount']); ?></td>
                                    <td>
                                        <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $pending_list['id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                    </td>
                                </tr>
                                <?php  $mq++;
}
}else{
                                    echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills Created</h4></td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane card-body p-0" id="submittedinvoice" role="tabpanel">
            <div class="card table-card">
            <div class="card-header">
                <h5>Submitted Rental Invoices</h5>
            </div>
            <div class="card-body">
                  <div class="card-block table-responsive" >
                    <table id="basic-btn1"  class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tenant</th>
                                <th scope="col">Invoices Number</th>
                                <th scope="col">Generated Date</th>
                                <th scope="col">Submitted Date</th>
                                <th scope="col" class="text-right">Price</th>

                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $mq = 1; if(count((array)$submittedinvoice)!='0'){ foreach($submittedinvoice as $pending_list){
                                $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                ?>
                                <tr>
                                    <td><?php echo $mq; ?></td>
                                    <td><?php echo $customers[$pending_list['party_id']]['display_name']; ?></td>
                                    <td><?php echo $pending_list['transaction_number']; ?></td>
                                    <td><?php echo fromDate($pending_list['transaction_date']); ?></td>
                                    <td><?php echo fromDate($pending_list['shared_datetime']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateifine($pending_list['amount']); ?></td>
                                    <td>
                                        <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $pending_list['id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                    </a>

                                    </td>
                                </tr>
                                <?php  $mq++;
}
}else{
                                    echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills Created</h4></td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane card-body p-0 <?php echo $_GET['type']=="P"?"active":"";?>" id="paidinvoicse" role="tabpanel">
            <div class="card table-card">
            <div class="card-header">
                <h5>Paid Rental invoices</h5>
            </div>
            <div class="card-body">
                  <div class="card-block table-responsive" >
                    <table id="basic-btn1_2"  class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Tenant</th>
                                <th scope="col">Invoices Number</th>
                                <th scope="col">Generated Date</th>
                                <th scope="col">Submitted Date</th>
                                <th scope="col" class="text-right">Price</th>

                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $mq = 1; if(count((array)$paidinvoicse)!='0'){ foreach($paidinvoicse as $pending_list){
                                $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                ?>
                                <tr>
                                    <td><?php echo $mq; ?></td>
                                    <td><?php echo $customers[$pending_list['party_id']]['display_name']; ?></td>
                                    <td><?php echo $pending_list['transaction_number']; ?></td>
                                    <td><?php echo fromDate($pending_list['transaction_date']); ?></td>
                                    <td><?php echo fromDate($pending_list['shared_datetime']); ?></td>

                                    <td class="text-right"><?php echo customeummarmonyformateifine($pending_list['amount']); ?></td>
                                    <td>
                                        <a href="index.php?option=COM_VIEW_RENTAL_INVOICES&rid=<?php echo $pending_list['id']; ?>&table=rental_invoices&title=Rental Invoices" target="_blank">
                                        <button type="button" class="input-group-text addon-style cursor-pointer float-left btn btn-primary"> View </button>
                                    </a>

                                    </td>
                                </tr>
                                <?php  $mq++;
}
}else{
                                    echo "<tr><td colspan='7' class='text-center'><h4 ><i class='fa fa-warning'></i> No Bills Created</h4></td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>

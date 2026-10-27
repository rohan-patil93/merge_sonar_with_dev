<?php
$_SESSION['apply_js']['table']= 1;
if(count((array)$admindashboardd['purchase_invoice_list'])!='0' || !isset($admindashboardd['purchase_invoice_list'])){
     $admindashboardurl = API."?mode=getadmindashboard&vend_limit=3&admin_type=".$_SESSION['admin_type']."&admin_access_employee=".$_SESSION['admin_access_employee'];
     $admindashboardres = callAPI($admindashboardurl);
     $admindashboardd = json_decode($admindashboardres,1);
     $approvedinvoices = $unapprovedinvoices = array();
    foreach($admindashboardd['purchase_invoice_list'] as $pending_list){
        if($pending_list['current_status']==ST_APPROVED){
            $approvedinvoices[] = $pending_list;
        }else{
            $unapprovedinvoices[] = $pending_list;
        }
    }
    $all_vendors = $admindashboardd['all_vendors'];
}
?>
<ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#approvedinvoicse" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-check-circle f-20"></i> Approved <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$approvedinvoices); ?></span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#unapprovedinvoicse" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> <i class="fa fa-shopping-basket f-20"></i> Un-Approved <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$unapprovedinvoices); ?></span></a>
    </li>
</ul>
<div class="tab-content tabs">
    <div class="tab-pane card-body active p-0" id="approvedinvoicse" role="tabpanel">
        <div class="card table-card">
            <div class="card-header">
                <h5>Approved Supplier invoices</h5>
            </div>
            <div class="card-body">
                  <div class="card-block table-responsive" >
                    <table id="basic-btn"  class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Invoices Number</th>
                                <th scope="col">Date</th>
                                <th scope="col" class="text-right">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $mq = 1; if(count((array)$approvedinvoices)!='0'){ foreach($approvedinvoices as $pending_list){
                                $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                ?>
                                <tr>
                                    <td><?php echo $mq; ?></td>
                                    <td><?php echo $all_vendors[$pending_list['party_id']]['display_name']; ?></td>
                                    <td><?php echo $pending_list['transaction_number']; ?></td>
                                    <td><?php echo fromDate($pending_list['transaction_date']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateifine($pending_list['amount']); ?></td>
                                    <td>
                                        <span class="form-label label " style="background-color: <?php echo $stausresultpending_list['background_color'][0] == '#'?$stausresultpending_list['background_color']:"#".$stausresultpending_list['background_color']; ?>; color:<?php echo $stausresultpending_list['color'][0] =='#'?$stausresultpending_list['color']:'#'.$stausresultpending_list['color']; ?>"><?php echo $stausresultpending_list['name']?></span>
                                    </td>
                                    <td>
                                        <a href="index?option=COM_INSERT_SUPPLIER_BILLS&Rid=<?php echo $ridrecengtbils; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Datails"><i class="fa fa-edit"></i></a>
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
        <div class="tab-pane card-body p-0" id="unapprovedinvoicse" role="tabpanel">
            <div class="card table-card">
            <div class="card-header">
                <h5>Un Approved Supplier invoices</h5>
            </div>
            <div class="card-body">
                  <div class="card-block table-responsive" >
                    <table id="basic-btn1"  class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Invoices Number</th>
                                <th scope="col">Date</th>
                                <th scope="col" class="text-right">Price</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php $mq = 1; if(count((array)$unapprovedinvoices)!='0'){ foreach($unapprovedinvoices as $pending_list){
                                $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                                $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                                ?>
                                <tr>
                                    <td><?php echo $mq; ?></td>
                                    <td><?php echo $all_vendors[$pending_list['party_id']]['display_name']; ?></td>
                                    <td><?php echo $pending_list['transaction_number']; ?></td>
                                    <td><?php echo fromDate($pending_list['transaction_date']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateifine($pending_list['amount']); ?></td>
                                    <td>
                                        <span class="form-label label " style="background-color: <?php echo $stausresultpending_list['background_color'][0] == '#'?$stausresultpending_list['background_color']:"#".$stausresultpending_list['background_color']; ?>; color:<?php echo $stausresultpending_list['color'][0] =='#'?$stausresultpending_list['color']:'#'.$stausresultpending_list['color']; ?>"><?php echo $stausresultpending_list['name']?></span>
                                    </td>
                                    <td>
                                        <a href="index?option=COM_INSERT_SUPPLIER_BILLS&Rid=<?php echo $ridrecengtbils; ?>" class="btn btn-sm btn-clean btn-icon btn-icon-sm" title="Edit Datails"><i class="fa fa-edit"></i></a>
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
    </div>

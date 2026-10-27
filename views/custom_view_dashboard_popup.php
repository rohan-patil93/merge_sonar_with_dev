<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
if($_POST['table'] == "emp_expenses"){
    $admindashboardurl = API."?mode=getemployeedashboard&emp_id=".$_SESSION['emp_id']."&bill_limit=3";
    $admindashboardres = callAPI($admindashboardurl);
    $admindashboardd = json_decode($admindashboardres,1);
}else{
    $vendordashboardurl = API."?mode=getvendordashboard&admin_type=".$_SESSION['admin_type'].".&admin_access_employee=".$_SESSION['admin_access_employee']."&vend_limit=3&vendor_id=".$_SESSION['vendor_id'];
    $vendordashboardres = callAPI($vendordashboardurl);
    $vendordashboardd = json_decode($vendordashboardres,1);
}
?>
<div class="modal-header">
    <h4 class="modal-title"><?php echo $_POST['title']; ?></h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="custom-scroll" style="height: 270px;">
        <?php  if($_POST['table'] == "emp_expenses"){ ?>
            <table class="table table-hover m-b-0">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Reference No</th>
                        <th scope="col">Reference date</th>
                        <th scope="col">Expense Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $mq = 1; if(count((array)$admindashboardd[$_POST['option']])!='0'){ foreach($admindashboardd[$_POST['option']] as $pending_list){
                        $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                        $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                        ?>
                        <tr>
                            <td><?php echo $mq; ?></td>
                            <td><?php echo $pending_list['expense_code']; ?></td>
                            <td><?php echo $pending_list['reference_date']; ?></td>
                            <td><?php echo $pending_list['expense_date']; ?></td>
                            <td><?php echo $pending_list['amount']; ?></td>
                            <td>
                                <span class="form-label label " style="background-color: <?php echo $stausresultpending_list['background_color'][0] == '#'?$stausresultpending_list['background_color']:"#".$stausresultpending_list['background_color']; ?>; color:<?php echo $stausresultpending_list['color'][0] =='#'?$stausresultpending_list['color']:'#'.$stausresultpending_list['color']; ?>"><?php echo $stausresultpending_list['name']?></span>
                            </td>
                            <td>
                                <a href="index.php?option=COM_INSERT_EMP_EXPENSES&Rid=<?php echo $ridrecengtbils; ?>">
                                    <i class="ti-pencil f-w-600 f-16 m-r-15 "></i>
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
            <?php }else{ ?>
                <table class="table table-hover m-b-0">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Vendor</th>
                            <th scope="col">Invoices Number</th>
                            <th scope="col">Date</th>
                            <th scope="col">Price</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $mq = 1; if(count((array)$vendordashboardd[$_POST['option']])!='0'){ foreach($vendordashboardd[$_POST['option']] as $pending_list){
                            $stausresultpending_list = $_SESSION['status'][$pending_list['current_status']];
                            $ridrecengtbils = deocdeId("id:".$pending_list['id']);
                            ?>
                            <tr>
                                <td><?php echo $mq; ?></td>
                                <td><?php echo $_SESSION['users'][$pending_list['party_id']]; ?></td>
                                <td><?php echo $pending_list['transaction_number']; ?></td>
                                <td><?php echo $pending_list['transaction_date']; ?></td>
                                <td><?php echo $pending_list['amount']; ?></td>
                                <td>
                                    <span class="form-label label " style="background-color: <?php echo $stausresultpending_list['background_color'][0] == '#'?$stausresultpending_list['background_color']:"#".$stausresultpending_list['background_color']; ?>; color:<?php echo $stausresultpending_list['color'][0] =='#'?$stausresultpending_list['color']:'#'.$stausresultpending_list['color']; ?>"><?php echo $stausresultpending_list['name']?></span>
                                </td>
                                <td>
                                    <a href="index.php?option=COM_INSERT_PURCHASE_INVOICES&Rid=<?php echo $ridrecengtbils; ?>">
                                        <i class="ti-pencil f-w-600 f-16 m-r-15 "></i>
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
                <?php } ?>
            </div>
        </div>

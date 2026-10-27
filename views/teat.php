<?php
 $month ='12';
 $year = '2020';
 $employe_id = '1';
 $cusurl = API."?mode=togetpayslipdetails&month=".$month."&year=".$year."&employe_id=".$employe_id;
 $cusresponse = callAPI($cusurl);
 $data = json_decode($cusresponse,1);

?>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                     <h3 class="card-label">&nbsp;<?php echo "Pay Slip"  ?> </h3>
                </div>
            </div>
        </div>
        <div class="card card-body card-custom">
            <table class="table table-bordered">
                <tr class="table-primary">
                    <th scope="col" colspan="4" class="font-weight-bold">
                        <?php echo $_SESSION['company'][$_SESSION['company_id']];?>
                    </th>
                </tr>
                <tr class="text-center ">
                    <td colspan="4" class="font-weight-bold">
                        <?php echo "Payslip For ".$data['month']." ".$data['year'];?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold w-25">
                       Employee Number:
                    </td>
                    <td  class="w-25">
                        <?php echo $data['emp_number'];?>
                    </td>
                    <td   class="table-primary font-weight-bold w-25">
                       Name:
                    </td>
                    <td  class="w-25">
                        <?php echo ucwords($data['emp_name']);?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold">
                       Bank:
                    </td>
                    <td  class="">
                        <?php echo $data['bank'];?>
                    </td>
                    <td   class="table-primary font-weight-bold">
                       Bank Ac/No:
                    </td>
                    <td  class="">
                        <?php echo $data['bank_account_no'];?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold">
                       DOJ:
                    </td>
                    <td  class="">
                        <?php echo $data['doj'];?>
                    </td>
                    <td   class="table-primary font-weight-bold">
                       LOP Days:
                    </td>
                    <td  class="">
                        <?php echo $data['lop'];?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold">
                       PF No.:
                    </td>
                    <td  class="">
                        <?php echo $data['pf'];?>
                    </td>
                    <td   class="table-primary font-weight-bold">
                       STD Days:
                    </td>
                    <td  class="">
                        <?php echo $data['std'];?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold">
                       Location:
                    </td>
                    <td  class="">
                        <?php echo $data['location'];?>
                    </td>
                    <td   class="table-primary font-weight-bold">
                       Worked Days:
                    </td>
                    <td  class="">
                        <?php echo $data['work'];?>
                    </td>
                </tr>
                <tr>
                    <td  class="table-primary font-weight-bold">
                       Department:
                    </td>
                    <td  class="">
                        <?php echo $data['department'];?>
                    </td>
                    <td   class="table-primary font-weight-bold">
                       Designation:
                    </td>
                    <td  class="">
                        <?php echo $data['designation'];?>
                    </td>
                </tr>
            </table>
            <div> </div>
            <div> </div>
            <table class="table table-bordered">
                <tr class="table-primary font-weight-bold">
                    <th scope="col" style="width: 40%;">
                       Earnngs
                    </th>
                    <th scope="col"  style="width: 10%;" class="text-right">
                        <?php echo "Amount";?>
                    </th>
                    <th scope="col"   class="" style="width: 40%;">
                       Deductions
                    </th>
                    <th scope="col"  style="width: 10%;" class="text-right">
                        <?php echo "Amount";?>
                    </th>
                </tr>
                <?php
                $actual_count= count((array)$data['actual']);
                $dedaction_count= count((array)$data['dedaction']);
                $count = ($actual_count>$dedaction_count) ? $actual_count : $dedaction_count;
                for($i=0; $i<$count;$i++) {?>
                <tr class="">
                    <td style="width: 40%;">
                      <?php echo  $data['actual'][$i]['name']; ?>
                    </td>
                    <td  style="width: 10%;" class="text-right">
                         <?php echo  $data['actual'][$i]['amount']; ?>
                    </td>
                    <td   class="" style="width: 40%;">
                       <?php echo  $data['dedaction'][$i]['name']; ?>
                    </td>
                    <td  style="width: 10%;" class="text-right">
                        <?php echo  $data['dedaction'][$i]['amount']; ?>
                    </td>
                </tr>
            <?php } ?>

                <tr class="font-weight-bold table-active">
                    <td style="width: 40%;">
                       Gross Earnings
                    </td>
                    <td  style="width: 10%;" class="text-right">
                        <?php echo $data['actual_amount'];?>
                    </td>
                    <td   class="" style="width: 40%;">
                       Gross Deductions
                    </td>
                    <td  style="width: 10%;" class="text-right">
                        <?php echo $data['dedaction_amount'];?>
                    </td>
                </tr>
                <tr class="font-weight-bold table-active">

                    <td  colspan="2" class="text-right">
                       NetPay
                    </td>
                    <td   colspan="2" class="text-left">
                         <?php echo $data['net'];?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

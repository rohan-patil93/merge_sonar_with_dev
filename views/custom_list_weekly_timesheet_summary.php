<?php
if(isset($_POST['pay_month_year'])) {
    $pay_month_year = $_POST['pay_month_year'];
    $reproting_manager = $_POST['reproting_manager'];
} else {
    $pay_month_year = date('F, Y');
    $reproting_manager = 0;
}

$title = "Payslip";
$pay_year = date('Y',strtotime($pay_month_year));
$pay_month = date('m',strtotime($pay_month_year));
$cusurl = API."?mode=getweecklytimesheetsummary&month=".$pay_month."&year=".$pay_year."&reproting_manager=".$reproting_manager;
$cusresponse = callAPI($cusurl);
$result = json_decode($cusresponse,1);
$displayarray = array();
$totalempcnt = $totalhrs = $firstweek = $secondweek = $thirdweek = $fourthweek = $fiveweek = 0;
$reporting_tolist = array();
foreach((array)$result as $key=>$ressum){
    $reporting_tolist[] =$key;
    $displayarray[$key]['total_emp'] = 0;
    foreach((array)$ressum as $key2=>$ressums){
        foreach((array)$ressums as $key3=>$arr_value){
            $totalempcnt++;
            $totalhrs += $arr_value['total_hrs'];
            $firstweek += $arr_value['1week'];
            $secondweek += $arr_value['2week'];
            $thirdweek += $arr_value['3week'];
            $fourthweek += $arr_value['4week'];
            $fiveweek += $arr_value['5week'];

            $displayarray[$key]['total_emp']++;
            $displayarray[$key]['total_hrs'] += $arr_value['total_hrs'];
            $displayarray[$key]['1week'] += $arr_value['1week'];
            $displayarray[$key]['2week'] += $arr_value['2week'];
            $displayarray[$key]['3week'] += $arr_value['3week'];
            $displayarray[$key]['4week'] += $arr_value['4week'];
            $displayarray[$key]['5week'] += $arr_value['5week'];
        }
    }

}






?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js" integrity="sha384-ZvpUoO/+PpLXR1lu4jmpXWu80pZlYUAfxl5NsBMWOEPSjUn/6Z/hRTt8+pR6L4N2" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>

<style>
    tr{}
    td, th{border-bottom: 1px solid #EBEDF3; padding: 10px;}
    table.data-table tr th{color: #B5B5C3; font-weight: 500; font-size: 14px; letter-spacing: 1px;}
    table.data-table .custom-check{position: relative;}
    table.data-table .custom-check input[type="checkbox"]{width: 17px; height: 17px;}
    table.data-table .dropdown .btn{padding: 0px 8px 2px 8px;}

    span.online-text{width: 8px; height: 8px; display: inline-block; background: #dc3545; border-radius: 50%;}
    span.direct-text{width: 8px; height: 8px; display: inline-block; background: #007bff; border-radius: 50%;}
    table.data-table .dropdown-toggle:after{display: none;}
    table.data-table a.edit{display: inline-block; vertical-align: middle;}
</style>
<div class="page-body">
    <div class="card">
        <div class="card-body card-title">
            <form method="post" action="#">
                <div class="row mb-2">
                    <div class="col-lg-6 col-md-12 col-sm-12 col-12 mb-3">
                        <div class="col bg-light-primary ">
                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                            </span>
                            <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <span class="f-w-700 mb-1">Reporting Manager</span>
                            <select aria-label="Reproting Manager" class="form-select js-example-basic-single p-2" name="reproting_manager">
                                <option value="0">All</option>
                                foreach((array)$_SESSION['users'] as $key=>$reporting_tolists){
                                ?>
                                    <option value="<?php echo $key; ?>" <?php echo $reproting_manager==$key?"selected":"";?>><?php echo $reporting_tolists; ?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                        <div class="form-group">
                            <span class="f-w-700 mb-1">Month</span>
                            <div class="input-group input-group-button">
                                <input aria-label="Pay Month Year" type="month" name="pay_month_year" value="<?php echo $pay_year."-".$pay_month?>"  id="example-month-input" class="form-control form-control-sm width1 p-2" style="">
                                <button type="submit" name="get_ecr_summary" class="btn btn-info waves-effect waves-light input-group-text mt-0 p-2">
                                    Submit
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row mb-2">
                <h4 class="card-label text-center f-w-700">Weekly Approved Timesheets <?php echo date("M Y",strtotime($pay_month_year)); ?>     <small class="f-10 d-block">Name : <?php echo $_SESSION['display_name']; ?> | |  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small>
                </h4>
            </div>


            <div class="dt-responsive table-responsive mt-2">

                <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
                    <tr>
                        <th scope="col" style="width: 3%;"></th>
                        <th scope="col" style="width: 5%;">SL.NO.</th>
                        <th scope="col" style="width: 7%;">Reporting Manager</th>
                        <th scope="col" class="text-right" style="width: 13%;">Total Emp</th>

                        <th scope="col" class="text-right" style="width: 12%;">Week-1</th>
                        <th scope="col" class="text-right" style="width: 10%;">Week-2</th>
                        <th scope="col" class="text-right" style="width: 10%;"> Week-3</th>
                        <th scope="col" class="text-right" style="width: 10%;">Week-4</th>
                        <th scope="col" class="text-right" style="width: 8%;">Week-5</th>
                        <th scope="col" class="text-right" style="width: 10%;">Total Hours</th>
                    </tr>
                    <?php $k=1; foreach((array)$result as  $key5=>$rows){

                    ?>
                    <tr class="main">
                        <td>
                            <a href="javascript:void(0)" class="main">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                                  <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                                </svg>
                            </a>
                        </td>

                        <td><?php echo $k; ?></td>
                        <td><?php echo $_SESSION['users'][$key5]; ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['total_emp']); ?>    </td>

                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['1week']); ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['2week']); ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['3week']); ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['4week']); ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['5week']); ?></td>
                        <td class="text-right"><?php echo  customeummarmonyformateround($displayarray[$key5]['total_hrs']); ?>    </td>
                    </tr>
                    <tr class="data">
                        <td colspan="16">
                            <table class="mb-3" style="width: 100%">
                                <tr>
                                    <th scope="col" style="width: 3%;"></th>
                                    <th scope="col" style="width: 5%;">SL.NO.</th>
                                    <th scope="col" style="width: 7%;">Approving Manager</th>
                                    <th scope="col" style="width: 13%;">Emp Name</th>
                                    <th scope="col" class="text-right" style="width: 12%;">Week-1</th>
                                    <th scope="col" class="text-right" style="width: 10%;">Week-2</th>
                                    <th scope="col" class="text-right" style="width: 10%;"> Week-3</th>
                                    <th scope="col" class="text-right" style="width: 10%;">Week-4</th>
                                    <th scope="col" class="text-right" style="width: 8%;">Week-5</th>
                                    <th scope="col" class="text-right" style="width: 10%;">Total Hours</th>

                                </tr>
                                <?php $m=1; foreach((array)$rows as $datas){
                                        foreach((array)$datas as $data){
                                ?>
                                <tr>
                                    <td></td>
                                    <td><?php echo $m; ?></td>
                                    <td><?php echo $_SESSION['users'][$data['approval_manager']]; ?></td>
                                    <td><?php echo $_SESSION['users'][$data['emp_id']]; ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateround($data['1week']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateround($data['2week']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateround($data['3week']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateround($data['4week']); ?></td>
                                    <td class="text-right"><?php echo customeummarmonyformateround($data['5week']); ?></td>
                                      <td class="text-right"><?php echo customeummarmonyformateround($data['total_hrs']); ?></td>
                                </tr>
                                <?php $m++; ?>
                                <?php } ?>
                                <?php } ?>
                            </table>
                        </td>
                    </tr>
                    <?php $k++;
} ?>
                    <?php if($totalempcnt !="0"){ ?>
                    <tr>
                        <td colspan='3'><b>Total</b></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Employees"><?php echo customeummarmonyformateround($totalempcnt); ?></span></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Nonbillable Days"><?php echo customeummarmonyformateround($firstweek); ?></span></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" PO Amount"><?php echo customeummarmonyformateround($secondweek); ?></span></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" VRC Amount"><?php echo customeummarmonyformateround($thirdweek); ?></span></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Amount"><?php echo customeummarmonyformateround($fourthweek); ?></span></td>
                        <td class="text-right"><span data-bs-toggle="tooltip" data-placement="bottom" title=" VRC Billable Amount"><?php echo customeummarmonyformateround($fiveweek); ?></span></td>
                        <td class="text-right" ><span data-bs-toggle="tooltip" data-placement="bottom" title=" Billable Days"><?php echo customeummarmonyformateround($totalhrs); ?></span></td>
                    </tr>
                    <?php }else{ ?>
                    <tr class="text-center">
                        <td colspan='10'>
                            <b>Record Not Founds</b>
                        </td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
    //Hide table rows with class 'min', but appear when clicked.
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>

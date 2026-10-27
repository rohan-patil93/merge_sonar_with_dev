<?php

$acconts[] = array('acont_name'=>'Capital Acount','debit'=>'0.00','credit'=>'190000.00');
$acconts[] = array('acont_name'=>'Secured Llabillty','debit'=>'0.00','credit'=>'12000.00');
$acconts[] = array('acont_name'=>'Unsecured Llabillty','debit'=>'0.00','credit'=>'11000.00');
$acconts[] = array('acont_name'=>'Current Llabillty','debit'=>'0.00','credit'=>'90000.00');
$acconts[] = array('acont_name'=>'Fixed Assert','debit'=>'303000.00','credit'=>'0.00');

$gen['1'][] = array('acont_name'=>'Secured Llabillty 1','debit'=>'0.00','credit'=>'4000.00');
$gen['1'][] = array('acont_name'=>'Secured Llabillty 2','debit'=>'0.00','credit'=>'3000.00');
$gen['1'][] = array('acont_name'=>'Secured Llabillty 3','debit'=>'0.00','credit'=>'5000.00');
$gen['2'][] = array('acont_name'=>'Unsecured Llabillty 1','debit'=>'0.00','credit'=>'3000.00');
$gen['2'][] = array('acont_name'=>'Unsecured Llabillty 2','debit'=>'0.00','credit'=>'2000.00');
$gen['2'][] = array('acont_name'=>'Unsecured Llabillty 3','debit'=>'0.00','credit'=>'5000.00');
$gen['3'][] = array('acont_name'=>'Current Llabillty 1','debit'=>'0.00','credit'=>'6000.00');
$gen['3'][] = array('acont_name'=>'Current Llabillty 1','debit'=>'0.00','credit'=>'3000.00');
$gen['4'][] = array('acont_name'=>'Fixed Llabillty 1','debit'=>'0.00','credit'=>'303000.00');
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
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <span class="kt-portlet__head-icon">
                        <i class="kt-font-brand flaticon2-line-chart"></i>
                    </span>
                    <h3 class="card-label">&nbsp;<?php echo "Trial Blance ";  ?> </h3>
                    <form class="kt-form" id="kt_form" method="post" action="" >
                    <div style="float: left;" class="mr-3">
                            <input aria-label="From Date" class="form-control" type="month" name="from_date" value="<?php echo $from_cur_year."-".$from_cur_month?>"  id="att_month_year" />
                    </div>
                    <div style="float: left;">
                    <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2"  name="applyform" value="apply">Apply</button>
                </div>
                </form>
                </div>
                <div class="kt-portlet__head-toolbar">
                    <div class="card-toolbar">
                            <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                                <i class="ki ki-long-arrow-back icon-xs"></i>
                                Back
                            </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom">
            <div class="card-body">
    <div class="row">

        <table class="table table-separate table-head-custom table-checkable data-table" id="kt_datatable1">
            <tr>
                <th scope="col" style="width: 3%;"></th>
                <th scope="col" style="width: 5%;">SL.NO.</th>
        <th scope="col" style="width: 60%;"> Account</th>
        <th scope="col" class="text-right" style="width: 16%;"> Debit Amount</th>
        <th scope="col" class="text-right" style="width: 16%;">Credit Amount</th>
            </tr>
            <?php $k=1; foreach($acconts as  $key=>$vender){ ?>
            <tr class="main">
                <td>
                    <a href="javascript:void(0)" class="main">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right-fill" viewBox="0 0 16 16">
                          <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z"/>
                        </svg>
                    </a>
                </td>

                <td><?php echo $k; ?></td>
                <td><?php echo  $vender['acont_name']; ?></td>
                <td class="text-right"><?php echo  customeummarmonyformate($vender['debit']); ?>    </td>
                <td class="text-right"><?php echo  customeummarmonyformate($vender['credit']); ?></td>
            </tr>
            <tr class="data">
                <td colspan="12">
                    <table class="mb-3" style="width: 100%">
                        <tr>
                            <th scope="col" style="width: 3%;"></th>
                            <th scope="col" style="width: 5%;">SL.NO.</th>
                    <th scope="col" style="width: 60%;"> Account</th>
                    <th scope="col" class="text-right" style="width: 16%;"> Debit Amount</th>
                    <th scope="col" class="text-right" style="width: 16%;">Credit Amount</th>
                        </tr>
                        <?php $l=1; foreach($gen[$key] as $data){ ?>
                        <tr>
                            <td></td>
                            <td><?php echo $l; ?></td>
                            <td><?php echo  $data['acont_name']; ?></td>
                            <td class="text-right"><?php echo  customeummarmonyformate($data['debit']); ?>    </td>
                            <td class="text-right"><?php echo  customeummarmonyformate($data['credit']); ?></td>
                        </tr>
                        <?php $l++;
} ?>

                    </table>
                </td>
            </tr>
            <?php $k++;
} ?>
                        <th scope="col" style="width: 3%;"></th>
                        <th scope="col" style="width: 5%;"></th>
                <th scope="col" class="text-right" style="width: 60%;">Total</th>
                <th scope="col" class="text-right" style="width: 16%;"> <?php echo customeummarmonyformate('303000'); ?></th>
                <th scope="col" class="text-right" style="width: 16%;"><?php echo customeummarmonyformate('303000'); ?></th>
        </table>
    </div>
</div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
        jQuery(this).parent().parent().next(".data").toggle("fast");
    });
});
</script>

<style>
    .headerprints, .header-space,
    .footer, .footer-space {
        height: 100px;
    }

    .footer {
        position: fixed;
        top: 0;
    }
</style>
<?php

$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];

//mew
$myreportwh["where"] = array('report_id'=>$rid,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
print_r($myreportwh);
$myhreportencodeurl = urlencode(json_encode($myreportwh));
$myreporturl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$myhreportencodeurl;
$myreprtdata = callAPI($myreporturl);
$myreprtdata = json_decode($myreprtdata,1);
$myreprtdata = $myreprtdata[0];

$sessionarray = array('userid'=>$_SESSION['userid'],'email'=>$_SESSION['email'],'emp_id'=>$_SESSION['emp_id'],'current_date'=>date("Y-m-d"));
$sessionarrays    = urlencode(json_encode($sessionarray));
$reportviewencodeurl = urlencode(json_encode($reportviewwh));
$reportviewurl = API."?mode=myreportviewsql&rid=".$rid."&emp_id=".$_SESSION['emp_id']."&where=".$reportviewencodeurl."&sessionarrays=".$sessionarrays;
$myreprtviewdata = callAPI($reportviewurl);
$myreprtviewdata = json_decode($myreprtviewdata,1);
$page_title = $myreprtdata['page_name'];
$report_list = json_decode($myreprtdata['report_list'],1);
$header = $myreprtdata['header'];
$columnslist = json_decode($myreprtdata['search_list'],1);
if($columnslist['show_table_type']=="mtarix"){
    header('Location: ?option=COM_VIEW_MY_REPORT_TABULARS&Rid='.$_GET['Rid']);
}elseif($columnslist['show_table_type']=="master"){
    header('Location: ?option=COM_VIEW_MY_REPORT_MASTERS&Rid='.$_GET['Rid']);
}
$report_formula =json_decode($myreprtdata['report_formula'],1);

$header_list = json_decode($myreprtdata['header'],1);
$headers[$header_list['orderhedding']] = $header_list['heddingdislay'] ==0 ? "<h3 class='card-label'>". $page_title."</h3>" : "";
$headers[$header_list['orderlogo']] = $header_list['logodislay'] ==0 ? "<img src='uploads/all/".$_SESSION['config']['display_logo']."' style='width:100px;' alt='Logo'>": "";
$headers[$header_list['addresorder']] = $header_list['addressdislay'] ==0 ? "<div class='row'><div class='col-lg-3'><span class='h6 text-black'> Report By</span></div> <div class='col-lg-1'>:</div><div class='col-lg-4'><span class='font-size-lg'>".$_SESSION['users'][$myreprtdata['created_by']]."</span></div></div> <div class='row'><div class='col-lg-3'><span class='h6 text-black'> Report Date:</span></div><div class='col-lg-1'>:</div><div class='col-lg-4'><span class='font-size-lg'> ".date("d-m-Y H:i:s")."</span></div></div>": "";
$header_position[$header_list['orderhedding']] = $header_list['heddingposition'];
$header_position[$header_list['orderlogo']] = $header_list['logoposition'];
$header_position[$header_list['addresorder']] = $header_list['addressposition'];
$footers = json_decode($myreprtdata['footer'],1);
$table = $myreprtdata['application_table_name'];

$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=display_attributes&data=".$vdata;
$page_response = callAPI($vurl);
$list = json_decode($page_response,1);
$list = $list['0'];
$lists = json_decode($list['list_attributes'],1);
$type_array = array('select','multi_select','disable_select','radio','checklist', 'color_pick','image');
foreach ($report_list as $key => $value){
    if($value['display'] ==0 ) {
        if(isset($dis[$value['Priority']])){
            $value['Priority'] = $value['Priority']+1;
        }
        $dis[$value['Priority']]['name'] = $value['feildname'];
        $dis[$value['Priority']]['lable'] = $value['label'];
        $dis[$value['Priority']]['heddingpositiontable'] = $value['heddingpositiontable'];
        $dis[$value['Priority']]['add_custom_field'] = $value['add_custom_field'];
        $dis[$value['Priority']] ['type'] = $value['filtertype'];
        $key_lists[] = $value['feildname'];
        if(in_array($value['filtertype'], (array)$type_array)){
            $field[$value['feildname']] =  dyanamicselectdata($value['tablenames'],$value['feildname']);
        }
    }
    if($value['switch']=='0'){
        $searchs[$value['feildname']] = $lists[$value['feildname']]['type'];
        $filters[$value['feildname']]['field']=$value['feildname'];
        $filters[$value['feildname']]['lable']= $value['label'];
        $filters[$value['feildname']]['filter_type']= $value['filtertype'];
        if(in_array($value['filtertype'],(array)$type_array)){
            $filters[$value['feildname']]['listoption']= dyanamicselectdata($value['tablenames'],$value['feildname']);
        }
    }
}

foreach($report_formula  as $report_formulkk){
        if(isset($dis[$report_formulkk['custom_report_Priority']])){
            $report_formulkk['custom_report_Priority'] = $report_formulkk['custom_report_Priority']+1;
        }
    $dis[$report_formulkk['custom_report_Priority']]['name'] = $report_formulkk['custom_report_heding'];
    $dis[$report_formulkk['custom_report_Priority']]['lable'] = ucwords(str_replace("_"," ",($report_formulkk['custom_report_heding'])));
}

if(count((array)$dis) !=0){
    ksort($dis);
}

if(isset($_POST['submit'])){
    $whr = $_POST;
    foreach($whr as $key => $where){
        if($key!="submit" &&  $where!=""){
            $name_field = str_replace('||', '.', $key);
            $reportviewwh["where"][$name_field] = $where;
        }
    }
    $reportviewencodeurl = urlencode(json_encode($reportviewwh));
    $reportviewurl = API."?mode=myreportviewsql&rid=".$rid."&where=".$reportviewencodeurl;
    $myreprtviewdata = callAPI($reportviewurl);
    $myreprtviewdata = json_decode($myreprtviewdata,1);
}

if (!function_exists('applyReportMathOperation')) {
    function applyReportMathOperation(&$values, &$ops) {
        if (empty($values) || empty($ops)) {
            return;
        }
        $val2 = array_pop($values);
        $val1 = array_pop($values);
        $op = array_pop($ops);
        switch ($op) {
            case '+':
                $values[] = $val1 + $val2;
                break;
            case '-':
                $values[] = $val1 - $val2;
                break;
            case '*':
                $values[] = $val1 * $val2;
                break;
            case '/':
                $values[] = ($val2 != 0) ? $val1 / $val2 : 0;
                break;
            case '%':
                $values[] = ($val2 != 0) ? $val1 % $val2 : 0;
                break;
            default:
                break;
        }
    }
}

if (!function_exists('handleParenthesisToken')) {
    function handleParenthesisToken($token, &$values, &$ops) {
        if ($token === '(') {
            $ops[] = '(';
            return true;
        }
        if ($token === ')') {
            while (!empty($ops) && end($ops) !== '(') {
                applyReportMathOperation($values, $ops);
            }
            array_pop($ops);
            return true;
        }
        return false;
    }
}

if (!function_exists('processOperatorToken')) {
    function processOperatorToken($token, &$values, &$ops, $precedence) {
        while (!empty($ops) && end($ops) !== '(' && isset($precedence[end($ops)]) && $precedence[end($ops)] >= $precedence[$token]) {
            applyReportMathOperation($values, $ops);
        }
        $ops[] = $token;
    }
}

if (!function_exists('processReportMathToken')) {
    function processReportMathToken($token, &$values, &$ops, $precedence) {
        if (is_numeric($token)) {
            $values[] = (float)$token;
            return;
        }
        if (handleParenthesisToken($token, $values, $ops)) {
            return;
        }
        if (isset($precedence[$token])) {
            processOperatorToken($token, $values, $ops, $precedence);
        }
    }
}

if (!function_exists('safeEvaluateReportMath')) {
    function safeEvaluateReportMath($expr) {
        $cleanExpr = preg_replace('/[^\d\+\-\*\/\%\.\(\)]/', '', (string)$expr);
        if (trim($cleanExpr) === '') {
            return 0;
        }
        $tokens = preg_split('/([\+\-\*\/\%\(\)])/', $cleanExpr, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        $values = array();
        $ops = array();
        $precedence = array('+' => 1, '-' => 1, '*' => 2, '/' => 2, '%' => 2);

        foreach ($tokens as $token) {
            $token = trim($token);
            if ($token !== '') {
                processReportMathToken($token, $values, $ops, $precedence);
            }
        }
        while (!empty($ops)) {
            applyReportMathOperation($values, $ops);
        }
        return !empty($values) ? array_pop($values) : 0;
    }
}

$showarraylist = array();

foreach($myreprtviewdata as $fk =>$rowlist){
$fnalrrr = 0;
    foreach($rowlist as $filenam =>$rowlistss){
        $showarraylist[$fk][$filenam] = $rowlistss;
        foreach($report_formula  as $report_formulkk){
            if(!isset($formulas[$fk][$report_formulkk['custom_report_heding']])){
                $formulas[$fk][$report_formulkk['custom_report_heding']] = $report_formulkk['custom_report_formula'];
            }
            $formulas[$fk][$report_formulkk['custom_report_heding']] = str_replace($filenam,$rowlistss,$formulas[$fk][$report_formulkk['custom_report_heding']]);
        }
    }
    foreach($report_formula  as $report_formulkk){
        $trrr= $formulas[$fk][$report_formulkk['custom_report_heding']];
        $showarraylist[$fk][$report_formulkk['custom_report_heding']] = safeEvaluateReportMath($trrr);
    }
}

$stausresult = ViewStatusData();

?>
<script type="text/javascript" src="assets/bower_components/jquery/js/jquery.min.js"></script>
<script>
    setTimeout(function() {
        $(".filterdynamic").each(function(index){
            if($(this).attr("data-type")== "select"){
                var select = $(this).val()==""?'N/A':$(this).find(":selected").text();
                $(".filertdisplay").append('<div class="row"><div class="col-lg-3 text-black">'+$(this).attr("data-lable")+'</div><div class="col-lg-1">:</div><div class="col-lg-3">'+select+'</div></div>');
            }else{
                var val = $(this).val()==""?'N/A':$(this).val();
                $(".filertdisplay").append('<div class="row"><div class="col-lg-3 text-black">'+$(this).attr("data-lable")+'</div><div class="col-lg-1">:</div><div class="col-lg-3">'+val+'</div></div>');
            }

    });
    },1000);

</script>
<script type="text/javascript">
    function printDiv(divId) {
        var printContents = document.getElementById(divId).innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = "<html lang='en'><head><title>rohan patil</title></head><body><div>rohan new project</div>" + printContents + "</body>";
        window.print();
        document.body.innerHTML = originalContents;
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <h5>view report</h5>
                <div class="card-header-right">
                    <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                        <i class="icofont icofont-reply"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card card-custom j-content">
                <div class="card-body  j-pro">
                <?php if(count((array)$filters) !='0'){ ?>
                    <form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
                        <div class="row kt-margin-b-20">
                            <?php

                            foreach($filters as $key => $filt) {
                                $ser_field = str_replace('.', '_', $filt['field'] );
                                $name_field = str_replace('.', '||', $filt['field'] ); ?>
                                <?php if($filt['filter_type'] == 'text'){ ?>
                                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                                        <span><?php echo $filt['lable']; ?>:</span>
                                        <input aria-label="Input field" type="text" class="form-control kt-inpu filterdynamic" data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>" value="<?php echo $_POST[$name_field]; ?>" name="<?php echo $name_field; ?>"  data-col-index="4">
                                    </div>
                                <?php }elseif($filt['filter_type']=='select') {
                                    $selectdata =$filt['listoption'];
                                    ?>
                                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                                        <span><?php echo $filt['lable']; ?>:</span>
                                        <select aria-label="Select option" class="form-control kt-input form-select js-example-basic-single filterdynamic" name="<?php echo $name_field; ?>" data-col-index="2" data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>">
                                            <option  value=""><?php echo "ALL"; ?></option>
                                            <?php foreach($selectdata as $selectkey=>$select){ ?>
                                                <option <?php if($_POST[$name_field] == $selectkey){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>"><?php echo $select; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }elseif($filt['filter_type']=='date_pick'){ ?>
                                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                                        <span><?php echo $filt['lable']; ?>:</span>

                                        <input aria-label="Input field" type="text" class="form-control filterdynamic datepicker"  value="<?php echo $_POST[$name_field]; ?>"  name="<?php echo $name_field; ?>"  id="kt_datepicker_3<?php echo $dateid; ?>"  data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>"/>
                                    </div>
                                    <?php $dateid++;
}elseif($filt['filter_type']=='month' || $filt['filter_type']=='year' || $filt['filter_type'] == 'month_year'){ ?>
                                        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
                                            <span><?php echo $filt['lable']; ?>:</span>
                                            <input aria-label="Input field" type="month" class="form-control filterdynamic"  value="<?php echo $_POST[$name_field]!=""?date('Y-m',strtotime($_POST[$name_field])):""; ?>"  name="<?php echo $name_field; ?>"   data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>"/>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="kt-separator kt-separator--md kt-separator--dashed"></div>
                            <div class="row mt-2">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary btn-brand--icon" id="kt_search" name="submit">
                                        <span>
                                            <i class="fa fa-search"></i>
                                            <span>Search</span>
                                        </span>
                                    </button>
                                </div>
                            </div>
                </form>
                <?php } ?>
                <div class="separator separator-solid separator-border-2 mb-3"></div>
                    <!--begin: Datatable -->
                    <table style="width: 100%;">
                        <thead >
                            <tr>
                                <th scope="col" colspan="<?php echo $tr_count; ?>">
                                    <div class="row" >
                                        <?php  for ($i=1; $i <7; $i++) { ?>
                                            <div class="col-lg-4 mt-2 <?php echo $header_position[$i]; ?>">
                                                <?php echo $headers[$i]; ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </th>
                            </tr>

                        </thead>
                    </table>
                    <div class="dt-responsive table-responsive  mt-3 " >
                        <table id="basic-btn" class="table table-striped table-bordered nowrap">

                            <thead>
                                <tr>
                                    <?php  foreach ($dis as $header) {
                                        $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                                        ?>
                                        <th scope="col" class="<?php echo $header['heddingpositiontable']; ?>"><?php echo $header['lable']; ?></th>
                                            <?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($showarraylist as $value2){   ?>
                                            <tr>
                                                <?php foreach($dis as $fieldlist){ $name_field = str_replace('.', '', $fieldlist['name']);
                                                ?>
                                                <?php if ($fieldlist['name'] == 'company_id') { ?>
                                                    <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $_SESSION['company'][$value2[$name_field]]; ?></td>
                                                <?php }elseif($fieldlist['name'] == 'current_status') { ?>
                                                    <td class="<?php echo $fieldlist['heddingpositiontable']; ?>">
                                                        <div class="btn-group">
                                                            <span  class="form-label label label-primary" style="color: <?php echo "#".$stausresult[$value2[$name_field]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$value2[$name_field]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$value2[$name_field]]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $stausresult[$value2[$name_field]]['name']; ?> </span>
                                                        </div>
                                                    </td>
                                                <?php }elseif(in_array($fieldlist['type'], (array)$type_array)){
                                                    ?>
                                                    <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $value2[$name_field]==""?"-":$field[$name_field][$value2[$name_field]];?></td>
                                                <?php }else{ ?>
                                                    <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $value2[$name_field]==""?"-":$value2[$name_field];?></td>
                                                <?php  } ?>
                                            <?php } ?>
                                        </tr>
                                    <?php }  ?>


                                                </tbody>

                                            </table>

                                        </div>
    </div>
                                </div>
                                <input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">
                            </div>
                        </div>
                    </div>

<?php /**/ ?>
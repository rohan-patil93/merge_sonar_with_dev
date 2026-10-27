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
$_SESSION['apply_js']['multiselest'] = 1;
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
//mew
$myreportwh["where"] = array('report_id'=>$rid,'is_active'=>0,'company_id'=>$_SESSION['company_id']);
$myhreportencodeurl = urlencode(json_encode($myreportwh));
$myreporturl = API."?mode=gettabledetailswithconditions&table=my_reports&data=".$myhreportencodeurl;
$myreprtdata = callAPI($myreporturl);
$myreprtdata = json_decode($myreprtdata,1);
$myreprtdata = $myreprtdata[0];

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
}else{
    $reportviewencodeurl = urlencode(json_encode($reportviewwh));
    $reportviewurl = API."?mode=myreportviewsql&rid=".$rid."&where=".$reportviewencodeurl;
}
$myreprtviewdata1 = callAPI($reportviewurl);
$myreprtviewdata2 = json_decode($myreprtviewdata1,1);
$myreprtviewdata = $myreprtviewdata2['result'];
foreach($myreprtviewdata2['filter'] as $filter1){
    $filterlist[$filter1['feildname']] = $filter1;
}


$page_title = $myreprtdata['page_name'];
$report_list = json_decode($myreprtdata['report_list'],1);
$header = $myreprtdata['header'];
$columnslist = json_decode($myreprtdata['search_list'],1);
if($columnslist['show_table_type']=="tabel"){
    header('Location: ?option=COM_VIEW_MY_REPORT&Rid='.$_GET['Rid']);
}elseif($columnslist['show_table_type']=="mtarix"){
    header('Location: ?option=COM_VIEW_MY_REPORT_TABULARS&Rid='.$_GET['Rid']);
}
$header_list = json_decode($myreprtdata['header'],1);
$headers[$header_list['orderhedding']] = $header_list['heddingdislay'] ==0 ? '<h5 class="card-label text-center f-w-700 d-block mb-0">'. $page_title.'</h5>' : "";
$headers[$header_list['orderlogo']] = $header_list['logodislay'] ==0 ? "<img src='uploads/all/".$_SESSION['config']['display_logo']."' class='img-fluid  img-120' alt='Logo'>": "";

$headers[$header_list['addresorder']] = $header_list['addressdislay'] ==0 ?'<span class="f-10"><span class="d-block">Name : '.ucwords($_SESSION['display_name']).' </span> <span> Date & Time : '.date("d-m-Y H:i:s").'</span></span>':'';

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
    if($value['display'] ==0 ){
        if(isset($dis[$value['Priority']])){
            $value['Priority'] = $value['Priority']+1;
        }
        $dis[$value['Priority']] ['name'] = $value['feildname'];
        $dis[$value['Priority']] ['lable'] = $value['label'];
        $dis[$value['Priority']] ['heddingpositiontable'] = $value['heddingpositiontable'];
        $dis[$value['Priority']] ['add_custom_field'] = $value['add_custom_field'];
        $dis[$value['Priority']] ['type'] = $value['filtertype'];
        $key_lists[] = $value['feildname'];
        if(in_array($value['filtertype'], $type_array)){
            $listarray = dyanamicselectdata($value['tablenames'],$value['displayfeildname']);
            $field[$value['feildname']] =  $listarray;
            $listoption[$value['feildname']]= $listarray;
        }
    }
    if($value['grooupbyoperators']!='0'){
        $grooupbyopr[$value['feildname']] = $value;
    }
    if($value['switch']=='0'){
        $searchs[$value['feildname']] = $lists[$value['feildname']]['type'];
        $filters[$value['feildname']]['field']=$value['feildname'];
        $filters[$value['feildname']]['lable']= $value['label'];
        $filters[$value['feildname']]['filter_type']= $value['filtertype'];
        if(in_array($value['filtertype'], $type_array)){
            $listarray = dyanamicselectdata($value['tablenames'],$value['displayfeildname']);
            $filters[$value['feildname']]['listoption']= $listarray;
            $listoption[$value['feildname']]= $listarray;
        }

    }
}
ksort($dis);
$showarraylist = array();

$sumarraylist = array();
foreach($myreprtviewdata as $rowlist){
    $showarraylist[$rowlist[$columnslist['master_list']]][] = $rowlist;
    $sumarraylist[$rowlist[$columnslist['master_list']]]['total']++;
    foreach($grooupbyopr as $grpbykey => $grooupbyoprt){
        if($grooupbyoprt['grooupbyoperators']=="COUNT"){
            $sumarraylist[$rowlist[$columnslist['master_list']]][$grpbykey]++;
        }elseif($grooupbyoprt['grooupbyoperators']=="MAX"){
            $sumarraylist[$rowlist[$columnslist['master_list']]][$grpbykey][] = $rowlist[$grpbykey];
        }elseif($grooupbyoprt['grooupbyoperators']=="MIN"){
            $sumarraylist[$rowlist[$columnslist['master_list']]][$grpbykey][] = $rowlist[$grpbykey];
        }elseif($grooupbyoprt['grooupbyoperators']=="SUM"){
            $sumarraylist[$rowlist[$columnslist['master_list']]][$grpbykey] += $rowlist[$grpbykey];
        }elseif($grooupbyoprt['grooupbyoperators']=="AVG"){
            $sumarraylist[$rowlist[$columnslist['master_list']]][$grpbykey] += $rowlist[$grpbykey];
        }
    }
}
$stausresult = ViewStatusData();
$filterfileds = "";
foreach($_POST as $fieldn=>$fieldv){
    if(isset($filterlist[$fieldn]) && $fieldv !=""){
        $filterfileds .= "<span class='d-block f-10'>".$filterlist[$fieldn]['label']." : ";
        if($filterlist[$fieldn]['filtertype'] == 'date_pick'){
            $filterfileds .= $fieldv[0]." To ".$fieldv[1];
        }elseif(in_array($filterlist[$fieldn]['filtertype'], $type_array)){
            $filterfileds .= $listoption[$fieldn][$fieldv];
        }else{
            $filterfileds .= $fieldv;
        }
        $filterfileds  .= "</span>";
    }
}
$headers[1] =  $filterfileds;

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
<script type="text/javascript">
    jQuery(document).ready(function() {
    jQuery(".data").hide();
    jQuery(".main").click(function() {
  });
});
    function opensubtable(id){
        var showhide = $("#showhide"+id).val();
        if(showhide=='0'){
            $(".showclass"+id).show('slow');
            $("#showhide"+id).val('1');
        }else{
            $(".showclass"+id).hide('slow');
            $("#showhide"+id).val('0');
        }
    }

    function dynamicSelectDateRange(type,name,field,start="",end=""){
        console.log(type);
        console.log(field);
        $("#btn_"+field).text(name);
        $("#pick_"+field).val(name);
        $("#shortcode_"+field).val(type);
        $(".empty_active").removeClass("active");
        $("#"+type).addClass("active");
        if(name == "Custom Date"){
            $("#custome_"+field).show('slow');

        }else{
            $("#custome_"+field).hide();
            $("#start_"+field).val(start);
            $("#end_"+field).val(end);
        }
    }
</script>



            <?php if(count((array)$filters) !='0'){ ?>
            <div class="card card-custom">
                <div class="card-body">
                <form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
                    <div class="row">
                        <div class="col-lg-10">
                            <div class="row">
                        <?php
                            foreach($filters as $key => $filt) {
                                $ser_field = str_replace('.', '_', $filt['field'] );
                                $name_field = str_replace('.', '||', $filt['field'] ); ?>
                                <?php if($filt['filter_type'] == 'text'){ ?>
                                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile form-group">
                                        <span class="form-label"><?php echo $filt['lable']; ?></span>
                                        <input aria-label="Input field" type="text" class="form-control kt-inpu filterdynamic" data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>" value="<?php echo $_POST[$name_field]; ?>" name="<?php echo $name_field; ?>"  data-col-index="4">
                                    </div>
                                <?php }elseif($filt['filter_type']=='select') {
                                    $selectdata =$filt['listoption'];
                                    ?>
                                    <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile form-group w-full">
                                        <span class="form-label"><?php echo $filt['lable']; ?></span>
                                        <select aria-label="Select option" class="form-control kt-input form-select js-example-basic-single filterdynamic" name="<?php echo $name_field; ?>" data-col-index="2" data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>">
                                            <option  value=""><?php echo "ALL"; ?></option>
                                            <?php foreach($selectdata as $selectkey=>$select){ ?>
                                                <option <?php if($_POST[$name_field] == $selectkey){ ?> selected <?php
} ?> value="<?php echo $selectkey; ?>"><?php echo $select; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php }elseif($filt['filter_type']=='date_pick'){ ?>

                            <div class="col-lg-3">
                                <span class="form-label d-block"><?php echo $filt['lable']; ?></span>
                                <input type="hidden" name="pick_<?php echo $name_field; ?>" id="pick_<?php echo $name_field; ?>" value="<?php echo $_POST['pick_'.$name_field] !=""?$_POST['pick_'.$name_field]:"Today"; ?>">
                                <?php $shortcode = $_POST['shortcode_'.$name_field]!=""?$_POST['shortcode_'.$name_field]:"TD"; ?>
                                <input type="hidden" name="shortcode_<?php echo $name_field; ?>" id="shortcode_<?php echo $name_field; ?>" value="<?php echo $shortcode; ?>">
                            <div class="btn-group dropdown-split-primary" style="width: 100%;">
                                <button type="button" class="btn btn-primary" style="width: 80%;height: 45px;"><i class="ti-calendar"></i>
                                    <span id="btn_<?php echo $name_field; ?>"><?php echo $_POST['pick_'.$name_field] !=""?$_POST['pick_'.$name_field]:"Today"; ?></span>
                                </button>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split waves-effect waves-light" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 20%;height: 45px;">
                                    <span class="visually-hidden" >Toggle primary</span>
                                </button>
                                <div class="dropdown-menu">
                                       <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TD"?"active":"";?> empty_active" id="TD" href="#!" onclick="dynamicSelectDateRange('TD','Today','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-m-d")); ?>','<?php echo fromDate(date("Y-m-d")); ?>')">Today</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TW"?"active":"";?> empty_active" id="TW" href="#!" onclick="dynamicSelectDateRange('TW','This Week','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this week')));?>','<?php echo fromDate(date("Y-m-d", strtotime('monday this sunday ')));?>')">This Week</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TM"?"active":"";?> empty_active" id="TM" href="#!" onclick="dynamicSelectDateRange('TM','This Month','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-m-01")); ?>','<?php echo fromDate(date("Y-m-t")); ?>')">This Month</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="TY"?"active":"";?> empty_active" id="TY" href="#!" onclick="dynamicSelectDateRange('TY','This Year','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-01-01")); ?>','<?php echo fromDate(date("Y-12-31")); ?>')">This Year</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="YD"?"active":"";?> empty_active" id="YD" href="#!" onclick="dynamicSelectDateRange('YD','Yesterday','<?php echo $name_field; ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>','<?php echo fromDate(date('Y-m-d',strtotime("-1 days"))); ?>')">Yesterday</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PW"?"active":"";?> empty_active" id="PW" href="#!" onclick="dynamicSelectDateRange('PW','Previous Week','<?php echo $name_field; ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week monday"))); ?>','<?php echo fromDate(date('Y-m-d', strtotime("previous week sunday"))); ?>')">Previous Week</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PM"?"active":"";?> empty_active" id="PM" href="#!" onclick="dynamicSelectDateRange('PM','Previous Month','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-m-01",strtotime('last month'))); ?>','<?php echo fromDate(date("Y-m-t",strtotime('last month'))); ?>')">Previous Month</a>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="PY"?"active":"";?> empty_active" id="PY" href="#!" onclick="dynamicSelectDateRange('PY','Previous Year','<?php echo $name_field; ?>','<?php echo fromDate(date("Y-01-01",strtotime('last year'))); ?>','<?php echo fromDate(date("Y-12-31",strtotime('last year'))); ?>')">Previous Year</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item waves-light waves-effect <?php echo $shortcode=="CD"?"active":""; ?> empty_active" id="CD" href="#!" onclick="dynamicSelectDateRange('CD','Custom Date','<?php echo $name_field; ?>')">Custom Date</a>
                                </div>
                            </div>
                            <div class="row mt-2" style="display: none" id="custome_<?php echo $name_field; ?>">
                                <div  class="col-lg-6">
                                    <input aria-label="Input field" type="text" class="form-control filterdynamic datepicker" name="<?php echo $name_field; ?>[]" id="start_<?php echo $name_field; ?>" value="<?php echo $_POST[$name_field][0] !=""?$_POST[$name_field][0]:fromDate(date("Y-m-d")); ?>">
                                </div>
                                <div class="col-lg-6">
                                    <input aria-label="Input field" type="text" class="form-control filterdynamic datepicker" name="<?php echo $name_field; ?>[]" id="end_<?php echo $name_field; ?>" value="<?php echo $_POST[$name_field][1] !=""?$_POST[$name_field][1]:fromDate(date("Y-m-d")); ?>">
                                </div>
                            </div>
                            </div>
                                <?php $dateid++;
}elseif($filt['filter_type']=='month' || $filt['filter_type']=='year' || $filt['filter_type'] == 'month_year'){ ?>
                                        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile form-group">
                                            <span class="form-label"><?php echo $filt['lable']; ?></span>
                                            <input aria-label="Input field" type="month" class="form-control filterdynamic"  value="<?php echo $_POST[$name_field]!=""?date('Y-m',strtotime($_POST[$name_field])):""; ?>"  name="<?php echo $name_field; ?>"   data-lable="<?php echo $filt['lable']; ?>" data-type="<?php echo $filt['filter_type']; ?>"/>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                            <div class="col-lg-2 text-right">
                                <button class="btn btn-primary btn-brand--icon" id="kt_search" name="submit"><i class="fa fa-search"></i> Preview</span></span></button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
                    <?php } ?>


            <div class="card card-custom">
                <div class="card-body ">
                    <div class="text-right">

                        <a href="custom_view_download.php?page=<?php echo $page_title; ?>" class="" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Download CSV"><button class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon"><span class="ti-cloud-down"></span></button></a>

                        <a href="custom_view_download.php?page=<?php echo $page_title; ?>" class="m-l-10" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Print"><button class="btn waves-effect waves-dark btn-danger btn-outline-danger btn-icon"><span class="ti-printer"></span></button>
                        </a>
                    </div>
                    <table style="width: 100%;">
                        <thead >
                            <tr>
                                <th scope="col" colspan="<?php echo $tr_count; ?>">
                                    <div class="row" >
                                        <?php  for ($i=1; $i <7; $i++) { ?>
                                            <div class="col-lg-4  <?php echo $header_position[$i]; ?>">
                                                <?php echo $headers[$i]; ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                        <div class=" mt-3 " >
                            <?php if(count((array)$showarraylist)>0){  $grandtotal = array(); ?>

                            <table class="table table-separate table-head-custom table-checkable data-table custom_asign_emp_mpc" >
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <?php $key5= 1; foreach ($dis as $header) {
                                        $printarray[$header['lable']] ="";
                                        ?>
                                        <th scope="col" class="<?php echo $header['heddingpositiontable']; ?>"></th>
                                    <?php } $csvarray[] = $printarray; ?>
                                </tr>
                                <?php foreach ($showarraylist as $masterkey=>$value2){  ?>
                                    <tr class="main">
                                        <td class="cursor-pointer">
                                            <i class="ti-angle-double-down" onclick="opensubtable(<?php echo $key5; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                            <input type="hidden" id="showhide<?php echo $key5; ?>" value="0">
                                        </td>
                                        <td></td>
                                        <?php foreach($dis as $fieldlist){
                                            if($fieldlist['name']==$columnslist['master_list']){
                                                $printarray = array();
                                                $mastervalue = $value2;
                                                $name_field = str_replace('.', '', $fieldlist['name']); ?>
                                        <td>
                                            <span class="form-label label label-lg label-info " ><?php echo count((array)$value2);?></span>

                                        <?php
                                        if ($fieldlist['name'] == 'company_id'){
                                                $mastervalue = $_SESSION['company'][$masterkey];
                                                echo $mastervalue;
                                        }elseif($fieldlist['name'] == 'current_status'){
                                            $mastervalue = $stausresult[$masterkey]['name'];
                                            ?>
                                            <div class="btn-group">
                                                <span  class="form-label label label-primary" style="color: <?php echo "#".$stausresult[$masterkey]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$masterkey]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$masterkey]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $mastervalue; ?> </span>
                                            </div>
                                        <?php }elseif(in_array($fieldlist['type'], $type_array)){
                                            $mastervalue = $listoption[$name_field][$masterkey]==""?"-":$listoption[$name_field][$masterkey];
                                            echo $mastervalue;
                                        }else{
                                            echo $masterkey;
                                        }
                                        $printarray[$fieldlist['lable']] =$mastervalue;
                                        ?>
                                        </td>
                                        <td colspan="<?php echo count((array)$dis)-1; ?>" class="text-right">
                                            <?php foreach($grooupbyopr as $grooupbyoprt){ ?>
                                                <span class=" m-r-20" >
                                                    <?php
                                                    if($grooupbyoprt['grooupbyoperators'] == "MAX"){
                                                        $grpoptotal = "max".MAX($sumarraylist[$masterkey][$grooupbyoprt['feildname']]);
                                                    }elseif($grooupbyoprt['grooupbyoperators'] == "MIN"){
                                                        $grpoptotal = "min".MIN($sumarraylist[$masterkey][$grooupbyoprt['feildname']]);
                                                    }elseif($grooupbyoprt['grooupbyoperators'] == "AVG"){
                                                        $grpoptotal = ($sumarraylist[$masterkey][$grooupbyoprt['feildname']]/$sumarraylist[$masterkey]['total']);
                                                    }else{
                                                        $grpoptotal = $sumarraylist[$masterkey][$grooupbyoprt['feildname']];
                                                    }
                                                    if($grooupbyoprt['filtertype'] == 'number_type'){
                                                        echo "<span class='text-primary'>".$grooupbyoprt['label']." :</span> ".customeummarmonyformate($grpoptotal);
                                                    }else{
                                                        echo "<span class='text-primary'>".$grooupbyoprt['label']." :</span> ".customeummarmonyformateifine($grpoptotal);
                                                    }
                                                    $grandtotal[$grooupbyoprt['label']]['total'] +=$grpoptotal;
                                                    $grandtotal[$grooupbyoprt['label']]['inputtype'] =$grooupbyoprt['filtertype'];
                                                    $printarray[$grooupbyoprt['label']] =$grpoptotal;
                                                    ?>
                                                </span>
                                            <?php } ?>
                                        </td>
                                <?php $csvarray[] =$printarray; ?>
                                <?php } ?>
                                <?php } ?>
                            </tr>
                            <tr class="data showclass<?php echo $key5; ?>">
                                <th scope="col"></th>
                                <th scope="col" class="w-b-w-s">#</th>
                                <?php foreach($dis as $header) {
                                    $after = isset($header['add_custom_field']) && $header['add_custom_field']!='' ? $header['add_custom_field'] :2;
                                    ?>

                                    <th scope="col" class="<?php echo $header['heddingpositiontable']; ?>"><?php echo $header['lable']; ?></th>

                                <?php } ?>
                            </tr>
                            <?php  $m=1; $printarray = array(); foreach($value2 as $datasdetails){
                                ?>
                                <tr class="data showclass<?php echo $key5; ?>">
                                    <td></td>
                                    <td class="w-b-w-s"><?php echo $m; ?></td>
                                    <?php foreach($dis as $fieldlist){
                                        $name_field = str_replace('.', '', $fieldlist['name']);
                                        $mastervalue ="";
                                     if ($fieldlist['name'] == 'company_id') {
                                        $mastervalue = $_SESSION['company'][$datasdetails[$name_field]];
                                        ?>
                                        <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $mastervalue; ?>
                                        </td>
                                    <?php }elseif($fieldlist['name'] == 'current_status') {
                                        $mastervalue = $stausresult[$datasdetails[$name_field]]['name'];
                                        ?>
                                        <td class="<?php echo $fieldlist['heddingpositiontable']; ?>">
                                            <div class="btn-group">
                                                <span  class="form-label label label-primary" style="color: <?php echo "#".$stausresult[$datasdetails[$name_field]]['color']; ?>;    background:  <?php echo "#".strval($stausresult[$datasdetails[$name_field]]['background_color']); ?>; border-color: <?php echo "#".strval($stausresult[$datasdetails[$name_field]]['background_color']); ?>;" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" ><?php echo $mastervalue; ?> </span>
                                            </div>
                                        </td>
                                    <?php }elseif($fieldlist['type'] == 'date_pick'){
                                        $mastervalue = fromDate($datasdetails[$name_field]);
                                        ?>

                                       <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $mastervalue==""?"-":fromDate($mastervalue);?></td>

                                    <?php }elseif($fieldlist['type'] == 'currency'){
                                        $mastervalue = $datasdetails[$name_field];
                                        ?>
                                       <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $mastervalue==""?"-":customeummarmonyformateifine($mastervalue);?></td>

                                    <?php }elseif($fieldlist['type'] == 'number_type'){
                                        $mastervalue = $datasdetails[$name_field];
                                        ?>
                                       <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $mastervalue==""?"-":customeummarmonyformate($mastervalue);?></td>

                                    <?php }elseif(in_array($fieldlist['type'], $type_array)){
                                        $mastervalue = $listoption[$name_field][$datasdetails[$name_field]];
                                        ?>
                                        <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo$datasdetails[$name_field]==""?"-":$mastervalue;?></td>

                                    <?php }else{
                                        $mastervalue = $datasdetails[$name_field];
                                        ?>
                                        <td class="<?php echo $fieldlist['heddingpositiontable']; ?>"><?php echo $mastervalue==""?"-":$mastervalue;
                                        ?></td>
                                    <?php  } ?>
                                <?php $printarray[$fieldlist['lable']] = $mastervalue;
} ?>
                            </tr>
                            <?php $m++;  $csvarray[] = $printarray;
} $key5++;
} ?>
                            <tfoot><tr><td>Grand Total</td>
                                <td colspan="100" class="text-right">
                                    <?php
                                        foreach($grandtotal as $labekey =>$labelvalue){
                                            if($grooupbyoprt['filtertype'] == 'number_type'){
                                                echo "<span class='m-r-20'><span class='text-primary'>".$labekey." :</span> <span>".customeummarmonyformate($labelvalue['total'])."</span></span>";
                                            }else{
                                                echo "<span class='m-r-20'><span class='text-primary'>".$labekey." :</span> <span>".customeummarmonyformateifine($labelvalue['total'])."</span></span>";
                                            }
                                        }
                                    ?>
                                </td></tr>
                            </tfoot>
                        </table>
                    <?php $_SESSION['download_data'] = $csvarray;
}else{ ?>
                        <div class="card-px text-center ">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Result</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                            </div>
                        </div>
                    <?php } ?>

                    </div>
                </div>
            </div>
            <input type="hidden" name="table_name" id="table_name" value="<?php echo $table; ?>">


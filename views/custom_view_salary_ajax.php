<?php
$cal = array();
$code_data['CTC'] = 600000;
$code_data['PAY_CTC'] = 600000;
if($_POST['ajaxpagecall'] == "Y"){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $url = API."?mode=togetemloyeesalarydetails&rid=";
    $response = callAPI($url);
    $resdata = json_decode($response,1);
    $earnings = $resdata['eraning'];
    $res = $resdata['salary_stracture'];
    $employees = $resdata['employees'];
    $noshow = array('NETPAYABLE', 'CTC');
    $code_data['CTC']  = $_POST['CTC'];
}
$esidata = $resdata['esidata'];
$pfdata = $resdata['pfdata'];
$ptalldata = $resdata['ptalldata'];
foreach((array)$earnings as $ern){
    $ern_data[$ern['display_seq']] = $ern;
    $cmp = $ern['code']."_component_number";
    $calculation_number = isset($_POST[$cmp]) ?  $_POST[$cmp] : $ern['calculation_number'];
    $cc_code =$ern['calculation_category'];
    if($ern['calculation_type']=='A'){
        $code_data[$ern['code']] = $calculation_number;
    }
    if($ern['calculation_type']=='P'){

        $base_amount=$code_data[$cc_code];

        $calculation_formula = $base_amount."*".$calculation_number."%";
        $code_data[$ern['code']] = ($base_amount*$calculation_number)/100;
    }
    if($ern['calculation_type']=='F'){
        $calculation_formula =  $ern['calculation_formula'] ;
        $formula_details = json_decode($ern['formula_details'],1) ;
        $count = $formula_details['count'];
        $f_details = $formula_details['details'];
        for($u=0;$u<$count;$u++){
            $fcall = is_numeric($f_details[$u]['lcode']) ? $f_details[$u]['lcode'] : $code_data[$f_details[$u]['lcode']];
            $fcalr = is_numeric($f_details[$u]['rcode']) ? $f_details[$u]['rcode'] : $code_data[$f_details[$u]['rcode']];
            $cals =   calucalationsum($fcall,$fcalr,$f_details[$u]['loperand']);
            if($u>0){
                $calq = calucalationsum($calq,$cals,$f_details[$u-1]['roperand']);
            }else{
                $calq = $cals;
            }
        }
        $code_data[$ern['code']]  = $calq;
    }
    if($ern['calculation_type']=='L'){
        $calculation_link = $ern['calculation_link'];
        if($calculation_link == 'PF'){

            $pf = tocalucalteepf($code_data,$pfdata['employee_contribution'],$pfdata);
            $code_data[$ern['code']]=$pf['amount'];
        }
        if($calculation_link == 'EMPLOYERPF'){
            $pf = tocalucalteepf($code_data,$pfdata['employer_contribution'],$pfdata);
            $code_data[$ern['code']]=$pf['amount'];
        }
        if($calculation_link == 'ESI'){
            $pass_esi_val = array('GS'=>$code_data['GS']);
            echo  "Name: &nbsp;&nbsp; ".$ern['name']."&nbsp;Code: &nbsp; &nbsp; ".$ern['code']."&nbsp; Calculation Type:&nbsp;&nbsp;  ".$ern['calculation_type']."&nbsp; Component Number: &nbsp;&nbsp;  ".$calculation_number."&nbsp; CC Code :&nbsp;&nbsp;  ".$cc_code."&nbsp; Base Amount: &nbsp;&nbsp;  ".$base_amount."<br>";
            print_r($pass_esi_val);
            $pf = tocalucaltesi($code_data,$esidata,$cc_code,'employee_contribution',$pass_esi_val);
            $code_data[$ern['code']]=$pf['amount'];
        }
        if($calculation_link == 'EMPLOYERESI'){
            $pf = tocalucaltesi($code_data,$esidata,$ern['calculation_category'],'employer_contribution',$pass_esi_val);
            $code_data[$ern['code']]=$pf['amount'];
        }
        if($calculation_link == 'PTX'){
            $pf = tocalucaltPTX($code_data,$ptdata['slabs'],$ern['calculation_category'],$pass_esi_val);
            $code_data[$ern['code']]=$pf['amount'];
        }
    }
}
ksort($ern_data);
function calucalationsum($item1,$item2,$option){
    $res ="0";
    if($option=='+'){ $res = $item1+$item2;
}
    if($option=='-'){ $res = $item1-$item2;
}
    if($option=='*'){ $res = $item1*$item2;
}
    if($option=='/'){ $res = $item1/$item2;
}
    return  $res;
}
function tocalucalteepf($code_data,$val,$list=""){
    $basic_faormula = $basic_faormula ?? '';
    $basic = $code_data['BASIC'];
    $da = $code_data['DA']!=""?$code_data['DA']:'0';
    $basicda = $basic+$da;
    $basic_amount = $basicda;
    $cackval = $list['condition_limit'];
    $basicsp = $cackval;
    if($basicda<=$cackval ){
        $sa = $code_data['OA'];
        $basicsp = $basic+$sa;
        if($basicsp<=$cackval ){
            $basic_amount = $basicsp;

            $month_amount = ($basicsp*$val)/100;
        }else{
            $basic_amount = $cackval;
            $month_amount = ($cackval *$val)/100;
        }
    }else{
        $month_amount = ($cackval *$val)/100;
    }
    $return = array('basic_amount'=>$basicsp,'cal_formula'=>$basic_faormula,'amount'=>$month_amount);
    return $return;
}
function tocalucaltesi($code_data,$val,$catcode,$type,$pass_esi_val=""){
    $basic_faormula = $basic_faormula ?? '';
    $catlink_value = $pass_esi_val[$catcode];
    $catlink_esvv = $val;
    $catlink_vv = $catlink_esvv[$type];
    $basic_amount = $catlink_value;
    $catlink_esvv['check_value'] = $catlink_esvv['check_value']!=""?$catlink_esvv['check_value']:21000;
    if( $catlink_esvv['check_value'] >= $catlink_value){
        if($catlink_value>0){
            $month_amount = ($catlink_value*$catlink_vv)/100;
        }else{
            $month_amount =  ($code_data['PAY_CTC']-$code_data['PF'])*((4/104)*($catlink_vv/4));
        }
    }else{
        $month_amount =0;
    }
    $return = array('basic_amount'=>$basic_amount,'cal_formula'=>$basic_faormula,'amount'=>$month_amount);
    return $return;
    }
function tocalucaltPTX($code_data,$val,$catcode,$pass_esi_val=""){
    $month1 = $month1 ?? '';
    $month = $month ?? '';
    $basic_faormula = $basic_faormula ?? '';
    $month1;
    if(isset($pass_esi_val[$catcode])){
        $catlink_value = $pass_esi_val[$catcode];
    }else{
        $catlink_value = $code_data[$catcode];
    }

    $catlink_esvv = json_decode($val,1);
    for($i=0; $i<count((array)$catlink_esvv); $i++){
        $checks = $catlink_esvv[$i];
        if($checks['start_range']<$catlink_value && $checks['end_range']>$catlink_value){
               $month_amount = $checks['months'][intval($month)];
            break;
        }
    }
    $return = array('basic_amount'=>$catlink_value,'cal_formula'=>$basic_faormula,'amount'=>$month_amount);
    return $return;
}
echo "<pre>"; print_r($code_data); echo "</pre>";
?>
<h6 class="f-w-500 m-l-5 m-r-5 bg-gray">Salary Details</h6>
        <div class="card-block">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Earnings</h4>
                            <div class="row align-items-end mt-3">
                                <div class="col-lg-4">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Salary Component </span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Calculation Type </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 ">
                                    <span class="form-label f-w-600 f-14">Amount Monthly </span>

                                </div>
                            </div>
                            <div   id="draggablePanelList" class="eraningclass">
                                <?php foreach((array)$ern_data as $ern){
                                    if($ern['calculation_mode_type']==1 && !in_array($ern['code'],$noshow) && $ern['part_of_ctc']==0){
                                        $read_only = "readonly";
                                        if($ern['calculation_type'] == 'P'){
                                            $codedetail = "% ".str_replace("_", "", strtoupper($ern['calculation_category']));
                                            $read_only = "";
                                        }elseif($ern['calculation_type'] == 'F'){
                                            $codedetail = "Formula";
                                        }elseif($ern['calculation_type'] == 'L'){
                                            $codedetail = "Link";
                                            $read_only = "readonly";
                                        }else{
                                           $codedetail = "Fixed";
                                           $read_only = "";
                                        }
                                        if($ern['code'] == 'PAY_CTC'){
                                            $read_only = "readonly";
                                        }
                                        $show_select = 'N'; $show_text = 'Y';
                                        $e = $ern['id'];
                                ?>
                                <div class="row align-items-end er_row" id="er_<?php echo $e; ?>">
                                    <div class="col-lg-4">
                                        <div class="form-group mt-3">
                                            <input aria-label="Input field" type="text" class="form-control" readonly id="er_name_<?php echo $e; ?>" value="<?php echo $ern['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="input-group input-group-button">
                                            <?php
                                            $component_number = $ern['calculation_number'];
                                            if ($rid!="" && $ern['calculation_type']=='A') {
                                                    $component_number = $placevalue[$ern['id']]['monthly_amount'];
                                                } ?>

                                            <input aria-label="Input field" type="text" <?php echo $read_only; ?> onblur="monthlytoyearlycat(this.value,<?php echo $ern['id'] ?>)" class="form-control text-right width1" value="<?php echo $component_number; ?>" name="<?php echo $ern['code']."_component_number"; ?>" id="<?php echo $ern['code']."_component_number"; ?>" />

                                            <button style="cursor: none; font-size: 12px;" class="btn btn-primary w-100PX f-10" type="button"><?php echo $codedetail; ?> </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-end mb-3">
                                        <input aria-label="0.00" type="text" class="form-control text-right" readonly placeholder="0.00" name="<?php echo $ern['code']."_month"; ?>" value ="<?php echo $placevalue[$ern['id']]['monthly_amount'];?>"  id="<?php echo $ern['code']."_month"; ?>">
                                        <input type="hidden" value="<?php echo $component_number; ?>" name="componet[]" id="<?php echo $ern['code']; ?>" />

                                        <input type="hidden"  value="" name="<?php echo $ern['code']."_year"; ?>" id="<?php echo $ern['code']."_year"; ?>" />

                                    </div>
                                </div>
                                 <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="mb-3">Deductions</h4>
                            <div class="row align-items-end mt-3">
                                <div class="col-lg-4">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Salary Component </span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="">
                                        <span class="form-label f-w-600 f-14">Calculation Type </span>
                                    </div>
                                </div>
                                <div class="col-lg-4 ">
                                    <span class="form-label f-w-600 f-14">Amount Monthly </span>

                                </div>
                            </div>
                            <div   id="draggablePanelList_2" class="eraningclass">
                                <?php foreach((array)$ern_data as $ern){
                                    if($ern['calculation_mode_type']==2 && !in_array($ern['code'],$noshow) && $ern['part_of_ctc']==0){
                                        $read_only = "readonly";
                                        if($ern['calculation_type'] == 'P'){
                                            $codedetail = "% ".str_replace("_", "", strtoupper($ern['calculation_category']));
                                            $read_only = "";
                                        }elseif($ern['calculation_type'] == 'F'){
                                            $codedetail = "Formula";
                                        }elseif($ern['calculation_type'] == 'L'){
                                            $codedetail = "Link";
                                            $read_only = "readonly";
                                        }else{
                                           $codedetail = "Fixed";
                                           $read_only = "";
                                        }
                                        if($ern['code'] == 'PAY_CTC'){
                                            $read_only = "readonly";
                                        }
                                        $show_select = 'N'; $show_text = 'Y';
                                        $e = $ern['id'];
                                ?>
                                <div class="row align-items-end dd_row" id="dd_<?php echo $e; ?>">
                                    <div class="col-lg-4">
                                        <div class="form-group mt-3">
                                            <input aria-label="Input field" type="text" class="form-control" readonly id="dd_name_<?php echo $e; ?>" value="<?php echo $ern['name']; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="input-group input-group-button">
                                            <?php
                                            if ($rid!="" && $ern['calculation_type']=='A') {
                                                    $component_number = $placevalue[$ern['id']]['monthly_amount'];
                                                } ?>
                                            <input aria-label="Input field" type="text" <?php echo $read_only; ?> onblur="monthlytoyearlycat(this.value,<?php echo $ern['id'] ?>)" class="form-control text-right width1" value="<?php echo $component_number; ?>" name="calucation[]" id="<?php echo 'ernam_'.$ern['id']; ?>" aria-describedby="basic-addon2"/>
                                            <button style="cursor: none; font-size: 12px;" class="btn btn-primary w-100PX f-10" type="button"><?php echo $codedetail; ?> </button>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-end mb-3">
                                        <input aria-label="0.00" type="text" class="form-control text-right" readonly placeholder="0.00" name="monthly_amount[]" value ="<?php echo $placevalue[$ern['id']]['monthly_amount'];?>"  id="m_amount_<?php echo $value['id']; ?>">
                                    </div>
                                </div>
                                 <?php }
                                } ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6 mx-auto">
                                    <div class="row align-items-end mt-3" id="draggablePanelList_3">
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                               <input aria-label="Er Name Nt" type="text" class="form-control" readonly id="er_name_nt" value="<?php echo $lsv['NETPAYABLE']['name']; ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-5">
                                            <div class="form-group">
                                                <input aria-label="Er Code Nt" type="text" readonly  id="er_code_nt" value="" class="form-control">
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h6 class="f-w-500 m-l-5 m-r-5 bg-gray">Footer Details</h6>
        <div class="col-lg-12 card-block">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Footer Amount</h4>
                    <div class="row">
                        <div class="col-lg-6 mx-auto">
                            <div class="row align-items-end mt-3" id="draggablePanelList_4">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span class="form-label f-w-600 f-14">Amount</span>
                                        <input aria-label="Ex: 75800.00" type="text" readonly class="form-control" placeholder="Ex: 75800.00">
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <span class="form-label f-w-600 f-14">On Words</span>
                                        <input aria-label="Ex: Seventy five thousand eight hundred" type="text" readonly placeholder="Ex: Seventy five thousand eight hundred" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

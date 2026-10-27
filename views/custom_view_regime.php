<?php
$url = API."?mode=singlerowtabledata&table=it_declaration_settings&fid=is_active&rid=0";
$it_settings = callAPI($url);$itdata = callAPI($iturl);
$it_setting = json_decode($it_settings, 1);
$data["where"] = array('is_active'=> '0');
$data['where_in'] = array($it_setting['new_regmie'],$it_setting['old_regmie']);
$data['whr_in_key'] = "assessment_year";
$jdata = urlencode(json_encode($data2));
$iturl = API."?mode=gettabledetailswithconditions&table=tax_slads&data=".$jdata;
$itdata = callAPI($iturl);
$itdata = json_decode($itdata, 1);
foreach ($itdata as $itda) {
    if($itda['assessment_year']==$it_setting['old_regmie']){
        $oldslad[]=$itda;
    }
    if($itda['assessment_year']==$it_setting['new_regmie']){
        $newslad[]=$itda;
    }
}

    $datawr["where"] = array('employee_id'=>$_SESSION['emp_id']);
        $costdata = urlencode(json_encode($datawr));
        $url = API."?mode=gettabledetailswithconditions&table=salary_template&data=".$costdata;
        $salarydata = callAPI($url);
        $salarydata = json_decode($salarydata,1);
        $salarydata = $salarydata[0];
    $stpdata["where"] = array('is_active'=> '0','salary_template_id'=>$salarydata['id']);
     $stpdata["order"] = "component_cat_id ASC";
     $stpmdata = urlencode(json_encode($stpdata));
    $stpurl = API."?mode=gettabledetailswithconditions&table=salary_template_components&data=".$stpmdata;
     $salarytmpdata = callAPI($stpurl);
     $salarytmpdata = json_decode($salarytmpdata,1);
    foreach($salarytmpdata as $salarytm){
        $scom[$salarytm['component_id']] = $salarytm;
    }
$assessment_year = $_GET['assessment_year']; $version =$_GET['version_no'];
  $vdata80["where"] = array('is_active'=> '0','assessment_year'=>$assessment_year,'emp_id'=>$_SESSION['emp_id'],'version'=>$version);

    $vdata80j = urlencode(json_encode($vdata80));
    $vurl80 = API."?mode=gettabledetailswithconditions&table=emp_it_declaration_80&data=".$vdata80j;
    $vurl80data = callAPI($vurl80);
    $emoitdecl80 = json_decode($vurl80data,1);
    foreach ($emoitdecl80 as  $emoitdecl8) {
      $emp80itdec[$emoitdecl8['particular']] = $emoitdecl8['amount'];
    }
    $conhouserent["where"] = array('is_active'=> '0','assessment_year'=>$assessment_year,'emp_id'=>$_SESSION['emp_id'],'version_no'=>$version);
    $conhousejson = urlencode(json_encode($conhouserent));
    $emphouseurl80 = API."?mode=gettabledetailswithconditions&table=emp_it_declaration_houserent&data=".$conhousejson;
    $emphousedata80 = callAPI($emphouseurl80);
    $emphouse = json_decode($emphousedata80,1);
    $emphousedata  = $emphouse['0'];

    $emporeve80 = API."?mode=gettabledetailswithconditions&table=emp_perevious_details&data=".$conhousejson;
    $empprvedata = callAPI($emporeve80);
    $empprvedata = json_decode($empprvedata,1);
    $empprvedata  = $empprvedata['0'];

?>
<style>
.vrmiddle{
    vertical-align: middle;
}
</style>
<div class="d-flex flex-column-fluid ">
  <div class="container-fluid j-pro">
    <div class="card">
          <div class="card-header table-card-header mb-2">
        <h5> Old Regime - New Regime : <?php echo $_GET['assessment_year']."  ".$_GET['version_no']; ?></h5>
        <div class="card-header-right table-card-header m-r-10 ">
            <b><?php echo $_SESSION['employee']['display_name']; ?></b>
        </div>
    </div>
    <div class="card card-custom">
     <div class="card-body">
    <div>
        <form method="post" action="#">
            <table class="table table-striped- table-bordered table-hover1 table-checkable dataTable no-footer fa-1s" id="invoiceItem">
                <tr class="table-primary text-white">
                    <th scope="col" style="width: 5%;"></th>
                    <th scope="col" style="width: 25%;"><b class="f-w-600">Particulars</b></th>
                    <th scope="col" style="text-align: center; width: 10%;"><b class="f-w-600">Limit</b></th>
                    <th scope="col" style="text-align: center; width: 15%;"> <b class="f-w-600"> Old Regime </b></th>
                    <th scope="col" style="width: 5%;"></th>
                    <th scope="col" style="text-align: center; width: 15%;"> <b class="f-w-600"> New Regime </b></th>
                </tr>

                <tr class="">
                    <td class="vrmiddle border-bottom" rowspan="7" style="width: 5%;"> B1  Salary / Pension</td>
                    <td><b>(i) Gross Salary of Employee(ia + ib + ic) </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($scom['1']['yearly_amount']); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> <?php echo customeummarmonyformate($scom['1']['yearly_amount']); ?></b></td>
                </tr>
                <?php $d1 =  $scom['1']['yearly_amount'];
                      $n1 =  $scom['1']['yearly_amount'];
                 ?>
                <tr class="">

                    <td><b>&nbsp;&nbsp;&nbsp;(a)<?php echo $scom['2']['component_name']; ?>   </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($scom['2']['yearly_amount']); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> <?php echo customeummarmonyformate($scom['2']['yearly_amount']); ?> </b></td>
                </tr>
                <tr class="">

                    <td><b>&nbsp;&nbsp;&nbsp;(b)<?php echo $scom['3']['component_name']; ?>  </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($scom['3']['yearly_amount']); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> <?php echo customeummarmonyformate($scom['3']['yearly_amount']); ?></b></td>
                </tr>
                <tr class="">

                    <td><b>&nbsp;&nbsp;&nbsp;(c) <?php echo $scom['4']['component_name']; ?> </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($scom['4']['yearly_amount']); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($scom['4']['yearly_amount']); ?></b></td>
                </tr>


                <tr class="">

                    <td><b> Allowance to extent exempt Travelling allowance etc.</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate(0); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate(0); ?></b></td>
                </tr>
                <?php
                      $d2 =  0;  $d3 =  50000;
                      $n2 =  0;     $n3 =  0;
                ?>
                <tr class="">

                    <td><b> Standard deduction u/s 16(iv) - Flat Rs. 50,000 to all    </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate(50000) ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> 0 </b></td>
                </tr>
                <tr class="color-default">

                    <td><b class="f-w-600"> Income Under the Head Salaries</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"><?php $bd1= $d1-$d2-$d3; $bn1= $n1-$n2-$n3; echo customeummarmonyformate($bd1);  ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"> <?php echo customeummarmonyformate($bn1); ?> </b></td>
                </tr>

                <tr class="">
                    <td class="vrmiddle border-bottom text-center"  rowspan="3" style="width: 5%;"> B2</td>
                    <td><b>Rent Recived Total in FY, if 'Let Out</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php $d4 = 0; echo customeummarmonyformate($d4); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> <?php $n4 = 0; echo customeummarmonyformate($n4); ?></b></td>
                </tr>
                <tr class="">

                    <td><b>Less (Interest on Home Loan)</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php $d5 = 0; echo $d5; ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b> <?php $n5 = 0; echo $n5; ?> </b></td>
                </tr>
                <tr class="color-default">

                    <td><b class="f-w-600">Income chargeable under the head ‘House Property </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"><?php $bd2= $d4-$d5; $bn2= $n4-$n5; echo customeummarmonyformate($bd2);  ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"> <?php echo customeummarmonyformate($bn2); ?> </b></td>
                </tr>

                <tr class="color-default">
                    <td class="vrmiddle border-bottom bg-white text-center" style="width: 5%;">B3 </td>
                    <td><b> Income from other sources</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b><?php $bd3= 0; $bn3= 0; echo customeummarmonyformate($bd3); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b><?php echo customeummarmonyformate($bn3); ?> </b></td>
                </tr>

                <tr class="color-default">
                    <td class="vrmiddle border-bottom bg-white text-center" style="width: 5%;">B4 </td>
                    <td><b class="f-w-600"> Gross Total Income (B1+B2+B3)        </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"><?php $bd4= $bd1+$bd2+$bd3; $bn4= $bn1+$bn2+$bn3; echo customeummarmonyformate($bd4); ?> </b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php echo customeummarmonyformate($bn4); ?></b></td>
                </tr>
                <tr class="table-active color-info text-white">
                    <td colspan="6"><b class="f-w-600">Deductions and Taxable Total Income</b></td>
                </tr>

                    <?php foreach ($send as $sent) {  ?>
                <tr class="table-active">
                    <td colspan="6"><b class="f-w-600"><?php echo $sent['category_name']; ?></b></td>
                </tr>
            <?php $k=1; foreach ($datails[$sent['id']] as $datail){
            if($emp80itdec[$datail['id']]!="" ){ ?>
                        <tr class="">
                           <td class="" style="width: 2%;"></td>
                           <td class="" style="width: <?php echo $width_len; ?>%;">
                            <?php echo $datail['name']."--".$emp80itdec[$datail['id']]; ?>
                               <span class="mytooltip tooltip-effect-1">
                                                              <span class="tooltip-item2"><i class="ti-info-alt"></i></span>
                                                              <span class="tooltip-content4 clearfix">
                                                                  <span class="tooltip-text2">
                                                                    <?php echo  $datail['remarks']; ?>
                                                                </span>
                                                            </span>

                            </td>
                            <td class="" style="width: 10%;"><?php echo customeummarmonyformate($datail['max_limit']); ?></td>
                            <td class="thheader text-right" style="width: 13%;">
                              <?php $it80amount = $emp80itdec[$datail['id']] !="" ?  $emp80itdec[$datail['id']] : '0.00';
                    echo customeummarmonyformate($it80amount) ; $tlit80amount =  $tlit80amount+$it80amount; ?>
                            </td>
                <td></td>
                            <td class="text-right">0</td>
                        </tr>
                        <?php $k++; ?>
                        <?php } ?>
                        <?php } ?>
                    <?php } ?>

             <tr class="color-default">
                    <td class="vrmiddle text-center bg-white border-top" style="width: 5%;">C1 </td>
                    <td><b class="f-w-600"> Total Deductions (C1)        </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"> <?php $cb1=$tlit80amount; $cn1=0; echo customeummarmonyformate($cb1);?></b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php echo customeummarmonyformate($cn1); ?></b></td>
                </tr>
           <tr class="color-default">
                    <td class="vrmiddle" style="width: 5%;">     </td>
                    <td><b class="f-w-600"> Total Income (B4 - C1)        </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"> <?php $rdt=$bd4-$cb1; $rnt=$bn4-$cn1; echo customeummarmonyformate($rdt); ?></b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php echo customeummarmonyformate($rnt);  ?></b></td>
                </tr>
            <tr class="table-active">
                    <td colspan="6"><b class="f-w-600">Part D - Computation of Tax Payable</b></td>
                </tr>
            <tr class="bg-primary">
                            <td></td>
                            <td class="f-w-600">INCOME TAX SALBS</td>
                            <td class="f-w-600" style="text-align: center;">Old Tax Rate (%)</td>
                                                <td class="f-w-600" style="text-align: center;">Old Tax Amount</td>
                            <td class="f-w-600" style="text-align: center;">New Tax Rate (%)</td>
                            <td class="f-w-600" style="text-align: center;">New Tax Amount</td>
                        </tr>
                        <?php $calslab_total = $rdt;  $cal_intax = 0; $dcal_intax =0; $ncalslab_total = $rnt; $ncal_intax = 0;
                foreach($oldslad as $skey=>$osal){
                $dcal_amount_dis =0; $ncal_amount_dis =0;
                $nsal = $newslad[$skey];
                if($osal['slad_from'] < $calslab_total){
                if( $osal['slad_from'] < $calslab_total && $calslab_total < $osal['slad_to'] ){
                          $dcal_amount = $calslab_total - ($osal['slad_from']-1);
                     $dcal_amount_dis = $dcal_amount * ($osal['rate']/100);
                       }else{
                         $dcal_amount = $osal['slad_to'] - ($osal['slad_from']-1);
                          $dcal_amount_dis = $dcal_amount * ($osal['rate']/100);
                      }
                }
                $dcal_intax = $dcal_intax + $dcal_amount_dis;
                if($nsal['slad_from'] < $ncalslab_total){
                if( $nsal['slad_from'] < $ncalslab_total && $ncalslab_total < $nsal['slad_to'] ){
                          $ncal_amount = $ncalslab_total - ($nsal['slad_from']-1);
                     $ncal_amount_dis = $ncal_amount * ($nsal['rate']/100);
                       }else{
                         $ncal_amount = $nsal['slad_to'] - ($nsal['slad_from']-1);
                          $ncal_amount_dis = $ncal_amount * ($nsal['rate']/100);
                      }
                }
                $ncal_intax = $ncal_intax + $ncal_amount_dis;
            ?>
                        <tr>
                <td></td>
                            <td><?php echo $osal['slad_from']." - ".$osal['slad_to']; ?></td>
                            <td><?php echo $osal['rate']; ?></td>
                <td class="text-right"><?php echo customeummarmonyformate($dcal_amount_dis); ?></td>
                            <td><?php echo $newslad[$skey]['rate']; ?></td>
                            <td class="text-right"><?php echo customeummarmonyformate($ncal_amount_dis); ?></td>
                        </tr>
                    <?php } ?>
             <tr class="color-default">
                    <td class="vrmiddle" style="width: 5%;">     </td>
                    <td><b class="f-w-600"> Net Tax Payable        </b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"> <?php echo customeummarmonyformate($dcal_intax); ?></b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php echo customeummarmonyformate($ncal_intax); ?></b></td>
                </tr>

            <?php $todl = $dcal_intax; $tndl = $ncal_intax;
                if($it_setting['education_cess']==0){ ?>
            <tr class="">
                    <td class="vrmiddle" style="width: 5%;">     </td>
                    <td><b> Education Cess </b></td>
                    <td><b><?php echo $it_setting['education_cess_precentage']; ?>%</b></td>
                    <td class="text-right"> <b> <?php $todl = $dcal_intax*($it_setting['education_cess_precentage']/100); echo customeummarmonyformate($todl); ?></b></td>
                    <td></td>
                    <td class="text-right"> <b><?php $tndl = $ncal_intax*($it_setting['education_cess_precentage']/100); echo customeummarmonyformate($tndl); ?></b></td>
                </tr>
            <?php } ?>
            <tr class="bg-info">
                    <td class="vrmiddle" style="width: 5%;">     </td>
                    <td><b class="f-w-600"> Total Tax Payable</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"> <?php echo customeummarmonyformate($todl); ?></b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php echo customeummarmonyformate($tndl); ?></b></td>
                </tr>
            <tr class="bg-info">
                    <td class="vrmiddle" style="width: 5%;">     </td>
                    <td><b class="f-w-600"> FINAL TAX Difference</b></td>
                    <td><b></b></td>
                    <td class="text-right"> <b class="f-w-600"> <?php if($todl>$tndl){ echo customeummarmonyformate($todl-$tndl);
} ?></b></td>
                    <td></td>
                    <td class="text-right"> <b class="f-w-600"><?php if($tndl>$todl){ echo customeummarmonyformate($tndl-$todl);
} ?></b></td>
                </tr>

                </table>
                </div>
            </form>
        </div>
</div>
 <form method="post" action="" >
         <?php $m_total = $rdt; ?>
    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
    <input type="hidden" name="emp_id" value="<?php echo $_SESSION['emp_id']; ?>">
    <input type="hidden" name="assessment_year" value="<?php echo $_GET['assessment_year']; ?>">
    <input type="hidden" name="taxable_income" value="<?php echo $maindata['potential_amount']; ?>">
    <input type="hidden" name="annual_tax" value="<?php echo $m_total; ?>">
    <input type="hidden" name="declaration_tax" value="<?php echo $m_total; ?>">
    <input type="hidden" name="gap_tax" value="<?php echo $maindata['potential_amount']-$m_total; ?>">
    <input type="hidden" name="it_plan" value="<?php echo $_GET['IT_PLAN']; ?>">
    <input type="hidden" name="version_no" value="<?php echo $_GET['version_no']; ?>">
    <input type="hidden" name="is_active" value="0">
    <input type="hidden" name="current_status" value="0">
    <input type="hidden" name="it_declaration" value="it_declaration">

    <button type="submit" name="Submit" class="btn btn-primary waves-effect waves-light text-right" value="Submit">Submit</button>
  </form>
</div>
</div>
</div>

<?php
    $vdata1["where"] = array('company_id'=> $_SESSION['company_id'],'is_active'=>'0','employee_type'=>1);
    $vdata = urlencode(json_encode($vdata1));
    $vurl = API."?mode=gettabledetailswithconditions&table=employees&data=".$vdata;
    $configesponse = callAPI($vurl);
    $employees = json_decode($configesponse,1);

    $main_ids = encodeId($_GET['Rid']);
    $main_id = explode(":",$main_ids);
    $datawr["where"] = array('is_active'=> '0','id'=>$main_id['1']);
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
    $datacat["where"] = array('is_active'=> '0');
    $catdata = urlencode(json_encode($datacat));
    $caturl = API."?mode=gettabledetailswithconditions&table=salary_categories&data=".$catdata;
    $salarycatdata = callAPI($caturl);
    $salarycatdata = json_decode($salarycatdata,1);
    foreach ($salarycatdata as $cate) {
        $cat[$cate['id']] = $cate['name'];
    }

    foreach ($employees as $employee) {
        $empdata[$employee['id']] = $employee['display_name'];
    }
    ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">

                <div class="card-header table-card-header">
                    <h5><?php echo "Salary Template";  ?></h5>
                    <div class="card-header-right table-card-header ">

                     <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
            </div>
        <div class="card-body">
            <div class="row">
                <div class="row align-items-center" style="margin-top: 10px;padding: 0px 10%;" id="printarea">

                    <div  class="col-lg-12" style="page-break-before: always;">
                        <table style="border: 1px solid #000;width: 100%;padding: 5px; margin: 20px 0px; margin-left: auto; margin-right: auto;" role="presentation">
                            <tr style="height: 50px;border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;"><b>Name</b></td>
                                <td  style="border:1px solid #000;padding: 5px;"colspan="2">&nbsp;<?php echo  $empdata[$salarydata['employee_id']]; ?></td>
                            </tr>

                            <tr style="border: 1px solid #000;">
                                <td style="border:1px solid #000;padding: 5px;"><b>COMPENSATION DETAILS</b>             </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>MONTHLY</b>       </td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><b>YEARLY</b>    </td>
                            </tr>
                            <?php $nm=0; foreach ($salarytmpdata as  $stvalue) { ?>
                                <?php if($stvalue['component_cat_id'] !=$nm ){ $nm =$stvalue['component_cat_id'];  ?>
                                <tr style="border: 1px solid #000;" >
                                    <td style="font-size: 14px;border:1px solid #000;padding: 5px;">&nbsp;  <?php echo $cat[$nm];?></td>
                                    <td style="border:1px solid #000;padding: 5px;"> </td>
                                    <td style="border:1px solid #000;padding: 5px;">  </td>
                                </tr>
                            <?php } ?>
                            <tr style="border: 1px solid #000;" >
                                <td style="border:1px solid #000;padding: 5px;" > &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $stvalue['component_name'];?></td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"> <?php echo customeummarmonyformate($stvalue['monthly_amount']);?></td>
                                <td style="text-align: center;border:1px solid #000;padding: 5px;"><?php echo customeummarmonyformate($stvalue['yearly_amount']);?></td>
                            </tr>
                        <?php } ?>

                        <tr style="border: 1px solid #000;" >
                            <td colspan="3" style="border:1px solid #000;padding: 5px;">&nbsp;  Notes: </br>
                                <p style="padding: 0px 5%;"><?php echo  $salarydata['note']; ?></p>
                            </td>

                        </tr>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>
</div>


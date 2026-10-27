<?php
$year = date('Y');
$month = date('m');
$apiurl = API."?mode=reportofyearmonthalldetails&year=".$year."&admin_type=".$_SESSION['admin_type'];
$apicallss = callAPI($apiurl);
$result = json_decode($apicallss,1);
    $sdate = $year."-01-01";

$farray = array('head_count'=>'Head Count','addtion_head_count'=>'Addtion to Head Count','re_joinee'=>'Re-Joinee','revoke'=> 'Revoke resignation', 'internal_transfer'=>'Internal Transfer', 'deletion_head_count'=>'Deletion to Head Count', 'promotion'=>'Promotion', 'revision_salary'=>'Revision on Salary','revision_promotion'=> 'Revision+Promotion', 'salary_correction'=>'Salary Correction', 'lop'=>'LOP for the Month', 'lop_reversal'=>'LOP reversal', 'location_transfer'=>'Location Transfer', 'stop_salary'=>'Stop Salary');
$ftarray = array('tag'=>'Per EE TAG','gross'=>'Per EE Gross Sal','tax'=>'Per EE TAX', 'tax_gross'=>'TAX to Gross Salary', 'net'=>'Per EE Net Pay');
$menus['0']['name']= 'Last 12 Months';
$menus['0']['code']= 'Last';
$menus['0']['data']=  array('0'=>'Last 12 Months');
$year = date('Y');
$year = $year-6;
for($i=1;$i<7;$i++){
    $array[] = $year+$i;
}
$menus['1']['name']= 'Assessment Year';
$menus['1']['data']=  $array;
$menus['1']['code']= 'ASS';
$menus['2']['name']= 'Yearly';
$menus['2']['data']=  $array;
$menus['2']['code']= 'YEAR';

?>
<script type="text/javascript">
    function donloadall(){
      var url = 'custom_view_download.php?page=Salary Summary&data=';
      window.open(url, '_blank').focus();
    }
</script>
<div class="card">
    <div class="card-header">
        <h5>Salary Summary</h5>
        <div class="card-header-right table-card-header">
            <button type="submit" class="btn btn-primary waves-effect waves-light  mb-2 submitattendancess" onclick="donloadall()"   data-container="body" data-toggle="tooltip" data-placement="bottom" title=""> Download</button>

            <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>

        </div>
    </div>
    <div class="card-body m-r-50 m-l-50"><div>
        <div class="row">
            <div class="col-lg-3">
              <nav class="all-employee m-b-10" style="margin-left: -12px;">
                <ul>
                  <li class="d-block border dropbtn"><a href="#">Filter</a>
                    <ul>
                      <?php foreach((array)$menus as $mkey=>$menu){ ?>
                        <li><a href="#"><?php echo $menu['name']; ?> <i class="ti-angle-right float-end f-12 m-t-5"></i></a>
                            <ul>
                                <?php foreach((array)$menu['data'] as  $fkey=>$menuvalue) { ?>
                                <li>
                                    <div class="border-checkbox-section">
                                        <div class="border-checkbox-group border-checkbox-group-primary">
                                            <a href="index.php?option=COM_VIEW_HR_MONTHlY_REPORT&TYPE=<?php echo $menu['code']; ?>&YEAR=<?php echo $menuvalue; ?>">
                                            <label class="form-label " for="<?php echo $mkey."_".$fkey; ?>"><?php echo $menuvalue;?></label> </a>
                                        </div>
                                    </div>
                                 </li>
                                <?php } ?>
                            </ul>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                </ul>
            </nav>
            </div>
        </div>
        <div class="row">
             <table class="table table-styling">
                 <thead>
                     <tr class="bg-primary">
                        <th scope="col"> Particulars </th>
                        <?php $head_data = array(); $download_data = array();
                            $download_data['0'][] = 'Particulars';
                            for($i=0; $i<12; $i++){ ?>
                            <th scope="col"> <?php $download_data['0'][] = date('M Y', strtotime($sdate));
                                     echo date('M Y', strtotime($sdate)); ?></th>
                        <?php
                            $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                          }
                        ?>
                     </tr>
                 </thead>
                 <tbody>

                        <?php $k=1; foreach((array)$farray as $fkey=>$fdis){ ?>
                            <tr>
                            <td class="bg-info"><?php echo ucwords(strtoupper($fdis)); ?></td>
                            <?php $download_data[$k][] = ucwords(strtoupper($fdis));
                            for($i=0; $i<12; $i++){

                                $nn = date('n', strtotime($sdate));

                                $ndata = isset($result[$fkey][$nn]) ? $result[$fkey][$nn] : 0 ;
                                $download_data[$k][] = customeummarmonyformateround($ndata);
                                ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?></td>
                                <?php   $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                            } $k++;
                            ?>
                            </tr>
                        <?php } ?>
                            <tr> <td colspan="13"></td></tr>
                         <?php $f_total =array();
                            foreach((array)$result['earn_data']['1'] as $sdis){ ?>
                            <tr>
                            <td class="bg-warning"><?php echo ucwords(strtoupper($sdis['payslip_name'])); ?></td>
                            <?php $download_data[$k][] = ucwords(strtoupper($sdis['payslip_name']));
                            for($i=0; $i<12; $i++){

                                $nn = date('n', strtotime($sdate));
                                $ndata = isset($result['paydata'][$nn][$sdis['id']]) ? $result['paydata'][$nn][$sdis['id']] : 0 ;
                                $f_total[$i] = $f_total[$i]+$ndata;
                                $download_data[$k][] = customeummarmonyformateround($ndata);
                                ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?> </td>
                                <?php  $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                            } $k++;
                            ?>
                            </tr>
                        <?php }
                        ?>
                        <tr class="bg-info">
                            <td>  TAG DIFF  </td>
                        <?php  $download_data[$k][] = 'TAG DIFF';
                             foreach((array)$f_total as $ftotal){
                                $download_data[$k][] = customeummarmonyformateround($ftotal);
                         ?>
                                <td> <?php echo customeummarmonyformateround($ftotal); ?> </td>
                        <?php   } $k++; ?>
                         </tr>
                        <?php $s_total = $f_total;
                            foreach((array)$result['earn_data']['3'] as $sdis){ ?>
                            <tr>
                            <td class="bg-warning"><?php echo ucwords(strtoupper($sdis['payslip_name'])); ?></td>
                            <?php $download_data[$k][] = ucwords(strtoupper($sdis['payslip_name']));
                            for($i=0; $i<12; $i++){

                                $nn = date('n', strtotime($sdate));
                                $ndata = isset($result['paydata'][$nn][$sdis['id']]) ? $result['paydata'][$nn][$sdis['id']] : 0 ;
                                $download_data[$k][] = customeummarmonyformateround($ndata);
                                $s_total[$i] = $s_total[$i]+$ndata;
                                ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?> </td>
                                <?php  $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                            }$k++;
                            ?>
                            </tr>
                        <?php } ?>
                         <tr class="bg-info">
                            <td>   TOTAL GROSS  </td>
                        <?php  $download_data[$k][] = 'TOTAL GROSS ';

                             foreach((array)$s_total as $ftotal){
                                $download_data[$k][] = customeummarmonyformateround($ftotal);
                         ?>
                                <td> <?php echo customeummarmonyformateround($ftotal); ?> </td>
                        <?php   } $k++; ?>
                         </tr>
                        <?php foreach((array)$result['earn_data']['2'] as $sdis){ ?>
                            <tr>
                            <td class="bg-warning"><?php echo ucwords(strtoupper($sdis['payslip_name'])); ?></td>
                            <?php $download_data[$k][] = ucwords(strtoupper($sdis['payslip_name']));
                            $t_total = array();
                            for($i=0; $i<12; $i++){

                                $nn = date('n', strtotime($sdate));
                                $ndata = isset($result['paydata'][$nn][$sdis['id']]) ? $result['paydata'][$nn][$sdis['id']] : 0 ;
                                if($sdis['id']==18){
                                     $tax_total[$i] = $ndata;
                                }
                                $download_data[$k][] = customeummarmonyformateround($ndata);
                                $t_total[$i] = $t_total[$i]+$ndata;
                                ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?> </td>
                                <?php $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                            }$k++;
                            ?>
                            </tr>
                        <?php } ?>
                        <tr class="bg-info">
                            <td>  TOTAL DEDUCTIONS  </td>
                        <?php  $download_data[$k][] = ' TOTAL DEDUCTIONS ';

                             foreach((array)$t_total as $ftotal){
                                $download_data[$k][] = customeummarmonyformateround($ftotal);
                         ?>
                                <td> <?php echo customeummarmonyformateround($ftotal); ?> </td>
                        <?php   } $k++; ?>
                         </tr>
                         <tr class="bg-success">
                            <td>  NET PAY </td>
                        <?php  $download_data[$k][] = 'NET PAY';

                             foreach((array)$t_total as $nkey=>$ftotal){
                                $ndata = $s_total[$nkey]-$ftotal;
                                $n_total[$nkey] = $ndata;
                                $download_data[$k][] = customeummarmonyformateround($ndata);
                         ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?> </td>
                        <?php   } $k++; ?>
                         </tr>
                        <tr> <td colspan="13"></td></tr>
                        <tr> <td colspan="13"></td></tr>
                        <tr> <td colspan="13"></td></tr>
                        <?php
                         foreach ((array)$ftarray as $key => $far) {?>
                        <tr class="bg-info">
                            <td>  <?php echo $far; ?>  </td>

                            <?php
                            $download_data[$k][] = ucwords(strtoupper($far));
                            for($i=0; $i<12; $i++){

                                $nn = date('n', strtotime($sdate));
                                if($key=='tag'){
                                    if($result['head_count'][$nn]!="" && $result['head_count'][$nn]!=0){
                                        $ndata = $f_total[$i]/$result['head_count'][$nn];
                                    }else{
                                        $ndata = 0;
                                    }
                                 }
                                if($key=='gross'){
                                    if($result['head_count'][$nn]!="" && $result['head_count'][$nn]!=0){
                                        $ndata = $s_total[$i]/$result['head_count'][$nn];
                                    }else{
                                        $ndata = 0;
                                    }

                                 }
                                if($key=='tax'){
                                 $ndata = $tax_total[$i];
                                }
                                if($key=='tax_gross'){
                                    if($tax_total[$i] !="0" && $s_total[$i]!=0){
                                        $ndata = ($tax_total[$i]/ $s_total[$i])*100;
                                    }else{
                                        $ndata = 0;
                                    }
                                }

                                $download_data[$k][] = customeummarmonyformateround($ndata);
                                ?>
                                <td> <?php echo customeummarmonyformateround($ndata); ?> </td>
                                <?php  $sdate = date('Y-m-d', strtotime('+1 month', strtotime($sdate)));
                            } $k++;
                            ?>

                         </tr>
                         <?php } ?>
                 </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$_SESSION['download_data'] = $download_data;
$_SESSION['head_data']= $head_data;

?>

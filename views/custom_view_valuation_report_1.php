<?php
$_SESSION['apply_js']['multiselest'] = 1;
// $getrid =  encodeId($_GET['Rid']);
// $decodrid = urldecode($getrid);
// $spitrid = explode(':', $decodrid);
// $rid = $spitrid['1'];
$url1 = API."?mode=getesopreports&type=ALL";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$companyvallist = $result['company_valuations_all'];
$contribution_users_type = $result['contribution_users_type'];
$invester_type = $result['invester_type'];
$lastpercentage = $result['percentagecheck'];
$utype =$itype =  array();
foreach($contribution_users_type as $cut){
	$utype[$cut['genc_detail_code']] = $cut['genc_detail_name'];
}
foreach($invester_type as $it){
	$itype[$it['genc_detail_code']] = $it['genc_detail_name'];
}
$cvdlist = $result['company_valuation_distributions'];
$total_amount = $otal_equity_shares_amount =$total_preference_shares_amount =$total_equity_shares_qty =$total_preference_shares_qty= 0;
//$companyvaluationyearly = array();
//$yearary = array();
// foreach($companyvallist as $cvl){
//     $year = date("Y",strtotime($cvl['valuation_date']));
//     $companyvaluationyearly[$year][$cvl['id']] = $cvl;
//     $yearary[$year] = $year; 
// }
// ksort($companyvaluationyearly);

// $esop_allocations = $result['esop_allocations'];
// $esopallocations = array();
// foreach($esop_allocations as $cvl){
//     $year = date("Y",strtotime($cvl['date_of_grant']));
//     $esopallocations[$year][$cvl['id']] = $cvl;
//     $yearary[$year] = $year; 
// }
// ksort($esopallocations);
// ksort($yearary);
///echo "<pre>";print_r($result);print_r($lastpercentage);echo "</pre>";

$finaltotal_shares =0; 
foreach($companyvallist as $cvresult){
    foreach($cvdlist[$cvresult['id']] as $cvd){ 
        if($cvd['dilution'] == '0'){
            $old_shares_qty = 0;
            $new_shares_qty = $cvd['new_shares'];
            $finaltotal_shares += $new_shares_qty;
           // $finaltotal_shares = $new_shares_qty;
        }else{
            $new_shares_qty = 0;
            $old_shares_qty =$cvd['total_shares'];
            $finaltotal_shares += $old_shares_qty;
            //$finaltotal_shares = $old_shares_qty;
        }
    }    

}
//echo "finaltotal_shares - ".$finaltotal_shares;
?>
<script type="text/javascript">
    function getValuationDetails(id){
        window.location.href = "index?option=COM_VIEW_PSP&Rid="+id;
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5>CAP Table</h5>
               <!--  <div class="card-header-right table-card-header">
                    <div class="w-full">
                    <select class ="form-control js-example-basic-single" id="reimbursementsyear" onchange="getValuationDetails(this.value)">
                        <optin vlaue="">Select Any One</optin>
                  <?php foreach ($companyvallist as $valuation) { 
                        $rrid = deocdeId('id:'.$valuation['id']);
                   ?>
                    <option  value="<?php echo $rrid; ?>" <?php echo $valuation['id']==$rid?"selected":"";?>>
                      <?php echo $valuation['valuation_name']; ?>
                    </option>
                    <?php
                   
                 } ?> 
               </select>
           </div>
                </div> -->
            </div>
            <div class="card-body">
                <div class="table-responsive custom-nowrap">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th class="text-capitalize">Investor</th>
                            <th colspan="4" class="text-center text-capitalize">Diluted And Can't Be Diluted Tab</th>
                            <th colspan="2" class="text-center text-capitalize">Issued Share</th>
                            <th colspan="3" class="text-center text-capitalize">% Age</th>
                        </tr>
                        <tr>
                            <td></td>
                            <th class="f-14 text-right text-capitalize">Could Be Diluted</th>
                            <th class="f-14 text-right text-capitalize">Can't Be Diluted</th>
                            <th class="f-14 text-right text-capitalize">Can't Be Diluted<br />% Age On Total</th>
                            <th class="f-14 text-right text-capitalize">Total</th>
                            <th class="f-14 text-right text-capitalize">No. of Equity<br />Shares</th>
                            <th class="f-14 text-right text-capitalize">No. of Preference<br />Shares</th>
                            <th class="f-14 text-right text-capitalize">Equity(%)</th>
                            <th class="f-14 text-right text-capitalize">Preference(%)</th>
                            <th class="f-14 text-right text-capitalize">Total(%)</th>
                        </tr>
                        <?php $sharedis =array(); foreach($companyvallist as $cvresult){ ?>
                        <tr >
                            <th colspan="10" class="pt-0 pb-0">
                                <a href="index?option=COM_VIEW_PSP&Rid=<?php echo deocdeId('id:'.$cvresult['id']); ?>" class="text-primary" target="_blank">
                                <?php echo $cvresult['valuation_name']; ?></br>
                            	<span class="f-w-600 text-capitalize">Date - </span>
                                <span class="m-r-10 f-w-500"><?php echo $cvresult['valuation_date'] !=""?date('M d-Y',strtotime($cvresult['valuation_date'])):"-";?></span>
                                </a>
                            </th>
                        </tr>


                        <?php  foreach($cvdlist[$cvresult['id']] as $cvd){ 
                            if($cvd['dilution'] == '0'){
                                $old_shares_qty = 0;
                                $new_shares_qty = $cvd['new_shares'];
                                $total_amount += $new_shares_qty;
                                $total_shares = $new_shares_qty;
                            }else{
                                $new_shares_qty = 0;
                                $old_shares_qty =$cvd['total_shares'];
                                $total_amount += $old_shares_qty;
                                $total_shares = $old_shares_qty;
                            }
                            

// if(!isset($sharedis['new_shares_qty'][$cvd['company_contribution_user_id']]) AND $new_shares_qty >0){
//     $sharedis['new_shares_qty'][$cvd['company_contribution_user_id']]=$new_shares_qty;
// }elseif($new_shares_qty >0){
//     $sharedis['new_shares_qty'][$cvd['company_contribution_user_id']] =$sharedis['new_shares_qty'][$cvd['company_contribution_user_id']] - $new_shares_qty;
// }
// if(!isset($sharedis['old_shares'][$cvd['company_contribution_user_id']]) AND $old_shares_qty >0){
//     $sharedis['old_shares'][$cvd['company_contribution_user_id']]=$old_shares_qty;
// }elseif($old_shares_qty > 0){
//     $sharedis['old_shares'][$cvd['company_contribution_user_id']] =$sharedis['old_shares'][$cvd['company_contribution_user_id']] - $old_shares_qty;
// }

// if($cvd['dilution'] == '0'){
//     $old_shares_qty = 0;
//     //$new_shares_qty = $sharedis['new_shares_qty'][$cvd['company_contribution_user_id']];
//     $total_amount += $new_shares_qty;
//     $total_shares = $new_shares_qty;
// }else{
//     $new_shares_qty = 0;
//     $old_shares_qty =$cvd['total_shares'];
//     $total_amount += $old_shares_qty;
//     $total_shares = $old_shares_qty;
// }

                         //   $sharedis['old_shares_qty'][$cvd['company_contribution_user_id']]=$old_shares_qty;


                            // if(!isset($checkrepet[$cvd['company_contribution_user_id']])){
                            //     $checkrepet[$cvd['company_contribution_user_id']] = $cvd['company_contribution_user_id'];
                            //     $lastpercentageval = $lastpercentage[$cvd['company_contribution_user_id']]; 
                            // }else{
                            //     $lastpercentageval ='0';
                            // }
                          // $lastpercentageval = $lastpercentage[$cvd['company_contribution_user_id']]; 

                            $lastpercentageval = ($total_shares/$finaltotal_shares)*100;
                            $total_new_shares_qty += $new_shares_qty;
                            $total_old_shares_qty += $old_shares_qty;
                            $distribute_percentage += $lastpercentageval;

                            $notbedilutedpercentageval = ($new_shares_qty/$finaltotal_shares)*100;
                            $totalnotbedilutedpercentageval +=$notbedilutedpercentageval;
                            $total_share += $total_shares;

                            $equity_shares_qty = round(($cvd['equity_shares_percentage']/100)*$total_shares);
                            $total_equity_shares_qty += $equity_shares_qty;


                            $preference_shares_qty = round(($cvd['preference_shares_percentage']/100)*$total_shares);
                            $total_preference_shares_qty += $preference_shares_qty;

                            ?>
                        <tr>
                            <th class="p-2">
                                <?php //echo "<pre>"; print_r($sharedis); echo "</pre>";?>
                            	<?php echo $cvd['distributer_name']; ?>
                            	<span class="d-block f-w-500"><?php echo $utype[$cvd['user_type']]; ?> (<?php echo $itype[$cvd['investor_type']]; ?>)</span>
                            </th>

                            <td class="text-right p-2"><?php echo round($old_shares_qty); ?></td>
                            <td class="text-right p-2"><?php echo round($new_shares_qty); ?></td>
                           
                            <td class="text-right p-2"><?php echo round($notbedilutedpercentageval,2); ?>%</td>
                            <td class="text-right p-2"><?php echo round($total_shares); ?></td>
                            <td class="text-right p-2"><?php echo round($equity_shares_qty); ?></td>
                            <td class="text-right p-2"><?php echo round($preference_shares_qty); ?></td>
                             
                            <td class="text-right p-2"><?php echo round($cvd['equity_shares_percentage'],2); ?></td>
                            <td class="text-right p-2"><?php echo round($cvd['preference_shares_percentage'],2); ?></td>
                            <td class="text-right p-2"><?php //echo customeummarmonyformateifine($cvd['distribute_percentage']); 
                          echo round($lastpercentageval,2); ?>%</td>
                        </tr>
                        <?php } ?>
                        <?php } ?>
                        
                        <?php  /** ?>
                         <tr>
                            <th>ESOP</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                            <th>-</th>
                        </tr> <?php  **/ ?>
                         <tr>
                            <th class="text-capitalize">Total</th>
                            <th class="text-right "><?php echo $total_old_shares_qty;?></th>
                            <th class="text-right"><?php echo $total_new_shares_qty; ?></th>
                            
                            <th class="text-right"><?php echo round($totalnotbedilutedpercentageval,2); ?>%</th>
                            <th class="text-right"><?php echo round($total_share); ?></th>
                            
                            <!-- <th><?php //echo customeummarmonyformateifine($total_equity_shares_amount); ?></th>
                            <th><?php //echo customeummarmonyformateifine($total_preference_shares_amount); ?></th> -->
                            <th class="text-right"><?php echo $total_equity_shares_qty; ?></th>
                            <th class="text-right"><?php echo $total_preference_shares_qty; ?></th>
                             <th class="text-right">-</th>
                            <th class="text-right">-</th>
                            <th class="text-right"><?php echo $distribute_percentage; ?>%</th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

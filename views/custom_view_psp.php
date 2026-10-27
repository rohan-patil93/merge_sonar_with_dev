<?php
$_SESSION['apply_js']['multiselest'] = 1;
$getrid =  encodeId($_GET['Rid']);
$decodrid = urldecode($getrid);
$spitrid = explode(':', $decodrid);
$rid = $spitrid['1'];
$url1 = API."?mode=getesopreports&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$companyvallist = $result['company_valuations_all'];
$company_valuations = $result['company_valuations'];
$cvdlist = $result['company_valuation_distributions'];
$total_amount = $otal_equity_shares_amount =$total_preference_shares_amount =$total_equity_shares_qty =$total_preference_shares_qty= 0;

?>
<script type="text/javascript">
    function getValuationDetails(id){
        if(id !=""){
            window.location.href = "index?option=COM_VIEW_PSP&Rid="+id;
        }
    }
</script>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header ">
                    <h5>Valuation Report </h5>
                    <div class="card-header-right table-card-header reimbursementsyear">
                            <select aria-label="Reimbursementsyear" class ="form-control js-example-basic-single" id="reimbursementsyear" onchange="getValuationDetails(this.value)">
                                <option value="">Select Any One</option>
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
            </div>
            <div class="card-body">
                <?php if(count((array)$company_valuations) !=0){?>
                <div class="table-responsive custom-nowrap">
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th scope="col">Investor</th>
                            <th scope="col" colspan="4" class="text-center">Diluted and can't be diluted tab</th>
                            <th scope="col" colspan="2" class="text-center">Issued Share</th>
                            <th scope="col" colspan="3" class="text-center">% Age</th>
                        </tr>
                        <tr>
                            <td></td>
                            <th scope="col" class="f-14 text-right">Could be diluted</th>
                            <th scope="col" class="f-14 text-right">can't be diluted</th>
                            <th scope="col" class="f-14 text-right">can't be diluted<br />% age on total</th>
                            <th scope="col" class="f-14 text-right">Total</th>
                            <th scope="col" class="f-14 text-right">No. of Equity<br />Shares</th>
                            <th scope="col" class="f-14 text-right">No. of Preference<br />Shares</th>
                            <th scope="col" class="f-14 text-right">Equity Shares</th>
                            <th scope="col" class="f-14 text-right">Preference(%)</th>
                            <th scope="col" class="f-14 text-right">Total(%)</th>
                        </tr>
                        <tr>
                            <th scope="col">Valuation Details</th>
                            <td colspan="9" >
                                <span class="f-w-600">Name - </span><span class="m-r-10"><?php echo $company_valuations['valuation_name']; ?></span>
                                <span class="f-w-600">Date - </span>
                                <span class="m-r-10"><?php echo $company_valuations['valuation_date'] !=""?date('M d-Y',strtotime($company_valuations['valuation_date'])):"-";?></span>
                                <span class="f-w-600">Valuation - </span><span class="m-r-10"><?php echo customeummarmonyformateifine($company_valuations['company_value']); ?></span>

                                <span class="f-w-600">Total Shares - </span><span class="m-r-10"><?php echo $company_valuations['total_share_qty']; ?></span>

                                <span class="f-w-600">Per Share Value - </span><span class="m-r-10"><?php echo customeummarmonyformateifine($company_valuations['per_share_value']); ?></span>

                            </td>
                        </tr>

                        <?php foreach($cvdlist as $cvd){
                            if($cvd['dilution'] == '0'){
                                $new_shares_qty = $cvd['new_shares'];
                                $old_shares_qty = $cvd['old_shares'];


                            }else{
                                $new_shares_qty = 0;
                                $old_shares_qty =$cvd['total_shares'];
                            }
                            $total_new_shares_qty += $new_shares_qty;
                            $total_old_shares_qty += $old_shares_qty;
                            $total_amount += $cvd['total_amount'];
                             $distribute_percentage += $cvd['distribute_percentage'];
                            $total_share += $cvd['total_shares'];
                            $total_equity_shares_qty += $cvd['equity_shares'];
                            $total_preference_shares_qty += $cvd['preference_shares'];

                            ?>
                        <tr>
                            <th scope="col"><?php echo $cvd['distributer_name']; ?> <span class="d-block"><?php echo "(".$cvd['start_contribution_valuation'],")"; ?></span></th>

                            <th scope="col" class="text-right"><?php echo round($old_shares_qty); ?></th>
                            <th scope="col" class="text-right"><?php echo round($new_shares_qty); ?></th>

                            <th scope="col" class="text-right">-</th>
                            <th scope="col" class="text-right"><?php echo round($cvd['total_shares']); ?></th>
                            <th scope="col" class="text-right"><?php echo round($cvd['equity_shares']); ?></th>
                            <th scope="col" class="text-right"><?php echo round($cvd['preference_shares']); ?></th>

                            <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($cvd['equity_shares_percentage']); ?></th>
                            <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($cvd['preference_shares_percentage']); ?></th>
                             <th scope="col" class="text-right"><?php echo customeummarmonyformateifine($cvd['distribute_percentage']); ?></th>
                        </tr>
                        <?php } ?>

                         <tr>
                            <th scope="col">Total</th>
                            <th scope="col" class="text-right"><?php echo $total_old_shares_qty;?></th>
                            <th scope="col" class="text-right"><?php echo $total_new_shares_qty; ?></th>

                            <th scope="col" class="text-right">-</th>
                            <th scope="col" class="text-right"><?php echo round($total_share); ?></th>


                            <th scope="col" class="text-right"><?php echo $total_equity_shares_qty; ?></th>
                            <th scope="col" class="text-right"><?php echo $total_preference_shares_qty; ?></th>
                             <th scope="col" class="text-right">-</th>
                            <th scope="col" class="text-right">-</th>
                            <th scope="col" class="text-right"><?php echo $distribute_percentage; ?></th>

                        </tr>
                    </table>
                </div>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> Please Select Company Valuation</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

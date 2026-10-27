<?php 
$url1 = API."?mode=getvaluationreport";  
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$companyvallist = $result['company_valuations'];
$company_valuation_distributions = $result['company_valuation_distributions'];
$company_contribution_users = $result['company_contribution_users'];
$companyvaluationyearly =$yearary =$comvaldist=  array();
foreach($company_valuation_distributions as $cvd){
    $comvaldist[$cvd['company_valuation_id']][] = $cvd;
}
foreach($companyvallist as $cvl){
    $year = date("Y",strtotime($cvl['valuation_date']));
    $companyvaluationyearly[$year][$cvl['id']] = $cvl;
    $yearary[$year] = $year;
}
ksort($companyvaluationyearly);
ksort($yearary);

$esop_plans = $result['esop_plans'];
//echo "<pre>"; print_r($comvaldist); echo "</pre>";
$distributuserslist = $onetimeploteusers = array();
?>
<div class="card">
    <div class="card-header table-card-header">
      <h5>Company Valuation Reports</h5>
      <div class="card-header-right table-card-header ">
      </div>
  </div>
  <div class="card-body">
    <div class="outer-wrapper" id="outer-wrapper">
        <div class="inner-wrapper">
            <div class="pseudo-item pseudo-item-1">
                <div class="table-box employee-table">
                    <table class="table m-0">
                        <tr>
                            <td colspan="3">&nbsp</td>
                        </tr>
                        <tr>
                            <td>&nbsp &nbsp</td>
                            <th align="right">&nbsp &nbsp</th>
                            <th align="right">&nbsp &nbsp</th>
                        </tr>
                        <tr>
                            <td></td>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;<br /></th>
                        </tr>
                         <tr>
                            <td></td>
                            <th align="right">&nbsp;</th>
                            <th align="right">&nbsp;<br /></th>
                        </tr>
                        <tr bgcolor="#eeeeee">
                            <th>Total Ownership</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                        </tr>
                    </table>
                       <?php foreach($yearary as $yr){ 
                $companyvaluation = $companyvaluationyearly[$yr];
                foreach($companyvaluation as $cv){ 
                	$distribution = $comvaldist[$cv['id']];
                	//print_r($distribution);
                	?>
                    <!-- Card 1 -->
                    <div class="card p-3 m-0">
                        <h5 class="font-weight-bold m-0 f-14"><?php echo $cv['valuation_name']; ?></h5>
                        <div class="card-body employee-table p-0">
                            <table class="table mt-3 mb-0">
                            	<?php foreach($distribution as $distribution1){ 
                                    if(!isset($distributuserslist[$distribution1['company_contribution_user_id']])){
$distributuserslist[$distribution1['company_contribution_user_id']] = $distribution1['company_contribution_user_id'];


                                    ?>
                                <tr>
                                    <th><a class="f-12" href=""><?php echo $company_contribution_users[$distribution1['company_contribution_user_id']]; ?></a></th>

                                </tr>
                            	<?php } } ?>
                            </table>
                        </div>
                        <!-- <span class="view-more">View More</span> -->
                    </div>
                <?php }} ?>
                   
                </div>
            </div>
            <?php foreach($yearary as $yr){ 
                $companyvaluation = $companyvaluationyearly[$yr];
                foreach($companyvaluation as $cv){
                	$distribution = $comvaldist[$cv['id']];
                	$totalpercenateg = 0; 
                	foreach($distribution as $distribution1){ 
                			$totalpercenateg += $distribution1['distribute_percentage'];
                	}
                    ?> 
                    <div class="pseudo-item border position-relative">
                        <div class="hide-box"><i class="fa-solid fa-circle-minus"></i></div>
                        <div class="table-box employee-table">
                            <table class="table m-0">
                                <tr>
                                    <th class="border-bottom align-center" align="center" colspan="3"><?php echo $yr;?></th>
                                </tr>
                                <tr align="center">
                                    <th class="align-center">Value<br /><?php echo customeummarmonyformateifine($cv['company_value']); ?></th>
                                    <th class="align-center">Shares<br /><?php echo round($cv['total_share_qty']); ?></th>
                                    <th class="align-center">Shr. Price<br /><?php echo customeummarmonyformateifine($cv['per_share_value']); ?></th>
                                </tr>
                                <tr align="center">
                                    <th class="align-center">Grand Total</th>
                                    <th class="align-center">Option</th>
                                    <th class="align-center">Equity<br />(%)</th>
                                </tr>
                                <tr bgcolor="#eeeeee" align="middle">
                                    <th>&nbsp &nbsp</th>
                                    <th></th>
                                    <th class="align-center align-middle"><?php echo $totalpercenateg; ?>%</th>
                                </tr>
                                <tr>
                                  <th class="h5 font-weight-bold border-bottom" colspan="3">&nbsp &nbsp</th>
                              </tr>
                          </table>
                          <!-- Card 1 -->

                          <div class="card p-3 m-0">
                            <div class="card-body employee-table p-0">
                                <table class="table m-0">
                                <?php foreach($distribution as $distribution1){ 
                                	$distributamount = ($distribution1['distribute_percentage']/100)*$cv['company_value'];
                                	$distributshare = ($distribution1['distribute_percentage']/100)*$cv['total_share_qty'];
                                		?>
                                    <tr>
                                        <td align="right"><?php echo customeummarmonyformateifine($distributamount); ?></td>
                                        <td align="right"><?php echo round($distributshare); ?></td>
                                        <td align="right"><?php echo $distribution1['distribute_percentage']; ?>%</td>
                                    </tr>
                                <?php } ?>    
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } } ?>
        </div>
    </div>
</div>
</div>





<script type="text/javascript">
    $( ".hide-box" ).on( "click", function() {
        $( this ).parent( ".pseudo-item" ).toggleClass("addwidth");
    });
    $( ".view-more" ).on( "click", function() {
        $( this ).parent( ".collapse" ).toggleClass("full-employee");
    });
</script>



<script type="text/javascript">
    function showStuff(id) {
      document.getElementById(id).style.display = 'block';
  }
</script>


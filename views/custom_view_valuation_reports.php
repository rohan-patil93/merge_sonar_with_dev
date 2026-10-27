<?php
$url1 = API."?mode=getvaluationreport";
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$company_valuations = $result['company_valuations'];
$company_valuation_distributions = $result['company_valuation_distributions'];
$company_contribution_users = $result['company_contribution_users'];
$comvaldist= $rowwithuser =  array();
foreach($company_valuation_distributions as $cvd){
    $comvaldist[$cvd['company_valuation_id']][$cvd['company_contribution_user_id']] = $cvd;
}

?>
<div class="card">
    <div class="card-header table-card-header">
      <h5>Valuation Overview</h5>
      <div class="card-header-right table-card-header ">
      </div>
  </div>
  <div class="card-body">
    <div class="outer-wrapper" id="outer-wrapper">
        <div class="inner-wrapper">
            <div class="pseudo-item pseudo-item-1">
                <div class="table-box employee-table p-2">
                    <table class="table p-2 mb-2">
                        <tr>
                            <td colspan="3">&nbsp</td>
                        </tr>
                        <tr>
                            <td>&nbsp &nbsp</td>
                            <th scope="col" style="text-align: right;">&nbsp &nbsp</th>
                            <th scope="col" style="text-align: right;">&nbsp &nbsp</th>
                        </tr>
                        <tr>
                            <td></td>
                            <th scope="col" style="text-align: right;">&nbsp;</th>
                            <th scope="col" style="text-align: right;">&nbsp;<br /></th>
                        </tr>
                         <tr>
                            <td></td>
                            <th scope="col" style="text-align: right;">&nbsp;</th>
                            <th scope="col" style="text-align: right;">&nbsp;<br /></th>
                        </tr>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col">Total Ownership</th>
                            <th scope="col">&nbsp;</th>
                            <th scope="col">&nbsp;</th>
                        </tr>
                    </table>
                    <?php foreach($company_valuations as $cv){
                        $distribution = $comvaldist[$cv['id']]; ?>
                    <!-- Card 1 -->
                    <div class="card p-2 m-2">
                        <h5 class="font-weight-bold m-0 f-14"><?php echo $cv['valuation_name']; ?></h5>
                        <div class="card-body employee-table p-0">
                            <table class="table mt-3" style="margin-bottom: 3.4px !important;">
<?php
foreach($distribution as $distribution1){
    if(!isset($distributuserslist[$distribution1['company_contribution_user_id']])){
        $distributuserslist[$distribution1['company_contribution_user_id']] = $distribution1['company_contribution_user_id'];
        $rowwithuser[$cv['id']][] = $distribution1['company_contribution_user_id'];
?>
    <tr>
        <th scope="col"><a class="f-12" href=""><?php echo $company_contribution_users[$distribution1['company_contribution_user_id']]; ?></a></th>
    </tr>
<?php } ?>
<?php } ?>
                            </table>
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>

           <?php foreach($company_valuations as $cv){
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
                                    <th scope="col" class="border-bottom align-center" style="text-align: center;" colspan="3"><?php echo date("Y",strtotime($cv['valuation_date']));?></th>
                                </tr>
                                <tr style="text-align: center;">
                                    <th scope="col" class="align-center">Value<br /><?php echo customeummarmonyformateifine($cv['company_value']); ?></th>
                                    <th scope="col" class="align-center">Shares<br /><?php echo round($cv['total_share_qty']); ?></th>
                                    <th scope="col" class="align-center">Shr. Price<br /><?php echo customeummarmonyformateifine($cv['per_share_value']); ?></th>
                                </tr>
                                <tr style="text-align: center;">
                                    <th scope="col" class="align-center">Grand Total</th>
                                    <th scope="col" class="align-center">Option</th>
                                    <th scope="col" class="align-center">Equity<br />(%)</th>
                                </tr>
                                <tr style="background-color: #eeeeee; text-align: center;">
                                    <th scope="col">&nbsp &nbsp</th>
                                    <th scope="col"></th>
                                    <th scope="col" class="align-center align-middle"><?php echo $totalpercenateg; ?>%</th>
                                </tr>

                          </table>
                          <!-- Card 1 -->
    <?php foreach($rowwithuser as $rows){  ?>
 <div class="card p-2 m-2">
    <h5 class="font-weight-bold m-0 f-14">&nbsp</h5>
    <div class="card-body employee-table p-0">
        <table class="table mt-3" style="margin-bottom: 5px !important;" role="presentation">
        <?php
            foreach($rows as $uuid){
                if(isset($distribution[$uuid])){
                    $distribuitionres = $distribution[$uuid];
        ?>
            <tr>
                <td style="text-align: right;"><?php echo customeummarmonyformateifine($distribuitionres['total_amount']); ?></td>
                <td style="text-align: right;"><?php echo $distribuitionres['total_shares']; ?></td>

                <td style="text-align: right;"><?php echo round($distribuitionres['distribute_percentage'],2); ?>%</td>
            </tr>
            <?php }else{ echo "<tr><td>-</td><td>-</td><td>-</td><tr>"; ?>
            <?php } ?>
            <?php } ?>
        </table>
    </div>
</div>
<?php } ?>

                    </div>
                </div>

            <?php }  ?>
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


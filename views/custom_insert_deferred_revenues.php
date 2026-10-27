<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitdeferredrevenues'])){
    $_POST['company_id'] = $_SESSION['company_id'];
    $_POST['user_id'] = $_SESSION['userid'];
    $url = API."?mode=insertdeferredrevenues&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $res = json_decode($response1,1);
    $response = $res['res'];
    if($response['status'] == 'sucess'){
        if($response['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
               header("location:index?option=COM_LIST_DEFERRED_REVENUES");
        }
        ?>
        <script type="text/javascript">
            document.onreadystatechange = function(){
                if(document.readyState == "complete") {
                    tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
                }
            }
        </script>
        <?php
    }else{
        if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
                $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
            $error_message = $response['message'];
        }
    }

}

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getdeferredrevenues&rid=".$rid;
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$chart_of_accounts = $result['chart_of_accounts'];
$depreciation_method = $result['depreciation_method'];
$deferred_revenues = $result['deferred_revenues'];
?>
<script type="text/javascript">
    function getDepreciationMethod(){
    var capitalization_date = $("#acquisition_date").val();
    var depreciation_method = $("#depreciation_method").val();
    var duration = $("#duration").val();
    var depn_rate = $("#deferred_revenue_amount").val();
    if(duration !="" && duration !=0 && capitalization_date !=""){
        jQuery.ajax({
            url: "ajax.php",
            data: 'page=getdepreciationtable&depreciation_method='+depreciation_method+"&duration="+duration+"&capitalization_date="+capitalization_date+'&depn_rate='+depn_rate,
            type: "POST",
            success:function(data){
                console.log(data);
                var res = JSON.parse(data);
                $("#deferred_revenue_table").html(res.depreciation_table);
                $("#end_deferred_revenue_date").val(res.end_of_life);
            }
        });
    }
}
function getscrapPrice(valu){
    var capitalization_price = $("#original_amount").val();
    var scrap_amount = $("#scrap_amount").val();
    console.log(capitalization_price);
    var grandtotal = 0;
    if(scrap_amount != 0 && capitalization_price != 0 && scrap_amount != "" && capitalization_price != ""){
        grandtotal = parseFloat(capitalization_price) - parseFloat(scrap_amount);
        $("#deferred_revenue_amount").val(grandtotal);
    }else{
        $("#deferred_revenue_amount").val(capitalization_price);
    }
    getDepreciationMethod();

}
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>

<div class="card">
    <div class="card-header">
        <h5>Deferred Revenues</h5>
        <div class="card-header-right table-card-header">
            <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
    </div>
    <div class="card-body">
        <form method="post">
        <div class="row">
            <input type="hidden" name="depreciation_method"  id="depreciation_method" value="SL">
                <div class="form-group col-lg-3">
                <span class="form-label">Deferred Revenue Name</span>
                <input aria-label="Deferred Revenue Name" class="form-control text-right"  type="text"  id="deferred_revenue_name" name="deferred_revenue_name"  value="<?php echo $deferred_revenues['deferred_revenue_name']; ?>">
            </div>
               <div class="form-group col-lg-3">
                <span class="form-label">Original Amount</span>
                <input aria-label="Original Amount" class="form-control text-right"  type="text"  id="original_amount" name="original_amount"  value="<?php echo $deferred_revenues['original_amount']; ?>"  onblur="getscrapPrice(this.value)">
            </div>
            <div class="form-group col-lg-3">
                <span class="form-label">Acquisition Date</span>
                <input aria-label="Acquisition Date" class="form-control datepicker"  type="text"  id="acquisition_date" name="acquisition_date"  value="<?php echo $deferred_revenues['acquisition_date'] !=""?fromDate($deferred_revenues['acquisition_date']):""; ?>" onchange="getDepreciationMethod()">
            </div>

            <div class="form-group col-lg-3">
                <span class="form-label">Scrap Amount</span>
                <input aria-label="Scrap Amount" class="form-control text-right "  type="text"  id="scrap_amount" name="scrap_amount"   value="<?php echo $deferred_revenues['scrap_amount']; ?>" onblur="getscrapPrice(this.value)">
            </div>

            <div class="form-group col-lg-3">
                <span class="form-label">Deferred Revenue Amount</span>
                <input aria-label="Deferred Revenue Amount" class="form-control text-right"  type="text"  id="deferred_revenue_amount" name="deferred_revenue_amount"  value="<?php echo $deferred_revenues['deferred_revenue_amount']; ?>" onblur="getDepreciationMethod()" readonly>
            </div>

            <div class="form-group col-lg-1">
                <span class="form-label">Duration</span>
                <input aria-label="Duration" class="form-control"  type="number"  id="duration" name="duration"  value="<?php echo $deferred_revenues['duration']!=""?$deferred_revenues['duration']:1; ?>" onblur="getDepreciationMethod()">
            </div>
            <div class="form-group col-lg-2">
                <span class="form-label">End Of life</span>
                <input aria-label="End Deferred Revenue Date" class="form-control datepicker"  type="text"  id="end_deferred_revenue_date" name="end_deferred_revenue_date"  value="<?php echo  $deferred_revenues['end_deferred_revenue_date']!=""?fromDate($deferred_revenues['end_deferred_revenue_date']):""; ?>" readonly>
            </div>

            <div class="form-group col-lg-3 w-full">
                <span class="form-label d-block">Deferred Revenue Account</span>
                <select aria-label="Deferred Revenue Account" name="deferred_revenue_account" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($chart_of_accounts as $coa) {  ?>
                        <option value="<?php echo $coa['id']; ?>" <?php echo $deferred_revenues['deferred_revenue_account'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-3 w-full">
                <span class="form-label d-block">Revenue Account</span>
                <select aria-label="Revenue Account" name="revenue_account" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($chart_of_accounts as $coa) {  ?>
                        <option value="<?php echo $coa['id']; ?>" <?php echo $deferred_revenues['revenue_account'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group col-lg-3 w-full">
                <span class="form-label d-block">Journal</span>
                <select aria-label="Journal" name="Journal" class="form-control form-select js-example-basic-single" required>
                    <option value="">Select Any One </option>
                    <?php foreach($chart_of_accounts as $coa) {  ?>
                        <option value="<?php echo $coa['id']; ?>" <?php echo $deferred_revenues['Journal'] ==$coa['id']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                    <?php } ?>
                </select>
            </div>
            <?php $depreciationtable = json_decode($deferred_revenues['deferred_revenue_table'],1); ?>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body" id="deferred_revenue_table">
                        <table class="table table-hover m-b-0" style="font-size:13px;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Depreciation Date</th>
                                    <th scope="col" class="text-right">Depreciation</th>
                                    <th scope="col" class="text-right">Accumulated Depreciation</th>
                                    <th scope="col" class="text-right">Book Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1;foreach($depreciationtable as $dp){?>

                                    <tr><td class="p-2"><?php echo $i; ?></td>
                <td class="p-2"><input type="hidden" name="depreciation_date[]" value="<?php echo $dp['depreciation_date']; ?>"><?php echo $dp['depreciation_date']; ?></td>
                <td class="p-2 text-right"><input type="hidden" name="per_year_deprciation[]" value="<?php echo $dp['per_year_deprciation']; ?>"><?php echo customeummarmonyformate($dp['per_year_deprciation']); ?></td>
                <td class="p-2 text-right"><input type="hidden" name="accumulated_depreciation[]" value="<?php echo $dp['accumulated_depreciation']; ?>"><?php echo $dp['accumulated_depreciation']; ?></td>
                <td class="text-right p-2"><input type="hidden" name="depreciation_price[]" value="<?php echo $dp['depreciation_price']; ?>"><?php echo customeummarmonyformate($dp['depreciation_price']); ?></td>
            </tr>

                                <?php $i++;
} ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <input type="hidden" name="rid" value="<?php echo $rid; ?>">
                <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitdeferredrevenues"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
            </div>
        </form>
    </div>
</div>

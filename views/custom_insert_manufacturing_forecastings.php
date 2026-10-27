<?php
$_SESSION['apply_js']['multiselest'] = "1";
if (isset($_POST['btn_BOM'])) {
    $_POST['user_id'] = $_SESSION['userid'];
    $_POST['company_id'] = $_SESSION['company_id'];
    $url = API . "?mode=insertmanufacturingboms&method=post";
    $response1 = file_post_contents($url, $_POST, 'insert');
    $response2 = json_decode($response1, 1);
    $res = $response2['manufacturing_boms'];
    if($res['status'] == 'sucess'){
        if($res['LASTID'] == 0){
            $msgcode = 3001; //update
        }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
            header("location:index?option=COM_LIST_MANUFACTURING_BOMS");
        }
        ?>
   <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message']['3001']; ?>','');
            }
        }
    </script>
<?php  }else{
        if(isset($_SESSION['error'][$res['error_no']])){
          $dis_err = explode(",", $res['other']);
          foreach ($dis_err as $va) {
            $dis_er [] = $dis_labels[$va];
          }
          $error_message = $res['error_no'] ." : ".$_SESSION['error'][$res['error_no']]." : ".implode(", ", (array)$dis_er);
        }else{
          $error_message = $res['message'];
        }
    }

}
$rid1 = $_GET['Rid'];
$rid = encodeId($rid1);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid = $rid['1'];
$url3 = API . "?mode=getmanufacturingboms&method=get&rid=" . $rid;
$che3 = callAPI($url3);
$result = json_decode($che3, 1);
$items = $result['items'];
$unit_of_measurements = $result['unit_of_measurements'];
$vendors = $result['vendors'];
$locations = $result['locations'];
$warehouses = $result['warehouses'];
$manufacturing_operations = $result['manufacturing_operations'];
$manufacturing_boms = $result['manufacturing_boms'];
$manufacturing_bom_items = $result['manufacturing_bom_items'];
$manufacturing_bom_operations = $result['manufacturing_bom_operations'];

$forecast_type = array(array('id'=>'1','nameval'=>'Sales'),array('id'=>'1','nameval'=>'Production'),array('id'=>'1','nameval'=>'Invetory Consumption'))
?>
<script>
    function monthlyForcastings(){
         const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
                      "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        var htmlvar = ``;
        htmlvar += `<table class="table table-hover mt-5">`;
        htmlvar += `<tr>`;
        htmlvar += `<th scope="col">Month</th><th scope="col">Qty</th>`;
        htmlvar += `</tr>`;
        var start_date = $("#start_date").val();
        let start_date_ary = start_date.split("-")
        var end_date = $("#end_date").val();
        let end_date_ary = end_date.split("-")
        let start = new Date(start_date_ary[0], start_date_ary[1]-1); // Month index starts from 0 -> Feb = 1
        let end = new Date(end_date_ary[0], end_date_ary[1]-1);  // Dec = 11
        while (start <= end) {
          let getmonth = monthNames[start.getMonth()];
          let getyear = start.getFullYear();

            htmlvar += `<tr>`;
            htmlvar += `<td class="p-2">`;
            htmlvar += `<span class="form-label d-block">`+getyear+` - `+getmonth+`</span>`;
            htmlvar += `</td>`;
            htmlvar += `<td class="p-2">`;
            htmlvar += `<input aria-label="Input field" type="text" class="form-control" id="" name="" value="0" required>`;
            htmlvar += `</td>`;
            htmlvar += `</tr>`;

          start.setMonth(start.getMonth() + 1);
        }
        htmlvar += `</table>`;
        $("#monthly_forcastings").html(htmlvar);
    }


</script>
 <?php if($error_message !=''){ ?>
    <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="card">
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Forecastings</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <form method="post" action="" name="kittingForm">
        <div class="card-body m-l-50 m-r-50">
            <input type="hidden" name="rid" value="<?php echo $rid; ?>">
            <div class="row" >
                <div class="col-md-3 w-full">
                    <label for="final_item_name" class="form-label d-block">Forecast Type <span class="text-danger">*</span></label>
                    <select aria-label="Bom Item" class="js-example-basic-single item-selection" name="bom_item_id" id="" onchange="selectProductName()" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($forecast_type as $item){ ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo $manufacturing_boms['bom_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['nameval']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 w-full">
                    <label for="final_item_name" class="form-label d-block">Product <span class="text-danger">*</span></label>
                    <select aria-label="Bom Item" class="js-example-basic-single item-selection" name="bom_item_id" id="bom_item_id" onchange="selectProductName()" required>
                        <option value="">Select Any one</option>
                        <?php foreach ($items as $item){ ?>
                            <option value="<?php echo $item['id']; ?>" <?php echo $manufacturing_boms['bom_item_id'] == $item['id']?"selected":""; ?>><?php echo $item['item_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="form-label">Start date <span class="text-danger">*</span></span>
                    <input aria-label="Start Date" type="month" class="form-control" id="start_date" name="start_date" value="" onchange="monthlyForcastings()" required>
                </div>
                <div class="col-md-3 mb-3">
                    <span class="form-label">From date <span class="text-danger">*</span></span>
                    <input aria-label="End Date" type="month" class="form-control" id="end_date" name="end_date"  value="" onchange="monthlyForcastings()" required>
                </div>
            </div>
            <div class="row" >
                <div class="col-lg-6"></div>
                <div class="col-lg-6" id="monthly_forcastings"></div>

            </div>
        </div>
    </form>
</div>

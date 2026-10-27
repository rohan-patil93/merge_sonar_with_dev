<?php
if (isset($_POST['submit']) && $_POST['submit'] == 'search') {
        $array =  array('category_id', 'vendor_id', 'cost_center_id', 'location_id');
        foreach ($array as  $value) {
            if($_POST[$value] != ''){
                $ress [$value] = $_POST[$value];
            }
        }
        $data1["where"] = $ress;
        $data = urlencode(json_encode($data1));
        $tableurl = API."?mode=serchforpos&method=post";
        $response = file_post_contents($tableurl, $_POST, '');
        $data = json_decode($response,1);

    }
$tablecat = API."?mode=gettabledetails&table=".TBL_ITEM_CATEGORIES;
$rescat = callAPI($tablecat);
$datacat = json_decode($rescat,1);
$tablecost = API."?mode=gettabledetails&table=".TBL_COST_CENTERS;
$rescost = callAPI($tablecost);
$datacost = json_decode($rescost,1);
$tablelocation = API."?mode=gettabledetails&table=".TBL_LOCATIONS;
$reslocation = callAPI($tablelocation);
$datalocation = json_decode($reslocation,1);

$content = $data['help_text']!='' ? $data['help_text'] : "Help Test";

?>
<script type="text/javascript">
     $(document).on('click', '#checkall1', function() {
          alert('haii');

    });
     function chackd(e){
         alert(check);
         var checkboxes = document.getelementbyid('check');
       if (e.checked) {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = true;
         }
       } else {
         for (var i = 0; i < checkboxes.length; i++) {
           checkboxes[i].checked = false;
         }
       }
     }

</script>
<div class="d-flex flex-column-fluid  j-wrapper">
    <div class="container-fluid j-pro">
        <div class=" flex-row j-content">
    <?php if($content !=''){ ?>
  <div class="alert alert-warning alert-dismissible">
      <strong><?php echo $content; ?></strong>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>
        <?php } ?>

                                         <div class="card card-custom card-sticky" id="kt_page_sticky_card">


                                            <div class="card-header">
    <h5>Stock Transfer</h5>
    <div class="card-header-right">
          <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
    </div>
</div>
<div class="card card-custom">
    <div class="card-body">
<form class="kt-form kt-form--fit kt-margin-b-20" action="" method="post">
    <div class="row kt-margin-b-20">
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span class="form-label"><?php echo "Location"; ?>:</span>
            <select aria-label="Vendor" class="form-control form-select js-example-basic-single" id="kt_select2_41" name="vendor_id">
                <option value=""> All Location </option>
                <?php foreach($datalocation as $location){ ?>
                <option  <?php if($ress['vendor_id'] == $location['id'] ){ ?> selected <?php
} ?> value="<?php echo $location['id']; ?>"><?php echo $location['location_description']; ?></option>
            <?php     $location_name[$location['id']] =  $location['location_description'];
            } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span class="form-label"><?php echo "Item Category"; ?>:</span>
            <select aria-label="Category" class="form-control form-select js-example-basic-single" id="kt_select2_42" name="category_id">
                <option value=""> All Categories </option>
                <?php foreach($datacat as $selectcat){ ?>
                <option  <?php if($ress['category_id'] == $selectcat['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcat['id']; ?>"><?php echo $selectcat['category_description']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">
            <span class="form-label"><?php echo "Cost Center"; ?>:</span>
            <select aria-label="Cost Center" class="form-control form-select js-example-basic-single" id="kt_select2_43" name="cost_center_id">
                <option value=""> All Cost Center </option>
                <?php foreach($datacost as $selectcost){ ?>
                <option   <?php if($ress['cost_center_id'] == $selectcost['id'] ){ ?> selected <?php
} ?> value="<?php echo $selectcost['id']; ?>"><?php echo $selectcost['cost_center_name']; ?></option>
            <?php } ?>
            </select>
        </div>
        <div class="col-lg-3 kt-margin-b-10-tablet-and-mobile">


            <div class="row">
                <div class="col-lg-6">
                    <span class="form-label">Start Date</span>
                <input aria-label="From" type="date" class="form-control kt-input" name="from" placeholder="From" data-col-index="5" />
                </div><div class="col-lg-6">
<span class="form-label">End Date</span>
                <input aria-label="To" type="date" class="form-control kt-input" name="to" placeholder="To" data-col-index="5" />
            </div>
        </div>
        </div>
        <input type="hidden" name="table" value="stock_transfer">

    </div>
<div class="row  mt-2 mb-2">
        <div class="col-lg-12">

            <input type="hidden" name="submit" value="search" />
            <button class="btn btn-primary btn-brand--icon" id="kt_search">
                <span>
                    <i class="la la-search"></i>
                    <span>Search</span>
                </span>
            </button>
            &nbsp;&nbsp;
            <button class="btn btn-secondary btn-secondary--icon" id="kt_reset">
                <span>
                    <i class="la la-close"></i>
                    <span>Reset</span>
                </span>
            </button>
        </div>
    </div>
</form>
<div>
    <!--begin: Datatable -->
        <div class="dt-responsive table-responsive">
        <table class="table table-striped- table-bordered table-hover table-checkable" style="margin-top: 15px;" id="kt_table_11">

            <thead>
                <tr>

                    <th scope="col">ID</th>
                    <th scope="col">Transaction Number</th>
                    <th scope="col">Transaction Date</th>
                    <th scope="col">Transfer Location</th>
                    <th scope="col">Total</th>
                    <th scope="col">Genrate Stock Return</th>
                    <th scope="col">Quality Check</th>

                </tr>
            </thead>
            <tbody>
                    <?php $i=0; foreach ($data as $row){
                        $rid = $row['id'];
                        $view_id = deocdeId("id:".$rid);
                        $grrn_row =  "index.php?option=COM_INSERT_STOCK_TRANSFER&table=stock_transfer&Rid=".$rid;
                        ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['transaction_number']; ?></td>
                    <td><?php echo fromDate($row['transaction_date']); ?></td>
                    <td><?php echo $location_name[$row['transfer_location_id']]; ?></td>
                    <td><?php echo customeummarmonyformate($row['total_amount']); ?></td>
                    <td>
                        <a  target="_blank" href="<?php echo $grrn_row; ?>" >
                        <button class="btn btn-info btn-brand--icon" id="kt_search_2">
                            <span>Genrate Stock Transfer</span>
                        </button></a>
                    </td>
                    <td>
                        <a  target="_blank" href="<?php echo $grrn_row; ?>" >
                        <button class="btn btn-info btn-brand--icon" id="kt_search_3">
                            <span>Genrate Stock Transfer</span>
                        </button></a>
                    </td>
                </tr>
                <?php $i++;
} ?>
            </tbody>
        </table></div>
        <!--end: Datatable -->
    </div>
</div>
</div></div>

<?php
if(isset($_POST['trip_id'])){
    include_once '../includes/session.php';
    include_once '../dbconfig.php';
    include_once '../comm.php';
    $trip_id =  $_POST['trip_id'];
    $url = API . "?mode=getempexpenses&method=get&trip_id=$trip_id&result_type=trip_details";
    $result = callAPI($url);
    $result = json_decode($result, 1);
}
$trips_select = $result['trips_select'];
$domestic_round_trips = $result['domestic_round_trips'];
$trip_hotels = $result['trip_hotels'];
$local_trips = $result['local_trips'];
$cities = $result['cities'];
$vehicle_types = $result['vehicle_types'];
if($_POST['expaince_list'] !="" && $_POST['expaince_list'] =="Y"){  ?>

            <option value=""><?= __('Select Any One') ?></option>
           <?php  if(count((array)$local_trips) !=0){ ?>
            <optgroup label="Journey">
                <?php foreach($local_trips as $trplocal){   $selectedtrpplan = "";
                    if($sub_details['trip_transaction_table'] == "domestic_round_trips" && $sub_details['trip_transaction_id'] == $trplocal['id']){
                        $selectedtrpplan = "selected";
                    }
                    ?>
                <option value="domestic_round_trips||<?php echo $trplocal['id']; ?>"  <?php echo $selectedtrpplan; ?>><?php echo $cities[$trplocal['from_location']]['city_name']." (".$cities[$trplocal['from_location']]['country_code'].") - ".fromDate($trplocal['trip_date']); ?></option>
                <?php } ?>
            </optgroup>
            <?php } ?>
            <?php if(count((array)$trip_hotels) !=0){ ?>
            <optgroup label="Hotel">
                <?php foreach($trip_hotels as $trphotel){   $selectedtrpplan = "";
                    if($sub_details['trip_transaction_table'] == "trip_hotels" && $sub_details['trip_transaction_id'] == $trphotel['id']){
                        $selectedtrpplan = "selected";
                    }?>
                <option value="trip_hotels||<?php echo $trphotel['id']; ?>" <?php echo $selectedtrpplan; ?>><?php echo $cities[$trphotel['location']]['city_name']." (".$cities[$trphotel['location']]['country_code'].") - (IN :".$trphotel['check_in']." - Out :".$trphotel['check_out'].")"; ?></option>
                <?php } ?>
            </optgroup>
            <?php } ?>
            <?php  if(count((array)$local_trips) !=0){ ?>
            <optgroup label="Local">
                  <?php foreach($local_trips as $trplocal){  $selectedtrpplan = "";
                    if($sub_details['trip_transaction_table'] == "local_trips" && $sub_details['trip_transaction_id'] == $trplocal['id']){
                        $selectedtrpplan = "selected";
                    }?>
                <option value="local_trips||<?php echo $trplocal['id']; ?>" <?php echo $selectedtrpplan; ?>><?php echo $cities[$trplocal['from_location']]['city_name']." (".$cities[$trplocal['from_location']]['country_code'].") -".fromDate($trplocal['trip_date']); ?></option>
                <?php } ?>
            </optgroup>
            <?php } ?>

<?php
}else{
    if($trips_select['id']!=""){
?>
<div class=" mt-3">
    <h4><?= __('Trips'); ?></h4>
    <table class="table table-hover table-sm">
        <tr>
            <th scope="col" class="p-2"><?= __('Trip Name') ?></th>
            <td class="w-b-w-s p-2"><?php echo $trips_select['trip_name']; ?></td>
        </tr>
        <tr>
            <th scope="col" class="p-2"><?= __('Trans Number') ?></th>
            <td class="p-2"><?php echo $trips_select['transaction_no']; ?></td>
        </tr>
        <tr>
            <th scope="col" class="p-2"><?= __('Trans Date') ?></th>
            <td class="p-2"><?php echo fromDate($trips_select['transaction_date']); ?></td>
        </tr>
        <tr>
            <th scope="col" class="p-2"><?= __('Budget') ?></th>
            <td class="p-2"><?php echo $trips_select['budget_amount']; ?></td>
        </tr>
    </table>
</div>

<div class=" mt-3">
    <h4><?= __('Journey'); ?></h4>
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th scope="col" class="p-2"><?= __('From') ?></th>
                <th scope="col" class="p-2"><?= __('To') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($domestic_round_trips as $roundtrp){ ?>
            <tr>
                <td class="p-2">
                    <span class="d-block"><?php echo $cities[$roundtrp['origin_location']]['city_name']." (".$cities[$roundtrp['origin_location']]['country_code'].")"; ?></span>
                    <span class="d-block"><?php echo fromDate($roundtrp['departure_date']); ?></span>
                </td>
                <td class="p-2">
                    <span class="d-block"><?php echo $cities[$roundtrp['destination_location']]['city_name']." (".$cities[$roundtrp['destination_location']]['country_code'].")"; ?> </span>
                    <span class="d-block"><?php echo $roundtrp['return_date'] !=""?fromDate($roundtrp['return_date']):"-"; ?></span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class=" mt-3">
    <h4><?= __('Hotel'); ?></h4>
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th scope="col" class="p-2"><?= __('Location') ?></th>
                <th scope="col" class="p-2"><?= __('In') ?></th>
                <th scope="col" class="p-2"><?= __('Out') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($trip_hotels as $trphotel){ ?>
            <tr>
                <td class="p-2">
                    <span class="d-block"><?php echo $cities[$trphotel['location']]['city_name']." (".$cities[$trphotel['location']]['country_code'].")"; ?></span>
                </td>
                <td class="p-2">
                    <span class="d-block"><?php echo $trphotel['check_in']; ?> </span>
                </td>
                <td class="p-2">
                    <span class="d-block"><?php echo $trphotel['check_out']; ?> </span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div class=" mt-3">
    <h4><?= __('Local'); ?></h4>
    <table class="table table-hover table-sm">
        <thead>
            <tr>
                <th scope="col" class="p-2"><?= __('From') ?></th>
                <th scope="col" class="p-2"><?= __('To') ?></th>
                <th scope="col" class="p-2"><?= __('Vehicle') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($local_trips as $trplocal){ ?>
            <tr>
                <td class="p-2">
                    <span class="d-block"><?php echo $cities[$trplocal['from_location']]['city_name']." (".$cities[$trplocal['from_location']]['country_code'].")"; ?></span>
                    <span class="d-block"><?php echo fromDate($trplocal['trip_date']); ?> </span>
                </td>
                <td class="p-2">
                    <span class="d-block"><?php echo $cities[$trplocal['to_location']]['city_name']." (".$cities[$trplocal['to_location']]['country_code'].")"; ?></span>
                </td>
                <td class="p-2 w-b-w-s">
                    <span class="d-block"><?php echo $vehicle_types[$trplocal['travel_mode']]; ?> </span>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php }else{ ?>

<div class="card-px text-center ">
    <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Trip'); ?></p>
    <div class="text-center px-4">
        <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
    </div>
</div>

<?php } ?>
<?php } ?>

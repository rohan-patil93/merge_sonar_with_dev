<?php
$_POST['user_id'] = $_SESSION['userid'];
$_POST['company_id'] = $_SESSION['company_id'];
if (!empty($_POST) && ($_POST['submit'] ?? '') === 'insert') {
    $type = $_POST['submit'];

    $url = API . "?mode=insertAndUpdateTrevllingPlans&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    $result = json_decode($resp, true);
    if ($result['trip_insert']['status']  == 'sucess') {
        echo "<script>document.addEventListener('DOMContentLoaded',()=>tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Trip saved successfully ',''))</script>";
        header("location:index.php?option=COM_LIST_TRIPS");
    }
}
if (!empty($_POST) && ($_POST['submit'] ?? '') === 'update') {
    $trip_id = (int)($_POST['trip_id'] ?? 0);
    $type = $_POST['submit'];

    $url = API . "?mode=insertAndUpdateTrevllingPlans&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    $result = json_decode($resp, true);
    if ($result['trip_update']['status']  == 'sucess') {
        echo "<script>document.addEventListener('DOMContentLoaded',()=>tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Trip updated successfully ',''))</script>";
    }

}
$rid = '';
if(isset($_GET['Rid']) && $_GET['Rid']!=''){
    $rid =  encodeId($_GET['Rid']);
    $rid = urldecode($rid);
    $rid = explode(':', $rid);
    $rid = $rid['1'];
}

$result = array(); // Initialize to avoid undefined variable
$url = API . "?mode=getTrevllingPlanData&method=get&id=$rid";
$result = callAPI($url);
$result = json_decode($result, 1);
$trip_res               = $result['trip'] ?? [];
$domestic_round_trips   = $result['domestic_round_trips'] ?? [];
$trip_hotels_list       = $result['trip_hotels'] ?? [];
$local_trips_list       = $result['local_trips'] ?? [];

$drt0   = $domestic_round_trips[0] ?? [];
$hotel0 = $trip_hotels_list[0] ?? [];
$local0 = $local_trips_list[0] ?? [];

$cities = $result['cities'] ?? array();
$clients_res = $result['clients'] ?? array();
$projects_res = $result['projects'] ?? array();

$local_modes = $result['local_modes'] ?? array();
$domestic_modes = $result['domestic_modes'] ?? array();
$journey_types = $result['journey_types'] ?? array();
$trip_types = $result['trip_types'] ?? array();

$journey_selected = $drt0['journey_type'] ?? ($journey_types[0]['genc_detail_code'] ?? '1');

function h($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

?>
<form method="post" action="" id="tripForm">

<?php
        $workflowsteps = getWorkflowStatus('trips',$rid,$trip_res['current_status']);
        $check_array = array(ST_ACTIVE, ST_DRAFT, ST_SEND_BACK,ST_CALLBACK,'');
        if(in_array($trip_res['current_status'], (array)$check_array)){

        }else{
            $disable = "disabled";
            $formdisable = "event_disabled";
        }
            ?>
    <div class="card">
        <?php if($rid != ""){ echo $workflowsteps['current_status_ribbon'];
} ?>
        <div class="card-header">
            <h5>New Trip</h5>
            <div class="card-header-right table-card-header m-r-50">
                <?php echo $workflowsteps['workflow_btn']; ?>
                <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
                    <i class="icofont icofont-reply"></i>
                    Back
                </a>
            </div>
        </div>
        <div class="card-body <?php echo $formdisable; ?>">
            <?php echo $workflowsteps['breadcrumb'];?>
            <div class="row">
                 <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Trip Name <b class="text-danger">*</b></span>
                        <input aria-label="Trip Name" type="text" class="form-control b-radius-5" name="trip_name" value="<?= h($trip_res['trip_name']) ?>"   minlength="3" maxlength="120" pattern="^[\w\s\-'\.&(),/]+$" required>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Transaction No.</span>
                        <input aria-label="Transaction No" type="text" class="form-control" name="transaction_no" value="<?= ($trip_res['transaction_no']) ?>"  readonly>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Transaction Date</span>
                        <input aria-label="Transaction Date" type="text" class="form-control datepicker" name="transaction_date" id="transaction_date" value="<?= fromDate($trip_res['transaction_date']) ?>"   >
                    </div>
                </div>
                <!-- Budget -->
                <div class="col-lg-3">
                    <span class="form-label">Trip Amount</span>
                    <input aria-label="0.00" type="number" step="any" min="0" class="form-control text-right" name="budget_amount" id="budget_amount" placeholder="0.00" value="<?= h($trip_res['budget_amount'] ?? ''); ?>" readonly>

                </div>
                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label d-block">Trip Type <b class="text-danger">*</b></span>
                        <?php if (count((array)$trip_types) > 0) {
                          $mk=1;  foreach($trip_types as $row) { ?>

                                <div class="radio radio-inline">
                                    <label class="form-label mb-0">
                                        <input aria-label="Trip Type" type="radio" name="trip_type" value="<?php echo $row['genc_detail_code']; ?>" <?= (($trip_res['trip_type']=="" && $mk == 1) || $trip_res['trip_type'] == $row['genc_detail_code'])  ? 'checked' : ''; ?>>
                                        <?php echo $row['genc_detail_name'] ?>
                                    </label>
                                </div>

                        <?php  $mk++;
}
                        } ?>

                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Client</span>
                        <select aria-label="Customer" class="form-control js-example-basic-single" name="customer_id" style="-webkit-appearance:auto;-moz-appearance:auto;appearance:auto;">
                            <option value=""> Select Any one </option>
                            <?php if (count((array)$clients_res) > 0) {
                                foreach ($clients_res as $value) {  ?>
                                    <option value="<?php echo $value['id']; ?>" <?php echo $trip_res['customer_id'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['display_name']; ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>
                <!-- Project -->
                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Project</span>
                        <select aria-label="Project" class="form-select js-example-basic-single" name="project_id" style="-webkit-appearance:auto;-moz-appearance:auto;appearance:auto;">
                            <option value=""> Select Any one </option>
                            <?php if (count((array)$projects_res) > 0) {
                                foreach ($projects_res as $value) {  ?>
                                    <option value="<?php echo $value['id']; ?>" <?php echo $trip_res['project_id'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['project_name']; ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                </div>


                <!-- Business Purpose -->
                <div class="col-lg-3">
                    <div class="form-group m-l-5">
                        <span class="form-label">Business Purpose</span>
                        <textarea aria-label="Trip Purpose" class="form-control" name="trip_purpose"><?= h($trip_res['trip_purpose']) ?></textarea>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-header mt-1">
            <h5 class="f-w-600">Trip Itinerary</h5>
        </div>
        <div class="card-body <?php echo $formdisable; ?>">

            <!-- Journey Type Radios -->
            <div class="row">
                <div class="col-lg-12 m-l-5 mt-2">
                    <span class="form-label">Journey Type</span>
                    <div class="form-radio" id="journeyTypeGroup">
                        <?php if (count((array)$journey_types) > 0) {
                            foreach ($journey_types as $row) { ?>
                                <label class="radio radio-inline d-inline-block">
                                    <input aria-label="Journey Type" type="radio" name="journey_type" class="journey-radio" value="<?php echo $row['genc_detail_code'] ?>" data-target=".sec-<?php echo $row['genc_detail_code'] ?>" <?= $journey_selected == $row['genc_detail_code']  ? 'checked' : ''; ?>>
                                    <i class="helper"></i> <?php echo $row['genc_detail_name'] ?>
                                </label>

                        <?php    }
                        } ?>

                    </div>
                </div>
            </div>

            <!-- ONE WAY -->
            <div class="row mt-3 sec-<?php echo $journey_types[0]['genc_detail_code']; ?>">
                <div class="col-lg-12 new-trip">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr style="background-color: #eeeeee;">

                                    <th scope="col" style="width: 20%;">Mode</th>
                                    <th scope="col" style="width: 15%;">Depart From</th>
                                    <th scope="col" style="width: 15%;">Arrive At</th>
                                    <th scope="col" style="width: 15%;">Departure Date</th>
                                    <th scope="col" style="width: 15%;">Departure Time</th>
                                    <th scope="col" style="width: 20%;">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]" onchange="getExpensePolicyamt('journy',this)">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$domestic_modes) > 0) {
                                                foreach ($domestic_modes as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['travel_mode'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['category_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <span class="maxlimit_amt d-block"><?php echo $drt0['estimated_cost']; ?></span>
                                        <input type="hidden" class="maxlimit_amt_text" name="itinerary[0][estimated_cost]" value="<?php echo $drt0['estimated_cost']; ?>">

                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][origin Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][origin_location]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$cities) > 0) {
                                                foreach ($cities as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['origin_location'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['city_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][destination Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][destination_location]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$cities) > 0) {
                                                foreach ($cities as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['destination_location'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['city_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Itinerary[0][departure Date]" type="text" class="form-control datepicker" name="itinerary[0][departure_date]" value="<?php echo fromDate($drt0['departure_date']); ?>" ></td>

                                    <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" value="<?php echo  $drt0['departure_time'] ?? ''; ?>" ></td>

                                    <td><textarea aria-label="Itinerary[0][trip Purpose]" class="form-control" name="itinerary[0][trip_purpose]"><?php echo  $drt0['trip_purpose'] ?? ''; ?></textarea></td>
                                </tr>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row mt-3 sec-<?php echo $journey_types[1]['genc_detail_code']; ?>" style="display:none">
                <div class="col-lg-12 new-trip">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr style="background-color: #eeeeee;">
                                    <th scope="col" style="width: 10%;">Mode</th>
                                    <th scope="col" style="width: 10%;">Depart From</th>
                                    <th scope="col" style="width: 10%;">Arrive At</th>
                                    <th scope="col" style="width: 15%;">Departure Date</th>
                                    <th scope="col" style="width: 10%;">Time</th>
                                    <th scope="col" style="width: 15%;">Return Date</th>
                                    <th scope="col" style="width: 10%;">Time</th>
                                    <th scope="col" style="width: 20%;">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Leg 0 -->
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]" onchange="getExpensePolicyamt('journy',this)">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$domestic_modes) > 0) {
                                                foreach ($domestic_modes as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['travel_mode'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['category_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                        <span class="maxlimit_amt d-block"><?php echo $drt0['estimated_cost']; ?></span>
                                        <input type="hidden" class="maxlimit_amt_text" name="itinerary[0][estimated_cost]" value="<?php echo $drt0['estimated_cost']; ?>">
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][origin Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][origin_location]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$cities) > 0) {
                                                foreach ($cities as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['origin_location'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['city_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][destination Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][destination_location]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$cities) > 0) {
                                                foreach ($cities as $value) {  ?>
                                                    <option value="<?php echo $value['id']; ?>" <?php echo $drt0['destination_location'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['city_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Itinerary[0][departure Date]" type="text" class="form-control datepicker" name="itinerary[0][departure_date]" id="round_dep_date" value="<?php echo  fromDate($drt0['departure_date']); ?>" ></td>
                                    <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" value="<?php echo  $drt0['departure_time'] ?? ''; ?>" ></td>
                                    <td><input aria-label="Itinerary[0][return Date]" type="text" class="form-control datepicker" name="itinerary[0][return_date]" id="round_ret_date" value="<?php echo  fromDate($drt0['return_date']); ?>" ></td>
                                    <td><input aria-label="Itinerary[0][return Time]" type="time" class="form-control" name="itinerary[0][return_time]" value="<?php echo  $drt0['return_time'] ?? ''; ?>" ></td>
                                    <td><textarea aria-label="Itinerary[0][trip Purpose]" class="form-control" name="itinerary[0][trip_purpose]"><?php echo  $drt0['trip_purpose'] ?? ''; ?></textarea></td>
                                </tr>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row mt-3 sec-<?php echo $journey_types[2]['genc_detail_code']; ?>" style="display:none">
                <div class="col-lg-12 new-trip">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tblItinMulti">
                            <thead>
                                <tr style="background-color: #eeeeee;">
                                    <th scope="col" style="width: 15%;">Mode</th>
                                    <th scope="col" style="width: 15%;">Depart From</th>
                                    <th scope="col" style="width: 15%;">Arrive At</th>
                                    <th scope="col" style="width: 15%;">Departure Date</th>
                                    <th scope="col" style="width: 15%;">Departure Time</th>
                                    <th scope="col" style="width: 20%;">Description</th>
                                    <th scope="col" style="width: 5%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itinMultiBody">
                                <?php if ($journey_selected == '3' && count($domestic_round_trips)): // adjust code "3" to your multi code
                                ?>
                                    <?php foreach ($domestic_round_trips as $i => $leg): ?>
                                        <tr>
                                            <td class="w-full leg-no">
                                                <select aria-label="Select option" class="form-select js-example-basic-single" name="itinerary[<?= $i ?>][travel_mode]" onchange="getExpensePolicyamt('journy',this)">
                                                    <option value=""> Select Any one </option>
                                                    <?php foreach ((array)$domestic_modes as $value): ?>
                                                         <option value="<?php echo $value['id']; ?>" <?php echo $leg['travel_mode'] == $value['id'] ? 'selected' : '' ?>> <?php echo  $value['category_name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <span class="maxlimit_amt d-block"><?php echo $leg['estimated_cost']; ?></span>
                                                <input type="hidden" class="maxlimit_amt_text" name="itinerary[0][estimated_cost]" value="<?php echo $leg['estimated_cost']; ?>">
                                            </td>
                                            <td class="w-full">
                                                <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="itinerary[<?= $i ?>][origin_location]">
                                                    <option value=""> Select Any one </option>
                                                    <?php foreach ((array)$cities as $value): ?>
                                                        <option value="<?= $value['id'] ?>" <?= ($leg['origin_location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($value['city_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="w-full">
                                                <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="itinerary[<?= $i ?>][destination_location]">
                                                    <option value=""> Select Any one </option>
                                                    <?php foreach ((array)$cities as $value): ?>
                                                        <option value="<?= $value['id'] ?>" <?= ($leg['destination_location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($value['city_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td><input aria-label="Input field" type="text" class="form-control datepicker" name="itinerary[<?= $i ?>][departure_date]" value="<?= fromDate($leg['departure_date']) ?>"></td>
                                            <td><input aria-label="Input field" type="time" class="form-control" name="itinerary[<?= $i ?>][departure_time]" value="<?= htmlspecialchars(substr($leg['departure_time'] ?? '', 0, 5)) ?>" ></td>
                                            <td><textarea aria-label="Text area" class="form-control" name="itinerary[<?= $i ?>][trip_purpose]"><?= htmlspecialchars($leg['trip_purpose'] ?? '') ?></textarea></td>
                                            <td class="text-center"><i class="fa fa-trash text-danger itin-remove" style="cursor:pointer"></i></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="w-full leg-no">
                                            <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]" onchange="getExpensePolicyamt('journy',this)">
                                                <option value=""> Select Any one </option>
                                                <?php if (count((array)$domestic_modes) > 0) {
                                                    foreach ($domestic_modes as $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>"> <?php echo  $value['category_name']; ?></option>

                                                <?php }
                                                } ?>
                                            </select>
                                            <span class="maxlimit_amt d-block"></span>
                                            <input type="hidden" class="maxlimit_amt_text" name="itinerary[0][estimated_cost]" value="0">
                                        </td>
                                        <td class="w-full">
                                            <select aria-label="Itinerary[0][origin Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][origin_location]">
                                                <option value=""> Select Any one </option>
                                                <?php if (count((array)$cities) > 0) {
                                                    foreach ($cities as $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </td>
                                        <td class="w-full">
                                            <select aria-label="Itinerary[0][destination Location]" class="form-select js-example-basic-single city-select" name="itinerary[0][destination_location]">
                                                <option value=""> Select Any one </option>
                                                <?php if (count((array)$cities) > 0) {
                                                    foreach ($cities as $value) {  ?>
                                                        <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </td>
                                        <td><input aria-label="Itinerary[0][departure Date]" type="text" class="form-control datepicker" name="itinerary[0][departure_date]" ></td>
                                        <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" ></td>
                                        <td><textarea aria-label="Itinerary[0][trip Purpose]" class="form-control" name="itinerary[0][trip_purpose]"></textarea></td>
                                        <td class="text-center"><i class="fa fa-trash text-danger itin-remove" style="cursor:pointer"></i></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <button type="button" id="btnAddItin" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Journey
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- HOTEL -->
            <h4 class="mt-2">Hotel</h4>

            <div class="table-responsive mt-1 ">
                <table class="table table-hover" id="tblHotels">
                    <thead>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col" style="width: 15%;">Location</th>
                            <th scope="col" style="width: 20%;">Hotel Name</th>
                            <th scope="col" style="width: 15%;">Check-In</th>
                            <th scope="col" style="width: 15%;">Check-Out</th>
                            <th scope="col" style="width: 20%;">Description</th>
                            <th scope="col" style="width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hotelBody">
                        <?php if (count((array)$trip_hotels_list) > 0): ?>
                            <?php foreach ($trip_hotels_list as $i => $h): ?>
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="hotel[<?= $i ?>][location]" onchange="getExpensePolicyamt('hotel',this)">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$cities as $value): ?>
                                                <option value="<?= $value['id'] ?>" <?= ($h['location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['city_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Input field" type="text" class="form-control" name="hotel[<?= $i ?>][hotel_name]" value="<?= htmlspecialchars($h['hotel_name'] ?? '') ?>"></td>
                                    <td><input aria-label="Input field" type="text" class="form-control datepicker" name="hotel[<?= $i ?>][check_in]" value="<?= htmlspecialchars($h['check_in'] ?? '') ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                    <td><input aria-label="Input field" type="text" class="form-control datepicker" name="hotel[<?= $i ?>][check_out]" value="<?= htmlspecialchars($h['check_out'] ?? '') ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                    <td><textarea aria-label="Text area" class="form-control" name="hotel[<?= $i ?>][description]"><?= htmlspecialchars($h['description'] ?? '') ?></textarea></td>
                                    <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>

                                <td class="w-full">
                                    <select aria-label="Hotel[0][location]" class="form-select js-example-basic-single city-select" name="hotel[0][location]" onchange="getExpensePolicyamt('hotel',this)">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$cities) > 0) {
                                            foreach ($cities as $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </td>
                                <td><input aria-label="Hotel[0][hotel Name]" type="text" class="form-control" name="hotel[0][hotel_name]" value=""></td>

                                <td><input aria-label="Hotel[0][check In]" type="text" class="form-control datepicker" name="hotel[0][check_in]" value="" min="<?php echo date('Y-m-d'); ?>"></td>
                                <td><input aria-label="Hotel[0][check Out]" type="text" class="form-control datepicker" name="hotel[0][check_out]" value="" min="<?php echo date('Y-m-d'); ?>"></td>
                                <td><textarea aria-label="Hotel[0][description]" class="form-control" name="hotel[0][description]"></textarea></td>
                                <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="4">

                                <button type="button" id="btnAddHotel" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Hotel
                                </button>
                            </td>
                            <td colspan="2">
                                <span class="d-block" id="hoteltotal"><?php echo $trip_hotels_list[0]['cost']; ?></span>
                                <input type="hidden" name="hotel_estimated_cost"  id="hotel_estimated_cost" value="<?php echo $trip_hotels_list[0]['cost']; ?>">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- CAR RENT -->
            <h4 class="mt-4">Local</h4>

            <div class="table-responsive mt-1">
                <table class="table table-hover" id="tblCars">
                    <thead>
                        <tr style="background-color: #eeeeee;">
                            <th scope="col" style="width: 15%;">Mode</th>
                            <th scope="col" style="width: 15%;">Pick-Up Location</th>
                            <th scope="col" style="width: 15%;">Drop Location</th>
                            <th scope="col" style="width: 15%;">Pick-Up</th>
                            <th scope="col" style="width: 15%;">Drop</th>


                            <th scope="col" style="width: 15%;">Description</th>
                            <th scope="col" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="carBody">
                        <?php if (count($local_trips_list)): ?>
                            <?php foreach ($local_trips_list as $i => $c): ?>
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single" name="car[<?= $i ?>][travel_mode]" onchange="getExpensePolicyamt('localtrip',this)">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$local_modes as $value): ?>
                                                <option value="<?= $value['genc_detail_code'] ?>" <?= ($c['travel_mode'] ?? null) == $value['genc_detail_code'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['genc_detail_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="maxlimit_amt d-block"><?php echo $c['estimated_cost']!=""?"Per KM/Mile -".$c['estimated_cost']:""; ?></span>
                                        <input type="hidden" class="maxlimit_amt_text" name="car[0][estimated_cost]" value="<?php echo $c['estimated_cost']; ?>">
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="car[<?= $i ?>][from_location]">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$cities as $value): ?>
                                                <option value="<?= $value['id'] ?>" <?= ($c['from_location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['city_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="car[<?= $i ?>][to_location]">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$cities as $value): ?>
                                                <option value="<?= $value['id'] ?>" <?= ($c['to_location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['city_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Input field" type="datetime-local" class="form-control" name="car[<?= $i ?>][pickup_at]" value="<?= htmlspecialchars(!empty($c['pickup_at']) ? date('Y-m-d\TH:i', strtotime($c['pickup_at'])) : '') ?>"></td>
                                    <td><input aria-label="Input field" type="datetime-local" class="form-control" name="car[<?= $i ?>][dropoff_at]" value="<?= htmlspecialchars(!empty($c['dropoff_at']) ? date('Y-m-d\TH:i', strtotime($c['dropoff_at'])) : '') ?>"></td>


                                    <td><textarea aria-label="Text area" class="form-control" name="car[<?= $i ?>][trip_purpose]"><?= htmlspecialchars($c['trip_purpose'] ?? '') ?></textarea></td>
                                    <td class="text-center"><i class="fa fa-trash text-danger car-remove" style="cursor:pointer"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="w-full">
                                    <select aria-label="Car[0][travel Mode]" class="form-select js-example-basic-single" name="car[0][travel_mode]"  onchange="getExpensePolicyamt('localtrip',this)">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$local_modes) > 0) {
                                            foreach ($local_modes as $value) {  ?>
                                                <option value="<?php echo $value['genc_detail_code']; ?>"> <?php echo  $value['genc_detail_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                     <span class="maxlimit_amt d-block"></span>
                                     <input type="hidden" class="maxlimit_amt_text" name="car[0][estimated_cost]" value="">
                                </td>

                                <td class="w-full">
                                    <select aria-label="Car[0][from Location]" class="form-select js-example-basic-single city-select" name="car[0][from_location]">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$cities) > 0) {
                                            foreach ($cities as $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </td>
                                <td class="w-full">
                                    <select aria-label="Car[0][to Location]" class="form-select js-example-basic-single city-select" name="car[0][to_location]">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$cities) > 0) {
                                            foreach ($cities as $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </td>
                                <td><input aria-label="Car[0][pickup At]" type="datetime-local" class="form-control" name="car[0][pickup_at]" value=""></td>
                                <td><input aria-label="Car[0][dropoff At]" type="datetime-local" class="form-control" name="car[0][dropoff_at]" value=""></td>

                                <td><textarea aria-label="Car[0][trip Purpose]" class="form-control" name="car[0][trip_purpose]"></textarea></td>
                                <td class="text-center"><i class="fa fa-trash text-danger car-remove" style="cursor:pointer"></i></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="9">
                                <button type="button" id="btnAddCar" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Local Rental
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php if (empty($rid)) { ?>
                        <button type="submit" name="submit" value="insert" class="btn btn-primary mt-2 <?php echo $disable; ?>">
                            <i class="fa fa-save"></i> Save
                        </button>
                    <?php } else { ?>
                        <input type="hidden" name="trip_id" id="trip_id" value="<?= (int)$rid ?>" />
                        <button type="submit" name="submit" value="update" class="btn btn-primary mt-2 <?php echo $disable; ?>">
                            <i class="fa fa-save"></i> Save
                        </button>
                    <?php } ?>

                </div>
            </div>

        </div> <!-- card-body -->
    </div> <!-- card -->
</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
<script>

(function(){
  const groups = [".sec-1",".sec-2",".sec-3"];
  function toggleJourney(selShow){
    groups.forEach(sel=>{
      const sec = document.querySelector(sel);
      if(!sec) return;
      const hide = sel !== selShow;
      sec.style.display = hide ? "none" : "block";
      sec.querySelectorAll("input,select,textarea").forEach(el=>el.disabled = hide);
    });
  }
  const checked = document.querySelector('.journey-radio:checked');
  toggleJourney(checked ? checked.dataset.target : ".sec-1");
  document.getElementById('journeyTypeGroup')?.addEventListener('change', e=>{
    if(e.target?.classList.contains('journey-radio')) toggleJourney(e.target.dataset.target);
  });

  // ------- small helpers -------

  function reindex(tbodySelector, prefix){
    document.querySelectorAll(`${tbodySelector} tr`).forEach((tr,i)=>{
      tr.querySelectorAll('select, input, textarea').forEach(el=>{
        if(el.name && el.name.startsWith(prefix+'[')){
          el.name = el.name.replace(new RegExp(prefix + '\\[\\d+\\]'), `${prefix}[${i}]`);
        }
      });
      const n = tr.querySelector('.leg-no');
      if(n) n.textContent = (i+1);
    });
  }

  // ------- delegated add/remove -------
  var idx = 100000; var hoidx = 200000; var tridx = 200000;
  document.addEventListener('click', function(e){
    if(e.target?.id === 'btnAddItin'){
      const tbody = $('#itinMultiBody');
      idx = idx+1;
      const rowHtml = `
        <tr>
          <td class="w-full leg-no">
            <select aria-label="Itinerary[${idx}][travel Mode]" class="form-select js-example-basic-single" name="itinerary[${idx}][travel_mode]" onchange="getExpensePolicyamt('hotel',this)">
              <option value="" > Select Any one </option>
              <?php foreach((array)$domestic_modes as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['category_name']) ?></option>
              <?php } ?>
            </select>
            <span class="maxlimit_amt d-block"></span>
            <input type="hidden" class="maxlimit_amt_text" name="itinerary[0][estimated_cost]" value="0">
          </td>
          <td class="w-full">
            <select aria-label="Itinerary[${idx}][origin Location]" class="form-select js-example-basic-single city-select" name="itinerary[${idx}][origin_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td class="w-full">
            <select aria-label="Itinerary[${idx}][destination Location]" class="form-select js-example-basic-single city-select" name="itinerary[${idx}][destination_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td><input aria-label="Itinerary[${idx}][departure Date]" type="text" class="form-control datepicker" name="itinerary[${idx}][departure_date]" ></td>
          <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" ></td>
          <td><textarea aria-label="Itinerary[${idx}][trip Purpose]" class="form-control" name="itinerary[${idx}][trip_purpose]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger itin-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
       var backdate_limit = 0;
      $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        minDate: backdate_limit,
        //yearRange: "-60:+10",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
    }

    // Add hotel
    if(e.target?.id === 'btnAddHotel'){
      const tbody = $('#hotelBody');
      hoidx  = hoidx+1;
      const rowHtml = `
        <tr>
          <td class="w-full">
            <select aria-label="Hotel[${hoidx}][location]" class="form-select js-example-basic-single city-select" name="hotel[${hoidx}][location]" onchange="getExpensePolicyamt('hotel',this)">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td><input aria-label="Hotel[${hoidx}][hotel Name]" type="text" class="form-control" name="hotel[${hoidx}][hotel_name]"></td>
          <td><input aria-label="Hotel[${hoidx}][check In]" type="text" class="form-control datepicker" name="hotel[${hoidx}][check_in]" min="<?php echo date('Y-m-d'); ?>"></td>
          <td><input aria-label="Hotel[${hoidx}][check Out]" type="text" class="form-control datepicker" name="hotel[${hoidx}][check_out]" min="<?php echo date('Y-m-d'); ?>"></td>
          <td><textarea aria-label="Hotel[${hoidx}][description]" class="form-control" name="hotel[${hoidx}][description]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
      var backdate_limit = 0;
      $( ".datepicker" ).datepicker({
        showOn: "both",
        buttonText: "Calendar",
        dateFormat: date_format,
        minDate: backdate_limit,
       // yearRange: "-60:+10",
        setDate: new Date(),
        changeMonth: true,
        changeYear: true
    }).next("button.ui-datepicker-trigger").html("<i class='ti-calendar text-dark f-12 f-w-800 m-r-10'></i>");
    }

    // Add car/local
    if(e.target?.id === 'btnAddCar'){
      const tbody = $('#carBody');
      tridx = tridx+1;
      const rowHtml = `
        <tr>
          <td class="w-full">
            <select aria-label="Car[${tridx}][travel Mode]" class="form-select js-example-basic-single" name="car[${tridx}][travel_mode]" onchange="getExpensePolicyamt('localtrip',this)">
              <option value=""> Select Any one </option>
              <?php foreach((array)$local_modes as $v){ ?>
                <option value="<?= $v['genc_detail_code'] ?>"><?= htmlspecialchars($v['genc_detail_name']) ?></option>
              <?php } ?>
            </select>
            <span class="maxlimit_amt d-block"></span>
            <input type="hidden" class="maxlimit_amt_text" name="car[0][estimated_cost]" value="0">
          </td>
          <td class="w-full">
            <select aria-label="Car[${tridx}][from Location]" class="form-select js-example-basic-single city-select" name="car[${tridx}][from_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td class="w-full">
            <select aria-label="Car[${tridx}][to Location]" class="form-select js-example-basic-single city-select" name="car[${tridx}][to_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td><input aria-label="Car[${tridx}][pickup At]" type="datetime-local" class="form-control" name="car[${tridx}][pickup_at]"></td>
          <td><input aria-label="Car[${tridx}][dropoff At]" type="datetime-local" class="form-control" name="car[${tridx}][dropoff_at]"></td>
          <td><textarea aria-label="Car[${tridx}][trip Purpose]" class="form-control" name="car[${tridx}][trip_purpose]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger car-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
      //       showOn: "both",
      //       dateFormat: date_format,
      //       minDate: backdate_limit,
      //       changeMonth: true,
      //       changeYear: true
    }

    // Remove handlers + reindex
    if(e.target?.classList.contains('itin-remove')){
        const tbody = document.querySelector('#itinMultiBody');
        const tr = e.target.closest('tr');
      if (tbody.querySelectorAll('tr').length === 1) {
        tr.querySelectorAll('input,textarea,select').forEach(el=>{ if(el.type==='radio'){el.checked=(el.value==='1')} else {el.value=''}; });
      } else { tr.remove(); reindex('#itinMultiBody','itinerary'); }

    }
    if(e.target?.classList.contains('hotel-remove')){
      const tbody = document.querySelector('#hotelBody');
      const tr = e.target.closest('tr');
      if (tbody.querySelectorAll('tr').length === 1) {
        tr.querySelectorAll('input,textarea,select').forEach(el=>{ if(el.type==='radio'){el.checked=(el.value==='1')} else {el.value=''}; });
      } else { tr.remove(); reindex('#hotelBody','hotel'); }
    }
    if(e.target?.classList.contains('car-remove')){
      const tbody = document.querySelector('#carBody');
      const tr = e.target.closest('tr');
      if (tbody.querySelectorAll('tr').length === 1) {
        tr.querySelectorAll('input,textarea,select').forEach(el=>{ if(el.type==='radio'){el.checked=(el.value==='1')} else {el.value=''}; });
      } else { tr.remove(); reindex('#carBody','car'); }
    }
  });
})();

document.getElementById('tripForm')?.addEventListener('submit', function (e) {
  const form = e.target;

  const tripName = (form.trip_name?.value || '').trim();
  const tripTypeChosen = [...form.querySelectorAll('input[name="trip_type"]')].some(r => r.checked);

  const errs = [];
  if (tripName.length === 0) errs.push('Trip Name is required.');
  if (!/^[\w\s\-'\.&(),/]+$/.test(tripName)) errs.push('Trip Name has invalid characters.');
  if (!tripTypeChosen) errs.push('Trip Type is required.');

  if (errs.length) {
    e.preventDefault();
    tostarnotify(
      'top','right','fa fa-exclamation-triangle',
      'form-label label label-danger','animated flipInX','animated flipOutX',
      errs.join('<br>'),''
    );
    form.trip_name?.focus();
  }
});

document.getElementById('round_ret_date')?.addEventListener('change', function() {
  const dep = document.getElementById('round_dep_date');
  const ret = this;
  if (dep && dep.value) {
    if (new Date(ret.value) <= new Date(dep.value)) {
      tostarnotify(
        'top','right','fa fa-exclamation-triangle',
        'form-label label label-danger','animated flipInX','animated flipOutX',
        'Return Date must be after Departure Date.',''
      );
      ret.value = '';
      ret.focus();
    }
  }else{
    tostarnotify(
        'top','right','fa fa-exclamation-triangle',
        'form-label label label-danger','animated flipInX','animated flipOutX',
        'Please Select Departure Date First',''
      );
      ret.value = ''; // clear invalid
      ret.focus();

  }
});

</script>
<script>
// === HOTEL DATE VALIDATION ===

(function () {
  const hotelBody = document.getElementById('hotelBody');
  if (!hotelBody) return;

  // Add days to a yyyy-mm-dd string
  function addDays(iso, days) {
    const d = new Date(iso + 'T00:00:00');
    d.setDate(d.getDate() + days);
    return d.toISOString().slice(0, 10);
  }

  // Validate a single table row
  function validateHotelRow(tr, {notify = true} = {}) {
    const cin  = tr.querySelector('input[name*="[check_in]"]');
    const cout = tr.querySelector('input[name*="[check_out]"]');
    if (!cin || !cout) return true; // nothing to validate

    const ci = cin.value?.trim();
    const co = cout.value?.trim();

    // keep UX nice: enforce min on checkout whenever we can
    if (ci) {
      // min checkout is the NEXT day because rule is strictly >
      cout.min = addDays(ci, 1);
    } else {
      cout.removeAttribute('min');
    }

    if (ci && co && (co <= ci)) {
      if (notify && typeof tostarnotify === 'function') {
        tostarnotify(
          'top','right','fa fa-exclamation-triangle',
          'form-label label label-danger','animated flipInX','animated flipOutX',
          'Hotel Check-Out must be after Check-In.',''
        );
      }
      cout.value = '';
      cout.focus();
      return false;
    }
    return true;
  }

  hotelBody.addEventListener('change', function (e) {
    const target = e.target;
    if (!target.matches('input[name*="[check_in]"], input[name*="[check_out]"]')) return;
    const tr = target.closest('tr');
    if (tr) validateHotelRow(tr);
  });

})();
</script>

<script>
(function () {
  const carBody = document.getElementById('carBody');

  function toast(msg) {
    // uses your existing toaster
    try {
     } catch(e) { console.warn(msg); }
  }

  function asDate(el) {
    return el && el.value ? new Date(el.value) : null;
  }

  // Validate a single Local row
  function validateCarRow(tr, { notify = true } = {}) {
    const pick = tr.querySelector('input[name$="[pickup_at]"]');
    const drop = tr.querySelector('input[name$="[dropoff_at]"]');

    if (!pick || !drop) return true; // nothing to validate here

    const p = asDate(pick);
    const d = asDate(drop);

    // keep the min on drop in sync with pickup
    drop.min = pick.value || "";

    if (p && d && d <= p) {
      if (notify){
         tostarnotify('top','right','fa fa-exclamation-triangle','form-label label label-danger','animated flipInX','animated flipOutX', 'Drop time must be after Pickup time', '');
      }
      drop.value = '';
      drop.focus();
      return false;
    }
    return true;
  }

  // Delegate changes inside the Local table body
  carBody?.addEventListener('change', function (e) {
    const t = e.target;
    if (!t.matches('input[name$="[pickup_at]"], input[name$="[dropoff_at]"]')) return;
    const tr = t.closest('tr');
    if (!tr) return;

    if (t.name.endsWith('[pickup_at]')) {
      const drop = tr.querySelector('input[name$="[dropoff_at]"]');
      if (drop) drop.min = t.value || "";
    }
    validateCarRow(tr, { notify: true });
  });

})();

function initBasicSelect2($ctx){
  $ctx.find('.js-example-basic-single:not(.city-select)').each(function (){
    if (!$(this).data('select2')) {
      $(this).select2({ width: '100%' });
    }
  });
}

// point this to your API
const CITY_API = 'API.php?mode=search_cities';

function initCitySelect($ctx) {
  $ctx.find('.city-select').each(function () {
    if ($(this).data('select2')) {
      $(this).select2('destroy');
    }
    console.log('init city select', this.name);
    $(this).select2({
      width: '100%',
      placeholder: 'Select a city',
      allowClear: true,
      minimumInputLength: 2,      // start after 2 chars
      ajax: {
        url: 'ajax.php',
        type: "POST",
        dataType: 'json',
        delay: 250,               // debounce
        cache: true,
        data: function (params) {
          return {
            q: params.term || '', // search text
            pagination: params.page || 1, // pagination
            'page' : 'getDynamicCities' // server mode'
          };
        },
        processResults: function (data, params) {

          params.page = params.page || 1;
          // Expecting { items:[{id, city_name}], has_more:true/false }
          return {
            results: (data.cities_res || []).map(c => ({ id: c.id, text: c.city_name })),
            pagination: { more: !!data.has_more }
          };
        }
      }
    });
  });
}

$(function () {
  initBasicSelect2($(document));  // non-city selects
  initCitySelect($(document));    // city selects with AJAX
});

$(document).on('select2:open', '.city-select', function(){ console.log('opened', this.name); });
$(document).on('select2:select', '.city-select', function(e){ console.log('selected', e.params.data); });

function submitDynamicWorkflowPopUp(table_name,transaction_id,current_status,next_status){
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#popup1").html(loader);
    $('#kt_modal_KTDatatable_remote').modal("show");
    jQuery.ajax({
        url: "views/custom_insert_dynamic_status_popup.php",
        data:'table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
        type: "POST",
        success:function(data){
            $('#popup1').html(data);
        },
        error:function (){}
    });
}

function submitDynamicWorkflow(table_name,transaction_id,current_status,next_status,old_status_name,new_status_name){
    swal({
        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
        text: "Please note the status of the document will change to "+new_status_name,
        type: "info",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
    function(){
        jQuery.ajax({
            url: "ajax.php",
            data:'page=submitstatusdynamicworkflow&table_name='+table_name+'&current_status='+current_status+'&transaction_id='+transaction_id+'&next_status='+next_status,
            type: "POST",
            success:function(response){
                console.log(response);
                var res = JSON.parse(response);
                window.location.href = window.location.href;
                if(res.transaction_update.status=='sucess'){
                     window.location.href = window.location.href;
                    swal({
                        title: "<?php echo ucwords($_SESSION['config']['name']); ?>",
                        text: "<?php echo $_SESSION['error_message']['9059']; ?>",
                        type: "success",
                        showCancelButton: false,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function () {
                        window.location.href = window.location.href;
                    });
                }else{
                    swal({
                        title: "Oops...",
                        text: res.transaction_update.message,
                        type: "error",
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: "Ok",
                        confirmButtonClass: "btn-danger",
                        closeOnCancel: false
                    });
                }
            },
            error:function (){}
        });
    });

}

function getExpensePolicyamt(trip_type,tis){
    var trip_id = $("trip_id").val();
    var journyid = $(tis).val();
    var transaction_date = $("#transaction_date").val();
    if(trip_type=="hotel"){
        journyid = 6;
    }
    jQuery.ajax({
        url: "ajax.php",
        data:'page=getTrevllingPlanData&journyid='+journyid+"&rid="+trip_id+"&trip_type="+trip_type+"&transaction_date="+transaction_date,
        type: "POST",
        success:function(response){
            var res = JSON.parse(response);
            if(res.response == "Y" && trip_type == "journy"){
                 $(tis).nextAll(".maxlimit_amt:first").text(res.limit);
                 $(tis).nextAll(".maxlimit_amt_text:first").val(res.limit);
                 console.log("cdscsdcsdcsdc");
            }else if(trip_type =="hotel"){
                $("#hoteltotal").text("Max Limit - "+res.limit);
                $("#hotel_estimated_cost").val(res.limit);
            }else if(res.response == "Y" && trip_type == "localtrip"){
                 $(tis).nextAll(".maxlimit_amt:first").text("Per KM/Mile - "+res.limit);
                 $(tis).nextAll(".maxlimit_amt_text:first").val(res.limit);
            }
            var totalval = 0;
            $('.maxlimit_amt_text').each(function(){
                totalval = parseFloat(totalval)+parseFloat($(this).val()!=""?$(this).val():0);

            });
            totalval = parseFloat(totalval)+parseFloat($("#hotel_estimated_cost").val()!=""?$("#hotel_estimated_cost").val():0);
            $("#budget_amount").val(totalval);
        }
    });
}
</script>

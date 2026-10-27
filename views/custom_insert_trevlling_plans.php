<?php
// custom_insert_trevlling_plans.php

if (!empty($_POST) && ($_POST['submit'] ?? '') === 'insert') {
    $type = $_POST['submit'];

    $url = API . "?mode=insertAndUpdateTrevllingPlans&method=post";
    $resp = file_post_contents($url, $_POST, $type);
    $result = json_decode($resp, true);
    if ($result['trip_insert']['status']  == 'sucess') {
        echo "<script>document.addEventListener('DOMContentLoaded',()=>tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Trip saved successfully ',''))</script>";
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

$rid = 17; // testing purpose
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

    <div class="card">
        <div class="card-header">
            <h5>New Trip</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group m-l-5">
                        <span class="form-label">Transaction No.</span>
                        <input aria-label="Transaction No" type="text" class="form-control b-radius-5" name="transaction_no" value="<?= h($trip_res['transaction_no']) ?>"  readonly>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group m-l-5">
                        <span class="form-label">Transaction Date</span>
                        <input aria-label="Transaction Date" type="date" class="form-control b-radius-5" name="transaction_date" value="<?= h($trip_res['transaction_date']) ?>"   readonly>
                    </div>
                </div>
                <div class="col-lg-4"></div>

                <div class="col-lg-4">
                    <div class="form-group m-l-5">
                        <span class="form-label">Trip Name <b class="text-danger">*</b></span>
                        <input aria-label="Trip Name" type="text" class="form-control b-radius-5" name="trip_name" value="<?= h($trip_res['trip_name']) ?>"   minlength="3" maxlength="120" pattern="^[\w\s\-'\.&(),/]+$" required>
                    </div>
                </div>


                <div class="col-lg-4">
                    <div class="form-group m-l-5">
                        <span class="form-label d-block">Trip Type <b class="text-danger">*</b></span>
                        <?php if (count((array)$trip_types) > 0) {
                            foreach ($trip_types as $row) { ?>

                                <div class="radio radio-inline">
                                    <label class="form-label mb-0">
                                        <input aria-label="Trip Type" type="radio" name="trip_type" value="<?php echo $row['genc_detail_code']; ?>" <?= $trip_res['trip_type'] == $row['genc_detail_code']  ? 'checked' : ''; ?>>
                                        <?php echo $row['genc_detail_name'] ?>
                                    </label>
                                </div>

                        <?php    }
                        } ?>

                    </div>
                </div>

                <!-- Business Purpose -->
                <div class="col-lg-4">
                    <div class="form-group m-l-5">
                        <span class="form-label">Business Purpose</span>
                        <textarea aria-label="Trip Purpose" class="form-control" name="trip_purpose"><?= h($trip_res['trip_purpose']) ?></textarea>
                    </div>
                </div>

                <!-- Project -->
                <div class="col-lg-4">
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

                <!-- Budget -->
                <div class="col-lg-2">
                    <div class="form-group m-l-5">
                        <span class="form-label">Budget Amount</span>
                        <div class="input-group">
                            <span class="input-group-text addon-style"><i class="fa fa-inr"></i></span>
                            <input aria-label="0.00" type="number" step="any" min="0" class="form-control" name="budget_amount" placeholder="0.00" value="<?= h($trip_res['budget_amount'] ?? ''); ?>">
                        </div>
                    </div>
                </div>

                <!-- Final Amount -->
                <div class="col-lg-2">
                    <div class="form-group m-l-5">
                        <span class="form-label">Final Amount</span>
                        <div class="input-group">
                            <span class="input-group-text addon-style"><i class="fa fa-dollar-sign"></i></span>
                            <input aria-label="0.00" type="number" step="any" min="0" class="form-control" name="final_amount" placeholder="0.00" value="<?= h($trip_res['final_amount'] ?? ''); ?>">
                        </div>
                    </div>
                </div>


                <div class="col-lg-4">
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

            </div>
        </div>

        <div class="card-header mt-1">
            <h5 class="f-w-600">Trip Itinerary</h5>
        </div>
        <div class="card-body">

            <!-- Journey Type Radios -->
            <div class="row">
                <div class="col-lg-12 m-l-5 mt-2">
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
                                    <th scope="col">#</th>
                                    <th scope="col">Mode</th>
                                    <th scope="col">Depart From</th>
                                    <th scope="col">Arrive At</th>
                                    <th scope="col">Departure Date</th>
                                    <th scope="col">Departure Time</th>
                                    <th scope="col">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$domestic_modes) > 0) {
                                                foreach ($domestic_modes as $value) {  ?>
                                                    <option value="<?php echo $value['genc_detail_code']; ?>" <?php echo $drt0['travel_mode'] == $value['genc_detail_code'] ? 'selected' : '' ?>> <?php echo  $value['genc_detail_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
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
                                    <td><input aria-label="Itinerary[0][departure Date]" type="date" class="form-control" name="itinerary[0][departure_date]" value="<?php echo  $drt0['departure_date'] ?? ''; ?>" min="<?php echo date('Y-m-d'); ?>"></td>
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
                                    <th scope="col">#</th>
                                    <th scope="col">Mode</th>
                                    <th scope="col">Depart From</th>
                                    <th scope="col">Arrive At</th>
                                    <th scope="col">Departure Date</th>
                                    <th scope="col">Departure Time</th>
                                    <th scope="col">Return Date</th>
                                    <th scope="col">Return Time</th>
                                    <th scope="col">Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Leg 0 -->
                                <tr>
                                    <td>1</td>
                                    <td class="w-full">
                                        <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]">
                                            <option value=""> Select Any one </option>
                                            <?php if (count((array)$domestic_modes) > 0) {
                                                foreach ($domestic_modes as $value) {  ?>
                                                    <option value="<?php echo $value['genc_detail_code']; ?>" <?php echo $drt0['travel_mode'] == $value['genc_detail_code'] ? 'selected' : '' ?>> <?php echo  $value['genc_detail_name']; ?></option>
                                            <?php }
                                            } ?>
                                        </select>
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
                                    <td><input aria-label="Itinerary[0][departure Date]" type="date" class="form-control" name="itinerary[0][departure_date]" id="round_dep_date" value="<?php echo  $drt0['departure_date'] ?? ''; ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                    <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" value="<?php echo  $drt0['departure_time'] ?? ''; ?>" ></td>
                                    <td><input aria-label="Itinerary[0][return Date]" type="date" class="form-control" name="itinerary[0][return_date]" id="round_ret_date" value="<?php echo  $drt0['return_date'] ?? ''; ?>" min="<?php echo date('Y-m-d'); ?>"></td>
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
                                    <th scope="col">#</th>
                                    <th scope="col">Mode</th>
                                    <th scope="col">Depart From</th>
                                    <th scope="col">Arrive At</th>
                                    <th scope="col">Departure Date</th>
                                    <th scope="col">Departure Time</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itinMultiBody">
                                <?php if ($journey_selected == '3' && count($domestic_round_trips)): // adjust code "3" to your multi code
                                ?>
                                    <?php foreach ($domestic_round_trips as $i => $leg): ?>
                                        <tr>
                                            <td class="leg-no"><?= $i + 1 ?></td>
                                            <td class="w-full">
                                                <select aria-label="Select option" class="form-select js-example-basic-single" name="itinerary[<?= $i ?>][travel_mode]">
                                                    <option value=""> Select Any one </option>
                                                    <?php foreach ((array)$domestic_modes as $value): ?>
                                                        <option value="<?= $value['genc_detail_code'] ?>" <?= ($leg['travel_mode'] ?? null) == $value['genc_detail_code'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($value['genc_detail_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
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
                                            <td><input aria-label="Select date" type="date" class="form-control" name="itinerary[<?= $i ?>][departure_date]" value="<?= htmlspecialchars($leg['departure_date'] ?? '') ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                            <td><input aria-label="Input field" type="time" class="form-control" name="itinerary[<?= $i ?>][departure_time]" value="<?= htmlspecialchars(substr($leg['departure_time'] ?? '', 0, 5)) ?>" ></td>
                                            <td><textarea aria-label="Text area" class="form-control" name="itinerary[<?= $i ?>][trip_purpose]"><?= htmlspecialchars($leg['trip_purpose'] ?? '') ?></textarea></td>
                                            <td class="text-center"><i class="fa fa-trash text-danger itin-remove" style="cursor:pointer"></i></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td class="leg-no">1</td>
                                        <td class="w-full">
                                            <select aria-label="Itinerary[0][travel Mode]" class="form-select js-example-basic-single" name="itinerary[0][travel_mode]">
                                                <option value=""> Select Any one </option>
                                                <?php if (count((array)$domestic_modes) > 0) {
                                                    foreach ($domestic_modes as $value) {  ?>
                                                        <option value="<?php echo $value['genc_detail_code']; ?>"> <?php echo  $value['genc_detail_name']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
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
                                        <td><input aria-label="Itinerary[0][departure Date]" type="date" class="form-control" name="itinerary[0][departure_date]" min="<?php echo date('Y-m-d'); ?>"></td>
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
                                            <i class="fa fa-plus"></i> City
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
                            <th scope="col">Location</th>
                            <th scope="col">Hotel Name</th>
                            <th scope="col">Check-In</th>
                            <th scope="col">Check-Out</th>
                            <th scope="col">Description</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="hotelBody">
                        <?php if (count((array)$trip_hotels_list) > 0): ?>
                            <?php foreach ($trip_hotels_list as $i => $h): ?>
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single city-select" name="hotel[<?= $i ?>][location]">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$cities as $value): ?>
                                                <option value="<?= $value['id'] ?>" <?= ($h['location'] ?? null) == $value['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['city_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td><input aria-label="Input field" type="text" class="form-control" name="hotel[<?= $i ?>][hotel_name]" value="<?= htmlspecialchars($h['hotel_name'] ?? '') ?>"></td>
                                    <td><input aria-label="Select date" type="date" class="form-control" name="hotel[<?= $i ?>][check_in]" value="<?= htmlspecialchars($h['check_in'] ?? '') ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                    <td><input aria-label="Select date" type="date" class="form-control" name="hotel[<?= $i ?>][check_out]" value="<?= htmlspecialchars($h['check_out'] ?? '') ?>" min="<?php echo date('Y-m-d'); ?>"></td>
                                    <td><textarea aria-label="Text area" class="form-control" name="hotel[<?= $i ?>][description]"><?= htmlspecialchars($h['description'] ?? '') ?></textarea></td>
                                    <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>

                                <td class="w-full">
                                    <select aria-label="Hotel[0][location]" class="form-select js-example-basic-single city-select" name="hotel[0][location]">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$cities) > 0) {
                                            foreach ($cities as $value) {  ?>
                                                <option value="<?php echo $value['id']; ?>"> <?php echo  $value['city_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </td>
                                <td><input aria-label="Hotel[0][hotel Name]" type="text" class="form-control" name="hotel[0][hotel_name]" value=""></td>

                                <td><input aria-label="Hotel[0][check In]" type="date" class="form-control" name="hotel[0][check_in]" value="" min="<?php echo date('Y-m-d'); ?>"></td>
                                <td><input aria-label="Hotel[0][check Out]" type="date" class="form-control" name="hotel[0][check_out]" value="" min="<?php echo date('Y-m-d'); ?>"></td>
                                <td><textarea aria-label="Hotel[0][description]" class="form-control" name="hotel[0][description]"></textarea></td>
                                <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                    <tfoot>
                        <tr>
                            <td colspan="6">
                                <button type="button" id="btnAddHotel" class="btn btn-primary">
                                    <i class="fa fa-plus"></i> Hotel
                                </button>
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
                            <th scope="col">Mode</th>
                            <th scope="col">Pick-Up Location</th>
                            <th scope="col">Drop Location</th>
                            <th scope="col">Pick-Up</th>
                            <th scope="col">Drop</th>
                            <th scope="col">Car Type</th>
                            <th scope="col">Driver Needed</th>
                            <th scope="col">Description</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="carBody">
                        <?php if (count($local_trips_list)): ?>
                            <?php foreach ($local_trips_list as $i => $c): ?>
                                <tr>
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single" name="car[<?= $i ?>][travel_mode]">
                                            <option value=""> Select Any one </option>
                                            <?php foreach ((array)$local_modes as $value): ?>
                                                <option value="<?= $value['genc_detail_code'] ?>" <?= ($c['travel_mode'] ?? null) == $value['genc_detail_code'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($value['genc_detail_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
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
                                    <td class="w-full">
                                        <select aria-label="Select option" class="form-select js-example-basic-single" name="car[<?= $i ?>][car_type]">
                                            <option value=""> Select Any one </option>
                                            <option value="Small" <?= ($c['car_type'] ?? '') === 'Small'  ? 'selected' : '' ?>>Small</option>
                                            <option value="Medium" <?= ($c['car_type'] ?? '') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                            <option value="Large" <?= ($c['car_type'] ?? '') === 'Large'  ? 'selected' : '' ?>>Large</option>
                                        </select>
                                    </td>
                                    <td>

                                        <div class="form-radio radio">
                                            <label class="form-label me-2">
                                                <input aria-label="Radio option" type="radio" name="car[<?= $i ?>][driver_needed]" value="0" <?= (isset($c['driver_needed']) && $c['driver_needed'] == '0') ? 'checked' : '' ?>><i class="helper"></i> Yes
                                            </label>
                                            <label class="form-label">
                                                <input aria-label="Radio option" type="radio" name="car[<?= $i ?>][driver_needed]" value="1" <?= (isset($c['driver_needed']) && $c['driver_needed'] == '1') ? 'checked' : '' ?>> <i class="helper"></i> No
                                            </label>
                                        </div>
                                    </td>
                                    <td><textarea aria-label="Text area" class="form-control" name="car[<?= $i ?>][trip_purpose]"><?= htmlspecialchars($c['trip_purpose'] ?? '') ?></textarea></td>
                                    <td class="text-center"><i class="fa fa-trash text-danger car-remove" style="cursor:pointer"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="w-full">
                                    <select aria-label="Car[0][travel Mode]" class="form-select js-example-basic-single" name="car[0][travel_mode]">
                                        <option value=""> Select Any one </option>
                                        <?php if (count((array)$local_modes) > 0) {
                                            foreach ($local_modes as $value) {  ?>
                                                <option value="<?php echo $value['genc_detail_code']; ?>"> <?php echo  $value['genc_detail_name']; ?></option>
                                        <?php }
                                        } ?>
                                    </select>
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
                                <td class="w-full">
                                    <select aria-label="Car[0][car Type]" class="form-select js-example-basic-single" name="car[0][car_type]">

                                        <option value=""> Select Any one </option>
                                        <option value="Small"> Small </option>
                                        <option value="Medium"> Medium </option>
                                        <option value="Large"> Large </option>

                                    </select>
                                </td>
                                <td>
                                    <div class="form-radio radio ">
                                        <label class="form-label me-2">
                                            <input aria-label="Car[0][driver Needed]" type="radio" name="car[0][driver_needed]" value="0">
                                            <i class="helper"></i>Yes
                                        </label>
                                        <label class="form-label">
                                            <input aria-label="Car[0][driver Needed]" type="radio" name="car[0][driver_needed]" value="1">
                                            <i class="helper"></i>No
                                        </label>
                                    </div>
                                </td>
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
                        <button type="submit" name="submit" value="insert" class="btn btn-primary mt-2">
                            <i class="fa fa-save"></i> Save
                        </button>
                    <?php } else { ?>
                        <input type="hidden" name="trip_id" value="<?= (int)$rid ?>" />
                        <button type="submit" name="submit" value="update" class="btn btn-primary mt-2">
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
  document.addEventListener('click', function(e){
    if(e.target?.id === 'btnAddItin'){
      const tbody = $('#itinMultiBody');
      const idx = tbody.find('tr').length;
      const rowHtml = `
        <tr>
          <td class="leg-no">${idx+1}</td>
          <td class="w-full">
            <select aria-label="Itinerary[${idx}][travel Mode]" class="form-select js-example-basic-single" name="itinerary[${idx}][travel_mode]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$domestic_modes as $v){ ?>
                <option value="<?= $v['genc_detail_code'] ?>"><?= htmlspecialchars($v['genc_detail_name']) ?></option>
              <?php } ?>
            </select>
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
          <td><input aria-label="Itinerary[${idx}][departure Date]" type="date" class="form-control" name="itinerary[${idx}][departure_date]" min="<?php echo date('Y-m-d'); ?>"></td>
          <td><input aria-label="Itinerary[0][departure Time]" type="time" class="form-control" name="itinerary[0][departure_time]" ></td>
          <td><textarea aria-label="Itinerary[${idx}][trip Purpose]" class="form-control" name="itinerary[${idx}][trip_purpose]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger itin-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
    }

    // Add hotel
    if(e.target?.id === 'btnAddHotel'){
      const tbody = $('#hotelBody');
      const idx = tbody.find('tr').length;
      const rowHtml = `
        <tr>
          <td class="w-full">
            <select aria-label="Hotel[${idx}][location]" class="form-select js-example-basic-single city-select" name="hotel[${idx}][location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td><input aria-label="Hotel[${idx}][hotel Name]" type="text" class="form-control" name="hotel[${idx}][hotel_name]"></td>
          <td><input aria-label="Hotel[${idx}][check In]" type="date" class="form-control" name="hotel[${idx}][check_in]" min="<?php echo date('Y-m-d'); ?>"></td>
          <td><input aria-label="Hotel[${idx}][check Out]" type="date" class="form-control" name="hotel[${idx}][check_out]" min="<?php echo date('Y-m-d'); ?>"></td>
          <td><textarea aria-label="Hotel[${idx}][description]" class="form-control" name="hotel[${idx}][description]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger hotel-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
    }

    // Add car/local
    if(e.target?.id === 'btnAddCar'){
      const tbody = $('#carBody');
      const idx = tbody.find('tr').length;
      const rowHtml = `
        <tr>
          <td class="w-full">
            <select aria-label="Car[${idx}][travel Mode]" class="form-select js-example-basic-single" name="car[${idx}][travel_mode]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$local_modes as $v){ ?>
                <option value="<?= $v['genc_detail_code'] ?>"><?= htmlspecialchars($v['genc_detail_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td class="w-full">
            <select aria-label="Car[${idx}][from Location]" class="form-select js-example-basic-single city-select" name="car[${idx}][from_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td class="w-full">
            <select aria-label="Car[${idx}][to Location]" class="form-select js-example-basic-single city-select" name="car[${idx}][to_location]">
              <option value=""> Select Any one </option>
              <?php foreach((array)$cities as $v){ ?>
                <option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['city_name']) ?></option>
              <?php } ?>
            </select>
          </td>
          <td><input aria-label="Car[${idx}][pickup At]" type="datetime-local" class="form-control" name="car[${idx}][pickup_at]"></td>
          <td><input aria-label="Car[${idx}][dropoff At]" type="datetime-local" class="form-control" name="car[${idx}][dropoff_at]"></td>
          <td class="w-full">
            <select aria-label="Car[${idx}][car Type]" class="form-select js-example-basic-single" name="car[${idx}][car_type]">
              <option value="Small">Small</option>
              <option value="Medium">Medium</option>
              <option value="Large">Large</option>
            </select>
          </td>
          <td>
            <div class="form-radio radio">
              <label class="form-label me-2">
                <input aria-label="Car[${idx}][driver Needed]" type="radio" name="car[${idx}][driver_needed]" value="0"><i class="helper"></i>Yes
              </label>
              <label class="form-label">
                <input aria-label="Car[${idx}][driver Needed]" type="radio" name="car[${idx}][driver_needed]" value="1"><i class="helper"></i>No
              </label>
            </div>
          </td>
          <td><textarea aria-label="Car[${idx}][trip Purpose]" class="form-control" name="car[${idx}][trip_purpose]"></textarea></td>
          <td class="text-center"><i class="fa fa-trash text-danger car-remove" style="cursor:pointer"></i></td>
        </tr>`;
      const $row = $(rowHtml);
      tbody.append($row);
      initBasicSelect2($row);
      initCitySelect($row);
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

</script>

<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';

$filter_id = $_GET['filter_id'] ?? '';
$company_id = $company_id ?? '';
$filter_table = 'employees';
$url = API."?mode=getfilterdetails&company_id=".$company_id."&filter_id=".$filter_id."&filter_table=".$filter_table;
$response = callAPI($url);
$tasks_details = !empty($response) ? json_decode($response, true) : array();

$filter_list = $tasks_details['filter_list'] ?? array();
$filter_category = $tasks_details['filter_option'] ?? array();
$emp_list = $tasks_details['emp_list'] ?? array();
$content_str = isset($tasks_details['filter_data']['content']) ? trim($tasks_details['filter_data']['content'], '"') : '';
$selectedlist = !empty($content_str) ? json_decode($content_str, true) : array();

if (empty($selectedlist)) {
    $selectedoption = 'All Employees';
} else {
    $selectedoption = 'Only Some Employees';
    ?>
    <script type="text/javascript">
        if (document.getElementById('filtershow')) {
            document.getElementById('filtershow').style.display = 'block';
        }
    </script>
    <?php
}

$emp_count = $selectedlist['matches_count'] ?? 0;
$emp_list_names_raw = $selectedlist['matches_names'] ?? array();
$emp_list_names = json_encode($emp_list_names_raw);

?>
<script src="assets/js/pages/myscrpit/selecter.js"></script>
<script type="text/javascript">
function populatenamelist() {
    if (typeof jQuery !== 'undefined' && typeof KTBootstrapDatepicker !== 'undefined') {
        jQuery(document).ready(function() {
            KTBootstrapDatepicker.init();
        });
    }

    var data = <?php echo !empty($emp_list_names) ? $emp_list_names : '[]'; ?>;
    var result = (typeof data === 'string') ? JSON.parse(data) : data;

    var sel = document.getElementById('sel_option');
    if (sel && Array.isArray(result)) {
        sel.innerHTML = '';
        for (var i = 0; i < result.length; i++) {
            if (result[i]) {
                var opt = document.createElement('option');
                opt.textContent = result[i].display_name || '';
                opt.value = result[i].id_value || '';
                sel.appendChild(opt);
            }
        }
    }
}
</script>
<?php
if (!empty($emp_list_names_raw)) {
    echo '<script type="text/JavaScript">
    populatenamelist();
    </script>';
}
?>
<div class="modal-header">
    <h5 class="modal-title"><?= __('Filter Options'); ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo __('Close'); ?>">
        <i aria-hidden="true" class="ki ki-close"></i>
    </button>
</div>
<form class="kt-form kt-form-label-right" action="" method="post">
    <div class="modal-body">
        <div class="kt-form kt-form--label-right kt-margin-t-20 kt-margin-b-10">
            <div class="row">
                <div class="col-lg-6">
                    <label for="filterfor"><?= __('Include'); ?></label>
                    <input type="hidden" name="filterlist" id="filterlist" value="">
                    <input type="hidden" name="filter_id" id="filter_id" value="<?php echo htmlspecialchars((string)$filter_id, ENT_QUOTES); ?>">
                    <input type="hidden" name="tablename" id="tablename" value="<?php echo htmlspecialchars($filter_table, ENT_QUOTES); ?>">

                    <select onchange="showfilter(this.value);" id="filterfor" name="filterfor" class="form-control" aria-label="<?= htmlspecialchars(__('Include'), ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (!empty($filter_list) && is_array($filter_list)) {
                            foreach ($filter_list as $filter) {
                                $selectedAttr = ($selectedoption === ($filter['genc_detail_name'] ?? '')) ? 'selected="selected"' : '';
                        ?>
                            <option <?php echo $selectedAttr; ?> value="<?php echo htmlspecialchars($filter['genc_detail_code'] ?? '', ENT_QUOTES); ?>"><?php echo htmlspecialchars(__($filter['genc_detail_name']) ?? '', ENT_QUOTES); ?></option>
                        <?php }
                        } ?>
                    </select>
                </div>
                <div class="col-lg-6">
                    <label for="sel_option"><?= __('Selected Employees'); ?></label>
                    <select id="sel_option" class="form-control" aria-label="<?= htmlspecialchars(__('Selected Employees'), ENT_QUOTES, 'UTF-8'); ?>"></select>
                </div>
                <div class="col-lg-12">
                    <div class="bg-white rounded row mt-5" style="<?php echo ($selectedoption === 'Only Some Employees') ? 'display: block;' : 'display: none;'; ?>" id="filtershow">
                        <div class="col-lg-6">
                            <span class="font-weight-bold"><?= __('Filter'); ?> <?php echo htmlspecialchars($filter_table); ?> <?= __('by'); ?></span>
                            <i class="fas fa-user icon-nm pl-40" aria-hidden="true"></i>
                            <span class="font-weight-bold"><span id="matches_count"> <?php echo htmlspecialchars((string)$emp_count); ?> </span> <?= __('Matches'); ?></span>
                        </div>
                        <div class="col-lg-6 card card-custom card-border">
                            <div class="accordion accordion-light accordion-svg-toggle" id="accordionExample7">
                                <?php
                                $index = 0;
                                $select_value = array();
                                if (!empty($filter_category) && is_array($filter_category)) {
                                    foreach ($filter_category as $filter) {
                                        $targetid = $filter['id'] ?? '';
                                        $targettable = $filter['application_tablename'] ?? '';
                                        $targettable_field = $filter['fieldname'] ?? '';
                                        $index++;
                                        $select_value[$targettable_field] = "kt_select2_4".$index;
                                ?>
                                <div class="card card-border">
                                    <div class="card-header" id="headingOne7_<?php echo htmlspecialchars((string)$targetid, ENT_QUOTES); ?>">
                                        <div class="card-title collapsed" data-toggle="collapse" data-target="#aaa<?php echo htmlspecialchars((string)$targetid, ENT_QUOTES); ?>">
                                            <span class="svg-icon svg-icon-primary">
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" aria-hidden="true">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <polygon points="0 0 24 0 24 24 0 24" />
                                                        <path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
                                                        <path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999)" />
                                                    </g>
                                                </svg>
                                            </span>
                                            <div class="card-label pl-4"><?php echo htmlspecialchars(__($filter['filter_name'] ?? ''), ENT_QUOTES); ?></div>
                                        </div>
                                    </div>
                                    <div id="aaa<?php echo htmlspecialchars((string)$targetid, ENT_QUOTES); ?>" class="collapse mb-5" data-parent="#accordionExample7">
                                        <?php
                                        $table_details = dyanamicselectdata($targettable, $targettable_field);
                                        ?>
                                        <select onchange="selectemployee()" class="form-control mt-5" id="kt_select2_4<?php echo $index;?>" name="empstatus[]" multiple="multiple" aria-label="<?php echo htmlspecialchars(__($filter['filter_name'] ?? 'Filter Option'), ENT_QUOTES); ?>">
                                            <?php if (!empty($table_details) && is_array($table_details)) {
                                                foreach ($table_details as $key => $value) {
                                                    $isSelected = (!empty($selectedlist[$targettable_field]) && in_array($key, array_keys((array)$selectedlist[$targettable_field])));
                                                    $selectedAttr = $isSelected ? 'selected="selected"' : '';
                                            ?>
                                                <option value="<?php echo htmlspecialchars((string)$key, ENT_QUOTES); ?>" <?php echo $selectedAttr; ?>><?php echo htmlspecialchars(__((string)$value), ENT_QUOTES); ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php }
                                }
                                $select_value_json = json_encode($select_value);
                                ?>
                                <input type="hidden" id="select_value" name="select_value" value="<?php echo htmlspecialchars($select_value_json, ENT_QUOTES); ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer mt-5">
        <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal"><?php echo __('Close'); ?></button>
        <button type="button" class="btn btn-primary mr-2" onclick="submitrom()"><?php echo __('Select'); ?></button>
    </div>
</form>
<script type="text/javascript">
var selectedemp = "";

function selectemployee() {
    var select_value = <?php echo !empty($select_value_json) ? $select_value_json : '{}'; ?>;
    var tableElem = document.getElementById('tablename');
    var application_table = tableElem ? tableElem.value : '';
    var str = "";
    var obj = {};

    for (var key in select_value) {
        if (Object.prototype.hasOwnProperty.call(select_value, key)) {
            var targetElem = document.getElementById(select_value[key]);
            if (targetElem) {
                var elements = targetElem.childNodes;
                var selectedKeyValue = {};
                var arrayOfSelecedIDs = [];

                for (var i = 0; i < elements.length; i++) {
                    if (elements[i].selected) {
                        selectedKeyValue[elements[i].value] = elements[i].textContent;
                        obj[key] = selectedKeyValue;
                        arrayOfSelecedIDs.push(elements[i].value);
                    }
                }
                str += '&' + key + '=' + arrayOfSelecedIDs;
            }
        }
    }

    $.ajax({
        url: "ajax.php",
        data: 'page=selectedfilter&table=' + application_table + str,
        type: "POST",
        success: function(data) {
            try {
                var result = JSON.parse(data);
                var matchesElem = document.getElementById("matches_count");
                if (matchesElem) {
                    matchesElem.innerHTML = result.count || 0;
                }
                obj['matches_count'] = result.count;
                obj['matches_names'] = result.details;

                var sel = document.getElementById('sel_option');
                if (sel && Array.isArray(result.details)) {
                    sel.innerHTML = '';
                    for (var i = 0; i < result.details.length; i++) {
                        if (result.details[i]) {
                            var opt = document.createElement('option');
                            opt.textContent = result.details[i].display_name || '';
                            opt.value = result.details[i].id_value || '';
                            sel.appendChild(opt);
                        }
                    }
                }

                var jsonresponse = JSON.stringify(obj);
                selectedemp = jsonresponse;

                var filterlistElem = document.getElementById('filterlist');
                if (filterlistElem) {
                    filterlistElem.value = jsonresponse;
                }
            } catch (e) {
                // Ignore parse errors
            }
        }
    });
}

function showfilter(value) {
    var filterShowElem = document.getElementById('filtershow');
    if (filterShowElem) {
        if (value === 'SE') {
            filterShowElem.style.display = 'block';
        } else {
            filterShowElem.style.display = 'none';
        }
    }
}

function submitrom() {
    var tableElem = document.getElementById('tablename');
    var application_table = tableElem ? tableElem.value : '';

    var selectElem = document.getElementById('filterfor');
    var selectmain = selectElem ? selectElem.value : '';
    if (selectmain === 'AE') {
        selectedemp = 'All Employees';
    }

    var filterlistElem = document.getElementById('filterlist');
    var rawJson = filterlistElem ? filterlistElem.value : '';
    var sendjsoncontent = JSON.stringify(rawJson);

    $.ajax({
        url: "ajax.php",
        data: 'page=sendtofiltertable&appln_table=' + application_table + '&jsoncontent=' + sendjsoncontent,
        type: "POST",
        success: function(response) {
            var filtertblRowidElem = document.getElementById('filtertbl_rowid');
            if (filtertblRowidElem) {
                filtertblRowidElem.value = response;
            }
            if (typeof getnamelist === 'function') {
                getnamelist(selectedemp);
            }
        }
    });
}
</script>

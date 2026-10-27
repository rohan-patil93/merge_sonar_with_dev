<?php
$_SESSION['apply_js']['multiselest'] = 1;
$reporttable = "vendors";
$reportfieldname = "display_name";
$reportempfieldname = "vendor_id";
$selecteddate = $_POST['from_date'] != ""?$_POST['from_date']:date('Y-m');

function valueemtpydash($val){
    $val = $val ?? [];
    return $val==""?" - ":$val;
}
$_SESSION['apply_js']['multiselest'] = '1';
$selected_year    = date('Y');
$month = date('n'); // Current month (1 to 12)
$quarter = ceil($month / 3);
$selected_quarter = 'Q'.$quarter;
$expand           = "";
$selected_vendor_id = $_POST['vendor_id'] ?? '';
$selected_manager_id = $_POST['manager_id'] ?? '';
$_SESSION['download_data'] = "";
$url = API . "?mode=togetlastvscurrentquarterlyaccrual"
    . "&company_id=" . $_SESSION['company_id']
    . "&year=" . $selected_year
    . "&quarter=" . $selected_quarter
    . "&admin_type=" . $_SESSION['admin_type']
    . "&user_type=" . $_SESSION['user_type']
    . "&user_email=" . $_SESSION['user_email']
    . "&emp_id=" . $_SESSION['emp_id']
    . "&check=1"
    . "&role_id=" . $_SESSION['role_id']
    . "&vendor_id=" . $selected_vendor_id
    . "&manager_id=" . $selected_manager_id;
if (isset($_POST['applyform']) || isset($_POST['expand'])) {
    $expand           = $_POST['expand'];
    $selected_year    = $_POST['year'];
    $selected_quarter = $_POST['quarter'];
    $url = API . "?mode=togetlastvscurrentquarterlyaccrual"
    . "&company_id=" . $_SESSION['company_id']
    . "&year=" . $selected_year
    . "&quarter=" . $selected_quarter
    . "&admin_type=" . $_SESSION['admin_type']
    . "&user_type=" . $_SESSION['user_type']
    . "&user_email=" . $_SESSION['user_email']
    . "&emp_id=" . $_SESSION['emp_id']
    . "&check=1"
    . "&role_id=" . $_SESSION['role_id']
    . "&vendor_id=" . $selected_vendor_id
    . "&manager_id=" . $selected_manager_id;
}

$response = callAPI($url);
$rows     = json_decode($response, 1);
$vendor = $rows['vendors'];
$current_budget = $rows['current_budget'] ?? array();
$last_budget = $rows['last_budget'];
$last_act = $rows['last_act'];
$comm_data = $rows['comm_data'];
$filter_vendors = $rows['filter_vendors'];

$managers = $rows['managers'];
$pre_arr = array('Q1'=>'Q4', 'Q2'=>'Q1', 'Q3'=>'Q2', 'Q4'=>'Q3');
$fy = $selected_year;
$fq = $pre_arr[$selected_quarter];
$ly = $selected_year;
$lf = $pre_arr[$fq];
$cy = $selected_year;
$cq = $selected_quarter;
$lcy = $selected_year;
$lcq = $pre_arr[$cq];
$vy = $selected_year;
if($selected_quarter!='Q1'){
    $fy = $selected_year+1;

}else{
    $lcy = $selected_year-1;
    $vy = $selected_year-1;
}
if($selected_quarter=='Q3' || $selected_quarter=='Q4'){
    $ly = $selected_year+1;
}
$quarterMonths = [
    "Q1" => ["Jan", "Feb", "Mar"],
    "Q2" => ["Apr", "May", "Jun"],
    "Q3" => ["Jul", "Aug", "Sept"],
    "Q4" => ["Oct", "Nov", "Dec"]
];
$h2quarterMonths = [
    "Q1" => "JAN TO MAR",
    "Q2" => "APR TO JUN",
    "Q3" => "JUL TO SEP",
    "Q4" => "OCT TO DEC"
];
$months = $quarterMonths[$selected_quarter];
if ($selected_quarter == "Q1") {
            $quarter_no = 1;
        }
        elseif ($selected_quarter == "Q2") {
            $quarter_no = 2;
        }
        elseif ($selected_quarter == "Q3") {
            $quarter_no = 3;
        }
        else { // Q4
            $quarter_no = 4;
        }

$head_1 = array('FY'.$ly.'-'.$lf.' CY'.$lcy.'-'.$lcq.' Current Quarter Actual','FY'.$ly.'-'.$lf.' CY'.$lcy.'-'.$lcq.' Actual  Headcount', 'FY'.$ly.'-'.$lf.' CY'.$lcy.'-'.$lcq.' Current Quarter Budget','FY'.$ly.'-'.$lf.' CY'.$lcy.'-'.$lcq.'  Current Quarter   Budget Headcount', 'FY'.$fy.'-'.$fq.' CY'.$selected_year.'-'.$selected_quarter.' Coming  Quarter Budget  ', 'FY'.$fy.'-'.$fq.' CY'.$selected_year.'-'.$selected_quarter.'  Coming Quarter   Budget Headcount', 'Difference  Amount', 'Comments');

?>
<script type="text/javascript">
    function tosavecommentdataquater(vendor_id, year, quarter, val){
        console.log(vendor_id);
         jQuery.ajax({
            url: "ajax.php",
            data:'page=tosavecommentdataquater&vendor_id='+vendor_id+'&year='+year+'&quarter='+quarter+'&val='+val,
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success  pt-2 pb-2','animated flipInX','animated flipOutX','Your comments have been successfully inserted.','');

            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid">

        <div class="card card-custom">

            <div class="card-header">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="col bg-light-primary ">
                            <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                            </span>
                            <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $_SESSION['config']['name']; ?></span>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h4 class="card-label text-center f-w-700">Vendor Wise Yearly Summary</h4>
                    </div>
                    <div class="col-lg-3">
                        <form class="kt-form" id="kt_form" method="post" action="">
                            <div class="row">
                                <div class="col-lg-6 w-full form-group">
                                    <select aria-label="Vendor" name="vendor_id" class="form-select js-example-basic-single">
                                        <option value="">All Vendors</option>
                                        <?php foreach($filter_vendors as $vid){ ?>
                                            <option value="<?php echo $vid['vendor_id']; ?>"
                                                <?php if(isset($_POST['vendor_id']) && $_POST['vendor_id'] == $vid['vendor_id']) { echo 'selected';
} ?>>
                                                <?php echo $vendor[$vid['vendor_id']]; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-6 w-full form-group">
                                    <select aria-label="Manager" name="manager_id" class="form-select js-example-basic-single">
                                        <option value="">All Managers</option>
                                        <?php foreach($managers as $mid => $mname){ ?>
                                            <option value="<?php echo $mid; ?>"
                                                <?php if(isset($_POST['manager_id']) && $_POST['manager_id'] == $mid) { echo 'selected';
} ?>>
                                                <?php echo $mname; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="input-group input-group-button">
                                <select aria-label="Year" name="year" id="year" class="form-control form-control-sm width1" required style=" border: 2px solid #ccc;">
                                    <?php for ($y = date('Y') - 3; $y <= date('Y') + 1; $y++) { ?>
                                        <option value="<?php echo $y; ?>" <?php echo ($y == $selected_year) ? 'selected' : ''; ?>>
                                            <?php echo $y; ?>
                                        </option>
                                    <?php } ?>
                                </select>

                                <select aria-label="Quarter" name="quarter" id="quarter" class="form-control form-control-sm width1" required style=" border: 2px solid #ccc;">
                                    <option value="Q1" <?php if ($selected_quarter == 'Q1') { echo "selected";
} ?>>Q1 (Jan - Mar)</option>
                                    <option value="Q2" <?php if ($selected_quarter == 'Q2') { echo "selected";
} ?>>Q2 (Apr - Jun)</option>
                                    <option value="Q3" <?php if ($selected_quarter == 'Q3') { echo "selected";
} ?>>Q3 (Jul - Sep)</option>
                                    <option value="Q4" <?php if ($selected_quarter == 'Q4') { echo "selected";
} ?>>Q4 (Oct - Dec)</option>
                                </select>

                                <button type="submit" name="applyform" value="apply" class="btn btn-info waves-effect waves-light input-group-text mt-0">
                                    Submit
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">

                    <div class="dt-responsive table-responsive">

                            <table id="basic-btn" class="table  table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                        <?php foreach((array)$head_1 as $hea){ ?>
                                            <th scope="col" class="text-right" style="white-space: normal; width: 12%;"><?php echo $hea; ?></th>
                                        <?php } ?>
                                    </tr>
                                     <tr>
                                         <th scope="col">Sl No</th>
                                        <th scope="col">Vendor Name</th>
                                        <th scope="col"><?php echo $h2quarterMonths[$lcq]." ".$vy; ?></th>
                                        <th scope="col"><?php echo $h2quarterMonths[$lcq]." ".$vy; ?></th>
                                        <th scope="col"><?php echo $h2quarterMonths[$lcq]." ".$vy; ?></th>
                                        <th scope="col"><?php echo $h2quarterMonths[$lcq]." ".$vy; ?></th>
                                        <th scope="col"><?php echo $h2quarterMonths[$selected_quarter]." ".$selected_year; ?></th>
                                        <th scope="col"><?php echo $h2quarterMonths[$selected_quarter]." ".$selected_year; ?></th>
                                        <th scope="col"></th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if(count((array)$current_budget)>0){
                                            $i=1;
                                            foreach((array)$current_budget as $ven=>$cur){
                                     ?>
                                            <tr>
                                                <td><?php echo $i; ?> </td>
                                                <td><?php echo $vendor[$cur['vendor_id']]; ?></td>
                                                <td class="text-right" ><?php echo $last_act[$cur['vendor_id']]['total']; ?></td>
                                                <td class="text-right"><?php echo $last_act[$cur['vendor_id']]['no_emp']; ?></td>
                                                <td class="text-right"><?php echo $last_budget[$cur['vendor_id']]['total']; ?></td>
                                                <td class="text-right"><?php echo $last_budget[$cur['vendor_id']]['no_emp']; ?></td>
                                                <td class="text-right"><?php echo $cur['total']; ?></td>
                                                <td class="text-right"><?php echo $cur['no_emp']; ?></td>
                                                <td class="text-right" ><?php echo $last_budget[$cur['vendor_id']]['total']-$cur['total']; ?></td>
                                                <td>
                                                    <textarea aria-label="Comments" class="form-control" id="comments" name="comments" onblur="tosavecommentdataquater(<?php echo $cur['vendor_id'].",".$selected_year.",".$quarter_no; ?>, this.value)"><?php echo $comm_data[$cur['vendor_id']];  ?>
                                                    </textarea>
                                                </td>
                                            </tr>
                                    <?php
                                            $i++;
                                            }
                                        }
                                    ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

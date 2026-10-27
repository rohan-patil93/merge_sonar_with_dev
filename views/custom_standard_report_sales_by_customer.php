<?php
$url = API."?mode=pmsdashboard2";
$apires = callAPI($url);
$result = json_decode($apires,1);
$menus = $result['menu_data'];

?>
<script type="text/javascript">
    function slectDateRange(type){
        console.log(type);

    }
</script>
<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-lg-2">
                <div class="col bg-light-primary ">
                    <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                        <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                    </span>
                    <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <h4 class="card-label text-center f-w-700 d-block mb-0">Sales By Customer Report</h4>
                <span class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></span>
                <span class="f-10 d-block">From 17-05-2023 To 17-05-2023</span>


            </div>
            <div class="col-lg-2">
                <?php $dynamiccollg = 12; include_once 'views/custom_standard_report_hr_employee_filter.php'; ?>

            </div>
            <div class="col-lg-2">
                <div class="dropdown-primary dropdown open " style="border: 1px solid #c3ffe1; width: 100%;">
                    <button
                        class="btn dropdown-toggle waves-effect waves-light today-toggle" type="button" id="dropdown-2" data-bs-toggle="dropdown"aria-haspopup="true" aria-expanded="true" style="width: 100%;"> <i class="ti-calendar"></i> Today</button>
                    <div class="dropdown-menu" aria-labelledby="dropdown-2" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut" style="width: 100%;">
                        <a class="dropdown-item waves-light waves-effect active" href="#">Today</a>
                        <a class="dropdown-item waves-light waves-effect"  href="#">This Week</a>
                        <a class="dropdown-item waves-light waves-effect"  href="#">This Month</a>
                        <a class="dropdown-item waves-light waves-effect" href="#">This Year</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item waves-light waves-effect" href="#">Yesterday</a>
                        <a class="dropdown-item waves-light waves-effect"  href="#">Previous Week</a>
                        <a class="dropdown-item waves-light waves-effect"  href="#">Previous Month</a>
                        <a class="dropdown-item waves-light waves-effect" href="#">Previous Year</a>
                        <div class="dropdown-divider"></div>
                        <button type="button" class="dropdown-item waves-light waves-effect" !" onclick="slectDateRange('CD')">Custom Date</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table class="table table-hover m-b-0" style="font-size:13px;">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Name</th>
                    <th scope="col" class="text-right">Invoice Count</th>
                    <th scope="col" class="text-right">Sales</th>
                    <th scope="col" class="text-right">Sales With tax</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="w-b-w-s">rohan kumar</td>
                    <td class="text-right">2</td>
                    <td class="text-right">$25801</td>
                    <td class="text-right">$950</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col" colspan="2">Total</th>
                    <th scope="col" class="text-right">2</th>
                    <th scope="col" class="text-right">$25801</th>
                    <th scope="col" class="text-right">$950</th>
                </tr>
            </tfoot>

        </table>
    </div>
</div>

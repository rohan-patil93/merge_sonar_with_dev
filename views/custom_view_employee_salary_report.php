
<?php
$_SESSION['apply_js']['table']='1';
$vurl = API."?mode=employeepayrollsalarydetails";
$configesponse = callAPI($vurl);
$result = json_decode($configesponse,1);
$ern = $result['earnings'];
$data = $result['data'];
?>
<div class="page-body">
    <div class="row">
        <div class="col-sm-12">
            <div class="card ">
                <div class="card-block">
                <div class="card-title">
                        <div class="row">
                            <div class="col-lg-3">
                            <div class="col bg-light-primary ">
                                <span class="svg-icon svg-icon-3x svg-icon-primary d-block my-2">
                                    <img class="img-fluid img-120" src="uploads/all/<?php echo $_SESSION['config']['display_logo']; ?>" alt="Theme-Logo" />
                                </span>
                                <span class="text-dark-75 font-weight-bolder font-size-h3"><?php echo $company_name; ?></span>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <h4 class="card-label text-center f-w-700">Employee's Salary Details </br><small class="f-10">Name : <?php echo $_SESSION['display_name']; ?> &nbsp;  Date & Time : <?php echo date("d-m-Y H:i:s"); ?></small></h4>

                        </div>

                        </div>

                <div class="dt-responsive table-responsive mt-2">
                    <?php
                    $viewlist = array(2,3,7,5,6,9,12,14,10); ?>
                    <table id="basic-btn" class="table table-striped table-bordered nowrap">
                        <thead>
                            <tr>
                                <th scope="col"> SL NO </th>
                                <th scope="col">Employee Code</th>
                                <th scope="col">Name</th>
                                <th scope="col">Data of Joining</th>
                                <th scope="col">Data of Leaving</th>
                                <?php
                                    foreach($viewlist  as $er ){?>
                                    <th scope="col"><?php echo $ern[$er]; ?></th>
                                <?php
                                    }
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach((array)$data as $rows){ ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $rows['emp_code']; ?></td>
                                    <td><?php echo ucwords($rows['display_name']); ?></td>
                                    <td><?php echo fromDate($rows['joining_date']); ?></td>
                                    <td><?php echo fromDate($rows['date_of_leaving']); ?></td>
                                    <?php $detail = $rows['details'];
                                    foreach($viewlist  as $er ){?>
                                    <td  class="text-right"><?php echo customeNumberFormate(round($detail[$er]['yearly_amount'])); ?></td>
                                    <?php
                                    } ?>
                                </tr>
                            <?php $i++;
} ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

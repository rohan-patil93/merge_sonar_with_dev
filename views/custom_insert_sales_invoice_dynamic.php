<?php
if (isset($_POST['submit']) && $_POST['submit'] === 'upload_csv' && isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tempPath = $_FILES['csv_file']['tmp_name'];
        $fileName = $_FILES['csv_file']['name'];
        $fileData = file_get_contents($tempPath);
        $postData = [
            'file_name' => $fileName,
            'filedata' => base64_encode($fileData),
            'submit' => 'upload_csv',
            'import_name' => $_POST['import_name'],
            'import_date' => $_POST['import_date'],
            'invoice_from_date' => $_POST['invoice_from_date'],
            'invoice_to_date' => $_POST['invoice_to_date'],
        ];

        $url = CRMAPI . "?mode=importcsv&method=post"; // Customize as needed
        $response = file_post_contents($url, $postData, 'insert');
        $response = json_decode($response, 1);
        if($response['parent']['status'] == 'sucess'){
            $_SESSION['insert_msg'] = 3000;
            $nrid = "id:".$response['parent']['LASTID'];
             header("location:index.php?option=COM_LIST_SALES_INVOICE_DYNAMIC&Rid=".deocdeId($nrid));
        }else {
            $error_message = "File upload error.";
        }
    }

$url = CRMAPI . "?mode=getsalesinvoicedynamicexcellist&method=get&LIMIT=10";
$che = callAPI($url);
$return = json_decode($che, 1);
$excel_data = $return['excel_data'];

?>
<script type="text/javascript">
  function enddatechange() {
    console.log("check date");
    var sd = $("#date_of_grant").val();
    $("#date_of_end").val(sd);
  }
</script>
<?php if($error_message !=''){ ?>
        <div class="alert alert-danger background-danger alert-dismissible">
            <strong><?php echo $error_message; ?></strong>
             <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
<?php } ?>
<div class="card">
    <div class="card-header table-card-header">
        <h5>Upload Invoices</h5>
        <div class="card-header-right table-card-header ">
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                <i class="icofont icofont-reply"></i>
                Back
            </a>
        </div>
    </div>
    <form class="kt-form " id="kt_form" method="post" action="" name="myForm" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group row">
                <div class="form-group col-lg-4">
                    <span class="form-label">Upload Date </span>
                    <div>
                        <input aria-label="Import Date" class="form-control datepicker" type="text" id="import_date" name="import_date" value="<?php echo fromDate(); ?>"  readonly
                            required>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <input type="hidden" class="rangedatepicker" data-id="1">
                    <span class="form-label">Invoice From Date</span>
                    <div>
                        <input aria-label="Invoice From Date" class="form-control startdatepicker_1"  type="text" id="date_of_grant" name="invoice_from_date"
                            value="<?php echo fromDate(); ?>"  required>
                    </div>
                </div>
                <div class="form-group col-lg-4">
                    <span class="form-label">Invoice To Date</span>
                    <div>
                        <input aria-label="Invoice To Date" class="form-control enddatepicker_1" type="text" id="date_of_end" name="invoice_to_date"
                            alue="<?php echo fromDate(); ?>"  required>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Upload Name</span>
                    <div>
                        <input aria-label="Enter Upload Name" class="form-control" type="text" id="import_name" name="import_name" value="" placeholder="Enter Upload Name" required>
                    </div>
                </div>
                <div class="form-group col-lg-6">
                    <span class="form-label">Import</span>
                    <div>
                        <input aria-label="Csv File" class="form-control" type="file" id="csv_file" name="csv_file" value="" accept=".csv" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <span><a class="text-primary" href="uploads/sale_invoice_csv_sample.csv" download>Download Sample Excel</a></span>
                <div class="col-lg-6">
                    <input type="hidden" name="submit" value="upload_csv">
                    <button type="submit" class="btn btn-primary" name="submit_value" value="exit"><i class="la la-check"></i>Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="card">
    <div class="card-header table-card-header">
        <h5>Upload Invoices</h5>
        <div class="card-header-right table-card-header ">
            <a href="index.php?option=COM_LIST_SALES_INVOICE_DYNAMIC_EXCEL_LIST" class=" text-primary">View all</a>
        </div>
    </div>

<div class="card-body">
        <div class="table-responsive mt-3">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col">Sr.No</th>
                    <th scope="col">File Name</th>
                    <th scope="col">Upload Name</th>
                    <th scope="col">Upload Date</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count((array)$excel_data) > 0) {
                    $i = 1;
                    foreach ($excel_data as $row) {
                ?>
                        <tr>
                            <td class="p-2"><?php echo  $i; ?></td>
                            <td class="p-2"><?php echo  $row['file_name']; ?></td>
                            <td class="p-2"><?php echo  $row['import_name']; ?></td>
                            <td class="p-2"><?php echo  fromDate($row['created_date']); ?></td>
                            <td class="p-2">
                        <a class="btn btn-primary"
                            href="index.php?option=COM_LIST_SALES_INVOICE_DYNAMIC&Rid=<?php echo deocdeId('id:' . $row['id']); ?>">View

                        </a>

                    </td>                            </tr>
                    <?php $i++;
                    }
                } else { ?>
                    <tr>
                        <td colspan="5">
                            <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data Available</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
      </div>

</div>

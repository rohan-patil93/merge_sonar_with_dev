<?php
$_SESSION['apply_js']['multiselest'] = "1";

if(isset($_POST['submit_bank_reconciliations'])) {
    print_r($_FILES);
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
        $tempPath = $_FILES['csv_file']['tmp_name'];
        $original_file_name = $_FILES["csv_file"]['name'];
        $ext=explode(".",$_FILES["csv_file"]['name']);
        $file_name = $ext[0];
        $file_insert_name = 'bank_reconciliations'.'_'.time().uniqid().'.'.$ext[1];
        $file_path='uploads/bank_reconciliations/'.$file_insert_name;

        $fileData = file_get_contents($tempPath);
        move_uploaded_file($tempPath,$file_path);

        $_POST['original_file_name'] = $original_file_name;
        $_POST['file_name'] = $file_insert_name;
        $_POST['filedata'] = base64_encode($fileData);
        $url = API . "?mode=importContractCSV&method=post";
        $response = file_post_contents($url, $_POST, 'insert');
        $response = json_decode($response, 1);
        echo "<pre>"; print_r($_POST); echo "</pre>";
    }
}

$rid1 = $_GET['Rid'];
$rid = encodeId($rid1);
$rid = urldecode($rid);
$rid = explode(":", $rid);
$rid = $rid['1'];
$url3 = API . "?mode=getbankreconciliations&method=get&rid=" . $rid;
$che3 = callAPI($url3);
$result = json_decode($che3, 1);
$bank_accounts = $result['bank_accounts'];
?>
<style>
    .match{
        background: #9bc5f5bd;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .unmatched{
        background: #e51d1d59;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .partial{
        background: #ffc10752;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .suggestions{
        background: #1987545c;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .active-card {
        outline: 3px solid #ff0000;
        transform: scale(1.02);
        transition: 0.2s;
    }
</style>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bank Reconciliation</h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body p-l-40 p-r-40">
        <form method="post" enctype="multipart/form-data">
            <div class="row align-items-center mt-3">
                <div class="col-lg-3">
                    <div class="form-group">
                        <span class="form-label">Bank Account</span>
                        <select aria-label="Select option" class="form-control js-example-basic-single" required>
                            <option>Select Any one</option>
                            <?php foreach($bank_accounts as $ba){ ?>
                            <option value="<?php echo $ba['id']; ?>"><?php echo $ba['account_code']." - ".$ba['bank_account_name']." - ".$ba['bank_account_number']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 form-group">
                    <span class="form-label">From Statement Date</span>
                    <input aria-label="Leave From" type="text" class="form-control rangedatepicker startdatepicker_1" data-id="1" name="leave_from" required/>
                </div>
                <div class="col-lg-3 form-group">
                    <span class="form-label">To Statement Date</span>
                    <input aria-label="Leave From" type="text" class="form-control rangedatepicker enddatepicker_1" name="leave_from" required/>
                </div>
                <div class="col-lg-3">
                    <div class="form-group">
                        <span class="form-label">Upload Bank Statement (CSV/XLS)</span>
                        <input aria-label="Csv File" type="file" name="csv_file" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" name="submit_bank_reconciliations" class="btn btn-primary" <?php echo $disable; ?>>
                        <i class="la la-check"></i> Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const cards = document.querySelectorAll(".filter-card");
    const tables = document.querySelectorAll(".data-table tbody tr");

    cards.forEach(card => {
      card.addEventListener("click", () => {
        const status = card.getAttribute("data-status").toLowerCase();

        tables.forEach(row => {
          const span = row.querySelector("span");
          if (!span) return;

          const rowStatus = span.classList[0].toLowerCase();

          if (status === "all" || rowStatus === status) {
            row.style.display = "";
          } else {
            row.style.display = "none";
          }
        });

        // Optional: highlight selected card
        cards.forEach(c => c.classList.remove("active-card"));
        card.classList.add("active-card");
      });
    });
</script>

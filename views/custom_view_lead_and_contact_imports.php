<?php
if($_SESSION['insert_msg'] !=""){
    $msgcode = $_SESSION['insert_msg'];
    $_SESSION['insert_msg'] = "";
    ?>
    <script type="text/javascript">
        document.onreadystatechange = function(){
            if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
            }
        }
    </script>
<?php
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rows  = json_decode($_POST['full_data'], true);
    $files = json_decode($_POST['file_data'], true);
    $payload = [
        'files' => $files,
        'rows'  => $rows,
    ];
    if (!empty($files) && isset($files[0]['import_type'])) {
        $importType = strtolower($files[0]['import_type']);

        if ($importType === 'contacts' || $importType === 'contact') {
            $url = API . "?mode=insertcontactdata&method=post";
        } elseif ($importType === 'leads' || $importType === 'lead') {
            $url = API . "?mode=insertleaddata&method=post";
        }
    }
    $response = file_post_contents($url, $payload, 'insert');
    $result = json_decode($response, true);
}
// Fetch the data via GET
$rid = encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$vurl = API . "?mode=getlistcontactleaddata&method=get&rid=" . $rid;
$che = callAPI($vurl);
$return = json_decode($che, true);
$rows = $return['crm_import_file_rows'] ?? [];
$files = $return['crm_import_files'] ?? [];
?>
<div class="card">
    <div class="card-header">
        <h4>Leads/Contacts List</h4>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light  m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                Back
            </a>
        </div>
    </div>
    <div class="card-body m-r-50  m-l-50">
        <form id="salesForm" action="" method="post">

            <input type="hidden" name="full_data" value='<?php echo htmlspecialchars(json_encode($rows), ENT_QUOTES, 'UTF-8'); ?>' />

            <input type="hidden" name="file_data" value='<?php echo htmlspecialchars(json_encode($files), ENT_QUOTES, 'UTF-8'); ?>' />

            <div class="table-responsive mt-3">
                <table class="table table-hover">
                    <thead class="f-14" style="background-color: #eeeeee;">
                        <tr>
                            <th scope="col">Sr.No</th>
                            <th scope="col">Display Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Office Phone</th>
                            <th scope="col">Address</th>
                            <th scope="col">Company Name</th>
                            <th scope="col">Duplicate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($rows)) {
                            $i = 1;
                            foreach ($rows as $row) {
                                echo "<tr>
                                    <td>{$i
}</td>
                                    <td>" . htmlspecialchars($row['display_name']) . "</td>
                                    <td>" . htmlspecialchars($row['email']) . "</td>
                                    <td>" . htmlspecialchars($row['phone']) . "</td>
                                    <td>" . htmlspecialchars($row['office_phone']) . "</td>
                                    <td>" . htmlspecialchars($row['address']) . "</td>
                                    <td>" . htmlspecialchars($row['company_name']) . "</td>
                                    <td>" . ($row['is_duplicate'] ? '<span class="badge bg-danger">Yes</span>' : '<span class="badge bg-success">No</span>') . "</td>
                                </tr>";
                                $i++;
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>No Data Available</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </form>
    </div>
</div>

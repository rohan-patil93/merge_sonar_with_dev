<?php
if($_POST['page_ajax']=='Y'){
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
}
?>
<?php

$employee_id = $_REQUEST['employee_id'] ?? 0;
$month       = $_REQUEST['assessment_year'] ?? '';

if (!$employee_id || !$month) {
	echo '<div class="alert alert-danger">' . __('Invalid request') . '</div>';
	exit;
}
// Call API
$url = API . "?mode=togetdocumentlistfile&method=get"
	. "&semp_id={$employee_id}"
	. "&assessment_year={$assessment_year}";
//echo $url;
$response = callAPI($url);
$response = json_decode($response, true);
//echo "<pre>"; print_r($response); echo "</pre>";

$data = $response['data'] ?? array();
?>

<div class="col-12">
	<div class="card">


		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead>
						<tr>
							<th scope="col" width="5%"><?= __('S.No') ?></th>
							<th scope="col"><?= __('Employee') ?></th>
							<th scope="col"><?= __('Assessment Year') ?></th>
							<th scope="col"><?= __('Document Name') ?></th>
                            <th scope="col"><?= __('PAN Number') ?></th>
                            <th scope="col"><?= __('View') ?></th>
						</tr>
					</thead>
					 <tbody>
                        <?php $i=1;
                             foreach ($data as $daa){ ?>

                        <tr>
                            <td><?= $i++; ?></td>
                            <td><?= $daa['display_name']; ?></td>
                            <td><?= $daa['assessment_year']; ?></td>
                            <td><?= $daa['document_name']; ?></td>
                            <td><?= $daa['pan_number']; ?></td>
                            <td>
                                <a class="btn btn-info" href="/uploads/xqwave/documents/<?= $daa['file']; ?>"><?= __('View') ?></a>

                            </td>
                        </tr>

                        <?php } ?>

                    </tbody>

				</table>
			</div>

		</div>
	</div>
</div>
<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getrecruitmentintegdashboard&apitype=candidatelistpopup&candidate_ids=".$_POST['candidate_ids'];
$apires = callAPI($url);
$result = json_decode($apires,1);
$candidates = $result['candidates'];
?>
<div class="modal-header">
	<h5 class="modal-title">
		<?= __('Candidates'); ?>
	</h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?= htmlspecialchars(__('Close'), ENT_QUOTES, 'UTF-8'); ?>"></button>
</div>
<div class="modal-body" >
	<div class="card">
		<div class="input-group input-group-button mb-0">
            <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="tr" class="form-control form-control-sm width1" placeholder="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>" aria-label="<?= htmlspecialchars(__('Search'), ENT_QUOTES, 'UTF-8'); ?>">
            <span class="input-group-text addon-style">
                <span class=" d-inline-block fw-bold" style="margin-bottom:5px"><span class="ti-search text-white mt-2"></span></span>
            </span>
        </div>
		<div class="card-body custom-scroll" style=" height: 450px;">
			<table  class="table table-hover m-b-0 " style="font-size:13px;" >
				<thead>
					<tr>
						<th scope="col">#.</th>
						<th scope="col"><?= __('Candidate'); ?> </th>
						<th scope="col"><?= __('Candidate Email'); ?></th>
						<th scope="col"><?= __('Phone No.'); ?></th>
						<th scope="col"><?= __('Skills'); ?></th>
					</tr>
				</thead>
				<tbody id="categoryfilter">
					<?php if(count((array)$candidates)>0){
						$k=1;
						foreach($candidates as $cand){
							$skils = json_decode($cand['skills'],1);
							?>
							<tr>
								<td><?php echo $k; ?></td>
								<td ><a class=" text-primary" href="index.php?option=COM_INSERT_CANDIDATE&Rid=<?php echo deocdeId("id:".$cand['id']); ?>"><i class="ti-pencil"></i> <?php  echo $cand['candidate_code'].'- '.$cand['display_name']; ?></a></td>
								<td class="text-capitalize"><?php  echo $cand['email_id']; ?></td>
								<td class="text-capitalize "><a href="tel:<?php  echo $cand['phone']; ?>" class=" text-primary"> <i class="ti-mobile"></i> <?php  echo $cand['phone']; ?> </a></td>
								<td class="text-capitalize w-b-w-s">
								<?php  if(count((array)$skils) != 0) { foreach((array)$skils as  $skil){ ?>
          							<span class="label label-success m-1"><?php echo  $skil; ?></span>
								<?php }
								}else{ echo "-";
								} ?>
								</td>
							<?php $k++;
							}
							}else{ ?>
							<tr>
								<td colspan="4">
									<div class="card-px text-center ">
										<p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> <?= __('No Candidates'); ?> </p>
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
				<!-- 	</div> -->
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal"><?= __('Close'); ?></button>
				</div>
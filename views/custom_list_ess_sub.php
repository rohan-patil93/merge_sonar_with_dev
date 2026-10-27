<?php
$application_page_name = $process_title['application_page_name'];
foreach ($data_doc[$process_title['process_id']] as $list) {
    if($list['process_is_checklist']==0){
        $data_documents[]=$list;
    }
    if($list['process_is_checklist']==1){
        $checklist_documents[]=$list;
    }
} ?>
<div class="ess-related">
    <?php if(empty($process_button1_url)) { ?>
        <div class="" role="group">
            <button id="btnGroupDrop1" type="button" class="btn btn-success font-weight-bold dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?php echo $process_button1_label ?>
            </button>
            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                <?php if(!empty($data_documents)){
                    foreach ($data_documents as $list){
                        $view = $list['document_url'] !="" ? "https://".$list['document_url'] : 'uploads/all/'.$list['process_file']; ?>
                        <a class="text-muted text-hover-primary" href="<?php echo $view; ?>" target="_blank"><?php echo $list['document_name']; ?></a>
                    <?php }
                } else {
                    echo '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9013'].'</div>';
                } ?>
            </div>
        </div>
    <?php } else { ?>
    <button type="button" class="btn btn-success"><?php echo $process_button1_label ?></button>
    <?php }  ?><br />
    <button type="button" class="btn btn-warning"><?php echo $process_button2_label ?></button><br /><br />
    <button type="button" class="btn btn-info"><?php echo $process_button3_label ?></button>
    <br /><br />
    <?php if(empty($process_button4_url)) { ?>
    <div class="" role="group">
        <button id="btnGroupDrop1_2" type="button" class="btn btn-success font-weight-bold dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $process_button4_label ?>
        </button>
        <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <?php
            if(!empty($checklist_documents))
            {
                foreach ($checklist_documents as $list) {
                    $view = $list['document_url'] !="" ? $list['document_url'] : 'uploads/all/='.$list['process_file'];
                    ?>

                    <a class="text-muted text-hover-primary" href="<?php echo $view; ?>" target="_blank"><?php echo $list['document_name']; ?></a>

                    <?php
                }
            } else {
                echo '<div class="dropdown-item text-danger">'.$_SESSION['error_message']['9013'].'</div>';
            }
            ?>
        </div>
    </div>
    <?php } else { ?>
        <button type="button" class="btn btn-success"><?php echo $process_button4_label ?></button>
    <?php }  ?>
    <br /><br />
</div>

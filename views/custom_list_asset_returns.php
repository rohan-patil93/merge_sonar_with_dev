<?php
    $url1 = API."?mode=getassetallocation&admin_type=".$_SESSION['admin_type']."&resultgettype=ASSET_RETURNS";
    $data_details1 = callAPI($url1);
    $result = json_decode($data_details1,1);
       $asset_returns = $result['asset_returns'];
       $asset_categories = $result['asset_categories'];
       $locations = $result['locations'];
       $statuse = ViewStatusData();
?>
<script type="text/javascript">
    function assetsReturnReceive(rid=""){
    jQuery.ajax({
        url: "views/custom_insert_asset_return_receive_accepts_or_reject.php",
        data: "asset_return_id="+rid,
        type: "POST",
        success:function(data){
            $('#dynamic_popup11').html(data);
            $('#kt_modal_dynamic_popup2').modal("show");
        }
    });
}

</script>
<div class="d-flex flex-column-fluid">
    <div class="container-fluid" >
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-custom card-sticky" id="kt_page_sticky_card">
                    <div class="card-header mb-3">
                        <h5>Assets Return</h5>
                        <div class="card-header-right">
                            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light">
                                <i class="icofont icofont-reply"></i>
                                Back
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(count((array)$asset_returns) !=0){ ?>
                        <div class="dt-responsive table-responsive">
                            <table id="basic-btn" class="table table-striped table-bordered nowrap">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Asset</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Return Date</th>
                                        <th scope="col">Request Date Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $mk=1; foreach($asset_returns as $assreturn){ ?>
                                        <tr class="<?php echo $varianc_class; ?>">
                                            <td><?php echo $mk; ?></td>
                                            <td><?php echo $_SESSION['users'][$assreturn['originator']]; ?></td>
                                            <td><?php echo $assreturn['asset_code']." - ".$assreturn['asset_description']; ?></td>

                                            <td><?php echo $assreturn['description']; ?></td>
                                            <td><?php echo $assreturn['return_date'] !=""?fromDate($assreturn['return_date']):"-"; ?></td>

                                            <td><?php echo $assreturn['request_datetime'] !=""?$assreturn['request_datetime']:"-"; ?></td>

                                            <td class="p-2">
                                                <span class="form-label label label-md" style="color:<?php echo $statuse[$assreturn['current_status']]['color']; ?>; background-color:<?php echo $statuse[$assreturn['current_status']]['background_color']; ?>;"><?php echo $statuse[$assreturn['current_status']]['name']; ?></span>
                                            </td>

                                            <td class="text-center">
                                                <i class="fa fa-eye" onclick="assetsReturnReceive('<?php echo $assreturn['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                                            </td>
                                        </tr>
                                    <?php $mk++;
} ?>

                                </tbody>
                            </table>
                        </div>
                        <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Asset Return</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

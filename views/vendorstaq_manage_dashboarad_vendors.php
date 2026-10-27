<div class="card card-custom " id="kt_page_sticky_card">
    <div class="card-header table-card-header mb-2">
        <h5><?php echo "Active Vendors";  ?></h5>
        <div class="card-header-right table-card-header ">

            <input type="hidden" name="emp_id" value="<?php echo $empp_id; ?>">

        </div>
    </div>
    <div class="table-responsive custom-scroll" style="height:540px;">
    <table class="table table table-separate table-head-custom table-checkable bordered dataTable px-6" id="kt_datatable1" style="">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vendor Name  </th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $sno= 0; $checkyy =0;
              if(count((array)$vendoralkadetails)>0){
                foreach($vendoralkadetails as $loc) {
                    if($loc['is_active']==0){
                    $sno++;  $checkyy =1; ?>
                    <tr>
                        <td> <?php echo $sno; ?> </td>
                        <td>

                            <div class="align-middle">
                                <?php if($loc['image']!=""){
                                    $loc_img_src = !empty($loc['image']) ? 'uploads/all/' . $loc['image'] : 'uploads/all/no-image.jpg'; ?>
                                    <img src="<?php echo $loc_img_src; ?>" class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                <?php } else { ?>
                                    <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($loc['display_name']); ?></span>
                                <?php } ?>
                                <div class="d-inline-block width73">
                                    <h6><?php echo $loc['display_name']; ?></h6>
                                    <span class="form-label label label-primary"><?php echo fromDate($loc['from_date']); ?></span>
                                </div>
                            </div>

                        </td>

                        <td>
                            <?php if($loc['is_active']==0){ ?>
                                <span class="label label-primary ">
                                <?php echo "Active"; ?>
                            </span>
                            <?php }else{ ?>
                                <span class="label label-danger ">
                                <?php echo "Inactive"; ?>
                            </span>
                            <?php } ?>


                        </td>

                </tr>

            <?php } ?>
            <?php }
        }else{ $checkyy =1; ?>
            <tr>
                <td colspan="3">
                    <div class="text-center mt-3">
                               <h6 class="text-center mt-3"> No Data</h6>
                                 <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                             </div>
                </td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
    </div>
</div>


<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url1 = API."?mode=getvendorcustomeritems&item_id=".$_POST['item_id']."&transaction_table=".$_POST['transaction_table']."&transaction_id=".$_POST['transaction_id'];
$data_details1 = callAPI($url1);
$result = json_decode($data_details1,1);
$all_items = $result['all_items'];
$item_categories = $result['item_categories'];
$all_party_table_detail = $result['all_party_table_detail'];
foreach($all_party_table_detail as $ic){
    $all_party_item_id[$ic->item_id] = $ic->item_id;
}

?>
<script type="text/javascript">
    function filterItemCategory(cat_id=""){
        console.log(cat_id);
        $(".all_item_category").hide();
        if(cat_id != ""){
            $(".item_category_"+cat_id).show();
        }else{
            $(".all_item_category").show();
        }
    }
</script>
<div class="row">
    <div class="col-lg-8">
    </div>
    <div class="col-lg-4">
        <div class="w-full">
            <span class="form-label ">Item Category<span class="text-danger">*</span></span>
            <select aria-label="Customer" name="customer_id" class="form-control form-select js-example-basic-single" onchange="filterItemCategory(this.value)">
                <option value="">All Item Categories</option>
                <?php foreach($item_categories as $icat){ ?>
                    <option value="<?php echo $icat['id']; ?>"><?php echo $icat['category_description']; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Vendor Items</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 500px; min-height: 500px;">
                <?php
                if(count((array)$all_party_table_detail) != 0){
                    foreach($all_party_table_detail as $partyitm){
                        $itm = $all_items[$partyitm['item_id']];
                        ?>
                        <div class="row mt-3 all_item_category item_category_<?php echo $itm['category_id'];?> " >
                            <div class="col-lg-10">
                                <div class="align-middle  ">
                                    <?php if($itm['item_image']!=""){?>
                                        <img src="uploads/all/<?php echo $itm['item_image']; ?>"   class="img-30 align-middle m-r-15 seating_emp_image" alt="Attachment Preview">
                                    <?php }else{ ?>
                                        <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($itm['item_name']); ?></span>
                                    <?php } ?>
                                    <div class="d-inline-block align-middle">
                                        <p class="f-14 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $itm['item_name']." - <span class='f-w-600'>(".$partyitm['vendor_part_name'].")</span>"; ?>
                                                <i class="ti-check text-success f-14 f-w-700"></i>
                                        </p>
                                        <p class="f-12 mb-0"><?php echo $itm['sku']." - <span class='f-w-600'>(".$partyitm['vendor_part_no'].")</span>"; ?> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 d-inline-block align-middle">
                                <i class="ti-close text-danger f-w-700 " data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Un-Allocate Item" onclick="selectItemsWithParty('<?php echo $itm['id']; ?>','REMOVE','<?php echo $partyitm['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            </div>
                        </div>
                <?php } ?>
            <?php }else{ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Items</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                    </div>
                </div>
            <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5>Items</h5>
            </div>
            <div class="card-body custom-scroll" style="max-height: 500px; min-height: 500px;">
                <?php
                if(count((array)$all_items) != 0){
                    foreach($all_items as $itm){
                        if(isset($all_party_item_id[$itm['id']])){

                        }else{
                        ?>
                        <div class="row mt-3 all_item_category item_category_<?php echo $itm['category_id'];?> " >
                            <div class="col-lg-10">
                                <div class="align-middle  ">
                                    <?php if($itm['item_image']!=""){?>
                                        <img src="uploads/all/<?php echo $itm['item_image']; ?>"   class="img-30 align-middle m-r-15 seating_emp_image" alt="Attachment Preview">
                                    <?php }else{ ?>
                                        <span  alt="user" class="align-middle m-r-10 custom_txt_workentry f-12 text-center" ><?php echo initials($itm['item_name']); ?></span>
                                    <?php } ?>
                                    <div class="d-inline-block align-middle">
                                        <p class="f-14 mb-0 custom-limit-text" style="--linehide: 1;"><?php echo $itm['item_name']; ?>
                                            <?php if($postemp== $itm['id']){ ?>
                                                <i class="ti-check text-success f-14 f-w-700"></i>
                                            <?php } ?>
                                        </p>
                                        <p class="f-12 mb-0"><?php echo $itm['sku']; ?> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 d-inline-block align-middle">
                                <i class="ti-check text-success f-w-700 <?php echo $disabled; ?>" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Click To Allocate Item" onclick="selectItemsWithParty('<?php echo $itm['id']; ?>','ADD')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                            </div>
                        </div>
                <?php } ?>
                <?php } ?>
            <?php }else{ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Items</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>


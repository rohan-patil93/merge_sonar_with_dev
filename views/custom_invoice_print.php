<?php $config = $_SESSION['config'];
$rpurl = API."?mode=getPurchaseRequisitionsData&pr_id=1";
$rp_data = callAPI($rpurl);
$rpres = json_decode($rp_data, 1);
$data = $rpres['data'];
$items = $rpres['items'];
$cat1["field"] = "id,category_description";
$datas = urlencode(json_encode($cat1));
$itemcaturl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEM_CATEGORIES."&data=".$datas;
$itemcat_data = callAPI($itemcaturl);
$itemcatres = json_decode($itemcat_data, 1);
foreach ($itemcatres as $item) {
    $cate[$item['id']] = $item['category_description'];
}

 ?>
 <style type="text/css">
     .align_center{
         text-align: center;
     }
     .align_right{
         text-align: right;
     }
     .discrwidth{
         width: 300px;
     }
 </style>

<div class="d-flex flex-column-fluid">

    <div class="container-fluid">
    <div class="card card-custom overflow-hidden">
                                <div class="card-body p-0">
                                    <div class="row justify-content-center py-8 px-8 py-md-27 px-md-0">
                                        <div class="col-md-9">
                        <div class="d-flex justify-content-between pb-10 pb-md-20 flex-column flex-md-row">
                                                    <h1 class="display-4 font-weight-boldest mb-10">INVOICE</h1>

                            <?php $print_img ='uploads/all/'.$config['display_logo']; ?>
                            <div class="d-flex flex-column align-items-md-end px-0">
                                <a href="#" class="mb-5">
                                                            <img src="uploads/all/<?php echo $print_img; ?>" alt="" />
                                                        </a>
                                                        <!--end::Logo-->
                                                        <span class="d-flex flex-column align-items-md-end opacity-70">
                                                            <span><?php echo $config['contact_address']; ?> </span>
                                                        </span>
                                                    </div>
                                                </div>

                        <div class="kt-invoice__items">
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">DATE</span>
                                <span class="kt-invoice__text"> <?php echo date("M d, Y", strtotime($data['pr_date'])); ?></span>
                            </div>
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">Requisition NO.</span>
                                <span class="kt-invoice__text">PR 00000<?php echo $data['id']; ?></span>
                            </div>
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">Requisition TO.</span>
                                <span class="kt-invoice__text"><?php echo $data['party_address']; ?></span>
                            </div>

                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">Cost Center.</span>
                                <span class="kt-invoice__text"><?php echo " DELHI HEAD OFFICE "; ?></span>
                            </div>
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">User Name.</span>
                                <span class="kt-invoice__text"><?php echo $data['party_name']; ?></span>
                            </div>
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">Payment Terms.</span>
                                <span class="kt-invoice__text"><?php echo $data['payment_terms']; ?></span>
                            </div>
                            <div class="kt-invoice__item">
                                <span class="kt-invoice__subtitle">Place Of Supply.</span>
                                <span class="kt-invoice__text"><?php echo $data['place_of_supply']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="kt-invoice__body">
                    <div class="kt-invoice__container">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col"># &nbsp;&nbsp;</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">NAME</th>
                                        <th scope="col">DESCRIPTION</th>
                                        <th scope="col" class="align_center">UOM</th>
                                        <th scope="col" class="align_center">QTY</th>
                                        <th scope="col" class="align_center">RATE</th>
                                        <th scope="col" class="align_center">AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1; foreach ($items as $item) { ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo "RM" ?></td>
                                        <td><?php echo $item['item_name'] ?></td>
                                        <td class="discrwidth"><?php echo $item['description'] ?></td>
                                        <td class="align_center"><?php echo $item['purchase_uom'] ?></td>
                                        <td class="align_center"><?php echo $item['item_quantity'] ?></td>
                                        <td class="align_center"><?php echo customeummarmonyformate ($item['item_price']); ?></td>
                                        <td class="kt-font-danger kt-font-lg align_right"><?php echo customeummarmonyformate ($item['total_amount']); ?></td>
                                    </tr>
                                <?php $i++;
} ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="kt-invoice__footer">
                                            <div class="kt-invoice__container">
                                                <div class="kt-invoice__brand" style="width: 50%;">
                                                    <div class="kt-invoice__title">Approves</div>
                                                    <div class="kt-invoice__item">
                                                        <span class="kt-invoice__label">Approve 1:</span>
                                                        <span class="kt-invoice__value"> <?php echo "Ravi/".date("M d, Y", strtotime($data['pr_date'])); ?></span></span>
                                                    </div>
                                                    <div class="kt-invoice__item">
                                                        <span class="kt-invoice__label">Approve 2:</span>
                                                        <span class="kt-invoice__value"><?php echo "Shankar/".date("M d, Y", strtotime($data['pr_date'])); ?></span></span>
                                                    </div>

                                                </div>
                                                <div class="kt-invoice__total"  style="font-weight: 700;width: 50%;">
                                                    <div class="kt-invoice__item" style="float: right;">
                                                        <span class="kt-invoice__label" >SUB TOTAL&nbsp;:&nbsp;&nbsp;</span>
                                                        <span class="kt-invoice__value kt-font-danger kt-font-lg" style="float: right;"><?php echo customeummarmonyformate ($data['pr_amount']); ?></span>
                                                    </div>
                                                    <div class="kt-invoice__item" style="clear: both;float: right;">
                                                        <span class="kt-invoice__label">TAX AMOUNT&nbsp;:&nbsp;&nbsp;</span>
                                                        <span class="kt-invoice__value kt-font-danger kt-font-lg" ><?php echo customeummarmonyformate ($data['pr_tax_amount']); ?></span>
                                                    </div>
                                                    <div class="kt-invoice__item" style="clear: both;float: right;">
                                                        <span class="kt-invoice__label">TOTAL AMOUNT&nbsp;:&nbsp;&nbsp;</span>
                                                        <span class="kt-invoice__value kt-font-danger kt-font-lg" ><?php echo customeummarmonyformate ($data['pr_total_amount']); ?></span>
                                                    </div>

                                                </div>
                                                <div class="kt-invoice__brand" style="margin: 0 auto;font-weight: 700;">
                                                    <?php echo  numberToWordFormateRupees($data['pr_total_amount']);  ?>
                                                </div>
                                            </div>
                                        </div>
                <div class="kt-invoice__actions">
                    <div class="kt-invoice__container">
                        <button type="button" class="btn btn-label-brand btn-bold" onclick="window.print();">Download Invoice</button>
                        <button type="button" class="btn btn-brand btn-bold" onclick="window.print();">Print Invoice</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

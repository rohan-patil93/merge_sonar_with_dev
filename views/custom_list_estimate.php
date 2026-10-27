<?php
$itemurl = API."?mode=gettabledetailswithconditions&table=".TBL_ITEMS."&data=".$data;
$item_data = callAPI($itemurl);
$itemres = json_decode($item_data, 1);
?>
<script type="text/javascript">
    function toselectproduct(val){
        jQuery.ajax({
            url: "views/custom_display_estimate.php",
            data:'p_id='+val,
            type: "POST",
            success:function(data){
                $('#popup2').html(data);
                $('#kt_modal_KTDatatable_remote1').modal({ show: true});
            },
            error:function (){}
        });
    }
</script>
<div class="d-flex flex-column-fluid">
<div class="container-fluid">
<div class="row">
    <div class="col-lg-12">
        <form class="kt-form" id="kt_form" method="post" action="" onsubmit="return validateForm()" name="myForm" enctype="multipart/form-data">
        <div class="card card-custom card-sticky" id="kt_page_sticky_card">
            <div class="card-header">
                <div class="card-title">
                    <span class="kt-portlet__head-icon">
                                <i class="kt-font-brand flaticon-add-circular-button"></i>
                            </span>
                    <h3 class="card-label">&nbsp; Estimate </h3>
                </div>
                <div class="card-toolbar">
                    &nbsp;
                        <a href="javascript:history.go(-1)" class="btn btn-light-primary font-weight-bolder mr-2">
                        <i class="ki ki-long-arrow-back icon-xs"></i>
                        Back
                    </a>
                </div>
            </div>
            <div class="card-body">

<div class="form-group row">
    <div class="form-group col-lg-6">
        <span>Estimate</span>
    <div>
         <select aria-label="Itemm" class="form-control"  name= "itemm" id="kt_select2_4" onchange="toselectproduct(this.value)">
            <option value="0"> Select Any Category  </option>
            <?php foreach ($itemres as $item) {  ?>
            <option <?php if($tab2['item_id'] == $item['id']){ ?>selected <?php
} ?> value = <?php echo $item['id']; ?>   > <?php echo $item['item_name']; ?>
            <?php } ?>
            </option> <option value="-1">Other</option>
        </select>
    </div>

</div>
</div>

</div>

<div class="card-footer">
    <div class="row">
        <div class="col-lg-6">
            <input type="hidden" name="submit" value="Add">
            <button id="kt_login_signin_submit" class="btn btn-primary btn-shadow-hover font-weight-bold mr-2" name="submit_value" value="exit"><i class="la la-check"></i>Submit</button>
    </div>
</div>
</div>
</div>
</div>
</form>
</div>
</div>
</div>
</div>
<div id="kt_modal_KTDatatable_remote1" class="modal fade" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
        <div class="modal-content"  id="popup2">

        </div>
    </div>
</div>

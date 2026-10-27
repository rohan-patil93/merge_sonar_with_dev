<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$url = API."?mode=getsubscriptionusers&planspopup=Y&subscription_group_id=".$_POST['subscription_group_id'];
$subscripusers = callAPI($url);
$subscriptusers = json_decode($subscripusers,1);
$subscription_plans = $subscriptusers['subscription_plans'];
$module_setups = $subscriptusers['module_setup'];
$moduledetail = $module_setups;
$subscriptiontypeunit = $subscriptusers['subscriptiontypeunit'];

?>
<script type="text/javascript">
    function moduleSelect(module_id){
        if($("#checkmodule_"+module_id).is(':checked')){
            $(".planedshow_"+module_id).css("pointer-events","visible");
            $(".planedshow_"+module_id).css("opacity","1");
        }else{
            $(".planedshow_"+module_id).css("pointer-events","none");
            $(".planedshow_"+module_id).css("opacity","0.6");
        }
    }
    function submitFormSubscrib(){
        var post = $("#submitformplans").serialize();
        var subscription_group_id = $("#subscription_group_id").val();
         $.ajax({
            url: "ajax.php",
            type: "POST",
            data: 'page=insertcompanysubscriptionplans&'+post,
            success: function(response) {
                console.log(response);
                $('#kt_modal_KTDatatable_remotem').modal('hide');
                $.ajax({
                    url: "views/custom_insert_subscription_product_table.php",
                    type: "POST",
                    data: 'ajaxsub=Y&subscription_group_id='+subscription_group_id,
                    success: function(response) {
                        $("#productajax").html(response);

                    }
                });
            }
        });
    }
    function selectSubcritpType(value){
        $(".allhideclass").hide();
        $('.uncheckall').prop('checked', false);
        $(".allcssshow").css("pointer-events","none");
        $(".allcssshow").css("opacity","0.6");
        if(value =='M'){
            $(".monthly_lot_price").show();
        }else if(value =='Q'){
            $(".quarterly_lot_price").show();
        }else if(value =='HY'){
            $(".half_yearly_lot_price").show();
        }else if(value =='Y'){
            $(".yearly_lot_price").show();
        }
    }
</script>
<div class="modal-header">
    <h5 class="modal-title">Subscription Plans</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="table-responsive">
        <form id="submitformplans">
            <div class="row mb-2">
                <div class="col-lg-9"></div>
                <div class="col-lg-3">
                    <select aria-label="Subscriptiontypeunit" class="form-select  js-example-basic-single" name="subscriptiontypeunit" onchange="selectSubcritpType(this.value)">
                        <?php foreach($subscriptiontypeunit as $kyvalue=>$kyename){ ?>
                            <option value="<?php echo $kyvalue; ?>"><?php echo $kyename; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <table class="table table-hover m-b-0 without-header">
              <input type="hidden" name="subscription_group_id" value="<?php echo $_POST['subscription_group_id']; ?>">
            <tbody>
<?php $im =0; foreach($subscription_plans as $mdid => $subscription_plan){
    $companysubscriptionplann  = $companysubscriptionplan[$mdid];
    $modelcheck = $companysubscriptionplann['id'] !=""?"checked":"";
    $showoffcss = $companysubscriptionplann['id'] !=""?"":"style='pointer-events:none; opacity:0.6'";
     $showclass= "";
     foreach($subscription_plan as $subscription_plann){
         $showclass .= $subscription_plann['monthly_lot_price']!='0'?" monthly_lot_price":"";
         $showclass .= $subscription_plann['quarterly_lot_price']!='0'?" quarterly_lot_price":"";
         $showclass .= $subscription_plann['half_yearly_lot_price']!='0'?" half_yearly_lot_price":"";
         $showclass .= $subscription_plann['yearly_lot_price']!='0'?" yearly_lot_price":"";
     }
    ?>
                <tr class="<?php echo $showclass; ?> allhideclass">
                    <th scope="col">
                        <div class="d-inline-block">
                            <div class="checkbox-fade fade-in-primary" >
                              <label class="form-label">

                                <input aria-label="Checkbox" type="checkbox" value="<?php echo $mdid; ?>" id="checkmodule_<?php echo $mdid; ?>" name="checkmodule[<?php echo $mdid; ?>]" class="uncheckall" onchange="moduleSelect(<?php echo $mdid; ?>)" <?php echo $modelcheck; ?>>
                                <span class="cr">
                                  <i class="cr-icon icofont icofont-ui-check txt-primary"></i>
                                </span>
                                <span><?php echo $moduledetail[$mdid]; ?></span>
                              </label>
                            </div>
                        </div>
                    </th>
                <?php $mk=0;

                 foreach($subscription_plan as $subscription_plann){
                      $checked = $mk=='0'?"checked":"";
                     if($companysubscriptionplann['subscription_plan_id']==$subscription_plann['id'] && $companysubscriptionplann['subscription_plan_id'] !=""){
                         $checked = "checked";
                     }
                     $showclass= "";
                     $showclass .= $subscription_plann['monthly_lot_price']!='0'?" monthly_lot_price":"";
                     $showclass .= $subscription_plann['quarterly_lot_price']!='0'?" quarterly_lot_price":"";
                     $showclass .= $subscription_plann['half_yearly_lot_price']!='0'?" half_yearly_lot_price":"";
                     $showclass .= $subscription_plann['yearly_lot_price']!='0'?" yearly_lot_price":"";
                     ?>
                    <input type="hidden" name="standerduaerlimit_<?php echo $subscription_plann['id']; ?>" value="<?php echo $subscription_plann['lot_user']; ?>">

                    <input type="hidden" name="standerpricemonthly_<?php echo $subscription_plann['id']; ?>" value="<?php echo $subscription_plann['monthly_lot_price']; ?>">
                    <input type="hidden" name="standerpricequarterly_<?php echo $subscription_plann['id']; ?>" value="<?php echo $subscription_plann['quarterly_lot_price']; ?>">
                    <input type="hidden" name="standerpricehalfyear_<?php echo $subscription_plann['id']; ?>" value="<?php echo $subscription_plann['half_yearly_lot_price']; ?>">
                    <input type="hidden" name="standerpriceyearly_<?php echo $subscription_plann['id']; ?>" value="<?php echo $subscription_plann['yearly_lot_price']; ?>">

                    <th scope="col" class="planedshow_<?php echo $mdid; ?> allhideclass allcssshow <?php echo $showclass; ?>" <?php echo $showoffcss; ?>>
                        <div class="d-inline-block align-middle">
                            <input aria-label="Radio option" type="radio" name="planedname_<?php echo $mdid; ?>" <?php echo $checked ; ?> value="<?php echo $subscription_plann['id']; ?>">
                            <h6 class="mb-0"><?php echo $subscription_plann['plan_name'];  ?></h6>
                            <p class="text-muted m-b-0"><?php echo $subscription_plann['lot_user']."- Users Limit"; ?></p>
<h6 class="f-w-700 monthly_lot_price allhideclass" ><?php echo "Rs. ".$subscription_plann['monthly_lot_price']; ?> / <span class="text-muted d-inline-block">Monthly</span></h6>
<h6 class="f-w-700 quarterly_lot_price allhideclass" style="display: none"><?php echo "Rs. ".$subscription_plann['quarterly_lot_price']; ?> / <span class="text-muted d-inline-block">Quarterly</span></h6>
<h6 class="f-w-700 half_yearly_lot_price allhideclass" style="display: none"><?php echo "Rs. ".$subscription_plann['half_yearly_lot_price']; ?> / <span class="text-muted d-inline-block">Half Yearly</span></h6>
<h6 class="f-w-700 yearly_lot_price allhideclass" style="display: none"><?php echo "Rs. ".$subscription_plann['yearly_lot_price']; ?> / <span class="text-muted d-inline-block">Yearly</span></h6>
                            </div>
                    </th>
                <?php $mk++;
} ?>
                </tr>
<?php  $im++;
}  ?>

            </tbody>
        </table>
    </form>
    </div>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-clean btn-bold btn-upper btn-font-sm" data-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary mr-2" onclick="submitFormSubscrib()" >Submit</button>
</div>

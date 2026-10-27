<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$vdata1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$vdata1["field"] = "id,item_name";
$vdata1['order'] = "item_name ASC";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=stationery_items&data=".$vdata;
$data_details = callAPI($vurl);
$itemdata = json_decode($data_details,1);
$type = isset($_POST['type']) ? $_POST['type'] :"" ;
$item_id = isset($_POST['item_id']) ? $_POST['item_id'] :"" ;
$cat_id = isset($_POST['cat_id']) ? $_POST['cat_id'] :"" ;

$vdata1["where"] = array('is_active'=>'0','company_id'=>$_SESSION['company_id']);
$vdata1["field"] = "id,name";
$vdata1['order'] = "name ASC";
$vdata = urlencode(json_encode($vdata1));
$vurl = API."?mode=gettabledetailswithconditions&table=stationery_categories&data=".$vdata;
$data_details = callAPI($vurl);
$cat_data = json_decode($data_details,1);

?>
<script type="text/javascript">
    function submitcreateproductrfrom(){
        // Validate required fields
        var category_id = $("#adddata #category_id").val();
        var item_name = $("#adddata #item_name").val();
        var available_qty = $("#adddata #available_qty").val();

        if(!category_id) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please select a category',
                ''
            );
            return false;
        }
        if(!item_name) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please enter product name',
                ''
            );
            return false;
        }
        if(!available_qty) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please enter quantity',
                ''
            );
            return false;
        }

        var post = $("#submitformadditionalservice").serialize();
        console.log(post);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=addstationery&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                setTimeout(function(){
            window.location.reload();
            }, 500);
            },
            error:function (){}
        });
    }
    function viewaddupdate(val){
         if(val=='add'){
                 $("#checkval").hide();
                 $("#adddata").show();
                 $("#topupdata").hide();
         }
         if(val=='topup'){
                 $("#checkval").hide();
                 $("#adddata").hide();
                 $("#topupdata").show();
         }
    }
    function submittopupproductrfrom(){
        // Validate required fields
        var category_id = $("#topupdata select[name='category_id']").val();
        var item_id = $("#topupdata select[name='item_id']").val();
        var available_qty = $("#topupdata input[name='available_qty']").val();

        if(!category_id) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please select a category',
                ''
            );
            return false;
        }
        if(!item_id) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please select an item',
                ''
            );
            return false;
        }
        if(!available_qty) {
            tostarnotify(
                'top', 'right',
                'fa fa-thumbs-down',
                'form-label label label-danger',
                'animated flipInX',
                'animated flipOutX',
                'Please enter quantity',
                ''
            );
            return false;
        }

        var post = $("#submitformupdatestopupervice").serialize();
        console.log(post);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=topupstationery&'+post,
            type: "POST",
            success:function(data){
                console.log(data);
                setTimeout(function(){
            window.location.reload();
            }, 500);
            },
            error:function (){}
        });

    }

</script>
<div class="modal-header">
    <h5 class="modal-title"> <?php if($_POST['id']!=""){ echo "Edit";
}else{ echo "Add";
} ?> / Topup Item  </h5>
    <button type="button" class="btn-close btn_white"  data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div id="checkval" <?php if($type!=""){?> style="display:none;" <?php
} ?> >
            <div class="col-lg-12 row p-2 text-center">
                  <div class="col-lg-12 ">
                      <button class="btn btn-primary p-3" type="button" onclick="viewaddupdate('add')"> Add New Product </button>
                  </div>
                  <div class="col-lg-12 p-3"> OR</div>
                    <div class="col-lg-12">
                      <button class="btn btn-primary p-3" type="button" onclick="viewaddupdate('topup')"> Topup Product </button>
                  </div>
            </div>
    </div>
    <div  style="display:none;" id="adddata">
        <form class="kt-form " id="submitformadditionalservice" >
            <div class="form-group w-full">
                <span>Category Name</span>
                <select aria-label="Category" class="form-select js-example-basic-single" name="category_id" id="category_id">
                    <option value="">Select Category</option>
                        <?php foreach($cat_data as $cat){ ?>
                            <option  <?php if($cat_id==$cat['id']){ echo "selected";
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <span>Product Name</span>
                <input aria-label="Please Enter Your Product Name" placeholder="Please Enter Your Product Name" type="text" name="item_name" id="item_name" class="form-control">
            </div>
            <div class="form-group">
                <span>Quantity</span>
                <input aria-label="Please Enter Your Quantity" placeholder="Please Enter Your Quantity" name="available_qty" id="available_qty" type="text" class="form-control text-right fill">
            </div>
            <div class="form-group">
                <span>Price</span>
                <input aria-label="Cost" name="cost" id="cost" type="text" class="form-control text-right fill">
            </div>
            <div class="form-group">
                <span>Description</span>
                <textarea aria-label="Please describe your product" class="form-control"  name="description" id="description" placeholder="Please describe your product"></textarea>
            </div>

            <div class="modal-footer">
                <input type="hidden" name="id" id="id" value="<?php echo $_POST['id']; ?>">
                <input type="hidden" name="location_id" id="location_id" value="<?php echo $_SESSION['location_id']; ?>">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submitcreateproductrfrom()">Save</button>
            </div>
        </form>
    </div>
    <div  <?php if($type==""){?> style="display:none;" <?php
} ?>id="topupdata">
        <form class="kt-form " id="submitformupdatestopupervice" >
            <div class="form-group w-full">
                <span>Category Name</span>
                <select aria-label="Category" class="form-select js-example-basic-single" name="category_id" id="category_id_2">
                    <option value="">Select Category</option>
                        <?php foreach($cat_data as $cat){ ?>
                            <option  <?php if($cat_id==$cat['id']){ echo "selected";
} ?> value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php } ?>
                </select>
            </div>
            <div class="form-group w-full">
                <span>Product </span>
                <select aria-label="Item" class="form-select js-example-basic-single" name="item_id">
                        <option value="">Select Item</option>
                        <?php foreach($itemdata as $item){ ?>
                            <option  <?php if($item_id==$item['id']){ echo "selected";
} ?> value="<?php echo $item['id']; ?>"><?php echo $item['item_name']; ?></option>
                        <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <span>Quantity</span>
                <input aria-label="Please Enter Your Quantity" placeholder="Please Enter Your Quantity" name="available_qty" id="available_qty_2" type="text" class="form-control text-right fill">
            </div>

            <div class="form-group">
                <span>Comments</span>
                <textarea aria-label="Please describe your product" class="form-control"  name="comment" id="comment" placeholder="Please describe your product"></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                <input type="hidden" name="location_id" id="location_id_2" value="<?php echo $_SESSION['location_id']; ?>">
                <button type="button" class="btn btn-primary waves-effect waves-light " onclick="submittopupproductrfrom()">Save</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript" src="assets/bower_components/moment/js/moment.min.js"></script>
<script type="text/javascript" src="assets/pages/advance-elements/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/js/custom_datepicker.js"></script>
<script type="text/javascript">
  $(".js-example-basic-single").select2();
  $(".js-example-basic-multiple").select2();
  $(".js-example-disabled-results").select2();
  $( ".datepicker" ).datepicker({
    showOn: "both",
    buttonText: "<i class='ti-calendar'></i>",
    dateFormat: date_format,
    changeMonth: true,
    changeYear: true
  });
</script>

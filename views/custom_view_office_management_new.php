<?php
$url = API."?mode=togetofficemanagement";
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$sitem = $data['sitem'];
print_r($sitem);

?>
<style type="text/css">
    .qty {
    width: 40px;
    height: 25px;
    text-align: center;
}
input.qtyplus { width:25px; height:25px;}
input.qtyminus { width:25px; height:25px;}
</style>
<script type="text/javascript">

    function addqty(item_id){
        var qty = $("#qty_"+item_id).val();
        console.log("qty:"+qty);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=addstationerycartqty&qty='+qty+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                $("#cartval").html(data);
                cartval
            },
            error:function (){}
        });
    }
    function addandupdatestationery(id=""){
        jQuery.ajax({
            url: "views/custom_insert_stationery_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function recicedcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_received_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    function sendcourier(id=""){
        jQuery.ajax({
            url: "views/custom_insert_send_courier_popup.php",
            data:'option=noption&id='+id,
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");
            },
            error:function (){}
        });
    }
    jQuery(document).ready(($) => {
        $('.quantity').on('click', '.plus', function(e) {
            let $input = $(this).prev('input.qty');
            let val = parseInt($input.val());
            $input.val( val+1 ).change();
        });

        $('.quantity').on('click', '.minus',
            function(e) {
            let $input = $(this).next('input.qty');
            var val = parseInt($input.val());
            if (val > 0) {
                $input.val( val-1 ).change();
            }
        });
    });
</script>
<div class="row">
    <div class="col-lg-8">
        <div class="courier">
            <ul class="nav nav-tabs tabs row">
                <li class="nav-item col-lg-3 col-4">
                    <a class="nav-link active position-relative" data-bs-toggle="tab" href="#courier" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                       <i class="ti-package f-30 text-info p-l-15"></i>
                        <span class="text-info f-18 f-w-700">Courier</span>
                    </a>
                </li>
                <li class="nav-item col-lg-3 col-4">
                    <a class="nav-link position-relative" data-bs-toggle="tab" href="#stationery" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <i class="ti-ruler-pencil f-30 text-info p-l-15"></i>
                        <span class="text-info f-18 f-w-700">Stationery</span>
                    </a>
                </li>
                <li class="nav-item col-lg-3 col-4">
                    <a class="nav-link position-relative" data-bs-toggle="tab" href="#transport" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <i class="ti-car f-30 text-info p-l-15"></i>
                        <span class="text-info f-18 f-w-700">Transport</span>
                    </a>
                </li>
                <li class="nav-item col-lg-3 col-4">
                    <a class="nav-link position-relative" data-bs-toggle="tab" href="#food" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">
                        <i class="ti-hand-open f-30 text-info p-l-15"></i>
                        <span class="text-info f-18 f-w-700">Food</span>
                    </a>
                </li>
            </ul>
        </div>
        <!-- Tab panes -->
        <div class="tab-content tabs card-block card mt-3 p-3">
            <div class="tab-pane active" id="courier" role="tabpanel">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="card social-card">
                            <div class="card-body text-center courier_popup">
                                <div class="text-facebook m-b-20"><i class="ti-back-left"></i></div>
                                <h3 class="text-facebook f-w-700">250</h3>
                                <p>Likes</p>
                                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                                <button type="button" class="btn btn-primary waves-effect c_btn" onclick="recicedcourier()">
                                    <span class="ti-plus"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="card social-card">
                            <div class="card-body text-center courier_popup">
                                <div class="text-facebook m-b-20"><i class="ti-back-right"></i></div>
                                <h3 class="text-facebook f-w-700">350</h3>
                                <p>Likes</p>
                                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                                <button type="button" class="btn btn-primary waves-effect c_btn" onclick="sendcourier()">
                                    <span class="ti-plus"></span>
                                </button>
                            </div>
                            <div class="modal fade" id="courier-out-Modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content align-left">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Courier in</h4>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <span>Date</span>
                                                <input aria-label="Select date" type="date" name="" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <span>Courier Number</span>
                                                <input aria-label="Input field" type="text" name="" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <span>Moblie No.</span>
                                                <input aria-label="Input field" type="tel" name="" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary waves-effect waves-light ">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12">
                        <div class="card social-card">
                            <div class="card-body text-center">
                                <div class="text-facebook m-b-20"><i class="ti-timer"></i></div>
                                <h3 class="text-facebook f-w-700">450</h3>
                                <p>Likes</p>
                                <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i>223 from last 7 days</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Latest Products</h5>
                        <div class="card-header-right">
                            <ul class="nav nav-tabs md-tabs m-r-20 border-0">
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link p-2 active" data-bs-toggle="tab" href="#month" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Month</a>
                                </li>
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link p-2" data-bs-toggle="tab" href="#week" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Week</a>
                                </li>
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link p-2" data-bs-toggle="tab" href="#day" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Day</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content card-block table-border-style">
                        <div class="tab-pane active" id="month" role="tabpanel">
                            <div class="table-responsive infisuite-hover">
                                <table class="table table-framed">
                                    <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="week" role="tabpanel">
                            <div class="table-responsive infisuite-hover">
                                <table class="table table-framed">
                                    <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme 2</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="day" role="tabpanel">
                            <div class="table-responsive infisuite-hover">
                                <table class="table table-framed">
                                    <tbody>
                                    <tr>
                                        <th scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </th>
                                        <th scope="col">
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme 3</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </th>
                                        <th scope="col">
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </th>
                                        <th scope="col">
                                            <span class="form-label label label-success">In Progress</span>
                                        </th>
                                        <th scope="col">
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-success">In Progress</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td scope="row" style="width: 10%;">
                                            <img src="../files/assets/images/avatar-4.jpg" class="img-radius img-40" alt="User-Profile-">
                                        </td>
                                        <td>
                                            <a class="d-block text-dark f-w-600 f-14" href="">Bestseller Theme</a>
                                            <span class="text-muted f-14 f-w-600">Best Customers</span>
                                        </td>
                                        <td>
                                            <span class="text-muted f-14 f-w-600">ReactJS, Ruby</span>
                                        </td>
                                        <td>
                                            <span class="form-label label label-danger">Rejected</span>
                                        </td>
                                        <td>
                                            <button class="btn waves-effect waves-light btn-primary"><i class="feather icon-edit-1"></i></button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane " id="stationery" role="tabpanel">
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn waves-effect waves-light btn-primary float-right m-l-5"data-bs-toggle="modal" data-bs-target="#create-product-modal">
                            <span class="ti-plus m-r-5"></span>Create
                        </button>
                        <button type="button" class="btn waves-effect waves-light btn-primary float-right "data-bs-toggle="modal" data-bs-target="#create-product-modal">
                            <span class="ti-shopping-cart m-r-5"></span>&nbsp; (<span id="cartval"><?php echo $data['cart_count']; ?></span>)
                        </button>
                        <div class="modal fade" id="create-product-modal" tabindex="-1" style="z-index: 1050; display: none;" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content align-left">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Sell new product</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <span>Product Name</span>
                                            <input aria-label="Please Enter Your Product Name" placeholder="Please Enter Your Product Name" type="text" name="" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <span>Quantity</span>
                                            <input aria-label="Please Enter Your Quantity" id="phone-2a" placeholder="Please Enter Your Quantity" name="phone" type="number" class="form-control phone fill">
                                        </div>
                                        <div class="form-group">
                                            <span>Price</span>
                                            <input aria-label="Phone" id="phone-2a_2" name="phone" type="number" class="form-control phone fill">
                                        </div>
                                        <div class="form-group">
                                            <span>Description</span>
                                            <textarea aria-label="Please describe your product" class="form-control" placeholder="Please describe your product"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary waves-effect waves-light ">Save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <?php foreach((array)$data['stationery_items'] as $stationery_items){
                            $sqty = $sitem[$stationery_items['id']]!="" ? $sitem[$stationery_items['id']] : 0;
                    ?>
                    <div class="col-lg-4 col-md-4 sol-sm-4 col-6 m-b-15">
                        <div class="pro-dsc-sec border">
                            <div class="pro-img">
                                <?php if($stationery_items['image']!=""){ ?>
                                <img src="uploads/all/<?php echo $stationery_items['image'] ?>" class="img-fluid" alt="">
                                <?php } ?>
                            </div>
                            <h4 class="mt-3 f-12 text-primary f-w-600 text-uppercase m-l-10"><?php echo $stationery_items['item_name'] ?></h4>
                            <h3 class="f-16 f-w-600 m-l-10 m-b-15"><?php echo $stationery_items['description'] ?></h3>
                            <div class="row">
                                <div class="col-lg-6"></div>
                                <div class="col-lg-6">
                                    <div class="text-right m-b-10 m-r-5 quantity-sec">
                                        <div class="input-group">
                                          <b class="input-group-btn position-static">
                                              <button type="button" class="quantity-left-minus btn btn-danger btn-number p-2"  data-type="minus" data-field="">
                                                <i class="ti-minus"></i>
                                              </button>
                                          </b>
                                          <input aria-label="Quantity" type="text" id="quantity_1" name="quantity" class="form-control input-number" value="10" min="1" max="100">
                                          <b class="input-group-btn position-static">
                                              <button type="button" class="quantity-right-plus btn btn-success btn-number p-2" data-type="plus" data-field="">
                                                  <i class="ti-plus"></i>
                                              </button>
                                          </b>
                                      </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane" id="transport" role="tabpanel">
                <p class="m-0">3. This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mas Cum sociis natoque penatibus et magnis dis.....</p>
            </div>
            <div class="tab-pane" id="food" role="tabpanel">
                <p class="m-0">3. This is Photoshop's version of Lorem IpThis is Photoshop's version of Lorem Ipsum. Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean mas Cum sociis natoque penatibus et magnis dis.....</p>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="" style="background: #B0DC00 url(../assets/images/city.png);
            width:100%;height:250px;background-repeat:no-repeat;background-position:100% 50px;background-size: 500px auto;border-radius: 15px;">
            <h3 class="f-w-600 pt-5 f-30 m-l-50">We are working<br />to boost lovely mood</h3>
            <button class="btn btn-inverse waves-effect waves-light m-l-50 mt-4">Create a Store</button>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5>Courier</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-12"><h3 class="f-16 f-w-600 mb-0 m-l-5">IN</h3></div>
                    <div class="col-lg-2 p-2">
                        <img src="uploads/all/file_1689092069_64ad7fe56a8f4.jpg" class="img-radius img-50 m-l-10" alt="">
                    </div>
                    <div class="col-lg-7 p-2">
                        <h4 class="f-14 m-0 f-w-600">Bestseller Theme</h4>
                        <span class="f-12">Best Customers</span>
                    </div>
                    <div class="col-lg-3 p-2">
                        <span class="form-label label label-success">In Progress</span>
                    </div>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-lg-12"><h3 class="f-16 f-w-600 m-l-5 mb-0">OUT</h3></div>
                    <div class="col-lg-2 p-2">
                        <img src="uploads/all/file_1689092069_64ad7fe56a8f4.jpg" class="img-radius img-50 m-l-10" alt="">
                    </div>
                    <div class="col-lg-7 p-2">
                        <h4 class="f-14 m-0 f-w-600">Bestseller Theme</h4>
                        <span class="f-12">Best Customers</span>
                    </div>
                    <div class="col-lg-3 p-2">
                        <span class="form-label label label-success">In Progress</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header">
                <h5>Stationery</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8 p-2">
                        <h4 class="f-14 mb-0 f-w-600 m-l-10">A4 SIZE PAPER</h4>
                        <span class="f-12 m-l-10">A4 size and photocopier paper</span>
                    </div>
                    <div class="col-lg-4 p-2">
                        <div class="text-right m-b-10 m-r-5 quantity-sec">
                            <div class="input-group">
                                <b class="input-group-btn position-static">
                                    <button type="button" class="quantity-left-minus btn btn-danger btn-number p-2"  data-type="minus" data-field="">
                                        <i class="ti-minus"></i>
                                    </button>
                                </b>
                                <input aria-label="Quantity" type="text" id="quantity_2" name="quantity" class="form-control input-number" value="10" min="1" max="100">
                                <b class="input-group-btn position-static">
                                    <button type="button" class="quantity-right-plus btn btn-success btn-number p-2" data-type="plus" data-field="">
                                        <i class="ti-plus"></i>
                                    </button>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center mt-3">
                    <div class="col-lg-8 p-2">
                        <h4 class="f-14 mb-0 f-w-600 m-l-10">PEN</h4>
                        <span class="f-12 m-l-10">Pen</span>
                    </div>
                    <div class="col-lg-4 p-2">
                        <div class="text-right m-b-10 m-r-5 quantity-sec">
                            <div class="input-group">
                                <b class="input-group-btn position-static">
                                    <button type="button" class="quantity-left-minus btn btn-danger btn-number p-2"  data-type="minus" data-field="">
                                        <i class="ti-minus"></i>
                                    </button>
                                </b>
                                <input aria-label="Quantity" type="text" id="quantity_3" name="quantity" class="form-control input-number" value="10" min="1" max="100">
                                <b class="input-group-btn position-static">
                                    <button type="button" class="quantity-right-plus btn btn-success btn-number p-2" data-type="plus" data-field="">
                                        <i class="ti-plus"></i>
                                    </button>
                                </b>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$_SESSION['apply_js']['multiselest'] = "1";
$url = API."?mode=togetofficemanagement&location_id=1&type=".$type."&admin_type=".$_SESSION['admin_type'];
//echo $url;
$data_details = callAPI($url);
$data = json_decode($data_details,1);
$sitem = $data['sitem'];
$_SESSION['st_show_type'] = 1;
$st_show_type=$_SESSION['st_show_type'];
?> 
<script type="text/javascript">
    function tosearchstationeritems(val){
        console.log("UUUU");
        var stationery_search = $("#stationery_search").val(); 
         var stationery_cat_id = $("#stationery_cat_id").val(); 
        jQuery.ajax({
            url: "views/custom_view_stationery_items.php",
            data:'ajax_choose=Y&cat_id='+stationery_cat_id+'&search='+stationery_search,
            type: "POST",
            success:function(data){
                console.log("work data"+data);
                 $("#stationeryitem_view").html(data);                             
            },
            error:function (){}
        });
     }
      function orderlist(){
         $("#stationeryitem").hide();
         $("#stationeryorders").show();
          $("#orderlist").hide();
         $("#stationeryitemlist").show();   
    }
    function stationeryitemlist(){
         $("#stationeryitem").show();
         $("#stationeryorders").hide();
          $("#orderlist").show();
         $("#stationeryitemlist").hide();   
    }
    function submitinstationaryfrom(){
        var deliver_type = $("#deliver_type").val();
        var request_date = $("#request_date_st").val();
        var require_date = $("#require_date_st").val();
        var request_comment = $("#request_comment").val();   
        var building_id = $("#building_id").val();
        var building_floor_id = $("#building_floor_id").val();
        var building_floor_space_id = $("#building_floor_space_id").val(); 
        var location_id = $("#st_location_id").val(); 
        var emp_id = $("#emp_id").val();
        var st_current_status = $("#st_current_status").val(); 
        var other_name = $("#other_name").val();  
        jQuery.ajax({
            url: "ajax.php",
            data:'page=tocreateuserstationeryrequest&deliver_type='+deliver_type+'&request_date='+request_date+'&require_date='+require_date+'&request_comment='+request_comment+'&building_id='+building_id+'&building_floor_id='+building_floor_id+'&building_floor_space_id='+building_floor_space_id+'&location_id='+location_id+'&emp_id='+emp_id+'&st_current_status='+st_current_status+'&other_name='+other_name,
            type: "POST",
            success:function(data){
                console.log(data);
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','Stationery request submitted successfully.','');
                window.location.reload();
                //window.location.href = 'index.php?option=COM_VIEW_OFFICE_MANAGEMENT&TAB=2';
            },
            error:function (){}
        });
    }
    function searchstationery(){
          var request_date = $("#request_date").val(); 
         var require_date = $("#require_date").val(); 
         var stationery_request_ref_no = $("#stationery_request_ref_no").val(); 
         var stationery_user_id = $("#stationery_user_id").val();
        
        var str = "&request_date="+request_date+"&require_date="+require_date+"&stationery_request_ref_no="+stationery_request_ref_no+"&stationery_user_id="+stationery_user_id;
        console.log(str);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=searchstationery'+str,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                //window.location.reload();
                $("#stationerysearchdata").html(data);
                
            },
            error:function (){}
        });
    }
    function viewstreport(item_id){
        var loc = $("#streportledage_"+item_id).val();
        if(loc ==1){
            $("#dis_streportledage_"+item_id).show();
            $("#streportledage_"+item_id).val(2);
        }
        if(loc ==2){
            $("#dis_streportledage_"+item_id).hide();
            $("#streportledage_"+item_id).val(1);
        }
    }
    function addmins(type,item_id){
        var qty = $("#qty_"+item_id).val();
        var oldqty = qty;
        if(type == 'add' ){
            qty  = parseFloat(qty)+1;
        }
        if(type == 'mins' && qty >0){
            qty  = parseFloat(qty)-1;
        }
        if(qty!=oldqty){
            $("#qty_"+item_id).val(qty);
            $("#sidqty_"+item_id).val(qty);            
            addqty(item_id);
        }
        if(qty==1 && type == 'add'){
             $("#checkoutcal").show();
            var count = $(".srow").length;
            var mins = "'mins'";
            var add = "'add'";
            var item_name = $("#item_name_"+item_id).val();
            count++;
            var htmlRows = '';
            htmlRows += '<div class="row align-items-center srow" id="shkrow_'+item_id+'" >';
            htmlRows += '<div class="col-lg-8 p-2">';
            htmlRows += ' <h4 class="f-14 mb-0 f-w-600 m-l-10">'+item_name+'</h4>';
            htmlRows += '<span class="f-12 m-l-10">'+item_name+'</span></div>';
            htmlRows += '<div class="col-lg-4 p-2"> <div class="text-right m-b-10 m-r-5 quantity-sec"><div class="input-group"><b class="input-group-btn position-static"><button type="button" class="quantity-left-minus btn btn-danger btn-number p-2" onclick="addmins('+mins+','+item_id+')"><i class="ti-minus"></i></button></b>';
            htmlRows += '<input type="text"  name="quantity" id="sidqty_'+item_id+'" value="'+qty+'"  onblur="addqty('+item_id+')"  class="form-control input-number text-center" min="1" max="100"><b class="input-group-btn position-static"><button type="button" class="quantity-right-plus btn btn-success btn-number p-2" onclick="addmins('+add+','+item_id+')" ><i class="ti-plus"></i></button></b></div></div></div>';
            $('#sidestationary').append(htmlRows);
        }
        if(qty==0){
             $("#shkrow_"+item_id).remove();
        }
    }
    function addqty(item_id){
        var qty = $("#qty_"+item_id).val();
        console.log("qty:"+qty);
        jQuery.ajax({
            url: "ajax.php",
            data:'page=addstationerycartqty&qty='+qty+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                //window.location.reload();
                $("#cartval").html(data);
                ///cartval
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
    function cartstationery(){
        jQuery.ajax({
            url: "views/custom_view_stationery_cart_popup.php",
            data:'option=noption',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");                
            },
            error:function (){}
        });
    }
    function cartlist(){
        jQuery.ajax({
            url: "views/custom_view_stationery_list_cart_popup.php",
            data:'option=noption',
            type: "POST",
            success:function(data){
                $('#popup1').html(data);
                $('#kt_modal_KTDatatable_remote').modal("show");                
            },
            error:function (){}
        });
    }
    function cartviewreqlist(val){
        jQuery.ajax({
            url: "views/custom_view_stationery_cart_details_popup.php",
            data:'option=noption&request_no='+val,
            type: "POST",
            success:function(data){
                $('#dynamic_popup1').html(data);
                $('#kt_modal_dynamic_popup').modal("show");                
            },
            error:function (){}
        });
    }
    function searchstationeryreport(){
         var list_date = $("#list_date").val(); 
         var item_id = $("#report_stationery_item_id").val(); 
         jQuery.ajax({
            url: "ajax.php",
            data:'page=searchstationeryreport&list_date='+list_date+'&item_id='+item_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);
                //window.location.reload();
                $("#searchreport").html(data);
                
            },
            error:function (){}
        });

    }
    function tocheckempdd(val){
        if (val == "999999") {
            $("#othername").show();
        } else {
            $("#othername").hide();
        }
    }
</script>
<div class="row">
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="row">
                <div class="col-lg-12">
                    <?php if($_SESSION['admin_type']==1){ ?>
                        <button type="button" class="btn waves-effect waves-light btn-primary float-right m-l-5" onclick="addandupdatestationery()">
                            <span class="ti-plus m-r-5"></span>Create
                        </button>
                    <?php } ?>
                    <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;" role="tablist">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stitems" role="tab">Items</a>

                        </li>
                         

                        <?php if($st_show_type==1){ ?>
                       
                        <?php if($_SESSION['admin_type']==1){ ?>
                             <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#stmyorder" role="tab">My Orders</a>

                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#stapprovalorder" role="tab">To Be Approved</a>
                            </li>
                            
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#sthistory" role="tab">History</a>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#streport" role="tab">Report</a>
                            </li>
                            <?php } ?>
                        <?php }else{ ?> 
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#stallorder" role="tab"> Orders</a>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#availableitem" role="tab">  Available Items </a>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#reportingitem" role="tab"> Reporting Items</a>
                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#monthlyreport" role="tab"> Monthly Report</a>
                            </li>
                        
                        <?php } ?>
                    </ul>
                        <!-- <button type="button" class="btn waves-effect waves-light btn-success float-right" id="orderlist" onclick="orderlist()" >
                           <span class="ti-list m-r-5"></span> Orders
                        </button>
                         <button type="button" class="btn waves-effect waves-light btn-primary float-right" id="stationeryitemlist" onclick="stationeryitemlist()" style="display: none;" >
                           <span class="ti-list m-r-5"></span> items
                       </button> -->
                   </div>
               </div>
               <div class="tab-content card-block table-border-style">
                <!----- stationery start 1.1 --------->
                <div class="tab-pane active" id="stitems" role="tabpanel">
                    <div class="row col-lg-12">
                        <div class="col-lg-4">
                            <label> Category : </label></br>
                            <select class="form-control border js-example-basic-single" name="stationery_cat_id" id="stationery_cat_id">
                                <option value="">Select Any One</option>
                                <?php foreach((array)$data['stationery_categories'] as $cate){ ?>
                                    <option value="<?php echo $cate['id']; ?>"><?php echo $cate['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-4">
                            <label> Search : </label>
                            <input type="text" class="form-control" name="stationery_search" id="stationery_search">
                        </div>
                        <div class="col-lg-4">
                            <label>  </label></br>
                            <button class="btn btn-success" onclick="tosearchstationeritems()" >Search</button>
                        </div>
                    </div>
                    <?php include('custom_view_stationery_items.php'); ?>
                </div>
                <!----- stationery end 1.1 --------->
                <!----- stationery start 1.2 --------->
                <div class="tab-pane " id="stmyorder" role="tabpanel">
                    <div class="row mt-3" id="stationeryorders"   >
                        <div class="card-header-right text-right">
                            <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;" role="tablist">
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#mysactive" role="tab">Active</a>                                        
                                </li>
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link " data-bs-toggle="tab" href="#myspending" role="tab">Pending</a>                                        
                                </li>
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link" data-bs-toggle="tab" href="#mysapproved" role="tab">Approved</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content card-block table-border-style">
                            <div class="tab-pane active" id="mysactive" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['my_stationery_requests'] as $down){
                                                if($down['current_status'] == ST_ACTIVE){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" >
                                                            <i class="ti-pencil-alt m-0"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane " id="myspending" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['my_stationery_requests'] as $down){
                                                if($down['current_status']> ST_ACTIVE && $down['current_status']<ST_APPROVED){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" >
                                                            <i class="ti-pencil-alt m-0"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="mysapproved" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['my_stationery_requests'] as $down){
                                                if($down['current_status'] ==ST_APPROVED){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" >
                                                            <i class="ti-pencil-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!----- stationery end 1.2 --------->
                <!----- stationery start 1.2.1--------->
                <div class="tab-pane " id="stallorder" role="tabpanel">
                    <div class="row mt-3" id="stationeryorders"   >                      
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <tbody>
                                    <?php foreach((array)$data['my_stationery_requests'] as $down){
                                        //if($down['current_status'] !=ST_APPROVED){
                                            if($down['emp_id'] == '999999'){ $data['emps'][$down['emp_id']]['display_name'] =  $down['other_name']; }
                                         ?>
                                            <tr>
                                                <td width="10%" scope="row">
                                                 <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['emp_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                 <?php }else{ ?>
                                                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['emp_id']]['display_name']); ?></span></a>
                                                 <?php } ?>

                                             </td>
                                             <td>
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['emp_id']]['display_name']." ".$down['current_status']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['emp_id']]['email_id']; ?></span>
                                            </td>
                                            <td>
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['require_date']); ?></span>
                                            </td>
                                            <td>
                                                
                                                <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                            </td>
                                            <td>
                                                <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                            </td>
                                            <td>
                                                <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" >
                                                    <i class="ti-pencil-alt m-0"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } 
                                    //} ?>

                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
                <!----- stationery end 1.2.1 --------->
                <!----- stationery start 1.2.2--------->
                <div class="tab-pane " id="availableitem" role="tabpanel">
                    <div class="row mt-3" id="availableitemreport"> 
                        <table class="table table-framed">
                            <thead>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Received</th>
                                <th>Current Stock</th>
                                <th>Consumed </th>
                                <th>Balance </th>
                            </thead>
                        </table>
                    </div>
                </div>
                <!----- stationery start 1.2.2--------->
                <!----- stationery start 1.2.3--------->
                <div class="tab-pane " id="reportingitem" role="tabpanel">
                    <div class="row mt-3" id="reportingitemreport"> 
                        <table class="table table-framed">
                            <thead>
                                <th>Employee Name</th>
                                <th>Item</th>
                                <th>Quantity </th>
                                <th>Date of Issue</th>
                                <th>Issued By </th>
                            </thead>
                        </table>
                    </div>
                </div>
                <!----- stationery start 1.2.3--------->
                <!----- stationery start 1.2.4--------->
                
                <div class="tab-pane " id="monthlyreport" role="tabpanel">
                    <div class="row mt-3" id="monthlyreportreport"> 
                        <table class="table table-framed">
                            <thead>
                                <th>Category</th>
                                <th>Item</th>
                                <th>Quantity </th>
                                <th>Date of Issue</th>
                                <th>Issued By </th>
                            </thead>
                        </table>
                    </div>
                </div>
                <!----- stationery start 1.2.4--------->
                <!----- stationery start 1.3 --------->
                <div class="tab-pane " id="stapprovalorder" role="tabpanel">
                    <div class="row mt-3" id="stationeryorders"   >
                        <div class="card-header-right text-right">
                            <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;" role="tablist">
                                <!-- <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#sactive" role="tab">Active</a>

                                </li> -->
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#spending" role="tab">Pending</a>

                                </li>
                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link" data-bs-toggle="tab" href="#sapproved" role="tab">Approved</a>

                                </li>

                                <li class="nav-item p-l-10 p-r-10">
                                    <a class="nav-link" data-bs-toggle="tab" href="#stsearch" role="tab">Search</a>
                                </li>

                            </ul>
                        </div>
                        <div class="tab-content card-block table-border-style">
                           <?php /* ?>
                             <div class="tab-pane active" id="sactive" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['all_stationery_requests'] as $down){
                                                if($down['current_status']== ST_ACTIVE){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')">
                                                            <i class="ti-pencil-alt m-0"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div> <?php */ ?>
                            <div class="tab-pane active" id="spending" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['all_stationery_requests'] as $down){
                                                if($down['current_status']> ST_ACTIVE && $down['current_status']< ST_APPROVED){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')">
                                                            <i class="ti-pencil-alt m-0"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane " id="sapproved" role="tabpanel">
                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                                    <table class="table table-framed">
                                        <tbody>
                                            <?php foreach((array)$data['all_stationery_requests'] as $down){
                                                if($down['current_status'] ==ST_APPROVED){ ?>
                                                    <tr>
                                                        <td width="10%" scope="row">
                                                         <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                             <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                         <?php }else{ ?>
                                                             <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                         <?php } ?>

                                                     </td>
                                                     <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']." ".$down['current_status']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['request_no']; ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['request_date']); ?></span>
                                                    </td>
                                                    <td>
                                                        <a class="d-block text-dark f-w-600 f-14" href=""><?php echo displayDate($down['require_date']); ?></a>
                                                        <span class="text-muted f-14 f-w-600"><?php echo  $down['request_comment']; ?></span>
                                                    </td>
                                                    <td>
                                                        <label class="form-label label label-warning d-block p-2 text-center f-14 f-w-600" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></label>
                                                        <span class="text-muted f-14 f-w-600"><?php echo $data['deliver_types'][$down['deliver_type']]; ?></span> 
                                                    </td>
                                                    <td>
                                                        <button class="btn waves-effect waves-light btn-primary" onclick="cartviewreqlist('<?php echo $down['request_no']; ?>')" >
                                                            <i class="ti-pencil-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php } } ?>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane search" id="stsearch" role="tabpanel">

                               <div class="">
                                <div class="col-sm-12">
                                    <div class="input-group input-group-button">

                                        <div class="bus-bg" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative; width: 100%;">
                                            <div class="banner-txt">
                                                <h2 class="f-w-700 f-20">Get your Search ,<br>Stationery  Order  Details</h2>
                                            </div> 
                                            <div class="booking-form">
                                                <h3 class="">Search</h3>
                                                <div class="row mb-1 mt-4">

                                                    <div class="col-lg-4 m-b-10">
                                                        <div class="input-group">
                                                            <span class="input-group-text addon-style">
                                                                <label class=""><i class="ti-write"></i></label>
                                                            </span>
                                                            <input type="text" name="stationery_request_ref_no" id="stationery_request_ref_no" class="form-control border" placeholder="Stationery Request Number">
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 m-b-10">
                                                        <div class="input-group">
                                                            <span class="input-group-text addon-style">
                                                                <label class=""><i class="ti-time"></i></label>
                                                            </span>
                                                            <input class="form-control" type="date" name="request_date" id='request_date'>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 m-b-10">
                                                        <div class="input-group">                                 
                                                            <select class="form-control border js-example-basic-single" name="stationery_user_id" id="stationery_user_id">
                                                                <option value="">Select Employee</option>
                                                                <?php foreach($data['emps'] as $employ){ ?>
                                                                    <option value="<?php echo $employ['id']; ?>"><?php echo $employ['display_name']; ?></option>
                                                                <?php } ?> 
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 m-b-10">
                                                        <div class="input-group">
                                                            <span class="input-group-text addon-style">
                                                                <label class=""><i class="ti-time"></i></label>
                                                            </span>
                                                            <input class="form-control" type="date" name="require_date" id='require_date'>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4 m-b-10">
                                                    </div>
                                                    <div class="col-lg-4 m-b-10">
                                                        <button class="btn btn-primary w-100 p-2" onclick="searchstationery()" fdprocessedid="s99pj">Find Orders</button>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div>


                                    </div>
                                </div>
                            </div>


                            <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;" id="stationerysearchdata">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!----- stationery end 1.3 --------->
            <!----- stationery start 1.4 --------->
            <div class="tab-pane " id="sthistory" role="tabpanel">
                <div class="col-lg-12 row">
                    <?php foreach((array)$data['stationery_items'] as $stationery_item){ ?>
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-body row">
                                    <div class="col-lg-8">
                                        <h4 class="f-w-700 "><?php echo $stationery_item['item_name']; ?></h4>
                                    </div>
                                    <div class="col-lg-3 f-w-700">
                                        <?php echo $stationery_item['available_qty']; ?>
                                    </div>
                                    <div class="col-lg-1"><div class="cursor-pointer" onclick="viewstreport(<?php echo $stationery_item['id']; ?>)"><i class="ti-eye"></i></div></div>
                                    <div class="col-lg-12">
                                        <span class="f-14 f-w-600">Descroption: </span>
                                        <span class="text-muted"><?php echo $stationery_item['description']; ?></span>
                                    </div>
                                </div>
                                <input type="hidden" name="" id="streportledage_<?php echo $stationery_item['id']; ?>" value="1"> 
                                <div class="card-body" id="dis_streportledage_<?php echo $stationery_item['id']; ?>" style="display: none;" >
                                    <div class="row col-lg-12">

                                        <div class="col-lg-1 f-w-600"> #</div>
                                        <div class="col-lg-3 f-w-600"> Type</div>
                                        <div class="col-lg-3 f-w-600">Date</div>
                                        <div class="col-lg-2 f-w-600">In </div>
                                        <div class="col-lg-2 f-w-600" style="padding: 0px;">Out </div>
                                        <div class="col-lg-1 f-w-600" style="padding: 0px;">Run </div>
                                        <?php $k =1;
                                        foreach((array)$data['stationery_item_ledger'][$stationery_item['id']] as $item_ledge){ 
                                            ?> 
                                            <div class="col-lg-1"><?php echo $k; ?></div>

                                            <div class="col-lg-3">
                                               <span class="text-muted"><?php echo $item_ledge['request_no']; ?> </span>
                                           </div>
                                           <div class="col-lg-3">
                                            <span class="text-muted"><?php echo displayDate($item_ledge['created_date']); ?> </span>
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <?php echo $item_ledge['in_qty']; ?> 
                                        </div>
                                        <div class="col-lg-2 text-right">
                                            <?php echo $item_ledge['out_qty']; ?> 
                                        </div>
                                        <div class="col-lg-1 text-right">
                                            <?php echo $item_ledge['run_qty']; ?> 
                                        </div>
                                        <?php $k++; } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!----- stationery end 1.4 --------->
            <!----- stationery start 1.5 --------->
            <div class="tab-pane " id="streport" role="tabpanel">
                <div class="bus-bg" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative; width: 100%;">
                    <div class="banner-txt">
                        <h2 class="f-w-700 f-20">Get your Search ,<br>Stationery  Item  Details</h2>
                    </div> 
                    <div class="booking-form">
                        <h3 class="">Search</h3>
                        <div class="row mb-1 mt-4">                                    
                           <div class="col-lg-6 m-b-10">
                            <div class="input-group">                                 
                                <select class="form-control border js-example-basic-single" name="report_stationery_item_id" id="report_stationery_item_id">
                                    <option value="">Select Item</option>
                                    <?php foreach($data['stationery_items'] as $stationery_item){ ?>
                                        <option value="<?php echo $stationery_item['id']; ?>"><?php echo $stationery_item['item_name']; ?></option>
                                    <?php } ?> 
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 m-b-10">
                            <div class="input-group">
                                <span class="input-group-text addon-style">
                                    <label class=""><i class="ti-time"></i></label>
                                </span>
                                <input class="form-control" type="date" name="list_date" id='list_date'>
                            </div>
                        </div>
                        <div class="col-lg-6 m-b-10">
                            &nbsp;
                        </div>
                        <div class="col-lg-6 m-b-10">
                            <button class="btn btn-primary w-100 p-2" onclick="searchstationeryreport()" fdprocessedid="s99pj">Find Orders</button>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="mt-4 " id="searchreport">

            </div>
        </div>
        <!----- stationery end 1.5 --------->

    </div>
</div>
</div>
<div class="col-lg-4"  id="side_view_2" >
<div class="card mt-4">
    <div class="row align-items-center">

        <div class="card-header">
            <h5>Stationery</h5>
                                    <!-- 
                                    <div class="card-header-right">
                                         <button type="button" class="btn waves-effect waves-light btn-primary float-right  m-l-5"  >
                                            <span class="ti-shopping-cart m-r-5"></span>&nbsp; (<span id="cartval"><?php echo $data['cart_count']; ?></span>)
                                        </button>
                                        
                                    </div> -->
                                </div>
                                <div class="card-body">
                                    <div class="card-body" id="sidestationary">
                                        <?php foreach($sitem as $key=>$siditem){  
                                            if($siditem!=0){
                                                ?>
                                                <div class="row align-items-center srow" id="shkrow_<?php echo $key; ?>"  >
                                                    <div class="col-lg-8 p-2">
                                                        <h4 class="f-14 mb-0 f-w-600 m-l-10"><?php echo $stisitem[$key]['item_name']; ?></h4>
                                                        <span class="f-12 m-l-10"><?php echo $stisitem[$key]['description']; ?></span>
                                                    </div>
                                                    <div class="col-lg-4 p-2">
                                                        <div class="text-right m-b-10 m-r-5 quantity-sec">
                                                            <div class="input-group">
                                                              <b class="input-group-btn position-static">
                                                                  <button type="button" class="quantity-left-minus btn btn-danger btn-number p-2" onclick="addmins('mins',<?php echo $key; ?>)">
                                                                    <i class="ti-minus"></i>
                                                                </button>
                                                            </b>
                                                            <input type="text"  name='quantity' id="sidqty_<?php echo $key; ?>" value='<?php echo $siditem; ?>'  onblur="addqty(<?php echo $key; ?>)"  class="form-control input-number text-center" min="1" max="100">
                                                            <b class="input-group-btn position-static">
                                                              <button type="button" class="quantity-right-plus btn btn-success btn-number p-2" onclick="addmins('add',<?php echo $key; ?>)" >
                                                                  <i class="ti-plus"></i>
                                                              </button>
                                                          </b>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>   
                                      <?php } 
                                  } ?>                     
                              </div>
                              <div class=" card-body row" id="checkoutcal"  <?php if(count((array)$sitem)==0){ ?> style="display:none;" <?php } ?> >
                                <?php if($st_show_type=='1'){ ?>
                                <div class="form-group col-lg-6 w-full">
                                    <label class="d-block">Building </label>
                                    <select  name="building_id" id="building_id" onchange="todisplayfloor(this.value)"  class="form-select js-example-basic-single" >
                                        <option value="">Select Any One</option>
                                        <?php foreach($data['buildings'] as $build){ ?> 
                                            <option value="<?php echo $build['id']; ?>"><?php echo $build['building_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 w-full">
                                    <label class="d-block">Building Floor </label>
                                    <select  name="building_floor_id" id="building_floor_id" onchange="todisplayspace(this.value)" class="form-select js-example-basic-single" >
                                        <option value="">Select Any One</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 w-full">
                                    <label class="d-block">Space </label>
                                    <select  name="building_floor_space_id" id="building_floor_space_id" <?php if( $row['deliver_type']!=""){ echo "disabled"; } ?> class="form-select js-example-basic-single" >
                                        <option value="">Select Any One</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-6 w-full">
                                    <label>Delivey Type </label>
                                    <select  name="deliver_type" id="deliver_type" <?php if( $row['deliver_type']!=""){ echo "disabled"; } ?> class="form-select js-example-basic-single" >
                                        <option value="">Select Any One</option>
                                        <?php foreach($data['deliver_types'] as $keydeliver=>$deliver_type){ ?> 
                                            <option value="<?php echo $keydeliver; ?>"><?php echo $deliver_type; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group  col-lg-6">
                                    <label>Request Date </label>
                                    <input type="text" name="request_date" id="request_date_st" class="form-control datepicker" value="<?php echo fromDate();?>">
                                </div>
                                <div class="form-group col-lg-6">
                                    <label>Required Date </label>
                                    <input type="text" name="require_date" id="require_date_st" class="form-control datepicker" value="<?php echo fromDate();?>">
                                </div>

                                 <?php } ?>
                                 <?php if($st_show_type=='2'){ ?>
                                    <div class="form-group col-lg-6 w-full">
                                        <label>Employee </label>
                                        <select  name="emp_id" id="emp_id" onchange="tocheckempdd(this.value)" class="form-select js-example-basic-single" >
                                            <option value="">Select Any One</option>
                                            <?php foreach($data['emps'] as $emp_val){ ?> 
                                                <option value="<?php echo $emp_val['id']; ?>"><?php echo $emp_val['display_name']; ?></option>
                                            <?php } ?>
                                            <option value="999999">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-lg-6" style="display:none;" id="othername">
                                        <label>Other Name</label>
                                        <input type="text" name="other_name" id="other_name" class="form-control" value="">
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <label>Date of Issue</label>
                                        <input type="text" name="request_date" id="require_date_st" class="form-control datepicker" value="<?php echo fromDate();?>">
                                    </div>

                                <?php } ?>
                                
                                <div class="form-group col-lg-6">
                                    <label>Comments </label>
                                    <textarea name="request_comment" class="form-control" id="request_comment"></textarea>
                                </div>

                                <div class="form-group col-lg-12 text-right"> 
                                    <input type="hidden" name="st_location_id" id="st_location_id" value="<?php echo $_SESSION['location_id']; ?>" >
                                    <input type="hidden" name="st_current_status" id="st_current_status" value="<?php if($st_show_type==2){ echo "199"; }else{ echo "10"; } ?>">
                                    
                                    <button class="btn btn-primary" name="checkout" onclick="submitinstationaryfrom()">Check Out </button>
                                </div>
                            </div>
                        </div> 
                    </div> 
                </div>
            </div>
        </div>

<script>
    
    function todisplayfloor(build_id){
        console.log(" build_id"+build_id);        
        jQuery.ajax({
            url: "ajax.php",
            data:'page=todisplayfloorbasedbuildid&build_id='+build_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);                        
                $("#building_floor_id").html(data);                
            },
            error:function (){}
        });
    }
    function todisplayspace(floor_id){
        console.log("floor_id="+floor_id);        
        jQuery.ajax({
            url: "ajax.php",
            data:'page=todisplayspacebasedfloorid&floor_id='+floor_id,
            type: "POST",
            success:function(data){
                console.log("All Count"+data);                        
                $("#building_floor_space_id").html(data);                
            },
            error:function (){}
        });
    }
</script>
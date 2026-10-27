<div class="row">
    <div class="col-lg-12">
        <div class="card p-3">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                    <div class="card social-card">
                        <div class="card-body courier_popup">
                            <div class="text-facebook m-b-10"><i class="ti-back-left"></i></div>
                            <span class="text-facebook f-w-700 f-20">
                                <?php echo $data['all']['receive']!="" ? $data['all']['receive']:0; ?>
                            </span>
                            <span class="f-18 float-right">In Courier</span>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i><?php echo $data['seven']['receive']!="" ? $data['seven']['receive']:0; ?> from last 7 days</p>
                            <?php if($type==SECURITYGUARD_ROLE){ ?>
                                <button type="button" class="btn btn-primary waves-effect c_btn" onclick="recicedcourier()">
                                    <span class="ti-plus"></span>
                                </button>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                    <div class="card social-card">
                        <div class="card-body courier_popup">
                            <div class="text-facebook m-b-10"><i class="ti-back-right"></i></div>
                            <span class="text-facebook f-w-700 f-20"><?php echo $data['all']['send']!="" ? $data['all']['send']:0; ?></span>
                            <span class="f-18 float-right">Out Courier</span>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i><?php echo $data['seven']['send']!="" ? $data['seven']['send']:0; ?> from last 7 days</p>
                            <button type="button" class="btn btn-primary waves-effect c_btn" onclick="sendcourier()">
                                <span class="ti-plus"></span>
                            </button>
                        </div>

                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-3 col-12">
                    <div class="card social-card">
                        <div class="card-body">
                            <div class="text-facebook m-b-10"><i class="ti-timer"></i></div>
                            <span class="text-facebook f-w-700 f-20"><?php echo $ar+$as; ?></span>
                            <span class="f-18 float-right">Total</span>
                            <p class="m-b-0 m-t-15"><i class="fas fa-caret-up m-r-10 f-18 text-c-green"></i><?php echo $sr+$ss; ?> from last 7 days</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-0">
                <div class="card-header">
                    <h5> Courier Tracker</h5>
                    <div class="card-header-right">
                        <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link active" data-bs-toggle="tab" href="#month" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">In Courier</a>

                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#week" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Out Courier</a>

                            </li>
                            <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#all" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Archived Couriers</a>
                            </li>
                            <?php if($type == SECURITYGUARD_ROLE){ ?>
                             <li class="nav-item p-l-10 p-r-10">
                                <a class="nav-link" data-bs-toggle="tab" href="#search" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Search</a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="tab-content card-block table-border-style">
                <div class="tab-pane active" id="month" role="tabpanel">
                    <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                        <?php
                        $tb_in_courier = array('SL.NO','Date','From','To ','Courier Company','Invoice No' ,'In Time','Description','Qty','Tracking','Emp Receiver Name','Receiver Name');
                        ?>
                        <table class="table table-framed">
                            <thead>
                                <?php foreach($tb_in_courier as $th){ ?>
                                    <th scope="col"><?php echo $th; ?></th>
                                <?php } ?>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                <?php $sl=0; foreach((array)$data['down'] as $down){
                                    if($down['courier_type']==2 && $down['current_status'] !=199){
                                        $sl++;
                                        ?>
                                        <tr>
                                            <td><?php echo $sl; ?></td>
                                            <td><?php echo fromDate($down['courier_date']); ?></td>
                                            <td><?php echo $down['from_detail']; ?></td>
                                            <td><?php echo $down['to_detail']; ?></td>
                                            <td><?php echo $down['courier_company']; ?></td>
                                            <td><?php echo $down['courier_ref_no']; ?></td>
                                            <td><?php echo $down['courier_time']; ?></td>
                                            <td><?php echo $down['description']; ?></td>
                                            <td><?php echo $down['courier_qty']; ?></td>
                                            <td>
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$down['current_status']]; ?></span>
                                            </td>
                                            <td><?php echo $data['emps'][$down['requested_by']]['emp_code'].' - '.$data['emps'][$down['requested_by']]['display_name']; ?></td>
                                            <td><?php echo $_SESSION['users'][$down['created_by']]; ?></td>

                                            <td>
                                                <button class="btn waves-effect waves-light btn-primary" onclick="recicedcourier(<?php echo $down['id']; ?>)" >
                                                    <i class="ti-pencil-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane" id="week" role="tabpanel">
                       <div class="table-responsive infisuite-hover">
                        <table class="table table-framed">
                            <thead>
                                <?php foreach($tb_in_courier as $th){ ?>
                                    <th scope="col"><?php echo $th; ?></th>
                                <?php } ?>
                                <th scope="col"></th>
                            </thead>
                            <tbody>
                                <?php $sl=0; foreach((array)$data['down'] as $down){
                                    if($down['courier_type']==1 && $down['current_status'] !=199){
                                        $sl++;
                                        ?>
                                        <tr>
                                            <td><?php echo $sl; ?></td>
                                            <td><?php echo fromDate($down['courier_date']); ?></td>
                                            <td><?php echo $down['from_detail']; ?></td>
                                            <td><?php echo $down['to_detail']; ?></td>
                                            <td><?php echo $down['courier_company']; ?></td>
                                            <td><?php echo $down['courier_ref_no']; ?></td>
                                            <td><?php echo $down['courier_time']; ?></td>
                                            <td><?php echo $down['description']; ?></td>
                                            <td><?php echo $down['courier_qty']; ?></td>
                                            <td>
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$down['current_status']]; ?></span>
                                            </td>
                                            <td><?php echo $data['emps'][$down['requested_by']]['emp_code'].' - '.$data['emps'][$down['requested_by']]['display_name']; ?></td>
                                            <td><?php echo $_SESSION['users'][$down['created_by']]; ?></td>

                                            <td>
                                                <button class="btn waves-effect waves-light btn-primary" onclick="recicedcourier(<?php echo $down['id']; ?>)" >
                                                    <i class="ti-pencil-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane " id="all" role="tabpanel">
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <thead>
                                    <?php foreach($tb_in_courier as $th){ ?>
                                        <th scope="col"><?php echo $th; ?></th>
                                    <?php } ?>
                                    <th scope="col"></th>
                                </thead>
                                <tbody>
                                    <?php $sl=0; foreach((array)$data['down'] as $down){
                                        if($down['current_status'] ==ST_APPROVED){ $sl++; ?>
                                            <tr>
                                            <td><?php echo $sl; ?></td>
                                            <td><?php echo fromDate($down['courier_date']); ?></td>
                                            <td><?php echo $down['from_detail']; ?></td>
                                            <td><?php echo $down['to_detail']; ?></td>
                                            <td><?php echo $down['courier_company']; ?></td>
                                            <td><?php echo $down['courier_ref_no']; ?></td>
                                            <td><?php echo $down['courier_time']; ?></td>
                                            <td><?php echo $down['description']; ?></td>
                                            <td><?php echo $down['courier_qty']; ?></td>
                                            <td>
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$down['current_status']]; ?></span>
                                            </td>
                                            <td><?php echo $data['emps'][$down['requested_by']]['emp_code'].' - '.$data['emps'][$down['requested_by']]['display_name']; ?></td>
                                            <td><?php echo $_SESSION['users'][$down['created_by']]; ?></td>

                                            <td>
                                                <button class="btn waves-effect waves-light btn-primary" onclick="recicedcourier(<?php echo $down['id']; ?>)" >
                                                    <i class="ti-pencil-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                        <?php } ?>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                                    <div class="tab-pane " id="search" role="tabpanel">
                                        <div class="">
                                            <div class="col-sm-12">
                                                <div class="input-group input-group-button">

                                                    <div class="bus-bg" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative; width: 100%;">
                                                        <div class="banner-txt">
                                                            <h2 class="f-w-700 f-20">Get your Search ,<br>Coureier &amp; Details</h2>
                                                        </div>
<div class="booking-form">
    <h3 class="">Search</h3>
    <div class="row mb-1 mt-4">
        <div class="col-lg-4 m-b-10">
            <div class="toggle-button-cover mt-2">
                <div class="button-cover">
                    <div class="button b2" id="button-17">
                        <input aria-label="Courier Type" type="checkbox" name="courier_type" class="checkbox" id="courier_type" onchange="" value="2" onclick="couriertype()" onkeydown="">
                        <div class="knobs"></div>
                        <div class="layer"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 m-b-10">
            <input aria-label="From" type="text" name="from_detail" id="from_detail" class="form-control border" placeholder="From">
        </div>
        <div class="col-lg-4 m-b-10">
            <input aria-label="To" type="text" name="to_detail" id="to_detail" class="form-control border" placeholder="To">
        </div>
        <div class="col-lg-4 m-b-10">
            <div class="input-group">
                <span class="input-group-text addon-style">
                    <span class=""><i class="ti-write"></i></span>
                </span>
                <input aria-label="Courier Number" type="text" name="courier_ref_no" id="courier_ref_no" class="form-control border" placeholder="Courier Number">
            </div>
        </div>


                <div class="col-lg-4 m-b-10">
                    <div class="input-group">
                        <span class="input-group-text addon-style">
                            <span class=""><i class="ti-time"></i></span>
                        </span>
                        <input aria-label="Courier Date" class="form-control" type="date" name="courier_date" id='courier_date'>
                    </div>
                </div>
                <div class="col-lg-4 m-b-10">
                    <div class="input-group">
                        <span class="input-group-text addon-style">
                            <span class=""><i class="ti-time"></i></span>
                        </span>
                        <input aria-label="Courier To Date" class="form-control" type="date" name="courier_to_date" id='courier_to_date'>
                    </div>
                </div>
                <div class="col-lg-4 m-b-10">
                    <button class="btn btn-primary w-100 p-2" onclick="searchcourier()" fdprocessedid="s99pj">Find Courier</button>
                </div>
            </div>
        </div>  -->
    </div>


</div>
                                                    </div>
                                                </div>
                                                <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;" id="searchdata">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row col-lg-12"  id="side_view_1"   >
                                <div class="col-lg-4" >
                                    <div class="" style="background : url(../assets/images/courier.jpg);width:100%;height:250px;background-repeat:no-repeat;background-size: 500px auto;border-radius: 15px;">
                                        <h3 class="f-w-600 pt-5 f-25 m-l-50 m-r-50 text-white">Ready to experience a seamless and stress-free courier process? Simply hit the "Book Now" button</h3>
                                        <button class="btn btn-warning waves-effect waves-light m-l-50 mt-4" onclick="sendcourier()">Book Now </button>
                                    </div>
                                </div>
                                <div class="col-lg-4" >
                                    <div class="card mt-4">
                                        <div class="card-header">
                                            <h5>In Courier</h5>

                                    </div>
                                    <div class="card-body" >
                                        <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#recent15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent15days);?></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent30days);?></span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#recent45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 60 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$recent45days);?></span></a>
                                            </li>
                                        </ul>
                                        <div class="tab-content tabs">
                                            <div class="tab-pane card-body active p-0 custom-scroll" id="recent15days" role="tabpanel"  style=" height: 250px;">
                                                <?php if(count((array)$recent15days) !=0 ){ ?>
                                                    <div class="row col-lg-12 p-2">
                                                        <?php foreach($recent15days as $sidein){?>
                                                           <div class="col-lg-2 p-2">
                                                             <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                                               <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                                           <?php }else{ ?>
                                                               <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                                           <?php } ?>
                                                       </div>
                                                       <div class="col-lg-6 p-2">
                                                        <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_company']; ?></h4>
                                                        <span class="f-12"> <?php echo $sidein['courier_ref_no']; ?> </span>
                                                    </div>
                                                    <div class="col-lg-4 p-2">
                                                        <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php }else{ ?>
                                            <div class="card-px text-center py-20 m-t-30">
                                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                                                <div class="text-center px-4">
                                                    <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane card-body p-0 custom-scroll" id="recent30days" role="tabpanel" style=" height: 250px;">
                                        <?php if(count((array)$recent30days) !=0 ){ ?>

                                            <?php foreach($recent30days as $sidein){?>
                                                <div class="col-lg-2 p-2">
                                                 <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                                   <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                               <?php }else{ ?>
                                                   <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                               <?php } ?>
                                           </div>
                                           <div class="col-lg-6 p-2">
                                            <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_company']; ?></h4>
                                            <span class="f-12"> <?php echo $sidein['courier_ref_no']; ?> </span>
                                        </div>
                                        <div class="col-lg-4 p-2">
                                            <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                                        </div>
                                    <?php } ?>

                                <?php }else{ ?>
                                    <div class="card-px text-center py-20 m-t-30">
                                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                                        <div class="text-center px-4">
                                            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="tab-pane card-body p-0 custom-scroll" id="recent45days" role="tabpanel" style=" height: 250px;">
                             <?php if(count((array)$recent45days) !=0 ){ ?>

                                <?php foreach($recent45days as $sidein){?>
                                    <div class="col-lg-2 p-2">
                                     <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                                   <?php }else{ ?>
                                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                                   <?php } ?>
                               </div>
                               <div class="col-lg-6 p-2">
                                <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_company']; ?></h4>
                                <span class="f-12"> <?php echo $sidein['courier_ref_no']; ?> </span>
                            </div>
                            <div class="col-lg-4 p-2">
                                <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                            </div>
                        <?php } ?>

                    <?php }else{ ?>
                        <div class="card-px text-center py-20 m-t-30">
                            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                            <div class="text-center px-4">
                                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4" >
    <div class="card mt-4">
        <div class="card-header">
            <h5>Out Courier</h5>

        </div>
        <div class="card-body" >
            <ul class="nav md-tabs infisuite_tab mt-2 ml-2 mb-3">
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12 active" data-bs-toggle="tab" href="#expire15days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">  15 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire15days);?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expire30days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 30 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire30days);?></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link f-w-600 f-12" data-bs-toggle="tab" href="#expire45days" role="tab" aria-selected="true" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"> 60 Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$expire45days);?></span></a>
                </li>
            </ul>
            <div class="tab-content tabs">
                <div class="tab-pane card-body active p-0 custom-scroll" id="expire15days" role="tabpanel"  style=" height: 250px;">
                    <?php if(count((array)$expire15days) !=0 ){ ?>
                        <div class="row col-lg-12 p-2">
                            <?php foreach($expire15days as $sidein){?>
                               <div class="col-lg-2 p-2">
                                 <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                                   <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                               <?php }else{ ?>
                                   <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                               <?php } ?>
                           </div>
                           <div class="col-lg-6 p-2">
                            <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_name']; ?></h4>
                            <span class="f-12"> <?php echo $sidein['courier_adderss']; ?> </span>
                        </div>
                        <div class="col-lg-4 p-2">
                            <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
                        </div>
                    <?php } ?>
                </div>
            <?php }else{ ?>
                <div class="card-px text-center py-20 m-t-30">
                    <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
                    <div class="text-center px-4">
                        <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="tab-pane card-body p-0 custom-scroll" id="expire30days" role="tabpanel" style=" height: 250px;">
            <?php if(count((array)$expire30days) !=0 ){ ?>

                <?php foreach($expire30days as $sidein){?>
                    <div class="col-lg-2 p-2">
                     <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                       <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                   <?php }else{ ?>
                       <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                   <?php } ?>
               </div>
               <div class="col-lg-6 p-2">
                <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_name']; ?></h4>
                <span class="f-12"> <?php echo $sidein['courier_adderss']; ?> </span>
            </div>
            <div class="col-lg-4 p-2">
                <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
            </div>
        <?php } ?>

    <?php }else{ ?>
        <div class="card-px text-center py-20 m-t-30">
            <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
            <div class="text-center px-4">
                <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
            </div>
        </div>
    <?php } ?>
</div>
<div class="tab-pane card-body p-0 custom-scroll" id="expire45days" role="tabpanel" style=" height: 250px;">
 <?php if(count((array)$expire45days) !=0 ){ ?>

    <?php foreach($expire45days as $sidein){?>
        <div class="col-lg-2 p-2">
         <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
           <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
       <?php }else{ ?>
           <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
       <?php } ?>
   </div>
   <div class="col-lg-6 p-2">
    <h4 class="f-14 m-0 f-w-600"><?php echo $sidein['courier_name']; ?></h4>
    <span class="f-12"> <?php echo $sidein['courier_adderss']; ?> </span>
</div>
<div class="col-lg-4 p-2">
    <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['couriers'][$sidein['current_status']]; ?></span>
</div>
<?php } ?>

<?php }else{ ?>
    <div class="card-px text-center py-20 m-t-30">
        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Couriers</p>
        <div class="text-center px-4">
            <img class="mw-100 mh-250px" alt="" src="assets/images/work.png" style="max-height: 250px!important;">
        </div>
    </div>
<?php } ?>
</div>
</div>
</div>
</div>
</div>

</div>
</div>

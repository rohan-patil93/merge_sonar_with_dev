
<div class="row">
    <div class="col-lg-8">
        <div class="card p-3">
            <div class="row">
                <div class="col-lg-12">
                   <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                    <li class="nav-item p-l-10 p-r-10">
                        <a class="nav-link active" data-bs-toggle="tab" href="#tssemyrequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Search Requests</a>
                    </li>
                    <li class="nav-item p-l-10 p-r-10">
                        <a class="nav-link" data-bs-toggle="tab" href="#tsmyrequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">My Requests</a>
                    </li>
                    <?php if($_SESSION['admin_type']==1){ ?>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#tsaprequest" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">To Be Approved</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
        <div class="tab-content card-block table-border-style">
            <div class="tab-pane active" id="tssemyrequest" role="tabpanel">
                <div class="bus-bg m-b-10" style="background: url(../assets/images/bus.jpg); height:250px; background-size: 100%; background-repeat: no-repeat; position: relative;">
                    <div class="banner-txt">
                        <h2 class="f-w-700 f-35">Get your bus online,<br>Easy &amp; Safely</h2>
                    </div>
                    <div class="booking-form">
                        <h3 class="">Choose Your Bus</h3>
                        <div class="row mb-1 mt-4">
                            <div class="col-lg-6 m-b-10">
                                <div class="input-group">
                                    <span class="input-group-text addon-style">
                                        <span class=""><i class="ti-location-arrow"></i></span>
                                    </span>
                                    <select aria-label="Pickup" class="form-control fill border" fdprocessedid="n6szf" id="pickup" name="pickup">
                                        <option value="">Pickup Point</option>
                                        <?php foreach($data['listschedlue'] as $listschedlue){ ?>
                                            <option value="<?php echo $listschedlue; ?>"><?php echo $listschedlue; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 m-b-10">
                                <div class="input-group">
                                    <span class="input-group-text addon-style">
                                        <span class=""><i class="ti-location-pin"></i></span>
                                    </span>
                                    <select aria-label="Droping" class="form-control border" fdprocessedid="ft4ukt" name="droping" id="droping">
                                        <option value="">Dropping Point</option>
                                        <?php foreach($data['listschedlue'] as $listschedlue){ ?>
                                            <option value="<?php echo $listschedlue; ?>"><?php echo $listschedlue; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 m-b-10">
                                <div class="input-group">
                                    <span class="input-group-text addon-style">
                                        <span class=""><i class="ti-time"></i></span>
                                    </span>
                                    <input aria-label="Pickdate" class="form-control" type="date" name="pickdate" id="pickdate">
                                </div>
                            </div>
                            <div class="col-lg-6 m-b-10">
                                <button class="btn btn-primary w-100 p-2" onclick="searchtransportbus()" fdprocessedid="s99pj">Find Bus</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="viewbus">
                    <?php foreach((array)$data['routes'] as $route){
                        $listroute[$route['id']] = $route;
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <div class="row mt-3">
                                    <div class="col-lg-3">
                                        <h4 class="f-w-700 "><?php echo $bus[$route['company_transport_id']]['vehicle_name']; ?></h4>
                                        <p>Seat Layout <?php echo $bus[$route['company_transport_id']]['seat_layout']; ?></p>
                                        <span class="text-danger"><?php echo $bus[$route['company_transport_id']]['vehicle_type']; ?></span>
                                    </div>
                                    <div class="col-lg-5">
                                        <div class="row">
                                            <div class="col-lg-4">
                                                <h4 class="f-w-700 f-20"><?php echo date("h:i A", strtotime($route['arrival_time'])); ?></h4>
                                                <p class="f-16 f-w-500"><?php echo $route['route_starts']; ?></p>
                                            </div>
                                            <div class="col-lg-3 text-center">
                                                <i class="ti-arrow-right text-primary f-w-700"></i>

                                            </div>
                                            <div class="col-lg-5">
                                                <h4 class="f-w-700 f-20"><?php echo date("h:i A", strtotime($route['departure_time'])); ?></h4>
                                                <p class="f-16 f-w-500"><?php echo $route['route_ends']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-right">
                                        <h4 class="f-w-700 f-22 text-success">&#8377 <?php echo $route['route_charges_monthly']; ?> </h4>
                                        <p>Off Days: <span class="form-label label label-success"><?php echo implode(" , ",json_decode($route['off_days'],1)); ?></span></p>
                                        <button class="btn btn-primary" onclick="viewlocation(<?php echo $route['id']; ?>)">Select Location</button>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="" id="view_location_<?php echo $route['id']; ?>" value="1">
                            <div class="card-body" style="display: none;" id="dis_location_<?php echo $route['id']; ?>" >
                                <div class="row col-lg-12">
                                    <?php foreach((array)$data['transport_schedule'][$route['id']] as $transport_schedule){ ?>
                                        <div class="col-lg-4 p-2"> <?php echo $transport_schedule['stop_name']; ?> </div>
                                        <div class="col-lg-2">
                                            <button onclick="viewrequest(<?php echo $transport_schedule['id']; ?>)" class="btn btn-info" style="font-size: 12px; padding: 5px;" >
                                            Request </button>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="tab-pane " id="tsmyrequest" role="tabpanel">
                <div class="card-header-right text-right">
                    <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                        </li>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#stmysapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content card-block table-border-style">
                    <div class="tab-pane active" id="stmyspending" role="tabpanel">
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <tbody>
                                    <?php foreach((array)$data['user_transport_schedule'] as $down){
                                        if($down['current_status'] !=ST_APPROVED){ ?>
                                            <tr>
                                                <th scope="row" style="width: 10%;">
                                                 <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                 <?php }else{ ?>
                                                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                 <?php } ?>

                                             </th>
                                             <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                            </th>
                                            <th scope="col">
                                                <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                    <i class="ti-pencil-alt m-0"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane " id="stmysapproved" role="tabpanel">
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <tbody>
                                    <?php foreach((array)$data['user_transport_schedule'] as $down){
                                        if($down['current_status'] ==ST_APPROVED){ ?>
                                            <tr>
                                                <th scope="row" style="width: 10%;">
                                                 <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                 <?php }else{ ?>
                                                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                 <?php } ?>

                                             </th>
                                             <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                            </th>
                                            <th scope="col">
                                                <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                    <i class="ti-pencil-alt m-0"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane " id="tsaprequest" role="tabpanel">
                <div class="card-header-right text-right">
                    <ul class="nav nav-tabs md-tabs infisuite_tab m-r-20 font-weight-bold" style="border:0px;">
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link active" data-bs-toggle="tab" href="#stapmyspending" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Pending</a>
                        </li>
                        <li class="nav-item p-l-10 p-r-10">
                            <a class="nav-link" data-bs-toggle="tab" href="#stapmysapproved" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Approved</a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content card-block table-border-style">
                    <div class="tab-pane active" id="stapmyspending" role="tabpanel">
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <tbody>
                                    <?php foreach((array)$data['all_transport_schedule'] as $down){
                                        if($down['current_status'] !=ST_APPROVED){ ?>
                                            <tr>
                                                <th scope="row" style="width: 10%;">
                                                 <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                 <?php }else{ ?>
                                                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                 <?php } ?>

                                             </th>
                                             <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                            </th>
                                            <th scope="col">
                                                <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                    <i class="ti-pencil-alt m-0"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane " id="stapmysapproved" role="tabpanel">
                        <div class="table-responsive infisuite-hover custom-scroll" style="max-height: 600px;">
                            <table class="table table-framed">
                                <tbody>
                                    <?php foreach((array)$data['all_transport_schedule'] as $down){
                                        if($down['current_status'] ==ST_APPROVED){ ?>
                                            <tr>
                                                <th scope="row" style="width: 10%;">
                                                 <?php if($data['emps'][$down['user_id']]['image']!=""){ ?>
                                                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$down['user_id']]['image']; ?>" class="img-radius img-40" alt=""></a>
                                                 <?php }else{ ?>
                                                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$down['user_id']]['display_name']); ?></span></a>
                                                 <?php } ?>

                                             </th>
                                             <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['emps'][$down['user_id']]['display_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $data['emps'][$down['user_id']]['email_id']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $data['bus'][$down['company_transport_id']]['vehicle_name']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo displayDate($down['transport_date']); ?></span>
                                            </th>
                                            <th scope="col">
                                                <a class="d-block text-dark f-w-600 f-14" href=""><?php echo $down['pickup_point']; ?></a>
                                                <span class="text-muted f-14 f-w-600"><?php echo $listroute[$down['route_id']]['route_ends']; ?></span>
                                            </th>
                                            <th scope="col">
                                                <span class="form-label label label-warning" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['stationery_requests'][$down['current_status']]; ?></span></br>

                                            </th>
                                            <th scope="col">
                                                <button class="btn waves-effect waves-light btn-primary" onclick="viewtransportrequest('<?php echo $down['id']; ?>')" >
                                                    <i class="ti-pencil-alt m-0"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    <?php } ?>
                                    <?php } ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-4"  id="side_view_3"   >
    <div class="" style="background: url(../assets/images/transport.jpg);
    width:100%;height:250px;background-repeat:no-repeat;background-size: 500px auto;border-radius: 15px;">
    <h3 class="f-w-600 pt-5 f-25 text-white m-l-50 m-r-50">Ready to elevate your daily commute experience? Click the "Request Transport" button below</h3>
    <button class="btn btn-warning waves-effect waves-light m-l-50 mt-4">Request Transport</button>
</div>
<div class="card mt-4">
    <div class="card-header">
        <h5>Transports</h5>
    </div>
    <div class="card-body">
        <div class="row align-items-center">
            <?php
            foreach((array)$data['user_transport_schedule'] as $sidein){
                $stas="warning";
                if($sidein['current_status']=="199"){ $stas="success";
}
                ?>

                <div class="col-lg-2 p-2">
                   <?php if($data['emps'][$sidein['user_id']]['image']!=""){ ?>
                     <a href=""><img  src="/uploads/all/<?php echo $data['emps'][$sidein['user_id']]['image']; ?>" class="img-radius img-40" alt="" ></a>
                 <?php }else{ ?>
                     <a href=""><span class="img-radius img-50 align-top m-r-10 custom_txt_round custom_txt_round-2 p-2 f-1 text-center bg-white text-black  mt-1"><?php echo initials($data['emps'][$sidein['user_id']]['display_name']); ?></span></a>
                 <?php } ?>
             </div>
             <div class="col-lg-6 p-2">
                <h4 class="f-14 m-0 f-w-600"><?php echo $bus[$sidein['company_transport_id']]['vehicle_name']; ?></h4>
                <span class="f-12"> Pickup Point: <?php echo $sidein['pickup_point']." Transport Date".displayDate($sidein['transport_date']) ; ?> </span>
            </div>
            <div class="col-lg-4 p-2">
                <span class="form-label label label-<?php echo $stas; ?>" style="background-color:<?php echo $_SESSION['status'][$down['current_status']]['background_color']; ?>;color:<?php echo $_SESSION['status'][$down['current_status']]['color']; ?> "><?php echo $data['status']['employee_transport_requests'][$sidein['current_status']]; ?></span>
            </div>

        <?php } ?>
    </div>

</div>
</div>
</div>
</div>

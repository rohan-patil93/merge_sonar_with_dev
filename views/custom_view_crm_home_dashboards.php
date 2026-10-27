<div class="row mt-3">
    <div class="col-lg-8">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Lead Status</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                        <input type="hidden" name="chart_data_lead_chart" id="chart_data_lead_chart" value='<?php echo json_encode((array)$crm_leads_chart); ?>' >
                        <?php if(count((array)$crm_leads_chart) =='0'){ ?>
                          <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php }else{ ?>
                          <div id="chart_by_leads" style="width: 100%; height: 400px;"></div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recently Created Leads</h5>

                          <div class="card-header-right">
                            <button type="button" !" class="mr-5" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Add Lead" onclick="leadPageCall('LEAD')"> <i class="icofont icofont-plus icon-dark"></i> </button>

                            <button type="button" !" onclick="viewAllListPage('crm_leads')" class=" b-b-primary text-primary float-right">View all</button>

                        </div>

                    </div>
                    <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                        <div class="">
                            <?php  if(count((array)$crm_leads) !=0){ ?>
                            <table class="table table-hover">
                                <tr>
                                    <th scope="col">Lead</th>
                                    <th scope="col">Source</th>
                                    <th scope="col">Status</th>
                                </tr>
                               <?php $im=1; foreach($crm_leads as $crmleads){ if($im ==5){ break;
} ?>
                                <tr>
                                    <td class="p-2">
                                        <div class="align-middle">
                                            <?php if($crmleads['image']) { ?>
                                                <img src="uploads/all/<?php echo $crmleads['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($crmleads['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('LEAD','<?php echo $crmleads['id']; ?>')"><h6><?php echo $crmleads['display_name']; ?></h6></button>
                                                <p class="text-muted m-b-0"><?php echo $crmleads['phone']; ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td  class="p-2"><?php echo $crm_source_types['SOURCETYPE'][$crmleads['contact_source']]; ?></td>
                                    <td  class="p-2"><?php echo $project_task_status['crm_leads'][$crmleads['crm_status']]['name']; ?></td>
                                </tr>
                        <?php $im++;
} ?>
                            </table>
                            <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Leads</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Recently Created Contacts</h5>
                        <button type="button" !" onclick="viewAllListPage('crm_contacts')" class=" b-b-primary text-primary float-right">View all</button>
                    </div>
                    <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                        <div class="">
                            <?php  if(count((array)$crm_contacts) !=0){ ?>
                            <table class="table table-hover">
                                <tr>
                                    <th scope="col">Name</th>

                                    <th scope="col">Source</th>
                                    <th scope="col">Status</th>
                                </tr>
                               <?php $im=1; foreach($crm_contacts as $crmleads){ if($im ==5){ break;
} ?>
                                <tr>
                                    <td  class="p-2">
                                        <div class="align-middle">
                                            <?php if($crmleads['image']) { ?>
                                                <img src="uploads/all/<?php echo $crmleads['image']; ?>"  class="img-radius img-50 align-top m-r-15" alt="Attachment Preview">
                                            <?php } else { ?>
                                                <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-2 f-12 text-center"><?php echo initials($crmleads['display_name']); ?></span>
                                            <?php } ?>
                                            <div class="d-inline-block width73">
                                                <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $crmleads['id']; ?>')"><h6><?php echo $crmleads['display_name']; ?></h6></button>
                                                <p class="text-muted m-b-0"><?php echo $crmleads['phone']; ?></p>
                                            </div>
                                        </div>
                                    </td>

                                    <td  class="p-2"><?php echo $crm_source_types['SOURCETYPE'][$crmleads['contact_source']]; ?></td>
                                    <td  class="p-2"><?php echo $project_task_status['crm_contacts'][$crmleads['crm_status']]['name']; ?></td>
                                </tr>
                        <?php $im++;
} ?>
                            </table>
                            <?php }else{ ?>
                            <div class="card-px text-center ">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Contacts</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Contact Status</h5>
                        <div class="card-header-right"></div>
                    </div>
                    <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                        <input type="hidden" name="chart_data_contact_chart" id="chart_data_contact_chart" value='<?php echo json_encode((array)$crm_contacts_chart); ?>' >
                        <?php /**/if(count((array)$crm_contacts_chart) =='0'){ ?>
                          <div class="card-px text-center py-20 m-t-30">
                                <p class="text-muted f-16 fw-bold m-b-30"><i class="ti-alert text-warning"></i> No Data</p>
                                <div class="text-center px-4">
                                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                                </div>
                            </div>
                        <?php }else{ ?>
                          <div id="chart_by_contact" style="width: 100%; height: 400px;"></div>
                        <?php } /**/ ?>
                    </div>
                </div>
            </div>
        </div>
        <?php  ?>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5>Sales Order Not Yet Generated</h5>
            </div>
            <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
                <?php if(count((array)$feasibility_requirements) !=0){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" >
                    <thead>
                        <tr>
                            <th scope="col" >Name</th>
                            <th scope="col" >Contact</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <?php foreach($feasibility_requirements as $crmld){  ?>
                        <tr>
                            <td class="w-b-w-s p-2">
                                <a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $crmld['crm_contact_id']; ?>')"><?php echo $crmld['display_name']; ?>

                                </a>
                            </td>
                            <td class="w-b-w-s p-2"><?php echo $crmld['phone']; ?></td>
                            <td class="p-2"><button class="btn btn-primary" onclick="createSalesOrdersWithFeasibilty('<?php echo $crmld['feasibility_id']; ?>','<?php echo $crmld['crm_contact_id']; ?>')">Generate SO</button></td>
                        </tr>
                    <?php } ?>
                    <tbody>
                    </tbody>
                </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Sales Order Not Yet Generated</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Pending Tasks</h5>
            </div>
            <div class="card-body">
                <?php if(count((array)$crm_pendding_tasks) !=0){ ?>
                <table class="table table-hover m-b-0" style="font-size:13px;" >
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" class="text-left">Tasks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($crm_pendding_tasks as $crmld){
                            $meg = $datetime = $address = "";
                            if($crmld['lead_type'] == '2' || $crmld['lead_type'] == '3'){
                                $meg .= $crmld['comment'];
                                $datetime .="<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
                            }elseif($crmld['lead_type'] == '7' || $crmld['lead_type'] == '6'){ //meeting
                                $meg .= $crmld['comment'];
                                $member = json_decode($crmld['member'],1);
                                foreach($member as $members){
                                    $address .= "<span class='m-r-5'>".$_SESSION['users'][$members].",</span>";
                                }
                                $datetime .="<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
                            }elseif($crmld['lead_type'] == '8'){ //visit
                                $meg .= $crmld['comment'];
                                $address .= "<span class='d-block'>".$crmld['address']." ".$crmld['post_code']."</span>";

                                $datetime .="<span class='d-block'>".$crmld['date']." ".$crmld['time']."</span>";
                            }
                            if($crmld['transaction_table'] =="crm_contacts"){
                                $pagecalltype ="CONTACT";
                            }elseif($crmld['transaction_table'] =="crm_leads"){
                                $pagecalltype ="LEAD";
                            }
                        ?>
                            <tr>
                                 <td class="w-b-w-s">
                                     <div class="align-middle">
                                            <span  alt="user" class="img-radius img-50 align-top m-r-10 custom_txt_round p-3 f-12 text-center"><i class="<?php echo $crm_activity_types[$crmld['lead_type']]['icon']; ?> f-18"></i></span>
                                            <div class="d-inline-block width73">
                                                <button type="button" !" class="leadsidebar text-primary" onclick="leadPageCall('<?php echo $pagecalltype; ?>','<?php echo $crmld['transaction_id']; ?>','<?php echo $crmld['id']; ?>')"><p class="mb-0"><?php echo $meg; ?></p></button>
                                                <a href="#!" class="text-reset" onclick="leadPageCall('<?php echo $pagecalltype; ?>','<?php echo $crmld['transaction_id']; ?>','<?php echo $crmld['id']; ?>')"><p class="mb-0"><?php echo $datetime; ?></p></a>
                                                <p class="text-muted m-b-0"><?php echo $address; ?></p>
                                            </div>
                                        </div>
                                </td>
                                <td class="w-b-w-s"><?php echo $crm_pendding_task_details[$crmld['transaction_table']][$crmld['transaction_id']]['display_name']; ?></td>
                            </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                    <div class="card-px text-center ">
                        <p class="text-muted f-16 fw-bold m-b-30"><i class="ti ti-alert text-warning"></i> No Pending Tasks</p>
                        <div class="text-center px-4">
                            <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                        </div>
                    </div>
                <?php } ?>

            </div>
        </div>
    </div>
</div>
<div class="col-lg-12">
    <div class="card">
  <div class="card-header">
    <h5>Feasibilities</h5>
  </div>
  <div class="card-body custom-scroll" style="min-height: 400px; max-height: 400px;">
    <ul class="nav md-tabs infisuite_tab mb-3" id="alertTabs">
      <li class="nav-item" role="presentation">
        <a class="nav-link f-w-600 f-12  active" id="expired-tab" data-bs-toggle="tab" href="#expired" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">Expired <span class="form-label label label-xs label-danger m-l-10"><?php echo count((array)$expired); ?></span></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link f-w-600 f-12" id="past-due-tab" data-bs-toggle="tab" href="#past-due" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">15-Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$days15); ?></span></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link f-w-600 f-12" id="pending-returns-tab" data-bs-toggle="tab" href="#pending-returns" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">30-Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$days30); ?></span></a>
      </li>
      <li class="nav-item" role="presentation">
        <a class="nav-link f-w-600 f-12" id="days-returns-tab" data-bs-toggle="tab" href="#days-returns" role="tab" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">45-Days <span class="form-label label label-xs label-success m-l-10"><?php echo count((array)$days45); ?></span></a>
      </li>
    </ul>

    <div class="tab-content" id="alertTabsContent">
      <!-- Tab 1: Closing Soon -->

      <div class="tab-pane fade show active" id="expired" role="tabpanel">
        <?php if (!empty($expired)): ?>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th scope="col">Feasibility Title</th>
                <th scope="col">Close Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($expired as $item): ?>
                <tr>
                  <td><a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $item['lead_id']; ?>')"><?php echo $item['display_name']." - ".$item['feasibility_type']; ?>
                      </a>
                  </td>
                  <td><?= htmlspecialchars($item['evaluation_end_date']) ?></td>
                  <td><?= $item['current_status'] == 0 ? "OPEN" : "CLOSED" ; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <tr>
            <td colspan="3">
              <div class="text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </div>
      <!-- Tab 2: Past Due -->
      <div class="tab-pane fade" id="past-due" role="tabpanel">
        <?php if (!empty($days15)): ?>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th scope="col">Feasibility Title</th>
                <th scope="col">Close Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($days15 as $item): ?>
                <tr>
                  <td> <a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $item['lead_id']; ?>')"><?php echo $item['display_name']." - ".$item['feasibility_type']; ?>
                      </a>
                  </td>
                  <td><?= htmlspecialchars($item['evaluation_end_date']) ?></td>
                  <td><?= $item['current_status'] == 0 ? "OPEN" : "CLOSED"; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <tr>
            <td colspan="3">
              <div class="text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </div>


      <!-- Tab 3: Pending Returns -->
      <div class="tab-pane fade" id="pending-returns" role="tabpanel">
        <?php if (!empty($days30)): ?>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th scope="col">Feasibility Title</th>
                <th scope="col">Close Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($days30 as $item): ?>
                <tr>
                  <td><a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $item['lead_id']; ?>')"><?php echo $item['display_name']." - ".$item['feasibility_type']; ?>
                      </a>
                  </td>
                  <td><?= htmlspecialchars($item['evaluation_end_date']) ?></td>
                  <td><?= $item['current_status'] == 0 ? "OPEN" : "CLOSED" ; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <tr>
            <td colspan="3">
              <div class="text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </div>
      <div class="tab-pane fade" id="days-returns" role="tabpanel">
        <?php if (!empty($days45)): ?>
          <table class="table table-bordered table-sm">
            <thead>
              <tr>
                <th scope="col">Feasibility Title</th>
                <th scope="col">Close Date</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($days45 as $item): ?>
                <tr>
                    <td><a href="#!" class="leadsidebar text-primary" onclick="leadPageCall('CONTACT','<?php echo $item['lead_id']; ?>')"><?php echo $item['display_name']." - ".$item['feasibility_type']; ?>
                      </a>
                  </td>

                  <td><?= htmlspecialchars($item['evaluation_end_date']) ?></td>
                  <td><?= $item['current_status'] == 0 ? "OPEN" : "CLOSED" ; ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <tr>
            <td colspan="3">
              <div class="text-center ">
                <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Records</p>
                <div class="text-center px-4">
                  <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                </div>
              </div>
            </td>
          </tr>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</div>
<div class="col-lg-12">
    <?php include_once 'views/custom_view_feasibility_dashboard.php'; ?>
</div>


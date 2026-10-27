<?php
$apiurl = "https://dev.infistaq.in/api.php";
$itsum_company_id = $_GET['company_id'];
$itsum_data_name = $_GET['data_name'];
$itsum_user_id = $_GET['user_id'];
$work_module_setup_id = $_GET['work_module_setup_id'];
if($_GET['refresh'] =="Y"){
  $postdata = array('company_id'=>$itsum_company_id,'data_name'=>$itsum_data_name,'user_id'=>$itsum_user_id, 'start_date'=>'2024-04-01','end_date'=>'2025-03-31');
  $sendpostreq = json_encode($postdata);
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://aidev.digixlabs.cloud/api/dashboard/refresh',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$sendpostreq,
    CURLOPT_HTTPHEADER => array(
      'API-TOKEN: X9mQ2Zp7Lk4T8wE5rV1yU0cN6bH3sAqD',
      'Accept: */*',
      'Accept-Encoding: gzip, deflate, br',
      'Connection: keep-alive',
      'Content-Type: application/json'

    ),
  ));
  $response23 = curl_exec($curl);
  $returnresult = array('company_id'=>$itsum_company_id,'application_table'=>$transaction_table,'transaction_id'=>$transaction_id,'api_master_id'=>48,'api_url'=>'','api_request_parameters'=>$sendpostreq,'request_date_time'=>date("Y-m-d H:i:s"),'api_response_details'=>urlencode(json_encode($decores)),'response_date_time'=>date("Y-m-d H:i:s"),'is_active'=>'0','current_status'=>'0','created_date'=>date("Y-m-d H:i:s"),'created_by'=>$itsum_user_id,'response_status'=>200);
  $inserturl = $apiurl."?mode=adddata&table=api_hits&method=post&company_id=".$itsum_company_id."&data_name=".$itsum_data_name."&userid=".$itsum_user_id; 
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $inserturl);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $returnresult);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $insertresponse = curl_exec($ch);
  $decore11 = json_decode($insertresponse,1);
}else{
  //$data1["where"] = array('api_master_id'=>48,'created_by'=>$itsum_user_id);
  $data1["where"] = array('id'=>554,'created_by'=>$itsum_user_id);
  $data1["field"] = "id,request_date_time,api_response_details";
  $data1['order'] = "id DESC";
  $data = urlencode(json_encode($data1));
  $curl = $apiurl."?mode=gettabledetailswithconditions&table=api_hits&company_id=".$itsum_company_id."&data_name=".$itsum_data_name."&userid=".$itsum_user_id."&data=".$data; 
  $cudata = callAPI($curl);
  $returnresult11 = json_decode($cudata,1);
  $returnresult = $returnresult11[0];
  $returnres11 = $returnresult['api_response_details'];
  //$returnres11 = str_replace('u2014', '\\u2014', $returnres11);
  //$returnres = preg_replace("/\r?\n/", "\\n", $returnres11);
  $decores = json_decode(urldecode($returnres11),1);
}
$notifications = $decores['notifications'];
$approvals = $decores['approvals'];
$tasks = $decores['tasks'];
$customer_data_check = $decores['customer_data_check'];
$vendor_data_check = $decores['vendor_data_check'];
$vendor_payment_ratings = $decores['vendor_payment_ratings'];
$customer_payment_ratings = $decores['customer_payment_ratings'];
$finance = $decores['finance'];
$vendorshowtabmodule = array(1,2,26,31,4);
$customershowtabmodule = array(3,31,4);
$financeshowtabmodule = array(4);
?>
<div class="card mt-4">
  <div class="card-header">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <h3 class="mb-0 f-w-600">Procurement Intelligence Dashboard</h3>
        <p class="mb-0">23 Apr 2026  <i class="fa fa-clock-o"></i> Last refreshed: 09:36 am</p>
      </div>
      <div class="col-lg-4 text-end">
        <button class="btn btn-primary" id="refreshBtn" onclick="refreshPage()"><i class="fa fa-refresh"></i> Refresh</button>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <ul class="nav nav-tabs md-tabs gap-2 border-0 party-summary-tab" role="tablist">
      <li class="nav-item">
        <a class="nav-link text-center border active" data-bs-toggle="tab" href="#notification" role="tab" aria-selected="true">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $notifications['total_unread']; ?></h3>
          <p class="m-0 f-w-600">Notification</p>
          <p class="m-0"><?php echo $notifications['action_unread']; ?> Action</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#pending-approvals" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $approvals['total_pending']; ?></h3>
          <p class="m-0 f-w-600">Pending Approvals</p>
          <p class="m-0"><?php echo $approvals['critical_count']; ?> Critical</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#unread-tasks" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $tasks['total_unread']; ?></h3>
          <p class="m-0 f-w-600">Unread Tasks</p>
          <p class="m-0"><?php echo $tasks['critical_count']; ?> Critical</p>
        </a>
      </li>
    <?php if(in_array($work_module_setup_id, $customershowtabmodule)){ ?>  
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#customer-issues" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $customer_data_check['incomplete_count']; ?></h3>
          <p class="m-0 f-w-600">Customer Issues</p>
          <p class="m-0"><?php echo $customer_data_check['breach_count']; ?> Breach</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#customer-ratings" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo COUNT((array)$customer_payment_ratings['ratings']); ?></h3>
          <p class="m-0 f-w-600">Customer Ratings</p>
          <!-- <p class="m-0">---- Rated</p> -->
        </a>
      </li>
    <?php } if(in_array($work_module_setup_id, (array)$vendorshowtabmodule)){ ?> 
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#vendor-issues" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $vendor_data_check['incomplete_count']; ?></h3>
          <p class="m-0 f-w-600">Vendor Issues</p>
          <p class="m-0"><?php echo $vendor_data_check['breach_count']; ?> Breach</p>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#vendor-ratings" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><?php echo $vendor_payment_ratings['total_vendors']; ?></h3>
          <p class="m-0 f-w-600">Vendor Ratings</p>
          <!-- <p class="m-0">---- Rated</p> -->
        </a>
      </li>
      <?php } if(in_array($work_module_setup_id, (array)$financeshowtabmodule)){ ?> 
      <li class="nav-item">
        <a class="nav-link text-center border" data-bs-toggle="tab" href="#finance-health" role="tab" aria-selected="false">
          <h3 class="f-w-600 f-30 mb-0"><i class="ti-check-box"></i> <?php //echo $finance['total_pending']; ?></h3>
          <p class="m-0 f-w-600">Finance Health</p>
          <p class="m-0"><?php echo $finance['total_pending']; ?></p>
        </a>
      </li>
      <?php } ?>
    </ul>
    <div class="tab-content tabs card-block">
      <div class="tab-pane customer-issues active" id="notification" role="tabpanel">
        <div class="mt-4">
          <h4 class="border-bottom pb-3">Module 1 — Notifications <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $notifications['total_unread']; ?> unread</label></h4>
          <div class="card-wrapper mt-4">
            <div class="card yellow mb-0">
              <div class="card-value"><?php echo $notifications['action_unread']; ?></div>
              <div class="card-title">ACTION UNREAD</div>
            </div>

            <div class="card red-light mb-0">
              <div class="card-value"><?php echo $notifications['info_unread']; ?></div>
              <div class="card-title">INFO UNREAD</div>
            </div>

            <div class="card red-dark mb-0">
              <div class="card-value"><?php echo $notifications['oldest_days']; ?></div>
              <div class="card-title">OLDEST (DAYS)</div>
            </div>
            <div class="card red-dark mb-0">
              <div class="card-value"><?php echo COUNT((array)$notifications['repeated_alerts']); ?></div>
              <div class="card-title">Repeated</div>
            </div>
          </div>

          <div class="mt-4">
            <?php foreach($notifications['flags'] as $flags){  ?>
            <p class="m-0">
              <label class="form-label badge badge-inverse-danger"><?php echo $flags['severity']; ?></label>
              <label class="f-12 m-l-10"><?php echo $flags['detail']; ?></label>
            </p>
            <?php } ?>
          </div> 
          <div class="mt-3">
            <h4 class="f-w-600 f-16">Age of unread notifications</h4>
          </div>
          <div class="card-wrapper">
            <div class="card yellow mb-0">
              <div class="card-value"><?php echo $notifications['age_buckets']['today'];?></div>
              <div class="card-title">Today</div>
            </div>

            <div class="card red-light mb-0">
              <div class="card-value"><?php echo $notifications['age_buckets']['this_week'];?></div>
              <div class="card-title">This Week</div>
            </div>

            <div class="card red-dark mb-0">
              <div class="card-value"><?php echo $notifications['age_buckets']['this_month'];?></div>
              <div class="card-title">This Month</div>
            </div>
          </div>
          <div class="mt-4">
            <h4 class="f-w-600 f-16">Action Items by Module (need your response)</h4>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#eeeeee">
                <th>ERP Module</th>
                <th>ACTION Count</th>
              </tr>
              <?php foreach($notifications['action_by_module'] as $keylable=>$keyval){ ?>
              <tr>
                <td><?php echo ucwords(strtolower(str_replace('_', ' ', $keylable))); ?></td>
                <td><?php echo $keyval; ?></td>
              </tr>
              <?php } ?>
            </table>
          </div>
          <div class="mt-2 blue-light p-3 b-radius-20 border-blue">
            <h4 class="text-primary f-16 f-w-600">AI Summary</h4>
            <p class="m-0"><?php echo $notifications['ai_summary'];?></p>
          </div>
          <div class="mt-3">
            <h4 class="f-w-600 f-16">Top Repeated Alert Patterns</h4>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#eeeeee">
                <th>ERP Repeated Alerts</th>
                <th>Times Seen</th>
                <th>Sample Message</th>
              </tr>
              <?php foreach($notifications['repeated_alerts'] as $repeated_alerts){ ?>
              <tr>
                <td><?php echo ucwords(strtolower(str_replace('_', ' ', $repeated_alerts['module']))); ?></td>
                <td><?php echo $repeated_alerts['count']; ?></td>
                <td class="f-10"><?php echo $repeated_alerts['sample_message']; ?></td>
              </tr>
              <?php } ?>
              </table>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#eeeeee">
                <th>ERP Breakdown</th>
                <th>Unread Count</th>
              </tr>
              <?php foreach($notifications['breakdown'] as $breakdownkey=> $breakdownbal){ ?>
              
              <tr>
                <td><?php echo ucwords(strtolower(str_replace('_', ' ', $breakdownkey))); ?></td>
                <td><?php echo $breakdownbal; ?></td>
              </tr>
              <?php } ?>
            </table>
          </div>
        </div>
      </div>
      <div class="tab-pane customer-issues" id="pending-approvals" role="tabpanel">
          <div class="mt-4">
            <h4 class="border-bottom pb-3">Module 2 — Pending Approvals <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $approvals['total_pending']; ?> Pending</label></h4>
            <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
              <h4 class="text-primary f-16 f-w-600">AI Summary</h4>
              <p class="m-0"> <?php echo $approvals['ai_summary']; ?></p>
            </div>
            <div class="table-responsive mt-4">
              <table class="table table-bordered table-sm">
                <tr bgcolor="#eeeeee">
                  <th>Transaction Table</th>
                  <th>Requested On</th>
                  <th>Days Pending</th>
                  <th>Severity</th>
                  <th>Issue</th>
                  <th>Review</th>
                </tr>
                <?php foreach($approvals['items'] as $itemsval){ ?>
                <tr>
                  <td><?php echo ucwords(strtolower(str_replace('_', ' ', $itemsval['transaction_table']))); ?></td>
                  <td><?php echo $itemsval['request_date']; ?></td>
                  <td class="text-danger"><?php echo $itemsval['days_pending']; ?></td>
                  <td><label class="form-label badge badge-inverse-danger"><?php echo $itemsval['severity']; ?></label></td>
                  <td class="white-space-normal"><?php echo $itemsval['gpt_issue']; ?></td>
                  <td class="white-space-normal"><?php echo $itemsval['gpt_action']; ?></td>
                </tr>
                <?php } ?>
                </table>
            </div>
          </div>
      </div>
      <div class="tab-pane customer-issues" id="unread-tasks" role="tabpanel">
        <div class="mt-4">
          <h4 class="border-bottom pb-3">Module 3 - Unread Tasks <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"></label></h4>
        </div>

        <div class="border-left mt-4 p-l-10 p-3 bg-light" style="">
          <h4>AI Summary</h4>
          <p class="m-0"><?php echo $tasks['ai_summary']; ?></p>
        </div>
        <div class="table-responsive mt-4">
          <table class="table table-bordered table-sm">
            <tbody><tr bgcolor="#eeeeee">
              <th>task_name</th>
              <th>Overdue Day</th>
              <th>Severity</th>
            </tr>
            <?php if(count((array)$tasks['items']) != 0){ foreach($tasks['items'] as $unredtask){ ?>
            <tr>
              <td class="white-space-normal">
                <h6 class="f-14 m-0"><?php echo $unredtask['task_name']; ?></h6>
              </td>
              <td><?php echo $unredtask['days_overdue']; ?></td>
              <td>
                <label class="form-label badge badge-inverse-danger"><?php echo $unredtask['severity']; ?></label>
              </td>
            </tr>
            <?php } }else{ ?>


            <?php } ?>
          </tbody>
        </table>
      </div>
      </div>
      <div class="tab-pane customer-issues" id="customer-issues" role="tabpanel">
            <div class="mt-4">
                <h4 class="border-bottom pb-3">Module 4 - Customer Data Check <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $customer_data_check['incomplete_count']; ?> Checked</label></h4>
                <div class="border-left mt-4 p-l-10 p-3 bg-light" style="">
                  <h4>AI Summary</h4>
                  <p class="m-0"><?php echo $customer_data_check['ai_summary']; ?></p>
                </div>
                <div class="mt-5">
                  <h6 class="f-14">Record Completeness </h6>
                  <div class="progress progress-xs">
                      <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                  </div>
                </div>
                <div class="card-wrapper mt-4">
                  <div class="card red-light mb-0">
                      <div class="card-value"><?php echo $customer_data_check['complete_count']; ?></div>
                      <div class="card-title">COMPLETE RECORDS</div>
                  </div>
                  <div class="card yellow mb-0">
                      <div class="card-value"><?php echo $customer_data_check['incomplete_count']; ?></div>
                      <div class="card-title">INCOMPLETE RECORDS</div>
                  </div>
                  <div class="card red-dark mb-0">
                      <div class="card-value"><?php echo $customer_data_check['critical_count']; ?></div>
                      <div class="card-title">CRITICAL ISSUES</div>
                  </div>
                </div>
                <h4 class="mt-4">Missing Fields Breakdown</h4>
                <div class="card-wrapper mt-3">
                  <div class="card border mb-0">
                      <div class="card-value text-danger"><?php echo $customer_data_check['field_breakdown']['payment_terms']; ?></div>
                      <div class="card-title">PAYMENT TERMS</div>
                  </div>

                  <div class="card border mb-0">
                      <div class="card-value text-danger"><?php echo $customer_data_check['field_breakdown']['credit_limit']; ?></div>
                      <div class="card-title">CREDIT LIMIT</div>
                  </div>

                  <div class="card border mb-0">
                      <div class="card-value text-danger"><?php echo $customer_data_check['field_breakdown']['shipping_address']; ?></div>
                      <div class="card-title">SHIPPING ADDRESS</div>
                  </div>

                  <div class="card border mb-0">
                      <div class="card-value text-danger"><?php echo $customer_data_check['field_breakdown']['billing_address']; ?></div>
                      <div class="card-title">BILLING ADDRESS</div>
                  </div>
                </div>
                <div class="table-responsive mt-4">
                  <table class="table table-bordered table-sm">
                      <tr bgcolor="#eeeeee">
                        <th>Customer</th>
                        <th>Missing Fields</th>
                        <th>Credit Limit Breach</th>
                        <th>Severity</th>
                      </tr>
                      <?php foreach($customer_data_check['issues'] as $issuesval){ ?>
                      <tr>
                        <td class="white-space-normal">
                          <h6 class="f-14 m-0"><?php echo $issuesval['company']; ?></h6>
                        </td>
                        <td class="white-space-normal">
                          <?php foreach($issuesval['missing_fields'] as $missingval){ ?>
                          <label class="form-label label label-warning text-dark"><?php echo $missingval; ?></label>
                          <?php } ?>
                        </td>
                        <td>-</td>
                        <td>
                          <label class="form-label badge badge-inverse-danger"><?php echo $issuesval['severity']; ?></label>
                        </td>
                      </tr>
                    <?php } ?>
                      </table>
                </div>
            </div>
      </div>
      <div class="tab-pane customer-issues" id="vendor-issues" role="tabpanel">
            <div class="mt-4">
              <h4 class="border-bottom pb-3">Module 5 — Vendor Data Check <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $vendor_data_check['incomplete_count']; ?> Checked</label></h4>
              <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
                <h4 class="text-primary f-16 f-w-600">AI Summary</h4>
                <p class="m-0"><?php echo $vendor_data_check['ai_summary']; ?></p>
              </div>
              <div class="mt-5">
                <h6 class="f-14">Record Completeness </h6>
                <div class="progress progress-xs">
                    <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
              </div>
              <div class="card-wrapper mt-4">
                <div class="card yellow mb-0">
                    <div class="card-value"><?php echo $vendor_data_check['incomplete_count']; ?></div>
                    <div class="card-title">INCOMPLETE RECORDS</div>
                </div>

                <div class="card red-light mb-0">
                    <div class="card-value"><?php echo $vendor_data_check['breach_count']; ?></div>
                    <div class="card-title">CREDIT LIMIT BREACH</div>
                </div>

                <div class="card red-dark mb-0">
                    <div class="card-value"><?php echo $vendor_data_check['overpaid_count']; ?></div>
                    <div class="card-title">OVERPAID VENDORS</div>
                </div>
                <div class="card red-dark mb-0">
                    <div class="card-value"><?php echo $vendor_data_check['critical_count']; ?></div>
                    <div class="card-title">CRITICAL ISSUES</div>
                </div>
              </div>
              <div class="mt-4">
                <h4 class="f-w-600 f-16">Missing Fields Breakdown</h4>
              </div>
              <div class="card-wrapper mt-3">
                <div class="card border mb-0">
                    <div class="card-value text-danger"><?php echo $vendor_data_check['field_breakdown']['credit_limit']; ?></div>
                    <div class="card-title">CREDIT LIMIT</div>
                </div>
                <div class="card border mb-0">
                    <div class="card-value text-danger"><?php echo $vendor_data_check['field_breakdown']['payment_terms']; ?></div>
                    <div class="card-title">PAYMENT TERMS</div>
                </div>
                <div class="card border mb-0">
                    <div class="card-value text-danger"><?php echo $vendor_data_check['field_breakdown']['shipping_address']; ?></div>
                    <div class="card-title">SHIPPING ADDRESS</div>
                </div>
                <div class="card border mb-0">
                    <div class="card-value text-danger"><?php echo $vendor_data_check['field_breakdown']['billing_address']; ?></div>
                    <div class="card-title">BILLING ADDRESS</div>
                </div>
              </div>
              <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                  <tr bgcolor="#eeeeee">
                    <th>Vendor</th>
                    <th>Missing Fields</th>
                    <th>Credit / Overpaid</th>
                    <th>Severity</th>
                  </tr>
                   <?php foreach($vendor_data_check['issues'] as $issuesval){ ?>
                  <tr>
                        <td class="white-space-normal">
                          <h6 class="f-14 m-0"><?php echo $issuesval['company']; ?></h6>
                        </td>
                        <td class="white-space-normal">
                          <?php foreach($issuesval['missing_fields'] as $missingval){ ?>
                          <label class="form-label label label-warning text-dark"><?php echo $missingval; ?></label>
                          <?php } ?>
                        </td>
                        <td>-</td>
                        <td>
                          <label class="form-label badge badge-inverse-danger"><?php echo $issuesval['severity']; ?></label>
                        </td>
                      </tr>
                  <?php } ?>
                </table>
              </div>
            </div>
      </div>
      <div class="tab-pane customer-issues" id="vendor-ratings" role="tabpanel">
            <div class="mt-4">
              <h4 class="border-bottom pb-3">Module 6 — Vendor Payment Ratings 
                <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $vendor_payment_ratings['total_vendors']; ?> Vendors</label>
              </h4>
              <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
                <h4 class="text-primary f-16 f-w-600">AI Summary</h4>
                <p class="m-0"><?php echo $vendor_payment_ratings['ai_module_summary']; ?></p>
              </div>
              <div class="card-wrapper mt-4">
                <div class="card yellow m-0">
                    <div class="card-title">TOTAL PAYABLE</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['total_payable']; ?></div>
                </div>

                <div class="card red-light mb-0">
                    <div class="card-title">OVERDUE AMOUNT</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['total_overdue_amount']; ?></div>
                </div>

                <div class="card red-dark mb-0">
                    <div class="card-title">OVERDUE INVOICES</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['total_overdue_invoices']; ?></div>
                </div>
                <div class="card purple-light mb-0">
                    <div class="card-title">VENDORS AT RISK</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['vendors_at_risk']; ?></div>
                </div>
                <div class="card green-light mb-0">
                    <div class="card-title">DUE IN 7 DAYS</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['cash_due_7d']; ?></div>
                </div>
                <div class="card blue-light mb-0">
                    <div class="card-title">DUE IN 30 DAYS</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['cash_due_30d']; ?></div>
                </div>
                <div class="card red-dark mb-0">
                    <div class="card-title">LEDGER NET PAYABLE</div>
                    <div class="card-value"><?php echo $vendor_payment_ratings['ledger_coverage']; ?></div>
                </div>
              </div>
              <div class="card-wrapper mt-4 mb-4">
                <div class="card yellow mb-0">
                    <div class="card-value"><?php echo $vendor_payment_ratings['rating_breakdown']['excellent_good']; ?></div>
                    <div class="card-title">EXCELLENT / GOOD</div>
                </div>
                <div class="card red-light mb-0">
                    <div class="card-value"><?php echo $vendor_payment_ratings['rating_breakdown']['fair']; ?></div>
                    <div class="card-title">FAIR</div>
                </div>
                <div class="card red-dark mb-0">
                    <div class="card-value"><?php echo $vendor_payment_ratings['rating_breakdown']['poor_critical']; ?></div>
                    <div class="card-title">POOR / CRITICAL</div>
                </div>
                <div class="card purple-light mb-0">
                    <div class="card-value"><?php echo $vendor_payment_ratings['rating_breakdown']['no_data']; ?></div>
                    <div class="card-title">NO DATA</div>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered table-sm">
                  <tr class="text-white" bgcolor="#333333">
                    <th class="text-white">#</th>
                    <th class="text-white">Vendor</th>
                    <th class="text-white">AI Status</th>
                    <th class="text-white">Rating</th>
                    <th class="text-white">Payment Progress</th>
                    <th class="text-white">Invoices</th>
                    <th class="text-white">Overdue ₹</th>
                    <th class="text-white">% of Total</th>
                    <th class="text-white">Recommended Action</th>
                  </tr>
                  <?php $mkseq= 1; foreach($vendor_payment_ratings['ratings'] as $ratingsred){ ?>
                  <tr>
                    <td><?php echo $mkseq; ?></td>
                    <td>
                      <h6 class="f-14 m-0"><?php echo $ratingsred['company']; ?></h6>
                      <label class="form-label label label-success text-dark">Save <?php echo $ratingsred['early_offer_savings']; ?> by <?php echo $ratingsred['early_offer_deadline']; ?></label>
                    </td>
                    <td>
                      <label class="form-label label label-danger text-white">Pay Immediately</label>
                    </td>
                    <td>
                      <div class="d-block mb-1">    
                        <i class="fa fa-star f-12 text-c-yellow"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                      </div>
                      <label class="form-label badge badge-inverse-danger"><?php $ratingsred['rating_label'];?></label>
                    </td>
                    <td>
                      <div class="">
                        <h6 class="f-14"><?php echo $ratingsred['total_paid'];?>% Paid</h6>
                        <div class="progress progress-xs mb-1">
                            <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="f-10 m-0"><?php echo $ratingsred['total_paid']; ?> paid of <?php echo $ratingsred['total_pending']; ?></p>
                        <p class="f-10 m-0 text-danger">Delayed by <?php echo $ratingsred['max_delay_days']; ?> days</p>
                      </div>
                    </td>
                    <td><?php echo COUNT((array)$ratingsred['invoices_detail']); ?></td>
                    <td class="text-danger"><?php echo $ratingsred['overdue_amount'];?></td>
                    <td class="text-danger"><?php echo $ratingsred['pct_of_total'];?>%</td>
                    <td class="f-10 white-space-normal"><?php echo  $ratingsred['recommended_action'] ?></td>
                  </tr>

                  <tr bgcolor="#eeeeee">
                    <th colspan="9">
                      <table class="table table-bordered table-sm f-13">
                        <tr>
                          <th colspan="5" class="f-14">
                            <i class="fa fa-file-text-o m-r-5"></i> Invoice Payment Breakdown — Aperture Science
                          </th>
                        </tr>
                        <?php foreach($ratingsred['invoices_detail'] as $invoices_detail){ ?>
                        <tr>
                          <th colspan="5" class="f-12 bg-info">
                            <i class="fa fa-file-text-o m-r-5"></i><?php echo $invoices_detail['invoice_number']."  ".$invoices_detail['invoice_date']; ?>
                          </th>
                        </tr>
                        <tr>
                          <th>DUE DATE</th>
                          <th>DUE AMOUNT</th>
                          <th>PENDING</th>
                          <th>STATUS</th>
                        <!--   <th>PAYMENTS MADE</th>-->
                      </tr>
                        <?php foreach($invoices_detail['terms'] as $termsval){ ?>
                        <tr>
                          <td><?php echo $termsval['due_date']; ?></td>
                          <td><?php echo $termsval['due_amount']; ?></td>
                          <td class="text-danger"><?php echo $termsval['pending_amount']; ?></td>
                          <td class="text-danger"><?php echo $termsval['status']; ?></td>
                        </tr>
                      <?php } } ?>
                      </table>
                    </th>
                  </tr>

                  <tr bgcolor="#eeeeee"> 
                    <th colspan="9"> 
                      <table class="table table-bordered table-sm f-13 text-white"> 
                        <tr class="header-row" onclick="toggleTable(this)">
                          <th colspan="5" class="f-14 text-dark cursor-pointer">
                            <i class="ti-angle-right m-r-10 arrow"></i>
                            <i class="fa fa-book m-r-5"></i> Ledger Statement — Aperture Science
                          </th>
                        </tr>

                        <tr class="data-row"> 
                          <td class="text-white">Opening Balance</td> 
                          <td class="text-white" colspan="2" align="right"><?php echo $vendor_payment_ratings['ledger_opening']!=""?$vendor_payment_ratings['ledger_opening']:0;?></td> 
                        </tr>

                       <!--  <tr class="data-row"> 
                          <td>Date</td> 
                          <td>Description</td> 
                          <td align="right">Balance</td> 
                        </tr> -->

                        <tr class="data-row"> 
                          <td class="text-white">Closing Balance</td> 
                          <td class="text-white" colspan="2" align="right"><?php echo $vendor_payment_ratings['ledger_closing']!=""?$vendor_payment_ratings['ledger_closing']:0;?> (Receivable)</td> 
                        </tr>
                      </table>
                    </th>
                  </tr>
                <?php $mkseq++; } ?>
                </table>
              </div>
            </div>
      </div>
      <div class="tab-pane customer-issues" id="customer-ratings" role="tabpanel">
            <div class="mt-4">
              <h4 class="border-bottom pb-3">Module 7 — Customer Payment Ratings 
                <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $customer_payment_ratings['total_customers']; ?> Customers</label>
              </h4>
              <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
                <h4 class="text-primary f-16 f-w-600">AI Summary</h4>
                <p class="m-0"><?php echo $customer_payment_ratings['ai_module_summary']; ?></p>
              </div>
              <div class="card-wrapper card-wrapper-receivable mt-5">
                <div class="card yellow mb-0">
                    <div class="card-title">TOTAL RECEIVABLE</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['total_receivable']; ?></div>
                </div>

                <div class="card red-light mb-0">
                    <div class="card-title">OVERDUE AMOUNT</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['total_overdue_amount']; ?></div>
                </div>

                <div class="card red-dark mb-0">
                    <div class="card-title">OVERDUE INVOICES</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['total_overdue_invoices']; ?></div>
                </div>
                <div class="card purple-light mb-0">
                    <div class="card-title">CUSTOMERS AT RISK</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['customers_at_risk']; ?></div>
                </div>
                <div class="card green-light mb-0">
                    <div class="card-title">COLLECT IN 7 DAYS</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['cash_due_7d']; ?></div>
                </div>
                <div class="card blue-light mb-0">
                    <div class="card-title">COLLECT IN 30 DAYS</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['cash_due_30d']; ?></div>
                    <p class="f-10 m-0">Early pay saves <?php echo $customer_payment_ratings['total_early_offer_savings']; ?></p>
                </div>
                <div class="card red-dark mb-0">
                    <div class="card-title">LEDGER NET RECEIVABLE</div>
                    <div class="card-value"><?php echo $customer_payment_ratings['ledger_net_receivable']; ?></div>
                    <p class="f-10 m-0"><?php echo $customer_payment_ratings['ledger_coverage_pct']; ?>% of <?php echo $customer_payment_ratings['total_customers']; ?> customers</p>
                </div>
              </div>
              <div class="card-wrapper mt-4">
                <div class="card yellow mb-0">
                    <div class="card-value"><?php echo $customer_payment_ratings['rating_breakdown']['excellent_good']; ?></div>
                    <div class="card-title">EXCELLENT / GOOD</div>
                </div>
                <div class="card red-light mb-0">
                    <div class="card-value"><?php echo $customer_payment_ratings['rating_breakdown']['fair']; ?></div>
                    <div class="card-title">FAIR</div>
                </div>
                <div class="card red-dark mb-0">
                    <div class="card-value"><?php echo $customer_payment_ratings['rating_breakdown']['poor_critical']; ?></div>
                    <div class="card-title">POOR / CRITICAL</div>
                </div>
                <div class="card purple-light mb-0">
                    <div class="card-value"><?php echo $customer_payment_ratings['rating_breakdown']['no_data']; ?></div>
                    <div class="card-title">NO DATA</div>
                </div>
              </div>
              <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                  <tbody><tr class="text-white" bgcolor="#333333">
                    <th class="text-white">#</th>
                    <th class="text-white">Customer</th>
                    <th class="text-white">AR Status</th>
                    <th class="text-white">Rating</th>
                    <th class="text-white">Collection Progress</th>
                    <th class="text-white">Invoices</th>
                    <th class="text-white">Overdue ₹</th>
                    <th class="text-white">% of Total</th>
                    <th class="text-white">Recommended Action</th>
                  </tr>
                  <?php $mkseq=1; foreach($customer_payment_ratings['ratings'] as $ratingsres){ ?>
                  <tr>
                    <td><?php echo $mkseq; ?></td>
                    <td class="white-space-normal">
                      <h6 class="f-14 m-0"><?php echo $ratingsres['company'];?></h6>
                    </td>
                    <td>
                      <label class="form-label label label-danger text-white">Collect Now</label>
                    </td>
                    <td>
                      <div class="d-block mb-1">    
                        <i class="fa fa-star f-12 text-c-yellow"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                        <i class="fa fa-star f-12 text-default"></i>
                      </div>
                      <label class="form-label badge badge-inverse-danger"><?php echo $ratingsres['rating_label'];?></label>
                    </td>
                    <td>
                      <div class="">
                        <h6 class="f-14"><?php echo $ratingsres['total_paid'];?>% collected</h6>
                        <div class="progress progress-xs mb-1">
                            <div class="progress-bar progress-bar-danger" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <p class="f-10 m-0"><?php echo $ratingsres['total_paid'];?> of <?php echo $ratingsres['total_pending'];?></p>
                        <p class="f-10 m-0 text-danger"><?php echo $ratingsres['overdue_amount'];?> overdue receivable(s)</p>
                      </div>
                    </td>
                    <td><?php echo $ratingsres['total_invoices'];?></td>
                    <td class="text-danger"><?php echo $ratingsres['total_due'];?></td>
                    <td class="text-danger"><?php $ratingsres['pct_of_total']; ?>%</td>
                    <td class="f-10"><?php $ratingsres['recommended_action']; ?></td>
                  </tr>
                  <tr bgcolor="#eeeeee">
                    <th colspan="9">
                      <table class="table table-bordered table-sm f-13">
                        <tr>
                          <th colspan="5" class="f-14">
                            <i class="fa fa-file-text-o m-r-5"></i> Sales Invoice Receivable Breakdown — <?php echo $ratingsres['company'];?>
                          </th>
                        </tr>
                        <?php foreach($ratingsres['invoices_detail'] as $invoicesdetail){ ?>
                        <tr bgcolor="#f6f7f8">
                          <th colspan="5" class="f-12 bg-info">
                            <i class="fa fa-file-text-o m-r-5"></i> <?php echo $invoices_detail['invoice_number']."  ".$invoices_detail['invoice_date']; ?>
                          </th>
                        </tr>
                        <tr>
                          <th>DUE DATE</th>
                          <th>DUE AMOUNT</th>
                          <th>PENDING</th>
                          <th>STATUS</th>
                          <!-- <th>PAYMENTS RECEIVED</th> -->
                        </tr>
                        <?php foreach($invoicesdetail['terms'] as $terms){ ?>
                        <tr>
                          <td><?php echo $termsval['due_date']; ?></td>
                          <td><?php echo $termsval['due_amount']; ?></td>
                          <td class="text-danger"><?php echo $termsval['pending_amount']; ?></td>
                          <td class="text-danger"><?php echo $termsval['status']; ?></td>
                        </tr>
                        <?php } ?>
                      <?php } ?>
                      </table>
                    </th>
                  </tr>
                <?php $mkseq++; } ?>
              </tbody>
                </table>
              </div>
            </div>
      </div>
      <div class="tab-pane customer-issues" id="finance-health" role="tabpanel">
        <div class="mt-4">
          <h4 class="border-bottom pb-3">Module 8 — Finance Details 
            <label class="b-radius-20 border f-12 p-l-10 p-r-10 m-l-10"><?php echo $finance['period_start']." To ".$finance['period_end'];?></label>
          </h4>
          <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
            <div class="d-inline-block align-middle">
                <i class="text-per img-radius align-top m-r-15 text-center d-inline-block text-white">
                  <h6 class="m-0 f-24"><?php echo $finance['health_score']; ?></h6>
                  <p class="m-0 f-12">/10</p>
                </i>
                <div class="d-inline-block">
                    <p class="text-success m-b-0 f-w-600 f-12">Overall Financial Health Score</p>
                    <h6><?php echo $finance['health_status']; ?></h6>
                    <div class="progress progress-xs mb-1">
                        <div class="progress-bar progress-bar-success" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="50" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
          </div>
          <div class="mt-4 blue-light p-3 b-radius-20 border-blue">
            <h4 class="text-primary f-16 f-w-600">AI Executive Summary</h4>
            <p class="m-0">Executive - <?php echo $finance['executive_summary']; ?></p>
            <p class="m-0">- <?php echo $finance['ai_summary']; ?></p>
          </div>
          <h4 class="f-18 f-w-600 mt-4">Key Metrics Snapshot</h4>
          <div class="table-responsive">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#333333" class="text-white">
                <th class="text-white">Category</th>
                <th class="text-white">Metric</th>
                <th class="text-white">Value</th>
                <!-- <th class="text-white">Trend</th> -->
                <th class="text-white">Status</th>
              </tr>
              <?php foreach($finance['metrics_snapshot'] as $metrics_snapshot){ ?>
              <tr>
                <td><?php echo $metrics_snapshot['category']; ?></td>
                <td><?php echo $metrics_snapshot['metric']; ?></td>
                <td><?php echo $metrics_snapshot['value']; ?></td>
                <!-- <td><i class="ti-angle-<?php //echo $metrics_snapshot['trend']; ?>"></i></td> -->
                <td><label class="form-label badge badge-inverse-success"><i class="fa fa-check f-10"></i> <?php echo $metrics_snapshot['status']; ?></label></td>
              </tr>
            <?php } ?>
              </table>
          </div>
          <div class="row mt-4">
            <div class="col-lg-6">
              <h6 class="text-success f-w-600 text-uppercase"><i class="fa fa-check m-r-5"></i> Strengths</h6>
              <?php foreach($finance['strengths'] as $strengths){ ?>
              <div class="card green-light p-2 text-start mb-3">
                <p class="m-0"><?php echo $strengths; ?></p>
              </div>
              <?php } ?>
            </div>
            <div class="col-lg-6">
              <h6 class="text-success f-w-600 text-uppercase"><i class="fa fa-check m-r-5"></i>  Risks & Concerns</h6>
              <?php foreach($finance['risks'] as $risks){ ?>
              <div class="card yellow p-2 text-start mb-3">
                <p class="m-0"><?php echo $risks; ?></p>
              </div>
            <?php } ?>
            </div>
          </div>
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> Trend Analysis
          </h6>
          <div class="card-wrapper mt-2">
            
          </div>
          <div class="bg-gray">
            <p class="m-0"><?php echo $finance['trend_analysis']['interpretation']; ?></p>
          </div>
        
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> AI Insights
          </h6>
           <?php foreach($finance['ai_insights'] as $ai_insights){ ?>
          <div class="card green-light p-2 text-start mb-3">
            <p class="m-0"><?php echo $ai_insights; ?></p>
          </div>
          <?php } ?>
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> Detailed Financial Data
          </h6>
          <div class="card-wrapper">
            <div class="card yellow mb-0">
                <div class="card-title">REVENUE</div>
                <div class="card-value"><?php echo $finance['profit_and_loss']['revenue']; ?></div>
            </div>

            <div class="card red-light mb-0">
                <div class="card-title">GROSS MARGIN</div>
                <div class="card-value"><?php echo $finance['profit_and_loss']['gross_margin_pct']; ?>%</div> 
                <p class="f-10 m-0"><?php echo $finance['profit_and_loss']['gross_profit']; ?></p>
            </div>

            <div class="card red-dark mb-0">
                <div class="card-title">NET MARGIN</div>
                <div class="card-value"><?php echo $finance['profit_and_loss']['net_margin_pct']; ?>%</div>
                <p class="f-10 m-0"><?php echo $finance['profit_and_loss']['net_profit']; ?></p>
            </div>
            <div class="card purple-light mb-0">
                <div class="card-title">WORKING CAPITAL</div>
                <div class="card-value"><?php echo $finance['profit_and_loss']['net_profit']; ?></div>
            </div>
          </div>
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> Profit & Loss Summary
          </h6>
          <div class="mt-2 blue-light p-3 b-radius-20 border-blue">
            <h4 class="text-primary f-16 f-w-600"> P&L Analysis</h4>
            <p class="m-0"><?php echo $finance['profit_and_loss']['ai_summary']; ?></p>
          </div>
          <div class="table-responsive mt-4">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#333333" class="text-white">
                <th colspan="3" class="text-white">Key P&L Figures</th>
              </tr>
              <tr>
                <td>Metric</td>
                <td>Current</td>
              </tr>
              <tr>
                <td>Revenue</td>
                <th class="text-success"><?php echo $finance['profit_and_loss']['revenue']; ?></th>
              </tr>
              <tr>
                <td>Gross Profit</td>
                <td class="text-success"><?php echo $finance['profit_and_loss']['gross_profit']; ?></td>
              </tr>
              <tr>
                <td>EBITDA</td>
                <td class="text-success"><?php echo $finance['profit_and_loss']['ebitda']; ?></td>
              </tr>
              <tr>
                <td>Net Profit / (Loss)</td>
                <td class="text-success"><?php echo $finance['profit_and_loss']['net_profit']; ?></td>
              </tr>
            </table>
          </div>
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> Balance Sheet Position
          </h6>
          <div class="mt-2 blue-light p-3 b-radius-20 border-blue">
            <h4 class="text-primary f-16 f-w-600">Balance Sheet Analysis</h4>
            <p class="m-0"><?php echo $finance['balance_sheet']['ai_summary']; ?></p>
          </div>
          <div class="card-wrapper mt-4">
            <div class="card yellow mb-0">
                <div class="card-title">TOTAL ASSETS</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['total_assets']; ?></div>
            </div>
            <div class="card red-light mb-0">
                <div class="card-title">TOTAL LIABILITIES</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['total_liabilities']; ?></div>
            </div>
            <div class="card red-dark mb-0">
                <div class="card-title">NET WORTH</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['net_worth']; ?></div>
            </div>
            <div class="card purple-light mb-0">
                <div class="card-title">SHAREHOLDERS FUNDS</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['shareholders_funds']; ?></div>
            </div>
            <div class="card green-light mb-0">
                <div class="card-title">TRADE PAYABLES</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['trade_payables']; ?></div>
                <!-- <p class="f-10 m-0">N/A</p> -->
            </div>
            <div class="card blue-light mb-0">
                <div class="card-title">DEBT-TO-EQUITY</div>
                <div class="card-value"><?php echo $finance['balance_sheet']['debt_to_equity']!=""?$finance['balance_sheet']['debt_to_equity']:0; ?></div>
            </div>
          </div>
          <h6 class="text-success f-w-600 text-uppercase mt-4">
            <i class="fa fa-check m-r-5"></i> Cash Flow Statement
          </h6>
          <div class="mt-2 blue-light p-3 b-radius-20 border-blue">
            <h4 class="text-primary f-16 f-w-600">Cash Flow Analysis</h4>
            <p class="m-0"><?php echo $finance['cash_flow']['ai_summary']; ?></p>
          </div>

          <div class="card-wrapper mt-4">
            <div class="card yellow mb-0">
                <div class="card-title">OPERATING ACTIVITIES</div>
                <div class="card-value"><?php echo $finance['cash_flow']['operating']; ?></div>
            </div>
          
            <div class="card red-light mb-0">
                <div class="card-title">INVESTING ACTIVITIES</div>
                <div class="card-value"><?php echo $finance['cash_flow']['investing']; ?></div>
            </div>
           
            <div class="card red-dark mb-0">
                <div class="card-title">FINANCING ACTIVITIES</div>
                <div class="card-value"><?php echo $finance['cash_flow']['financing']; ?></div>
            </div>
          
            <div class="card purple-light mb-0">
                <div class="card-title">CLOSING CASH</div>
                <div class="card-value"><?php echo $finance['cash_flow']['closing_cash']; ?></div>
                <p class="m-0 f-10">Previous:<?php echo $finance['cash_flow']['prev_closing_cash']; ?></p>
            </div>
          </div>
          <div class="table-responsive mt-4">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#333333" class="text-white">
                <th colspan="3" class="text-white">Operating Activities Detail</th>
              </tr>
              <tr>
                <td>Item</td>
                <td>Current</td>
                <td>Prior</td>
              </tr>
              <?php foreach($finance['cash_flow']['operating_activities'] as $operating_activities){ ?>
              <tr>
                <td><?php echo $operating_activities['label']; ?></td>
                <th class="text-dark"><?php echo $operating_activities['current']; ?></th>
                <td class="text-muted"><?php echo $operating_activities['previous']; ?></td>
              </tr>
              <?php } ?>
              </table>
          </div>
          <div class="table-responsive mt-4">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#333333" class="text-white">
                <th colspan="3" class="text-white">Investing Activities Detail</th>
              </tr>
              <tr>
                <td>Item</td>
                <td>Current</td>
                <td>Prior</td>
              </tr>
              <?php foreach($finance['cash_flow']['investing_activities'] as $investing_activities){ ?>
              <tr>
                <td><?php echo $investing_activities['label']; ?></td>
                <th class="text-dark"><?php echo $investing_activities['current']; ?></th>
                <td class="text-muted"><?php echo $investing_activities['previous']; ?></td>
              </tr>
              <?php } ?>
              </table>
          </div>
          <div class="table-responsive mt-4">
            <table class="table table-bordered table-sm">
              <tr bgcolor="#333333" class="text-white">
                <th colspan="3" class="text-white"> Financing Activities Detail</th>
              </tr>
              <tr>
                <td>Item</td>
                <td>Current</td>
                <td>Prior</td>
              </tr>
              <?php foreach($finance['cash_flow']['financing_activities'] as $financing_activities){ ?>
              <tr>
                <td><?php echo $financing_activities['label']; ?></td>
                <th class="text-dark"><?php echo $financing_activities['current']; ?></th>
                <td class="text-muted"><?php echo $financing_activities['previous']; ?></td>
              </tr>
              <?php } ?>
              </table>
          </div>
        </div>
      </div>
      <!--------------- Finance Start ----------------->
    </div>
  </div>
</div>

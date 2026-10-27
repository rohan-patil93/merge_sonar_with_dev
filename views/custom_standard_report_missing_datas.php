<?php
$_SESSION['apply_js']['multiselest'] = "1";
$tname = $_GET['TNAME'];
$apiurl = API."?mode=getstandardmissiongreport&tname=".$tname;
$response = callAPI($apiurl);
$result = json_decode($response,1);

$table_list = $result['table_list'];
$transactionres = $result['transaction_res'];
$showtableonly = array('application_users','employees','candidates','vendors','customers');
$notcheckfield = array('id','company_id','is_active','current_status','status_date','from_date','to_date','created_date','created_by','updated_by','updated_date');
?>
<script type="text/javascript">
    function selectTableName(tname){
        window.location = "index.php?option=COM_STANDARD_REPORT_MISSING_DATAS&TNAME="+tname;
    }
    function NewMessageAdded(id){
    $(".custom-popup-open").hide();
    $(".cardpopup_"+id).show('slow');
  }
  function closedPopupcompliance22(){
        $(".custom-popup-open").hide();
      }
  </script>
<div class="card" >
    <div class="card-header">
        <h5>Missing / Completion Transaction</h5>
        <div class="card-header-right">
            <button type="button" class="btn waves-effect waves-light"><i class="icofont icofont-reply"></i>Back</button>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-4">
                <div class="form-group">
                    <span class="form-label d-block">Select Table</span>
                    <select aria-label="Table Name" class="form-select js-example-basic-single" name="table_name"  onchange="selectTableName(this.value)">
                           <option value="0"> Select Any One</option>
                           <?php foreach ($table_list as $tablename) {
                               if(in_array($tablename, (array)$showtableonly)){
                               ?>
                               <option value="<?php echo $tablename; ?>" <?php echo $tname == $tablename?"selected":"";?>> <?php echo ucwords(str_replace('_',' ',$tablename)); ?></option>
                           <?php } ?>
                           <?php } ?>
                       </select>
                </div>
            </div>
      <div class="col-lg-2">
           <span class="form-label label label-lg label-warning float-left m-t-30 text-center"><span class="f-14">Total Records : <?php echo count((array)$transactionres); ?> </span></span>
      </div>
        </div>
  </div>
</div>

   <div class="row">
      <div class="col-lg-8">
        <div class="card" >
            <div class="card-header">
                <h5>Missing / Completion Transaction</h5>
                <div class="card-header-right">
                </div>
            </div>
            <div class="card-body" >
    <?php if(count((array)$transactionres) !=0){ $resultfieldcnt = array();?>
        <div class="row custom-scroll" style="height: 500px">
            <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
                <thead>
                    <tr><th scope="col" style="width: 40%;">Transaction Name </th>
                        <th scope="col" style="width: 60%;">Progress</th>
                    </tr>
                </thead>
                  <tbody>
                      <?php foreach($transactionres as $transaction){
                               $completioncnt = $completionper= 0;
                      $missingfieldary =$complitionfieldary = array();
                              foreach($transaction as $filedkey=>$transacti){
                        if(!in_array($filedkey, (array)$notcheckfield)){
                                  if($transacti != ""){
                                      $completioncnt++;
                          $resultfieldcnt[$filedkey]++;
                          $missingfieldary[] =$filedkey;
                                  }else{
                          $complitionfieldary[] =$filedkey;
                        }
                      }
                              }
                              $totalfiles = count((array)$transaction);
                              $completionper = ($completioncnt/$totalfiles)*100;
                       ?>
                          <tr id="custom-popup">
                              <td class="p-2">

                            <div class="align-middle">
                                    <span  alt="user" class="img-radius img-40 align-top m-r-10 custom_txt_round pt-1 f-12 text-center"><?php echo initials($transaction['display_name']); ?></span>
                                <div class="d-inline-block width73 text-primary">
                                    <h6 class="mb-0"><?php echo $transaction['display_name']; ?></h6>
                                </div>
                            </div>
                              </td>
                              <td class="p-2">
                                  <div class="bar multi-color" style="min-height:40px" onclick="NewMessageAdded('<?php echo $transaction['id']; ?>')" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button">
                                  <div class="color bg-success" style="width: <?php echo round($completionper); ?>%;background-color:#82bac7 !important; min-height:40px">Completion - <?php echo round($completionper); ?>%</div>
                                  <div class="color bg-success" style="width: <?php echo 100-round($completionper); ?>%;background-color:#cf9eb5 !important; min-height:40px">Missing - <?php echo 100-round($completionper); ?>%</div>

                              </div>

                  <div class="custom-popup-open cardpopup_<?php echo $transaction['id']; ?> text-start" style="left: 0px; top: 90px;">
                    <div class="card">
                        <div class="card-header">
                          <h5>Missing / Completion : <?php echo $transaction['display_name']; ?></h5>
                          <div class="card-header-right">
                            <button type="button" class="btn p-0 border-0 bg-transparent ti-close" onclick="closedPopupcompliance22()" data-bs-toggle="tooltip" data-placement="bottom" data-bs-original-title="Closed" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>
                          </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                              <div class="col-lg-6">
                                <div class="card">
                                  <div class="card-header">
                                    <h5>Completion</h5>
                                    <div class="card-header-right">
                                    <span class="form-label label label-lg label-primary"><?php echo count((array)$missingfieldary); ?></span>
                                </div>
                                  </div>
                                  <div class="card-body">
                                      <div class="w-b-w-s">
                                      <?php foreach($missingfieldary as $mfa){ ?>
                                          <span class="form-label label label-lg label-primary text-center m-1"><span class="f-10"><?php echo $mfa; ?></span></span>
                                      <?php } ?>
                                      </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="card">
                                  <div class="card-header">
                                      <h5>Missing</h5>
                                      <div class="card-header-right">
                                          <span class="form-label label label-lg label-danger"><?php echo count((array)$complitionfieldary); ?></span>
                                      </div>
                                    </div>
                                    <div class="card-body">
                                      <div class="w-b-w-s">
                                      <?php foreach($complitionfieldary as $mfa){ ?>
                                          <span class="form-label label label-lg label-danger text-center m-1"><span class="f-10"><?php echo $mfa; ?></span></span>
                                      <?php } ?>
                                    </div>
                                  </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                  </div>
                              </td>
                          </tr>
                      <?php } ?>
                  </tbody>
              </table>
        </div>
    <?php }else{?>
              <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Data </p>
                    <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                  </div>
            <?php  } ?>
          </div>
        </div>
        </div>
          <div class="col-lg-4">
            <div class="card" >
              <div class="card-header">
                  <h5>Missing Field Count</h5>
                  <div class="card-header-right">
                      <span class="form-label label label-lg label-primary"><?php echo count((array)$transactionres[0]); ?></span>
                  </div>
              </div>
               <?php if(count((array)$transactionres[0]) !=0){?>
              <div class="card-body custom-scroll" style="height: 530px">
                  <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter_2">
                    <thead>
                        <tr><th scope="col" style="width: 30%;">Field</th>
                            <th scope="col" style="width: 80%;">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  $totalfields = count((array)$transactionres);
                        foreach($transactionres[0] as $filedkey=>$fieldval){
            if(!in_array($filedkey, (array)$notcheckfield)){

                $completionperr = $totalfields != 0?($resultfieldcnt[$filedkey]/$totalfields)*100:0;
            ?>
            <tr>
              <td class="w-b-w-s p-2"><?php echo ucwords(str_replace('_',' ',$filedkey)); ?> </td>
              <td class="p-2">
                  <div class="bar multi-color" style="min-height:30px">
                      <div class="color bg-success" style="width: <?php echo round($completionperr); ?>%;background-color:#82bac7 !important; min-height:30px"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Completion"><?php echo round($completionperr); ?>%</div>
                      <div class="color bg-success" style="width: <?php echo 100-round($completionperr); ?>%;background-color:#cf9eb5 !important; min-height:30px"  data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Missing"><?php echo 100-round($completionperr); ?>%</div>
                  </div>
              </td>
            </tr>
          <?php } ?>
          <?php } ?>

                    </tbody>
                  </table>

              </div>
            <?php }else{ ?>
              <div class="card-px text-center py-20 mt-2">
                    <p class="text-muted f-16 fw-bold "><i class="ti-alert text-warning"></i> No Data </p>
                    <div class="text-center px-4">
                      <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                    </div>
                  </div>
            <?php } ?>

            </div>
          </div>
        </div>


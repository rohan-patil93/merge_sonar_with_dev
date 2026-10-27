<?php
$apiurl = API."?mode=getdebitandcreditnotes&list=Y";
$apires = callAPI($apiurl);
$result = json_decode($apires, 1);
$debit_notes = $result['credit_notes'];
$stausresult = ViewStatusData();
?>
<div class="card">
    <div class="card-header row">
        <h5 class="col-lg-6"> Credit Notes</h5>
        <div class="card-header-right table-card-header col-lg-6">
          <div class="row">
            <div class="col-lg-5">
              <div class="input-group input-group-button mb-0">
                  <input type="text" id="dynamicfilterhideshow" data-id="categoryfilter" data-type="div" class="form-control form-control-sm width1" placeholder="Search" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Search" aria-label="Search">
                  <span class="input-group-text addon-style">
                      <span class=""><span class="ti-search text-white mt-2"></span></span>
                  </span>
              </div>
            </div>
            <a href="index?option=COM_INSERT_CREDIT_NOTES"  class="btn btn-success mb-2 desktop-view col-lg-4">
              <i class="icofont icofont-plus icon-white "></i> Credit Notes</a>
            <a href="javascript:history.go(-1)" class="btn waves-effect waves-light col-lg-2">
              <i class="icofont icofont-reply"></i>
              Back
            </a>
          </div>
        </div>
      </div>
      <div class="card-body m-t-20">

          <div class="row">
            <?php if(count((array)$debit_notes) !=0){ foreach($debit_notes as $ri){ ?>
              <div class="col-lg-3" id="categoryfilter_2">
                  <div class="card">
                      <div class="card-header table-card-header">
                          <h5 class=""> Credit Note </h5>
                          <div class="card-header-right">
                            <span class="form-label label label-primary" style="color: <?php echo $stausresult[$ri['current_status']]['color']; ?>;    background:  <?php echo $stausresult[$ri['current_status']]['background_color']; ?>; border-color: <?php echo $stausresult[$ri['current_status']]['background_color']; ?>;"><?php echo $stausresult[$ri['current_status']]['name']; ?></span>
                          </div>
                      </div>
                      <div class="card-body">
                          <table class="table table-hover m-b-0" style="font-size:13px;">
                            <tbody>
                                  <tr>
                                    <th scope="col" class="p-2">Number</th>
                                    <td class="text-right p-2 "> <a href="index?option=COM_INSERT_CREDIT_NOTES&Rid=<?php echo deocdeId("id:".$ri['id']);?>" class="text-primary"><?php echo $ri['transaction_number']; ?></a></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Transaction Date</th>
                                    <td class="text-right p-2"><?php echo fromDate($ri['transaction_date']); ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Part Name</th>
                                    <td class="text-right p-2 w-b-w-s"><?php echo $ri['party_name']; ?></td>
                                  </tr>
                                  <tr>
                                    <th scope="col" class="p-2">Total</th>
                                    <td class="text-right p-2"><?php echo customeummarmonyformate($ri['total_amount']); ?></td>
                                </tr>
                                <tr>
                                    <th scope="col" class="p-2">Ref Number</th>
                                    <td class="text-right p-2 "> <?php echo $ri['ref_no']; ?></td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
            <?php } ?>
            <?php } else { ?>
              <div class="card-px text-center ">
                  <p class="text-muted f-16 fw-bold m-b-30"><i class="fa fa-warning"></i> No Debit Notes</p>
                  <div class="text-center px-4">
                    <img class="mw-100 mh-300px" alt="" src="assets/images/work.png" style="max-height: 300px!important;">
                  </div>
                </div>
            <?php } ?>
          </div>
      </div>
</div>

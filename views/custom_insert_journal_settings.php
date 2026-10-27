<?php
$_SESSION['apply_js']['multiselest'] = "1";
if(isset($_POST['submitjournal'])){
  $_POST['company_id'] = $_SESSION['company_id'];
  $_POST['user_id'] = $_SESSION['userid'];
  $url = API."?mode=insertjournals&method=post";
  $response1 = file_post_contents($url, $_POST, 'insert');
  $res = json_decode($response1,1);
  echo "<pre>"; print_r($res); echo "</pre>";
  $response = $res['res'];
  if($response['status'] == 'sucess'){
    if($response['LASTID'] == 0){
            $msgcode = 3001; //update
          }else{
            $msgcode = 3000; //insert
            $_SESSION['insert_msg'] = $msgcode;
          }
          ?>
          <script type="text/javascript">
            document.onreadystatechange = function(){
              if(document.readyState == "complete") {
                tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX','<?php echo $_SESSION['error_message'][$msgcode]; ?>','');
              }
            }
          </script>
          <?php
        }else{
          if(isset($_SESSION['error'][$response['error_no']])){
            $dis_err = explode(",", $response['other']);
            foreach ($dis_err as $va) {
              $dis_er [] = $dis_labels[$va];
            }
            $error_message = $response['error_no'] ." : ".$_SESSION['error'][$response['error_no']]." : ".implode(", ", (array)$dis_er);
          }else{
            $error_message = $response['message'];
          }
        }

      }

$rid =  encodeId($_GET['Rid']);
$rid = urldecode($rid);
$rid = explode(':', $rid);
$rid = $rid['1'];
$url1 = API."?mode=getjournals&rid=".$rid."&tt=".$_GET['TT'];
$data_details1 = callAPI($url1);
$result =  json_decode($data_details1,1);
$chart_of_accounts = $result['chart_of_accounts'];
$currencies = $result['currencies'];
$journal_settings = $result['journal_settings'];
$journal_details =  json_decode($journal_settings['journal_details'],1);
$dynamic_mapping_tables = $result['dynamic_mapping_tables'];
$selectcurrencies = array();
foreach($journal_details as $jvd){
  $selectcurrencies[$jvd['currency_id']] = $jvd['currency_id'];
}
?>
<script type="text/javascript">
  function selectJournalType(type){
    window.location.href = "index.php?option=COM_INSERT_JOURNALS&JT="+type;
      }
      function getsuspensecheck(id){
        if($("#provision_account_check_"+id).is(":checked")){
            $(".provision_account_check_"+id).show();
        }else{
            $(".provision_account_check_"+id).hide();
        }
      }
      function deletRow(id){
        $("#rowid_"+id).remove();
      }
      var mxid = 1000;
  function AddMoreJournarCourrency(){
      var html = "";
      mxid = mxid+1;
      html +='<div class="row" id="rowid_'+mxid+'">';

      html +='<div class="form-group col-lg-3"><select aria-label="Currency [" name="currency_id['+mxid+']" class="form-control form-select js-example-basic-single current_unique" onchange="changeCurrencies()"  id="currency_id_'+mxid+'"><option value="">Select Any One </option><?php foreach($currencies as $cur) {  ?> <option value="<?php echo $cur['id']; ?>" <?php echo $cur['id'] ==$journals['currency_id']?"selected":""; ?>><?php echo $cur['currency_name']."(".$cur['currency_short_code']." - ".$cur['currency_symbol'].")";?></option> <?php
} ?></select>  </div>';

    html +='<div class="form-group col-lg-3 w-full" > <select aria-label="Item Account[" name="item_account['+mxid+']" class="form-control form-select js-example-basic-single" ><option value="">Select Any One </option><?php foreach($chart_of_accounts as $coa) {  ?> <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code']==$journals['item_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option> <?php
} ?></select>  </div>';


    html +='<div class="form-group col-lg-2 w-full" > <input aria-label="Provision Account Check[" class="js-primary-sm-'+mxid+'" type="checkbox" name="provision_account_check['+mxid+']" id="provision_account_check_'+mxid+'" value="0" onchange="getsuspensecheck('+mxid+')"> </div>';
    html +='<div class="form-group col-lg-3 "> <div class="provision_account_check_'+mxid+' w-full "  style="display:none"> <select aria-label="Provision Account[" name="provision_account['+mxid+']" class="form-control form-select js-example-basic-single" > <option value="">Select Any One </option> <?php foreach($chart_of_accounts as $coa) {  ?> <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$journals['provision_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option> <?php
} ?> </select></div></div>';
    html +='<div class="form-group col-lg-1 text-center"><button type="button" class="btn p-0 border-0 bg-transparent ti-trash" onclick="deletRow('+mxid+')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></button>  </div> </div>';
      $("#addmorerows").append(html);
       $(".js-example-basic-single").select2();
    var elemdefault = document.querySelector('.js-primary-sm-'+mxid);
    var switchery = new Switchery(elemdefault, { color: '#4099ff', jackColor: '#fff',size: 'small' });
changeCurrencies();
    }
    function GetJournalType(){
      var transaction_table =  $("#transaction_table option:selected").text();
      var journal_name = $("#journal_name").val();
      if(journal_name ==""){
         $("#journal_name").val(transaction_table);
      }
    }

function changeCurrencies(){
  $('.current_unique').select2();
    var currentarry = [];
    $( ".current_unique" ).each(function( index, element ) {
      var selectedValue = $(this).val();
     currentarry.push(selectedValue);
    });

      console.log(currentarry);
    $( ".current_unique" ).each(function( index, element ) {
        var selectedValues = $(this).val();
        var thisid = $(this).attr('id');
        $("#"+thisid).find("option").prop("disabled", false);
        jQuery.each(currentarry, function( i, val ) {
            if(val == selectedValues){
              console.log(val+" -- "+ selectedValues +" "+thisid+" enable");

            }else{
               console.log(val+" -- "+ selectedValues +" "+thisid+" Disabled");
             $("#"+thisid).find("option[value='" + val + "']").prop("disabled", true);
            }
        });
    });
}

    </script>
    <div class="card">
      <div class="card-header">
        <h5>Journal Settings</h5>
        <div class="card-header-right table-card-header">
          <button type="button" class="btn waves-effect waves-light mb-2 desktop-view"><i class="icofont icofont-reply"></i>Back</button>
        </div>
      </div>
      <form method="post">
        <div class="card-body p-l-50 p-r-50">
          <div class="row">

            <div class="form-group col-lg-6">
              <span class="form-label">Journal Type <span class="text-danger">*</span></span>
              <select aria-label="Transaction Table" id="transaction_table" name="transaction_table" class="form-control form-select js-example-basic-single" onchange="GetJournalType(this.val)">
                <option value="">Select Any One </option>
                <?php foreach($dynamic_mapping_tables as $dmt) {  ?>
                  <option value="<?php echo $dmt['application_table_name']; ?>" <?php echo $dmt['application_table_name'] ==$journal_settings['transaction_table']?"selected":""; ?>><?php echo $dmt['page_title'];?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-6">
              <span class="form-label">Journal Entry <span class="text-danger">*</span></span>
              <div class="form-radio">
                <div class="radio radio-inline">
                    <label class="form-label">
                    <input aria-label="Journal Entry" type="radio" name="journal_entry" <?php echo ('A'==$journal_settings['journal_entry'] || $journal_settings['journal_entry'] =="")?"checked":""; ?> value="A">
                    <i class="helper"></i>Auto
                    </label>
                </div>
                <div class="radio radio-inline">
                  <label class="form-label">
                  <input aria-label="Journal Entry" type="radio" name="journal_entry" <?php echo 'M'==$journal_settings['journal_entry']?"checked":""; ?> value="M">
                  <i class="helper"></i>Manual
                  </label>
                </div>
              </div>
            </div>

          </div>
          <div class="row">
            <div class="form-group col-lg-3">
              <span class="form-label">Journal Name <span class="text-danger">*</span></span>
              <input aria-label="Journal Name" type="text" class="form-control text-capitalize" name="journal_name" id="journal_name" value="<?php echo $journal_settings['journal_name']; ?>" required>
            </div>
            <div class="form-group col-lg-3">
              <span class="form-label">Discount Account <span class="text-danger">*</span></span>
              <select aria-label="Discount Account" name="discount_account" class="form-control form-select js-example-basic-single">
                <option value="">Select Any One </option>
                 <?php foreach($chart_of_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$journal_settings['discount_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                 <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-3">
              <span class="form-label">Suspense Account <span class="text-danger">*</span></span>
              <select aria-label="Suspense Account" name="suspense_account" class="form-control form-select js-example-basic-single">
                <option value="">Select Any One </option>
                 <?php foreach($chart_of_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$journal_settings['suspense_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                 <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-3">
              <span class="form-label">Forex Difference account <span class="text-danger">*</span></span>
              <select aria-label="Forex Difference Account" name="forex_difference_account" class="form-control form-select js-example-basic-single">
                <option value="">Select Any One </option>
                 <?php foreach($chart_of_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$journal_settings['forex_difference_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                 <?php } ?>
              </select>
            </div>
          </div>
          <div class="row" >
           <div class="form-group col-lg-3">
             <span class="form-label">Currencies <span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-3 w-full" >
             <span class="form-label">Items account<span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-2 w-full" >
             <span class="form-label d-block">Provision Check<span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-3 w-full" >
             <span class="form-label d-block">Provision Account<span class="text-danger">*</span></span>
           </div>
           <div class="form-group col-lg-1 w-full" >
              <span class="form-label d-block"> Remove </span>
            </div>
        </div>
        <div id="addmorerows">
        <?php
        if(count((array)$journal_details) ==0){
          $journal_details[] = array();
        }
        $mk=1; foreach($journal_details as $jvd){ ?>
          <div class="row" id="rowid_<?php echo $mk; ?>">
            <div class="form-group col-lg-3">
              <select aria-label="Select option" name="currency_id[<?php echo $mk; ?>]" class="form-control form-select js-example-basic-single current_unique" onchange="changeCurrencies()" id="currency_id_<?php echo $mk; ?>">
                <option value="">Select Any One </option>
                <?php foreach($currencies as $cur) {  ?>
                  <option value="<?php echo $cur['id']; ?>" <?php echo $cur['id'] ==$jvd['currency_id']?"selected":""; ?>  <?php echo (isset($selectcurrencies[$cur['id']]) && $cur['id'] !=$jvd['currency_id'])?"disabled":"";  ?>><?php echo $cur['currency_name']."(".$cur['currency_short_code']." - ".$cur['currency_symbol'].")";?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-3 w-full" >
              <select aria-label="Select option" name="item_account[<?php echo $mk; ?>]" class="form-control form-select js-example-basic-single" >
                <option value="">Select Any One </option>
                <?php foreach($chart_of_accounts as $coa) {  ?>
                  <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code'] ==$jvd['item_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group col-lg-2 w-full" >
              <input aria-label="Checkbox" class="js-primary-sm" type="checkbox" name="provision_account_check[<?php echo $mk; ?>]" id="provision_account_check_<?php echo $mk; ?>" <?php echo ($jvd['provision_account_check']=='0')?"checked":"";?> value="0" onchange="getsuspensecheck('<?php echo $mk; ?>')">
            </div>
            <div class="form-group col-lg-3 ">
             <div class="provision_account_check_<?php echo $mk; ?> w-full " <?php echo ($jvd['provision_account_check']=='0')?'':'style="display: none"';?>>
               <select aria-label="Select option" name="provision_account[<?php echo $mk; ?>]" class="form-control form-select js-example-basic-single" >
                 <option value="">Select Any One </option>
                 <?php foreach($chart_of_accounts as $coa) {  ?>
                   <option value="<?php echo $coa['account_code']; ?>" <?php echo $coa['account_code']==$jvd['provision_account']?"selected":""; ?>><?php echo $coa['account_name']." (".$coa['account_code'].")";?></option>
                 <?php } ?>
               </select>
             </div>
           </div>
           <div class="form-group col-lg-1 text-center">
            <i class="ti-trash"  onclick="deletRow('<?php echo $mk; ?>')" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Remove" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}" role="button"></i>
          </div>
        </div>
        <?php $mk++;
} ?>
      </div>
        <div class="row">
          <div class="col-xs-12">
            <button class="btn btn-success" type="button" onclick="AddMoreJournarCourrency()">+ Add More</button>
          </div>
        </div>

        <div class="mt-3">
          <input type="hidden" name="rid" value="<?php echo $rid; ?>">
          <button type="submit" class="btn btn-primary waves-effect waves-light" name="submitjournal"><i class="fa fa-check m-r-10"></i> <?php echo $rid ==""?"Submit":"Update"; ?></button>
        </div>
      </div>
    </form>
  </div>

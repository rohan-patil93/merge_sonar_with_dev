<?php
if($auditrail_already_call !="Y"){
  $url = API."?mode=getaudittrails&user_profie_id=".isset($rid)&&$rid!=''?$rid:"";
  $apires = callAPI($url);
  $auditrails = json_decode($apires,1);
}
$audittrailview = $auditrails['audit_trails'];
$application_pages = $auditrails['application_pages'];
$listaudtitrails = array();
foreach($audittrailview as $audittrailvie){
    $listaudtitrails[date("Y-m-d",strtotime($audittrailvie['created_date']))][]= $audittrailvie;
}
?>
<script type="text/javascript">
    function filterapplay(type,reset=""){
    $("#type").val(type);
    if(type == 'action'){
        $('.login').prop('checked', false);
        $('.approval').prop('checked', false);
    }else if(type == 'approval'){
        $('.login').prop('checked', false);
        $('.action').prop('checked', false);
    }else{
        $('.approval').prop('checked', false);
        $('.action').prop('checked', false);
    }
    var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
    $("#formview").html(loader);
    if(reset !="Y"){
      var postarray = $("#filterfrom").serialize();
    }
    var user_profie_id = $("#user_profie_id").val();
    $.ajax({
        url: "views/custom_view_audittrails_table.php",
        type: "POST",
        data: "filter=Y&type="+type+"&user_profie_id="+user_profie_id+"&"+postarray,
        success: function(response){
            $("#formview").html(response);
        }
    });
}
function filterApplayDate(){
  var from_date = $("#from_date").val();
  var to_date = $("#to_date").val();
  var user_profie_id = $("#user_profie_id").val();
  if(from_date !="" && to_date !=""){
    var loader = '<div class="text-center" id="loader-show_2"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;" alt="Preview"></div>';
      $("#formview").html(loader);
      var postarray = $("#filterfrom").serialize();
      $.ajax({
          url: "views/custom_view_audittrails_table.php",
          type: "POST",
          data: "filter=Y&type="+type+"&user_profie_id="+user_profie_id+"&"+postarray,
          success: function(response){
              $("#formview").html(response);
          }
      });
  }
}
</script>
<?php echo $rid; ?>
<input type="hidden" name="user_profie_id" id="user_profie_id"
value="<?php echo isset($rid)&&$rid!=''?$rid:""; ?>">
 <div class="page-body">
  <div class="card">
    <div class="card-header">
      <h5>AuditTrails</h5>
      <div class="card-header-right">
       <a href="javascript:history.go(-1)" class="btn waves-effect waves-light mb-2 desktop-view">
        <i class="icofont icofont-reply"></i>
        Back
      </a>
    </div>
  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-lg-3">
          <form id="filterfrom">
        <input type="hidden" name="type" id="type" value="">
        <div class="bg-gray p-3 " >
          <h5 class="f-18 mb-3">Timeline</h5>
          <div class="form-group">
            <span class="form-label">From</span>
            <input aria-label="From Date" type="text" class="form-control datepicker" name="from_date" id="from_date" onchange="filterApplayDate()">
          </div>
          <div class="form-group">
            <span class="form-label">To</span>
            <input aria-label="To Date" type="text" class="form-control datepicker" name="to_date" id="to_date" onchange="filterApplayDate()">
          </div>
          <hr>

          <h6 class="f-w-600 f-16 mb-3">Actions</h6>
          <div class="border-checkbox-section">
            <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input class="border-checkbox action" type="checkbox" name="action_type[]" id="edited" value="UPDATE" onchange="filterapplay('action')">
              <label class="form-label border-checkbox-label text-muted" for="edited">Update</label>
            </div>
            <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input class="border-checkbox action" type="checkbox" name="action_type[]" id="added" value="INSERT" onchange="filterapplay('action')">
              <label class="form-label border-checkbox-label text-muted" for="added">Insert</label>
            </div>
            <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input class="border-checkbox action" type="checkbox" name="action_type[]" id="deleted" value="DELETE" onchange="filterapplay('action')">
              <label class="form-label border-checkbox-label text-muted" for="deleted">Deleted</label>
            </div>
             <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input class="border-checkbox action" type="checkbox"  name="action_type[]" id="view" value="VIEW" onchange="filterapplay('action')">
              <label class="form-label border-checkbox-label text-muted" for="view">View</label>
            </div>
          </div>
          <hr>
          <h6 class="f-w-600 f-16 mb-3">Approvals</h6>
          <div class="border-checkbox-section custom-scroll" style="height: 200px;">
            <?php foreach($application_pages  as $applicationpages){ ?>
            <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input aria-label="Action Type[]" class="border-checkbox approval" type="checkbox" name="action_type[]" value="<?php echo $applicationpages['application_table_name']; ?>" onchange="filterapplay('approval')" id="view-<?php echo $applicationpages['application_table_name']; ?>">
              <label class="form-label border-checkbox-label text-muted" for="view-<?php echo $applicationpages['application_table_name']; ?>"><?php echo viewTitle($applicationpages['application_table_name']); ?></label>
            </div>
          <?php } ?>

          </div>
          <hr>
          <h6 class="f-w-600 f-16 mb-3">Account Access</h6>
          <div class="border-checkbox-section">
            <div class="border-checkbox-group border-checkbox-group-primary d-block">
              <input class="border-checkbox login" type="checkbox" id="logins" name="action_type" value="login" onchange="filterapplay('login')">
              <label class="form-label border-checkbox-label text-muted" for="logins" >Logins</label>
            </div>
          </div>
          <button class="btn btn-danger waves-effect waves-light mt-3" >Reset</button>
        </div>
        </form>
      </div>
      <div class="col-lg-9 custom-scroll pt-3" id="formview" style="height: 800px">
              <?php include_once 'views/custom_view_audittrails_table.php'; ?>
      </div>
    </div>
  </div>
</div>
</div>

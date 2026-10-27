<?php
// visitor_out.php
session_start();

if(isset($_POST['submit'])){
	if($_POST['submit'] == 'update'){
		$data = $_POST;
	    $rid = $data['rid'];
	    $data['updated_date'] = date('Y-m-d H:i:s');
	    $data['updated_by'] = $_SESSION['userid'];
	    $data['current_status'] = 199;
	    unset($data['rid'], $data['submit']);
	    $url = API . "?mode=updaterowtabledata&rid=".$rid."&table=visitor_logs&method=post&fid=id";
	    $response = file_post_contents($url, $data, 'update');
	    $data1 = json_decode($response,1);
	    // print_r($data1);
	    if($data1['status']=='sucess'){
	    	$message = 'Successfully Data Updated';
	    }

    }
}

?>

<?php if (!empty($message)): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  tostarnotify('top','right','fa fa-thumbs-up','form-label label label-success','animated flipInX','animated flipOutX', <?= json_encode($message) ?>, '');
});
</script>
<?php endif; ?>

<div class="card">
    <div class="continer-fluid">
        <div class="card-header">
            <h3 class="mb-3">Visitor Out</h3>

        </div>
        <div class="card-body">
            <!-- Search -->
            <div id="searchForm" class="row g-2 mb-3">
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="visitorId"
                        name="visitor_id" placeholder="Enter Visitor ID" required>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" type="button" onclick="SerachVisitor()">Search</button>
                </div>
                <div class="col-auto">
                    <div id="searchMsg" class="text-muted small"></div>
                </div>
            </div>

            <!-- Result container (filled by visitor_view.php) -->
            <div id="result1">
            </div>
        </div>
    </div>

</div>

<script>
    function showMsg(el, msg, cls) {
        $(el).removeClass('text-success text-danger text-muted').addClass(cls || 'text-muted').text(msg || '');
    }
    // GET -> visitor_view.php?id=...
    function SerachVisitor() {
        const id = $('#visitorId').val().trim();
        if (!id) {
            tostarnotify('top', 'right', 'fa fa-thumbs-down', 'form-label label label-danger', 'animated flipInX', 'animated flipOutX', 'Please enter a visitor id.', '');
            return;
        }
        var loader = '<div class="text-center" id="loader-show"><img src="assets/images/spiner/spiner3.gif" style="width: 300px;"></div>';
        $("#result1").html(loader);

        $.ajax({
            url: 'views/visitor_out_view.php',
            type: "GET",
            data: {
                id: id,
            },
            success: function(response) {
                $('#result1').html(response);
            },
            error: function(error) {
                console.error(`Failed to load ${action} page:`, error);
                alert("Failed to load the page. Please try again.");
            },
        });

    };
</script>
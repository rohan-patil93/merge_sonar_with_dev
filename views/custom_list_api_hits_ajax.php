<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
$api_master_id = $_POST['api_master_id'];
$url = API . "?mode=getapihits&api_master_id=".$api_master_id;
$response = callAPI($url);
$result = json_decode($response, 1);
$api_hits = $result['api_hits'];
$api_masters = $result['api_masters'];
$api_code = $api_masters['api_code'];
?>
<div class="row">
    <div class="col-3 text-center">
        <div class="s-cont d-inline-block">
            <span class="f-w-700 m-b-0">Provider Name</span>
            <p class="m-b-0 f-10"> <?php echo $api_masters['api_service_provider']; ?> </p>
        </div>
    </div>
    <div class="col-3 b-l-default text-center">
        <div class="s-cont d-inline-block">
            <span class="f-w-700 m-b-0">API Name</span>
            <p class="m-b-0 f-10"> <?php echo $api_masters['api_name']; ?> </p>
        </div>
    </div>
    <div class="col-2 b-l-default text-center">
        <div class="s-cont d-inline-block">
            <span class="f-w-700 m-b-0">Method Type</span>
            <p class="m-b-0 f-10"> <?php echo $api_masters['api_method']; ?></p>
        </div>
    </div>
    <div class="col-2 b-l-default text-center">
        <div class="s-cont d-inline-block">
            <span class="f-w-700 m-b-0">Hit API</span>
            <p class="m-b-0 f-10"> <?php echo count((array)$api_hits); ?> </p>
        </div>
    </div>
    <div class="col-2 b-l-default text-center">
        <div class="s-cont d-inline-block">
            <span class="f-w-700 m-b-0">Sync Response</span>
            <p class="m-b-0 f-10">  - </p>
        </div>
    </div>
</div>
<table class="table table-hover m-b-0" style="font-size:13px;" id="costcenterfilter">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Requst Date</th>
            <th scope="col">Response Status Code</th>
            <th scope="col">Sync API Response</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if(count((array)$api_hits) != 0){
             $im=1; foreach($api_hits as $apihit){ ?>
        <tr class="<?php echo $apihit['response_status'] =="200"?"":"bg-danger"; ?>">
            <td class="p-2">
                <?php echo $im; ?>
            </td>
            <td class="p-2">
                <?php echo $apihit['request_date_time']; ?>
            </td>
            <td class="p-2"> <?php echo $apihit['response_status']; ?></td>
            <td class="p-2">
                No
            </td>
            <td class="p-2 text-center">
                <?php if($apihit['response_status'] =="200"){ ?>
                <i class="ti-server cursor-pointer" data-bs-toggle="tooltip" data-placement="bottom" title="" data-bs-original-title="Sync API Response to Database" onclick="syncAPIResponse('<?php echo $apihit['id']; ?>')" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"></i>
                <?php } ?>
            </td>
        </tr>
        <?php $im++;
}
}else{ ?>
            <tr class="" >
                <td colspan="6" class="text-center">
                    <i class="ti-alert text-danger"></i>  No API Hits
                </td>
            </tr>

        <?php } ?>
    </tbody>
</table>

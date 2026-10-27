<?php
// Mobile User Status Dashboard

$url = API . "?mode=getUserMobileStatusDashboard&company_id=" . $_SESSION['company_id'];
$response = callAPI($url);
$api_data = json_decode($response, true);

// Initialize variables
$all_users = array();
$mobile_user_ids = array();
$total_users = 0;
$mobile_users_count = 0;
$needs_mobile_count = 0;

if ($api_data && isset($api_data['status']) && $api_data['status'] === 'success') {
    $data = $api_data['data'];
    $all_users = $data['all_users'] ?? array();
    $mobile_user_ids = $data['mobile_user_ids'] ?? array();
    $total_users = $data['total_users'] ?? 0;
    $mobile_users_count = $data['mobile_users_count'] ?? 0;
    $needs_mobile_count = $data['needs_mobile_count'] ?? 0;
}

$mobile_user_ids_json = htmlspecialchars(json_encode($mobile_user_ids), ENT_QUOTES, 'UTF-8');
$all_users_json = htmlspecialchars(json_encode($all_users), ENT_QUOTES, 'UTF-8');
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Mobile User Status Dashboard</h2>
        </div>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row mb-4" id="statsCardsRow">
        <!-- Card 1: All Users -->
        <div class="col-md-4 mb-3">
            <div class="card stats-card cursor-pointer" data-action="show-all-users" data-card-type="all-users" style="height: 100%;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 fw-500">All Users</p>
                            <h3 class="mb-0 fw-bold text-primary"><?php echo $total_users; ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-users fa-3x text-primary opacity-25"></i>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 small">
                        <i class="fas fa-info-circle"></i> Click to view all users
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 2: Mobile Logged-In Users -->
        <div class="col-md-4 mb-3">
            <div class="card stats-card cursor-pointer" data-action="show-mobile-users" data-card-type="mobile-users" style="height: 100%;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 fw-500">Mobile Logged-In Users</p>
                            <h3 class="mb-0 fw-bold text-success"><?php echo $mobile_users_count; ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-mobile-alt fa-3x text-success opacity-25"></i>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 small">
                        <i class="fas fa-check-circle"></i> Already using mobile app
                    </p>
                </div>
            </div>
        </div>

        <!-- Card 3: Need to Login Mobile -->
        <div class="col-md-4 mb-3">
            <div class="card stats-card cursor-pointer" data-action="show-needs-mobile" data-card-type="needs-mobile" style="height: 100%;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted mb-2 fw-500">Need to Login Mobile</p>
                            <h3 class="mb-0 fw-bold text-warning"><?php echo $needs_mobile_count; ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning opacity-25"></i>
                        </div>
                    </div>
                    <p class="text-muted mt-3 mb-0 small">
                        <i class="fas fa-arrow-right"></i> Need to activate on mobile
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- User Details Table Container -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light p-3">
                    <h5 class="mb-0" id="tableTitle">User Details</h5>
                </div>
                <div class="card-body p-0 custom-scroll" id="userDetailsTable" style="height: 400px; overflow-y: auto;">
                    <p class="text-center text-muted p-4">
                        <i class="fas fa-arrow-up"></i> Click on a card above to view user details
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden data containers for JavaScript -->
<div id="mobileUserIdsData" data-mobile-ids='<?php echo $mobile_user_ids_json; ?>'></div>
<div id="allUsersData" data-all-users='<?php echo $all_users_json; ?>'></div>

<link rel="stylesheet" href="assets/css/custom_mobile_user_status.css">
<script src="assets/js/custom_mobile_user_status.js"></script>

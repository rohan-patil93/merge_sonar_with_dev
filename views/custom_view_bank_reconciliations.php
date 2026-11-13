<?php
$_SESSION['apply_js']['multiselest'] = "1";
?>
<style>
    .match{
        background: #9bc5f5bd;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .unmatched{
        background: #e51d1d59;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .partial{
        background: #ffc10752;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .suggestions{
        background: #1987545c;
        padding: 3px 7px;
        border-radius: 5px;
        text-transform: capitalize;
    }
    .active-card {
        outline: 3px solid #ff0000;
        transform: scale(1.02);
        transition: 0.2s;
    }
</style>
<div class="card card-custom card-sticky" id="kt_page_sticky_card">
    <div class="card-header table-card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?= __('Bank Reconciliation'); ?></h5>
        <div class="card-header-right">
            <a href="javascript:history.go(-1)" class="btn btn-light-primary waves-effect waves-light m-r-50">
                <i class="icofont icofont-reply mr-1"></i>
                <?= __('Back'); ?>
            </a>
        </div>
    </div>
    <div class="card-body p-l-40 p-r-40">
        <div class="row mt-3">
            <div class="col-lg-2">
                <div class="card bg-info text-center p-3 filter-card active-card" data-status="all">
                    <h2>8</h2>
                    <p class="mb-0 f-16"><?= __('Total Transactions'); ?></p>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card bg-primary text-center p-3 filter-card" data-status="match">
                    <h2>2</h2>
                    <p class="mb-0 f-16"><?= __('Matched'); ?></p>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card bg-dark text-center p-3 text-white filter-card" data-status="unmatched">
                    <h2>2</h2>
                    <p class="mb-0 f-16"><?= __('Unmatched'); ?></p>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card bg-warning text-center p-3 filter-card" data-status="partial">
                    <h2>2</h2>
                    <p class="mb-0 f-16"><?= __('Partial Matches'); ?></p>
                </div>
            </div>
            <div class="col-lg-2">
                <div class="card bg-success text-center p-3 filter-card" data-status="suggestions">
                    <h2>2</h2>
                    <p class="mb-0 f-16"><?= __('Suggestions'); ?></p>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <h5 class="mb-2 f-w-600"><?= __('ERP Ledger Entries'); ?></h5>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-bordered data-table">
                        <thead>
                            <tr style="background-color: #eeeeee;">
                                <th scope="col"><?= __('Date'); ?></th>
                                <th scope="col"><?= __('Description'); ?></th>
                                <th scope="col"><?= __('Debit'); ?></th>
                                <th scope="col"><?= __('Credit'); ?></th>
                                <th scope="col"><?= __('Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-10-01</td>
                                <td><?= __('Invoice #1001'); ?></td>
                                <td>5000</td>
                                <td></td>
                                <td><span class="match f-12"><?= __('Match'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-01</td>
                                <td><?= __('Invoice #1001'); ?></td>
                                <td>5000</td>
                                <td></td>
                                <td><span class="match f-12"><?= __('Match'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-02</td>
                                <td><?= __('Payment to Vendor'); ?></td>
                                <td></td>
                                <td>2000</td>
                                <td><span class="unmatched f-12"><?= __('Unmatched'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-03</td>
                                <td><?= __('Bank Charges'); ?></td>
                                <td></td>
                                <td>50</td>
                                <td><span class="partial f-12"><?= __('Partial'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-04</td>
                                <td><?= __('Invoice #1002'); ?></td>
                                <td>7000</td>
                                <td></td>
                                <td><span class="match f-12"><?= __('Match'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-05</td>
                                <td><?= __('Payment Refund'); ?></td>
                                <td></td>
                                <td>3000</td>
                                <td><span class="unmatched f-12"><?= __('Unmatched'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-06</td>
                                <td><?= __('Charges'); ?></td>
                                <td></td>
                                <td>50</td>
                                <td><span class="suggestions f-12"><?= __('Suggestions'); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="mb-2 f-w-600"><?= __('Bank Statement Entries'); ?></h5>
                <div class="table-responsive mt-2">
                    <table class="table table-sm table-bordered data-table">
                        <thead>
                            <tr style="background-color: #eeeeee;">
                                <th scope="col"><?= __('Date'); ?></th>
                                <th scope="col"><?= __('Description'); ?></th>
                                <th scope="col"><?= __('Debit'); ?></th>
                                <th scope="col"><?= __('Credit'); ?></th>
                                <th scope="col"><?= __('Status'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>2025-10-01</td>
                                <td><?= __('Payment Received - INV1001'); ?></td>
                                <td>5000</td>
                                <td></td>
                                <td><span class="match f-12"><?= __('Match'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-02</td>
                                <td><?= __('ATM Withdrawal'); ?></td>
                                <td></td>
                                <td>2000</td>
                                <td><span class="unmatched f-12"><?= __('Unmatched'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-03</td>
                                <td><?= __('Bank Charges'); ?></td>
                                <td></td>
                                <td>50</td>
                                <td><span class="partial f-12"><?= __('Partial'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-03</td>
                                <td><?= __('Bank Charges'); ?></td>
                                <td></td>
                                <td>50</td>
                                <td><span class="partial f-12"><?= __('Partial'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-04</td>
                                <td><?= __('Payment Received - INV1002'); ?></td>
                                <td>7000</td>
                                <td></td>
                                <td><span class="match f-12"><?= __('Match'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-05</td>
                                <td><?= __('ATM Deposit'); ?></td>
                                <td></td>
                                <td>3000</td>
                                <td><span class="unmatched f-12"><?= __('Unmatched'); ?></span></td>
                            </tr>
                            <tr>
                                <td>2025-10-06</td>
                                <td><?= __('Misc Suggestion'); ?></td>
                                <td></td>
                                <td>50</td>
                                <td><span class="suggestions f-12"><?= __('Suggestions'); ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
    </div>
</div>

<script>
    const cards = document.querySelectorAll(".filter-card");
    const tables = document.querySelectorAll(".data-table tbody tr");

    cards.forEach(card => {
      card.addEventListener("click", () => {
        const status = card.getAttribute("data-status").toLowerCase();

        tables.forEach(row => {
          const span = row.querySelector("span");
          if (!span) return;

          const rowStatus = span.classList[0].toLowerCase();

          if (status === "all" || rowStatus === status) {
            row.style.display = "";
          } else {
            row.style.display = "none";
          }
        });

        // Optional: highlight selected card
        cards.forEach(c => c.classList.remove("active-card"));
        card.classList.add("active-card");
      });
    });
</script>

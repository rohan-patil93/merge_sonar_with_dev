<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content"
    style="margin-top: 20px;">
    

        
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title">
                                        <?= __('Total Income'); ?>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                        <?= __('Total Acounts Receivable'); ?>
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-brand">
                                    <?= __('₹ 18 Lac'); ?>
                                </span>
                            </div>
                            <div class="progress progress--sm">
                                <progress class="progress-bar kt-bg-brand" value="78%" max="100" style="width: 78%;"></progress>
                            </div>
                            <div class="kt-widget24__action">
                                <span class="kt-widget24__change">
                                    <?= __('Due'); ?>
                                </span>
                                <span class="kt-widget24__number">
                                    78%
                                </span>
                            </div>
                        </div>

                        
                        <div class="kt-widget24">
                            <div class="kt-widget24__details">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title">
                                        <?= __('Total Expenses'); ?>
                                    </h4>
                                    <span class="kt-widget24__desc">
                                        <?= __('Total Accounts Payable'); ?>
                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-warning">
                                    <?= __('₹ 13 Lac'); ?>
                                </span>
                            </div>
                            <div class="progress progress--sm">
                                <progress class="progress-bar kt-bg-warning" value="84%" max="100" style="width: 84%;"></progress>
                            </div>
                            <div class="kt-widget24__action">
                                <span class="kt-widget24__change">
                                    <?= __('Due'); ?>
                                </span>
                                <span class="kt-widget24__number">
                                    84%
                                </span>
                            </div>
                        </div>

                        
                        <div class="kt-widget1">
                            <div class="kt-widget1__item">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title"><?= __('Equity Ratio'); ?> </h3>
                                </div>
                                <span class="kt-widget1__number kt-font-brand">+$17,800</span>
                            </div>
                            <div class="kt-widget1__item">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title"><?= __('Debt Equity'); ?></h3>
                                </div>
                                <span class="kt-widget1__number kt-font-danger">+1,800</span>
                            </div>
                            <div class="kt-widget1__item">
                                <div class="kt-widget1__info">
                                    <h3 class="kt-widget1__title"><?= __('Current Ratio'); ?></h3>
                                </div>
                                <span class="kt-widget1__number kt-font-success">-27,49%</span>
                            </div>
                        </div>

                        
                        <div class="kt-widget24">
                            <div class="kt-widget24__details" style="margin-top: 40px;">
                                <div class="kt-widget24__info">
                                    <h4 class="kt-widget24__title">
                                        <?= __('Current Ratio'); ?>
                                    </h4>
                                    <span class="kt-widget24__desc">

                                    </span>
                                </div>
                                <span class="kt-widget24__stats kt-font-success">
                                    33%
                                </span>
                            </div>

                        </div>

                        
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-widget14">
                        <div class="kt-widget14__header kt-margin-b-30">
                            <h3 class="kt-widget14__title">
                                <?= __('Daily Sales'); ?>
                            </h3>
                            <span class="kt-widget14__desc">
                                <?= __('Check out each collumn for more details'); ?>
                            </span>
                        </div>
                        <div class="kt-widget14__chart" style="height:120px;">
                            <canvas id="kt_chart_daily_sales"></canvas>
                        </div>
                    </div>
                </div>

                
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-widget14">
                        <div class="kt-widget14__header">
                            <h3 class="kt-widget14__title">
                                <?= __('Profit Share'); ?>
                            </h3>
                            <span class="kt-widget14__desc">
                                <?= __('Profit Share between customers'); ?>
                            </span>
                        </div>
                        <div class="kt-widget14__content">
                            <div class="kt-widget14__chart">
                                <div class="kt-widget14__stat">45</div>
                                <canvas id="kt_chart_profit_share" style="height: 140px; width: 140px;"></canvas>
                            </div>
                            <div class="kt-widget14__legends">
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-success"></span>
                                    <span class="kt-widget14__stats">37% Sport Tickets</span>
                                </div>
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-warning"></span>
                                    <span class="kt-widget14__stats">47% Business Events</span>
                                </div>
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-brand"></span>
                                    <span class="kt-widget14__stats">19% Others</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-widget14">
                        <div class="kt-widget14__header">
                            <h3 class="kt-widget14__title">
                                <?= __('Revenue Change'); ?>
                            </h3>
                            <span class="kt-widget14__desc">
                                <?= __('Revenue change breakdown by cities'); ?>
                            </span>
                        </div>
                        <div class="kt-widget14__content">
                            <div class="kt-widget14__chart">
                                <div id="kt_chart_revenue_change" style="height: 150px; width: 150px;"></div>
                            </div>
                            <div class="kt-widget14__legends">
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-success"></span>
                                    <span class="kt-widget14__stats">+10% New York</span>
                                </div>
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-warning"></span>
                                    <span class="kt-widget14__stats">-7% London</span>
                                </div>
                                <div class="kt-widget14__legend">
                                    <span class="kt-widget14__bullet kt-bg-brand"></span>
                                    <span class="kt-widget14__stats">+20% California</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?= __('Current Month'); ?>
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="#" class="btn btn-label-brand btn-bold btn-sm dropdown-toggle"
                                data-toggle="dropdown">
                                <?= __('Sales Reports'); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-fit dropdown-menu-right">

                                
                            </div>
                        </div>
                    </div>

                    <div class="kt-portlet__body">

                        
                            <div class="tab-pane active" id="kt_widget11_tab1_content">

                                
                            </div>

                            
                                <div class="kt-widget11">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col" style="width:1%">#</th>
                                                    <th scope="col" style="width:40%"><?= __('Application'); ?></th>
                                                    <th scope="col" style="width:14%"><?= __('Sales'); ?></th>
                                                    <th scope="col" style="width:15%"><?= __('Change'); ?></th>
                                                    <th scope="col" style="width:15%"><?= __('Status'); ?></th>
                                                    <th scope="col" style="width:15%" class="kt-align-right">
                                                        <?= __('Total'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <label
                                                            aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>"
                                                            class="kt-checkbox kt-checkbox--single">
                                                            <input type="checkbox"><span></span>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <span class="kt-widget11__title"><?= __('Loop'); ?></span>
                                                        <span class="kt-widget11__sub"><?= __('CRM System'); ?></span>
                                                    </td>
                                                    <td>19,200</td>
                                                    <td>$63</td>
                                                    <td><span
                                                            class="kt-badge kt-badge--inline kt-badge--danger"><?= __('pending'); ?></span>
                                                    </td>
                                                    <td class="kt-align-right kt-font-brand  kt-font-bold">$23,740</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label
                                                            aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>"
                                                            class="kt-checkbox kt-checkbox--single"><input
                                                                type="checkbox"><span></span></label>
                                                    </td>
                                                    <td>
                                                        <span class="kt-widget11__title"><?= __('Selto'); ?></span>
                                                        <span
                                                            class="kt-widget11__sub"><?= __('Powerful Website Builder'); ?></span>
                                                    </td>
                                                    <td>24,310</td>
                                                    <td>$39</td>
                                                    <td><span
                                                            class="kt-badge kt-badge--inline kt-badge--success"><?= __('new'); ?></span>
                                                    </td>
                                                    <td class="kt-align-right kt-font-success  kt-font-bold">$46,010
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label
                                                            aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>"
                                                            class="kt-checkbox kt-checkbox--single"><input
                                                                type="checkbox"><span></span></label>
                                                    </td>
                                                    <td>
                                                        <span class="kt-widget11__title"><?= __('Jippo'); ?></span>
                                                        <span
                                                            class="kt-widget11__sub"><?= __('The Best Selling App'); ?></span>
                                                    </td>
                                                    <td>9,076</td>
                                                    <td>$105</td>
                                                    <td><span
                                                            class="kt-badge kt-badge--inline kt-badge--brand"><?= __('approved'); ?></span>
                                                    </td>
                                                    <td class="kt-align-right kt-font-danger kt-font-bold">$15,800</td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <label
                                                            aria-label="<?= htmlspecialchars(__('Select row'), ENT_QUOTES, 'UTF-8'); ?>"
                                                            class="kt-checkbox kt-checkbox--single"><input
                                                                type="checkbox"><span></span></label>
                                                    </td>
                                                    <td>
                                                        <span class="kt-widget11__title"><?= __('Verto'); ?></span>
                                                        <span
                                                            class="kt-widget11__sub"><?= __('Web Development Tool'); ?></span>
                                                    </td>
                                                    <td>11,094</td>
                                                    <td>$16</td>
                                                    <td><span
                                                            class="kt-badge kt-badge--inline kt-badge--info"><?= __('done'); ?></span>
                                                    </td>
                                                    <td class="kt-align-right kt-font-warning kt-font-bold">$8,520</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="kt-widget11__action kt-align-right">
                                        <button type="button"
                                            class="btn btn-label-success btn-bold btn-sm"><?= __('Generate Report1'); ?></button>
                                    </div>
                                </div>

                                
                        </div>

                        
                <div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?= __('MIS'); ?>
                            </h3>

                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-brand">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#kt_widget11_tab1_content"
                                        role="tab">
                                        <?= __('YTD'); ?>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
                                        <?= __('QTD'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
                                        <?= __('MTD'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fluid">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <div class="kt-widget12__item">
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"> <?= __('Taxes'); ?></span>
                                        <span class="kt-widget12__value">$400,000</span>
                                    </div>
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"><?= __('Finance Review Date'); ?></span>
                                        <span class="kt-widget12__value"><?= __('July 24,2019'); ?></span>
                                    </div>
                                </div>
                                <div class="kt-widget12__item">
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"> <?= __('Revenue'); ?></span>
                                        <span class="kt-widget12__value">$60M</span>
                                    </div>
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"><?= __('Revenue Margin'); ?></span>
                                        <div class="kt-widget12__progress">
                                            <div class="progress kt-progress--sm">
                                                <progress class="progress-bar kt-bg-brand" value="40%" max="100" style="width: 40%;"></progress>
                                            </div>
                                            <span class="kt-widget12__stat">
                                                40%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-widget12__chart" style="height:250px;">
                                <canvas id="kt_chart_order_statistics"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?= __('Cash'); ?>
                            </h3>

                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-tabs nav-tabs-line nav-tabs-bold nav-tabs-line-brand">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#kt_widget11_tab1_content"
                                        role="tab">
                                        <?= __('YTD'); ?>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
                                        <?= __('QTD'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#kt_widget11_tab2_content" role="tab">
                                        <?= __('MTD'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body kt-portlet__body--fluid">
                        <div class="kt-widget12">
                            <div class="kt-widget12__content">
                                <div class="kt-widget12__item">
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"> <?= __('Taxes'); ?></span>
                                        <span class="kt-widget12__value">$400,000</span>
                                    </div>
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"><?= __('Finance Review Date'); ?></span>
                                        <span class="kt-widget12__value"><?= __('July 24,2019'); ?></span>
                                    </div>
                                </div>
                                <div class="kt-widget12__item">
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"> <?= __('Revenue'); ?></span>
                                        <span class="kt-widget12__value">$60M</span>
                                    </div>
                                    <div class="kt-widget12__info">
                                        <span class="kt-widget12__desc"><?= __('Revenue Margin'); ?></span>
                                        <div class="kt-widget12__progress">
                                            <div class="progress kt-progress--sm">
                                                <progress class="progress-bar kt-bg-brand" value="40%" max="100" style="width: 40%;"></progress>
                                            </div>
                                            <span class="kt-widget12__stat">
                                                40%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="kt-portlet__body">
                                <div id="kt_amcharts_3" style="height: 250px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?= __('Recent Activities'); ?>
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <div class="dropdown dropdown-inline">
                                <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="flaticon-more-1"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-fit dropdown-menu-md">

                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="kt-portlet__body">

                        
                    </div>
                </div>

                
                <div class="kt-portlet kt-portlet--height-fluid">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                <?= __('Recent Notifications'); ?>
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <ul class="nav nav-pills nav-pills-sm nav-pills-label nav-pills-bold">
                                <li class="nav-item">
                                    <a class="nav-link active" data-toggle="tab" href="#kt_widget3_tab1_content"
                                        role="tab">
                                        <?= __('Today'); ?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-toggle="tab" href="#kt_widget3_tab2_content" role="tab">
                                        <?= __('Month'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="kt-portlet__body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="kt_widget3_tab1_content">

                                
                            </div>
                            <div class="tab-pane" id="kt_widget3_tab2_content">

                                
                            </div>
                        </div>
                    </div>
                </div>

                <!--End::Portlet-->
            </div>
        </div>

        <!--End::Row-->
        <!--End::Dashboard 6-->
    </div>

    <!-- end:: Content -->
</div>

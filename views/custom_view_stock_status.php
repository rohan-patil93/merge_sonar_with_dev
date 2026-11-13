<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
                                <div class="card card-custom">
                                    <div class="card-header flex-wrap row border-0 pt-6 pb-0">
                                        <div class="col-4">
                                            <img class="mt-3" src="uploads/all/<?php echo $_SESSION['config']['display_logo'];?>" title="" alt="<?= htmlspecialchars(__('Infisuite Logo'), ENT_QUOTES, 'UTF-8'); ?>" width="100px" />
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h3 class="card-label mt-3"><?= __('Stock Status'); ?>

                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <h5><span class="text-muted pt-2 font-size-md d-block float-right"><?= __('Report Taken by : A001-NARESH'); ?></span></h5>
                                            <h5><span class="text-muted pt-2 font-size-md d-block float-right"><?= __('Report Taken Time : 20-2-2021 : 14:00'); ?></span></h5>
                                        </div>

                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4"><?= __('List Item'); ?></span>
                                                <select aria-label="<?= htmlspecialchars(__('Example Selectl'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control form-control-lg col-8" id="exampleSelectl">
                                                    <option><?= __('List Item'); ?></option>
                                                    <option><?= __('List Item'); ?></option>
                                                    <option><?= __('List Item'); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4"><?= __('From Date'); ?></span>
                                                <div class="col-8">
                                                    <input aria-label="<?= htmlspecialchars(__('Example Date Input'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" value="2021-08-19" id="example-date-input-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4"><?= __('To Date'); ?></span>
                                                <div class="col-8">
                                                    <input aria-label="<?= htmlspecialchars(__('Example Date Input'), ENT_QUOTES, 'UTF-8'); ?>" class="form-control" type="date" value="2021-08-19" id="example-date-input-2">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <button type="reset" class="btn btn-success mr-2"><?= __('Submit'); ?></button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="datatable datatable-bordered datatable-head-custom datatable-default datatable-loaded stock-status-table" style="display: block;">
                                            <table class="datatable-table">
                                                <thead class="datatable-head">
                                                    <tr class="datatable-row">
                                                        <th scope="col" class="datatable-cell" style="width: 14%;">
                                                            <span><?= __('Item Details'); ?></span>
                                                        </th>
                                                        <th scope="col" class="datatable-cell" style="width: 6%;"><span><?= __('Unit'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Op. Qty'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 8%;" class="datatable-cell"><span><?= __('Price'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 8%;" class="datatable-cell"><span><?= __('Op. Amount'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Qty. In'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 8%;" class="datatable-cell"><span><?= __('Avg. Price'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Amt. In'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Qty. Out'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 8%;" class="datatable-cell"><span><?= __('Avg. Price'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Amt. Out'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Cl. Qty'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Price'); ?></span></th>
                                                        <th scope="col" style="text-align: right; width: 6%;" class="datatable-cell"><span><?= __('Cl. Amt.'); ?></span></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="datatable-body">
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 14%;">
                                                            <?= __('Plastic Round Table'); ?>
                                                        </td>
                                                        <td class="datatable-cell" style="width: 6%;"><?= __('Pcs.'); ?></td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">10,000</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,722.46</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">14,889.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">7.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">24,500.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 14%;">
                                                            <?= __('Plastic Round Table'); ?>
                                                        </td>
                                                        <td class="datatable-cell" style="width: 6%;"><?= __('Pcs.'); ?></td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">10,000</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,722.46</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">14,889.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">7.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">24,500.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 14%;">
                                                            <?= __('Plastic Round Table'); ?>
                                                        </td>
                                                        <td class="datatable-cell" style="width: 6%;"><?= __('Pcs.'); ?></td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">10,000</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4,000.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">4.00</td>
                                                        <td style="text-align: right; width: 8%;" class="datatable-cell">3,722.46</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">14,889.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">7.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 6%;" class="datatable-cell">24,500.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
                            <!--end::Container-->
                        </div>

</div>

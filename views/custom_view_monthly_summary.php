<div class="d-flex flex-column-fluid">
    <div class="container-fluid">
        <!--begin::Card-->
        <div class="card card-custom">
            <div class="card-header flex-wrap row border-0 pt-6 pb-0">
                                        <div class="col-4">
                                            <img class="mt-3" src="uploads/all/<?php echo $_SESSION['config']['display_logo'];?>" title="" alt="Infisuite Logo" width="100px" />
                                        </div>
                                        <div class="col-4">
                                            <div class="text-center">
                                                <h3 class="card-label mt-3">Inventory - Monthly Summary

                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <h5><span class="text-muted pt-2 font-size-md d-block float-right">Report Taken by : A001-NARESH</span></h5>
                                            <h5><span class="text-muted pt-2 font-size-md d-block float-right">Report Taken Time : 20-2-2021 : 14:00</span></h5>
                                        </div>

                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4">List Item</span>
                                                <select aria-label="Example Selectl" class="form-control form-control-lg col-8" id="exampleSelectl">
                                                    <option>List Item</option>
                                                    <option>List Item</option>
                                                    <option>List Item</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4">From Date</span>
                                                <div class="col-8">
                                                    <input aria-label="Example Date Input" class="form-control" type="date" value="2021-08-19" id="example-date-input-1">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <div class="form-group row">
                                                <span class="col-4 mt-4">To Date</span>
                                                <div class="col-8">
                                                    <input aria-label="Example Date Input" class="form-control" type="date" value="2021-08-19" id="example-date-input-2">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-10">
                                            <button type="reset" class="btn btn-success mr-2">Submit</button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="datatable datatable-bordered datatable-head-custom datatable-default datatable-loaded stock-status-table" style="display: block;">
                                            <table class="datatable-table">
                                                <thead class="datatable-head">
                                                    <tr class="datatable-row">
                                                        <th scope="col" class="datatable-cell" style="width: 20%;">
                                                            <span>Month</span>
                                                        </th>
                                                        <th scope="col" style="text-align: right; width: 20%;" class="datatable-cell"><span>Qty. In</span></th>
                                                        <th scope="col" style="text-align: right; width: 20%;" class="datatable-cell"><span>Qty. Out</span></th>
                                                        <th scope="col" style="text-align: right; width: 20%;" class="datatable-cell"><span>Balance</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="datatable-body">
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 20%;">
                                                            April
                                                        </td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">10.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 20%;">
                                                            May
                                                        </td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">10.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 20%;">
                                                            June
                                                        </td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">0.00</td>
                                                        <td style="text-align: right; width: 20%;" class="datatable-cell">10.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Card-->
        </div>
        <!--end::Container-->
    </div>


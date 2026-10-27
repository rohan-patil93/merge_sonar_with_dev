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
                                                <h3 class="card-label mt-3">Stock Ledger

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
                                                        <th scope="col" class="datatable-cell" style="width: 15%;">
                                                            <span>Date</span>
                                                        </th>
                                                        <th scope="col" style="text-align: left; width: 10%;" class="datatable-cell"><span>Type</span></th>
                                                        <th scope="col" style="text-align: right; width: 10%;" class="datatable-cell"><span>Vch/Bill No.</span></th>
                                                        <th scope="col" style="text-align: left; width: 15%;" class="datatable-cell"><span>Particulars</span></th>
                                                        <th scope="col" style="text-align: right; width: 15%;" class="datatable-cell"><span>Qty. In(Pcs.)</span></th>
                                                        <th scope="col" style="text-align: right; width: 15%;" class="datatable-cell"><span>Amt. In</span></th>
                                                        <th scope="col" style="text-align: right; width: 15%;" class="datatable-cell"><span>Qty. Out(Pcs.)</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody class="datatable-body">
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 15%;">
                                                            05-09-2021
                                                        </td>
                                                        <td style="text-align: left; width: 10%;" class="datatable-cell">Sale</td>
                                                        <td style="text-align: right; width: 10%;" class="datatable-cell">STPL/004/2000</td>
                                                        <td style="text-align: left; width: 15%;" class="datatable-cell">Arshia Enterprices</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">4,000.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 15%;">
                                                            05-09-2021
                                                        </td>
                                                        <td style="text-align: left; width: 10%;" class="datatable-cell">Sale</td>
                                                        <td style="text-align: right; width: 10%;" class="datatable-cell">STPL/004/2000</td>
                                                        <td style="text-align: left; width: 15%;" class="datatable-cell">Arshia Enterprices</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">4,000.00</td>
                                                    </tr>
                                                    <tr class="datatable-row">
                                                        <td class="datatable-cell" style="width: 15%;">
                                                            05-09-2021
                                                        </td>
                                                        <td style="text-align: left; width: 10%;" class="datatable-cell">Sale</td>
                                                        <td style="text-align: right; width: 10%;" class="datatable-cell">STPL/004/2000</td>
                                                        <td style="text-align: left; width: 15%;" class="datatable-cell">Arshia Enterprices</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">3,500.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">1.00</td>
                                                        <td style="text-align: right; width: 15%;" class="datatable-cell">4,000.00</td>
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



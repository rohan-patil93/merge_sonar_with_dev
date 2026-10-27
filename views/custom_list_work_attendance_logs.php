<?php
include_once '../includes/session.php';
include_once '../dbconfig.php';
include_once '../comm.php';
?>
<div class="modal-header">
    <h4 class="modal-title">Logs</h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-hover m-b-0" style="font-size:13px;" id="categoryfilter">
        <thead>
            <tr><th scope="col">Transaction No.</th>
                <th scope="col">Originator</th>
                <th scope="col">Date</th>
                <th scope="col">Party Name</th>
                <th scope="col" class="text-right">Amount</th>
                <th scope="col">Satus</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><a href="index.php?option=COM_INSERT_QUOTATIONS&amp;Rid=YVdRNk5BPT0=">QT0035</a></td>
                <td class="w-b-w-s">Roger Moore</td>
                <td>01-08-2023</td>
                <td class="w-b-w-s">Infisuite Hotel</td>
                <td class="text-right">руб8,240.00</td>
                <td>
                    <span class="form-label label label-md" style="color:#4099ff; background-color:#ccff33;">Active - Quote</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default waves-effect " data-bs-dismiss="modal">Close</button>
    <button type="button" class="btn btn-primary waves-effect waves-light"  onclick="submitdetails()">Submit</button>
</div>

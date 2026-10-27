$(document).ready(function() {
	$('#basic-btn').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
    });
    $('#basic-btn-download').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'csv', 'excel', 'pdf']
    });
    $('#basic-btn-print').DataTable({
        dom: 'Bfrtip',
        buttons: ['print']
    });
    $('#basic-btn-non').DataTable({
        dom: 'Bfrtip',
        buttons: []
    });
});
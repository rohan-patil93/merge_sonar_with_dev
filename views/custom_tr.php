<style type="text/css">
    table th, table td
    {
        width: 100px;
        padding: 5px;
        border: 1px solid #ccc;
    }
    .selected
    {
        background-color: #666;
        color: #fff;
    }
</style>
<script type="text/javascript">
</script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" integrity="sha384-lifoBlbdwizTl3Yoe612uhI3AcOam/QtWkozF7SuiACaf5UJl5reOYu4MigVxrCH" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/themes/smoothness/jquery-ui.css"  integrity="sha384-eX7Mg1s071G94N7MUCaAGEadC1UuTJPIvW8SM0AuD3D60GlC1lp1S7zEOPE0S2zl" crossorigin="anonymous"/>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js" integrity="sha384-q8pVyAK2InF9wi3dnZ5Wkzj906k+Rvjw1V125dWmOTXWa9vnbqqdWRqUSY5AioHg" crossorigin="anonymous"></script>
<script type="text/javascript">
$(function () {
    $("#tblLocations").sortable({
        items: 'tr:not(tr:first-child)',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");
            $(this).find("tr").each(function (index) {
                if (index > 0) {
                    $(this).find("td").eq(2).html(index);
                }
            });
        }
    });
});
</script>
<table id="tblLocations">
    <tr>
        <th scope="col">ID </th>
        <th scope="col">Location</th>
        <th scope="col">Preference</th>
    </tr>
    <tr>
        <td>1</td>
        <td> Goa</td>
        <td>1</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Mahabaleshwar</td>
        <td>2</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Kerala</td>
        <td>3</td>
    </tr>
    <tr>
        <td>4</td>
        <td>Kashmir</td>
        <td>4</td>
    </tr>
    <tr>
        <td>5</td>
        <td>Ooty</td>
        <td>5</td>
    </tr>
    <tr>
        <td>6</td>
        <td>Simla</td>
        <td>6</td>
    </tr>
    <tr>
        <td>7</td>
        <td>Manali</td>
        <td>7</td>
    </tr>
    <tr>
        <td>8</td>
        <td>Darjeeling</td>
        <td>8</td>
    </tr>
    <tr>
        <td>9</td>
        <td>Nanital</td>
        <td>9</td>
    </tr>
</table>

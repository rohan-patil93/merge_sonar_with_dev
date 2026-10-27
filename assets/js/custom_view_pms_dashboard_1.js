$(document).ready(function() { 
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);

    function drawChartDonut() {
       var payrunprocess  = JSON.parse($("#performance_review_summary").val()); 
       console.log(payrunprocess);
       var dataDonut = google.visualization.arrayToDataTable([
        ['Status', 'Employees'],
        ['Self Assessment', payrunprocess.self_assessment],
        ['Manager Assessment', payrunprocess.manager_assessment],
        ['Completed', payrunprocess.completed]
        ]);
       var optionsDonut = {
        pieHole: 0.4,
        colors: ['#448aff', '#11c15b', '#ffe100']
    };
    var chart = new google.visualization.PieChart(document.getElementById('performance_review_summary_chasrt'));
    chart.draw(dataDonut, optionsDonut);
}
});
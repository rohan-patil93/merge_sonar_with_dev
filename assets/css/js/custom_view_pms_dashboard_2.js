$(document).ready(function() { 
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);

    function drawChartDonut() {
       var payrunprocess  = JSON.parse($("#performancereviewratting").val()); 
       console.log(payrunprocess);
       var dataDonut = google.visualization.arrayToDataTable(payrunprocess);
       var optionsDonut = {
        pieHole: 0.4,
        colors: ['#448aff', '#11c15b', '#ffe100', '#ffe100', '#ffe100']
    };
    var chart = new google.visualization.PieChart(document.getElementById('performance_review_ratting'));
    chart.draw(dataDonut, optionsDonut);
}

function categoryChart() {
        // var data = [
        //     ["January", 20],
        //     ["February", 8],
        //     ["March", 50],
        //     ["April", 13],
        //     ["May", 5],
        //     ["June", 9]
        // ];
        var data = JSON.parse($("#allperformancereviewratting").val());

        $.plot("#placeholder", [data], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
                    align: "center",
                }
            },

            xaxis: {
                mode: "categories",
                tickLength: 0,
                tickColor: '#f5f5f5',
            },
            colors: ["#ff5252", "#83D6DE"],
            labelBoxBorderColor: "red"

        });
    };
    categoryChart();
});
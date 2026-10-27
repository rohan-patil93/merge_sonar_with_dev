$(document).ready(function() { 
    //Donut chart
    var asset_condition_chart = $("#asset_condition_json").val();
    var conditionchartjson = JSON.parse(asset_condition_chart);
    var constraints = [['Status','Count']];
    for (var i = 0; i < conditionchartjson.length; i++) {
        var name = conditionchartjson[i].condition;
        var cnt = conditionchartjson[i].cnt;
        var emtyaray = [];
        emtyaray.push(name);
        emtyaray.push(cnt);
        constraints.push(emtyaray); 
    }
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);
    function drawChartDonut() {
        var dataDonut = google.visualization.arrayToDataTable(constraints);



        var optionsDonut = {
            title: 'My Daily Activities',
            pieHole: 0.4,
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#ff5252']
        };

        var chart = new google.visualization.PieChart(document.getElementById('asset_condition_chart'));
        chart.draw(dataDonut, optionsDonut);
    }

 setTimeout(function(){
    $(window).on('resize',function() {
        categoryChart();
    }); 
    categoryChart();
},350);

        /*categories chart*/
    function categoryChart() {
        var asset_condition_chart = $("#asset_status_json").val();
        var conditionchartjson = JSON.parse(asset_condition_chart);
        var constraints = [['Status','Count']];
        for (var i = 0; i < conditionchartjson.length; i++) {
            var name = conditionchartjson[i].staus;
            var cnt = conditionchartjson[i].cnt;
            var emtyaray = [];
            emtyaray.push(name);
            emtyaray.push(cnt);
            constraints.push(emtyaray); 
        }
        var data = constraints;

        $.plot("#asset_status_chart", [data], {
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



});
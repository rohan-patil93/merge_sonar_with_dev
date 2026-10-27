"use strict";
//$(document).ready(function() { console.log("hello2");
 $(".chertvalues").each(function(){ console.log("hello3");
     var id = $(this).data("id");
     var value = $(this).val();
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);
    function drawChartDonut() {
           var datax = value;
          var x = datax.split("||");
          var a = x[0].split(":");
          var b = x[1].split(":");
          var c = x[2].split(":");

        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])]
    
        ]);
        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: ['#fe3995', '#f6aa33', '#6e4ff5']
        };
        var chart = new google.visualization.PieChart(document.getElementById(id));
        chart.draw(dataDonut, optionsDonut);
    }
});

//Slice Visibility Threshold
    // google.charts.load('current', { 'packages': ['corechart'] });
    // google.charts.setOnLoadCallback(drawChartThreshold);
    // function drawChartThreshold() {
    //     var dataThreshold = new google.visualization.DataTable();
    //     dataThreshold.addColumn('string', 'Pizza');
    //     dataThreshold.addColumn('number', 'Populartiy');
    //     dataThreshold.addRows([
    //         ['Pepperoni', 33],
    //         ['Hawaiian', 26],
    //         ['Mushroom', 22],
    //         ['Sausage', 10], // Below limit.
    //         ['Anchovies', 9] // Below limit.
    //     ]);
    //     var optionsThreshold = {
    //         title: 'Popularity of Types of Pizza',
    //         sliceVisibilityThreshold: .2,
    //         colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100']
    //     };
    //     var chart = new google.visualization.PieChart(document.getElementById('chart_Threshold'));
    //     chart.draw(dataThreshold, optionsThreshold);
    // }
//});

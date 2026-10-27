"use strict";
$(document).ready(function() { console.log("hello2");
	google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut);
    
    function drawChartDonut() {
        var datax = $('#dchart1').val();
        var x = datax.split("||");
        var a = x[0].split(":");
        var b = x[1].split(":");
        var c = x[2].split(":");
        var d = x[3].split(":");
        var e = x[4].split(":");
        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
            [d[0], parseInt(d[1])],
            [e[0], parseInt(e[1])]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: [a[2], b[2], c[2], d[2], e[2]]
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_hiring_recejtion_1'));
        chart.draw(dataDonut, optionsDonut);
    }

    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut2);
    function drawChartDonut2() {
        var datax = $('#dchart2').val();
        var x = datax.split("||");
        var a = x[0].split(":");
        var b = x[1].split(":");
        var c = x[2].split(":");
        var d = x[3].split(":");
        var e = x[4].split(":");
        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
            [d[0], parseInt(d[1])],
            [e[0], parseInt(e[1])]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: [a[2], b[2], c[2], d[2], e[2]]
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_hiring_recejtion_2'));
        chart.draw(dataDonut, optionsDonut);
    }
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut3);
    function drawChartDonut3() {
        var datax = $('#dchart3').val();
        var x = datax.split("||");
        var a = x[0].split(":");
        var b = x[1].split(":");
        var c = x[2].split(":");
        var d = x[3].split(":");
        var e = x[4].split(":");
        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
            [d[0], parseInt(d[1])],
            [e[0], parseInt(e[1])]
        ]);

        var optionsDonut = {
            title: '',
            pieHole: 0.4,
            colors: [a[2], b[2], c[2], d[2], e[2]]
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_hiring_recejtion_3'));
        chart.draw(dataDonut, optionsDonut);
    }
});






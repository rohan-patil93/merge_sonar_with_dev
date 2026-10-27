
    //Donut chart
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut1);

    function drawChartDonut1() {
        var datax = document.getElementById('chart_data_age').value;
         var x = datax.split("||");
          var a = x[0].split(":");
          var b = x[1].split(":");
          var c = x[2].split(":");
          var d = x[3].split(":");
          var e = x[4].split(":");
          var f = x[5].split(":");
          var g = x[6].split(":");
          var h = x[7].split(":");
        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
            [d[0], parseInt(d[1])],
            [e[0], parseInt(e[1])],
            [f[0], parseInt(f[1])],
            [g[0], parseInt(g[1])],
            [h[0], parseInt(h[1])]
        ]);

        var optionsDonut = {
            pieHole: 0.4,
            legend: 'bottom',
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D','#44ffff', '#11cffb', '#ffcc00', '#FE007D']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_age'));
        chart.draw(dataDonut, optionsDonut);
    }
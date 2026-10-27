
    //Donut chart
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut1);

    function drawChartDonut1() {
        var datax = document.getElementById('chart_data_bill_vendor').value;
          var x = datax.split("||");
          var a = x[0].split(":");
          var b = x[1].split(":"); 
          var c = x[2].split(":");
          var d = x[3].split(":"); 
          var e = x[4].split(":");
          
        var dataDonut = google.visualization.arrayToDataTable([
            ['Days', 'Amount'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
            [d[0], parseInt(d[1])],
            [e[0], parseInt(e[1])], 
        ]);

        var optionsDonut = {
            pieHole: 0.4,
            legend: 'bottom',
            colors: ['#448aff', '#11c15b']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_gender'));
        chart.draw(dataDonut, optionsDonut);
    }
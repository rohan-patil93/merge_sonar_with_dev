    //Donut chart
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartDonut1);

    function drawChartDonut1() {
        var datax = document.getElementById('manga_ransport_sahboard').value;
        var x = datax.split("||");
        var a = x[0].split(":");
        var b = x[1].split(":");
        var c = x[2].split(":");
        //var d = x[3].split(":");
          
        var dataDonut = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])],
           
        ]);

        var optionsDonut = {
            //title: 'Projects',
            pieHole: 0.4,
            //legend: 'bottom',
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_mdash'));
        chart.draw(dataDonut, optionsDonut);
    }
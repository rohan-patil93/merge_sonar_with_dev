     //BAR CHART
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawStacked);

    function drawStacked() {
         var datax = document.getElementById('chart_project_workload').value;
        var x = datax.split("||");
        var a = x[0].split(":");
        var b = x[1].split(":");
        var c = x[2].split(":");
        var d = x[3].split(":");
        var e = x[4].split(":");
        var data = google.visualization.arrayToDataTable([
            ['', 'Completed ', 'Remaning'],
            [a[0], parseInt(a[1]), parseInt(a[2])],
            [b[0], parseInt(b[1]), parseInt(b[2])],
            [c[0], parseInt(c[1]), parseInt(c[2])],
            [d[0], parseInt(d[1]), parseInt(d[2])],
            [e[0], parseInt(e[1]), parseInt(e[2])]
        ]);

        var options = {
            //title: 'Population of Largest U.S. Cities',
            chartArea: { width: '50%' },
            isStacked: true,
           /* hAxis: {
                title: 'Total Population',
                minValue: 0,
            },*/
           /* vAxis: {
                title: 'City'
            },*/
            colors: ['#11c15b', '#ff5370']
        };
        var chart = new google.visualization.BarChart(document.getElementById('chart_bar_workload'));
        chart.draw(data, options);
    }
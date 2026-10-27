  //Combo chart
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var datax = document.getElementById('chart_project_cost').value;
        var x = datax.split("||");
        var data = google.visualization.arrayToDataTable([
            ['', 'Actual', 'Planed', 'Budget'],
            ['', parseInt(x[0]), parseInt(x[1]), parseInt(x[2])]
        ]);

        var options = {
            //title: 'Monthly Coffee Production by Country',
            //vAxis: { title: 'Cups' },
            //hAxis: { title: 'Month' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#69CEC6', '#ff5252']
        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_Combo'));
        chart.draw(data, options);
    }
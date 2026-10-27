
  //Combo chart
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var datax = document.getElementById('dchart1').value;
        var x = datax.split("||");
        var x = datax.split("||");
          var a = x[0].split(":");
          var b = x[1].split(":");
          var c = x[2].split(":");

        var dataDonut = google.visualization.arrayToDataTable([
            ['Month', 'Hours'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])]
    
        ]);
  

        var options = {
           
            legend: 'bottom',            
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#69CEC6', '#ff5252']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_at1'));
        chart.draw(dataDonut, options);
    }

//Combo chart
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawVisualizationtwo);

    function drawVisualizationtwo() {
        // Some raw data (not necessarily accurate)
        var datax = document.getElementById('dchart2').value;
        var x = datax.split("||");
        var x = datax.split("||");
          var a = x[0].split(":");
          var b = x[1].split(":");
          var c = x[2].split(":");

        var dataDonut = google.visualization.arrayToDataTable([
            ['Month', 'Hours'],
            [a[0], parseInt(a[1])],
            [b[0], parseInt(b[1])],
            [c[0], parseInt(c[1])]
    
        ]);
  

        var options = {
            
            legend: 'bottom',
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#69CEC6', '#ff5252']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Donut_at2'));
        chart.draw(dataDonut, options);
    }








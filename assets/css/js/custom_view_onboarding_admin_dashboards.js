if (document.getElementById('chart_data_contact_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_contact_chart').value;
        console.log(datax);
           ///  console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' },
            plugins: {
                legend: {
                    position: 'top',  // Keeping the legend at the bottom
                }
            },
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_contact'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}

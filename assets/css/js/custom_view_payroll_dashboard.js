$(document).ready(function() { 
 google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);

    function drawChartThreshold() {
        var payrunprocess  = JSON.parse($("#payrunprocess").val()); 
        console.log(payrunprocess);   
        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Pizza');
        dataThreshold.addColumn('number', 'Populartiy');
        dataThreshold.addRows([
            ['Completed', payrunprocess.Complited],
            ['In Process', payrunprocess.InProcess]
             // Below limit.
        ]);

        var optionsThreshold = {
            legend:'bottom',
            sliceVisibilityThreshold: .2,
            colors: ['#11c15b', '#ffb64d']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_Threshold'));
        chart.draw(dataThreshold, optionsThreshold);
    }
     //BAR CHART
    google.charts.load('current', { packages: ['corechart', 'bar'] });
    google.charts.setOnLoadCallback(drawStacked);

    function drawStacked() {
        var payrunsummarycomponents = JSON.parse($("#pay_run_summary_components").val()); 
        console.log(payrunsummarycomponents);
        var data = google.visualization.arrayToDataTable(
            payrunsummarycomponents
        );
        // var data = google.visualization.arrayToDataTable([
        //     ['Month','Gross Salary', 'Employeer Contribution'],
        //     ['New York City, NY', 0, 0],
        //     ['Los Angeles, CA', 3792000, 3694000],
        //     ['Chicago, IL', 2695000, 2896000],
        //     ['Houston, TX', 2099000, 1953000],
        //     ['Philadelphia, PA', 1526000, 1517000]
        // ]);

        var options = {
            chartArea: { width: '50%' },
            isStacked: true,
            hAxis: {
                title: 'Amount',
                minValue: 0,
            },
            vAxis: {
                title: 'Month Year'
            },
            colors: ['#11c15b', '#448aff','#ffb64d']
        };
        var chart = new google.visualization.BarChart(document.getElementById('chart_bar'));
        chart.draw(data, options);
    }
});

//Slice Visibility Threshold
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);

    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_bill_vendor').value;
         console.log(datax);
             console.log(JSON.parse(datax));
         dataThreshold.addRows(JSON.parse(datax));
        // dataThreshold.addRows([
        //     ['Pepperoni', 33],
        //     ['Hawaiian', 26],
        //     ['Mushroom', 22],
        //     ['Sausage', 10], // Below limit.
        //     ['Anchovies', 9] // Below limit.
        // ]);
        var optionsThreshold = {
            sliceVisibilityThreshold: .2,
            colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_category'));
        chart.draw(dataThreshold, optionsThreshold);
    }
   



    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold1);
    function drawChartThreshold1() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_bill_customer').value;
         console.log(datax);
             console.log(JSON.parse(datax));
         dataThreshold.addRows(JSON.parse(datax));

        var optionsThreshold = {
           // title: 'Popularity of Types of Pizza',
            sliceVisibilityThreshold: .2,
            colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100']
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_customer'));
        chart.draw(dataThreshold, optionsThreshold);
    }
 
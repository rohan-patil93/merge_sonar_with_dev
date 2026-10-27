 //Donut chart
    // google.charts.load("current", { packages: ["corechart"] });
    // google.charts.setOnLoadCallback(drawChartDonut);

    // function drawChartDonut() {
    //     var dataDonut = google.visualization.arrayToDataTable([
    //         ['Task', 'Hours per Day'],
    //         ['Work', 11],
    //         ['Eat', 2],
    //         ['Commute', 2],
    //         ['Watch TV', 2],
    //         ['Sleep', 7]
    //     ]);

    //     var optionsDonut = {
    //         title: 'My Daily Activities',
    //         pieHole: 0.4,
    //         colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#ff5252'],
    //         legend: { position: 'bottom' }
    //     };

    //     var chart = new google.visualization.PieChart(document.getElementById('chart_Donut'));
    //     chart.draw(dataDonut, optionsDonut);
    // }

if (document.getElementById('chart_data_lead_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_lead_chart').value;
        // console.log(datax);
            // console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .1,
            colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_leads'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_contact_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_contact_chart').value;
        // console.log(datax);
           ///  console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_contact'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_companies_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_companies_chart').value;
         //console.log(datax);
             //console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .2,
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_companies'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_deals_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_deals_chart').value;
        // console.log(datax);
           //  console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .2,
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_deals'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}


if (document.getElementById('chart_data_lead_chart2')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_lead_chart2').value;
        // console.log(datax);
          //   console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .1,
            colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_leads2'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_contact_chart2')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_contact_chart2').value;
        // console.log(datax);
          //   console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
           // sliceVisibilityThreshold: .2,
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_contact2'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_companies_chart2')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_companies_chart2').value;
        // console.log(datax);
          //   console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
           // sliceVisibilityThreshold: .2,
            colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_companies2'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
if (document.getElementById('chart_data_deals_chart2')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_deals_chart2').value;
        // console.log(datax);
          //   console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .2,
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_deals2'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}


if (document.getElementById('chart_data_campaigns_chart')) {
    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawChartThreshold);
    function drawChartThreshold() {

        var dataThreshold = new google.visualization.DataTable();
        dataThreshold.addColumn('string', 'Category');
        dataThreshold.addColumn('number', 'Amount');
        var datax = document.getElementById('chart_data_campaigns_chart').value;
        // console.log(datax);
         //    console.log(JSON.parse(datax));
        dataThreshold.addRows(JSON.parse(datax));
        var optionsThreshold = {
            //sliceVisibilityThreshold: .2,
             colors: ['#448aff', '#11c15b', '#ff5252', '#ffe100'],
            legend: { position: 'bottom' }
        };

        var chart = new google.visualization.PieChart(document.getElementById('chart_by_campaigns'));
        chart.draw(dataThreshold, optionsThreshold);
    }
}
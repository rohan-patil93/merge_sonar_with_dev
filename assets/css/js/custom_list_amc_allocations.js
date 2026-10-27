$(document).ready(function() { 
    

    setTimeout(function(){
        $(window).on('resize',function() {
            listbarstrqChart();
        }); 
        listbarcomChart();
        listbarstrqChart();

    },350);

    function listbarstrqChart() {
        var asset_condition_chart = $("#amc_comp_json").val();
        var conditionchartjson = JSON.parse(asset_condition_chart);
        var constraints = [['Status','Count']];
        for (var i = 0; i < conditionchartjson.length; i++) {
            var name = conditionchartjson[i].staus;
            var cnt = conditionchartjson[i].cnt;
            var emtyaray = [];
            emtyaray.push(name);
            emtyaray.push(cnt);
            constraints.push(emtyaray); 
        }
        var data = constraints;

        $.plot("#amc_comp_chart", [data], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
                    align: "center",
                }
            },

            xaxis: {
                mode: "categories",
                tickLength: 0,
                tickColor: '#f5f5f5',
            },
            colors: ["#6aa84f", "#83D6DE"],
            labelBoxBorderColor: "red"

        });
    }
    function listbarcomChart() {
        var asset_condition_chart = $("#ams_strq_json").val();
        var conditionchartjson = JSON.parse(asset_condition_chart);
        var constraints = [['Status','Count']];
        for (var i = 0; i < conditionchartjson.length; i++) {
            var name = conditionchartjson[i].staus;
            var cnt = conditionchartjson[i].cnt;
            var emtyaray = [];
            emtyaray.push(name);
            emtyaray.push(cnt);
            constraints.push(emtyaray); 
        }
        var data = constraints;

        $.plot("#asset_strq_chart", [data], {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
                    align: "center",
                }
            },

            xaxis: {
                mode: "categories",
                tickLength: 0,
                tickColor: '#f5f5f5',
            },

            colors: ["#83D6DE", "#ff5252"],
            labelBoxBorderColor: "red"

        });
    }
});
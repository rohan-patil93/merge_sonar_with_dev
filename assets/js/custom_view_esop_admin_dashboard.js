"use strict";
$(document).ready(function() {
    //Combo chart

    google.charts.load('current', { 'packages': ['corechart'] });
    google.charts.setOnLoadCallback(drawVisualization);

    function drawVisualization() {
        var distribution_graph = $("#distribution_graph").val();
                console.log(distribution_graph);
        var graph = JSON.parse(distribution_graph);
        console.log(graph);
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable(graph);
        var options = {
            title: '',
            vAxis: { title: 'Percentage' },
            hAxis: { title: 'Year' },
            seriesType: 'bars',
            series: { 5: { type: 'line' } },
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#69CEC6', '#ff5252']
        };
        var chart = new google.visualization.ComboChart(document.getElementById('chart_Combo'));
        chart.draw(data, options);
    }


        //EXPLOADING A SLICE CHART
    google.charts.load("current", { packages: ["corechart"] });
    google.charts.setOnLoadCallback(drawChartExploading);

    function drawChartExploading() {
        var ownership_encode = $("#ownership_encode").val();
        var ownership = JSON.parse(ownership_encode);
        var dataExploading = google.visualization.arrayToDataTable(ownership);

        var optionsExploading = {
            legend: 'none',
            pieSliceText: 'label',
            slices: {
                4: { offset: 0.2 },
                12: { offset: 0.3 },
                14: { offset: 0.4 },
                15: { offset: 0.5 },
            },
            colors: ['#448aff', '#11c15b', '#ffe100', '#FE8A7D', '#69CEC6', '#ff5252']
        };

        var chart = new google.visualization.PieChart(document.getElementById('ownership_chart'));
        chart.draw(dataExploading, optionsExploading);
    }





































/**
    // page statustic chart start
    setTimeout(function() {
        // var valuation_trend_chart = $("#valuation_trend_chart_id").val();
        // var valuation_trend_charts = JSON.parse(valuation_trend_chart);
        // var widgetlineChart = new Chartist.Line('#valuation_trend_chart', {
        //     series: [valuation_trend_charts]
        // }, {
        //     axisX: {
        //         showGrid: true,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     axisY: {
        //         showGrid: false,
        //         low: 40,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     fullWidth: true,
        // });

        // var share_price_trends_chart_id = $("#share_price_trends_chart_id").val();
        // var share_price_trends_chars = JSON.parse(share_price_trends_chart_id);
        // var widgetlineChart = new Chartist.Line('#share_price_trends_chart', {
        //     series: [share_price_trends_chars]
        // }, {
        //     axisX: {
        //         showGrid: true,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     axisY: {
        //         showGrid: false,
        //         low: 40,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     fullWidth: true,
        // });


        // var esop_trends_chart_id = $("#esop_trends_chart_id").val();
        // var esop_trends_charts = JSON.parse(esop_trends_chart_id);
        // var widgetlineChart = new Chartist.Line('#esop_trends_chart', {
        //     series: [esop_trends_charts]
        // }, {
        //     axisX: {
        //         showGrid: true,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     axisY: {
        //         showGrid: false,
        //         low: 40,
        //         showLabel: false,
        //         offset: 0,
        //     },
        //     fullWidth: true,
        // });

    }, 800);
    // page statustic chart end



**/








    floatchart()
    $(window).on('resize', function() {
        floatchart();
    });
    $('#mobile-collapse').on('click', function() {
        setTimeout(function() {
            floatchart();
        }, 700);
    });
 
function floatchart() {
    $(function() {
    var valuation_trend_chart = $("#valuation_trend_chart_id").val();
     var valuation_trend_chart_type = $("#valuation_trend_chart_type").val();
    var valuation_trend_charts = JSON.parse(valuation_trend_chart);
    var options1 = {
            legend: {
                show: false
            },
            series: {
                label: "",
                curvedLines: {
                    active: true,
                    nrSplinePoints: 1
                },
            },
            tooltip: {
                show: true,
                content: "x : %x | "+valuation_trend_chart_type+" : %y"
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                labelMargin: 0,
                axisMargin: 0,
                minBorderMargin: 0,
            },
            yaxis: {
                min: 0,
                max: 100,
                color: 'transparent',
                font: {
                    size: 0,
                }
            },
            xaxis: {
                color: 'transparent',
                font: {
                    size: 0,
                }
            }
        };
$.plot($("#valuation_trend_chart"), [{
        data : valuation_trend_charts,
        color: "#ff5252",
        lines: {
            show: true,
            fill: false,
            lineWidth: 2
        },
        points: {
            show: true,
            radius: 3,
            fill: true,
            fillColor: '#fff'
        },
        curvedLines: {
            apply: true,
        }
    }], options1);

var share_price_trends_chart_id = $("#share_price_trends_chart_id").val();
var share_price_trends_chart_type = $("#share_price_trends_chart_type").val();
var share_price_trends_chars = JSON.parse(share_price_trends_chart_id);
    var options2 = {
            legend: {
                show: false
            },
            series: {
                label: "",
                curvedLines: {
                    active: true,
                    nrSplinePoints: 1
                },
            },
            tooltip: {
                show: true,
                content: "x : %x | "+share_price_trends_chart_type+" : %y"
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                labelMargin: 0,
                axisMargin: 0,
                minBorderMargin: 0,
            },
            yaxis: {
                min: 0,
                max: 100,
                color: 'transparent',
                font: {
                    size: 0,
                }
            },
            xaxis: {
                color: 'transparent',
                font: {
                    size: 0,
                }
            }
        };
$.plot($("#share_price_trends_chart"), [{
        data : share_price_trends_chars,
        color: "#ff5252",
        lines: {
            show: true,
            fill: false,
            lineWidth: 2
        },
        points: {
            show: true,
            radius: 3,
            fill: true,
            fillColor: '#fff'
        },
        curvedLines: {
            apply: true,
        }
    }], options2);

var esop_trends_chart_id = $("#esop_trends_chart_id").val();
var esop_trends_chart_type = $("#esop_trends_chart_type").val();
var esop_trends_charts = JSON.parse(esop_trends_chart_id);
    var options3 = {
            legend: {
                show: false
            },
            series: {
                label: "",
                curvedLines: {
                    active: true,
                    nrSplinePoints: 1
                },
            },
            tooltip: {
                show: true,
                content: "x : %x |  "+esop_trends_chart_type+" : %y"
            },
            grid: {
                hoverable: true,
                borderWidth: 0,
                labelMargin: 0,
                axisMargin: 0,
                minBorderMargin: 0,
            },
            yaxis: {
                min: 0,
                max: 100,
                color: 'transparent',
                font: {
                    size: 0,
                }
            },
            xaxis: {
                color: 'transparent',
                font: {
                    size: 0,
                }
            }
        };
$.plot($("#esop_trends_chart"), [{
        data : esop_trends_charts,
        color: "#ff5252",
        lines: {
            show: true,
            fill: false,
            lineWidth: 2
        },
        points: {
            show: true,
            radius: 3,
            fill: true,
            fillColor: '#fff'
        },
        curvedLines: {
            apply: true,
        }
    }], options3);









// $.plot($("#sec-ecommerce-chart-line"), [{
//             data: valuation_trend_charts,
//             color: "#fff",
//             lines: {
//                 show: true,
//                 fill: false,
//                 lineWidth: 2
//             },
//             points: {
//                 show: true,
//                 radius: 3,
//                 fill: true,
//                 fillColor: '#fff'
//             },
//             curvedLines: {
//                 apply: false,
//             }
//         }], options);








})



} 
/**
    floatchart()
    $(window).on('resize', function() {
        floatchart();
    });
    $('#mobile-collapse').on('click', function() {
        setTimeout(function() {
            floatchart();
        }, 700);
    });
function floatchart() {
    //flot options
    var options = {
        legend: {
            show: false
        },
        series: {
            label: "",
            curvedLines: {
                active: true,
                nrSplinePoints: 2
            },
        },
        tooltip: {
            show: true,
            content: "x : %x | y : %y"
        },
        grid: {
            hoverable: true,
            borderWidth: 0,
            labelMargin: 0,
            axisMargin: 0,
            minBorderMargin: 0,
        },
        yaxis: {
            min: 0,
            max: 100,
            color: 'transparent',
            font: {
                size: 0,
            }
        },
        xaxis: {
            color: 'transparent',
            font: {
                size: 0,
            }
        }
    };
  $(function() {
$.plot($("#sec-ecommerce-chart-line"), [{
            data: [
                [0, 1],
                [1, 30],
                [2, 5],
                [3, 50],
                [4, 80],
            ],
            color: "#fff",
            lines: {
                show: true,
                fill: false,
                lineWidth: 2
            },
            points: {
                show: true,
                radius: 3,
                fill: true,
                fillColor: '#fff'
            },
            curvedLines: {
                apply: false,
            }
        }], options);

});
}
**/


});    
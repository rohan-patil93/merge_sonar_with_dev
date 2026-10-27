"use strict";
$(document).ready(function(){
    var employee_performance_appraisals_ratting = $("#employee_performance_appraisals_ratting").val();
    console.log(employee_performance_appraisals_ratting); 
    var eparchart = JSON.parse(employee_performance_appraisals_ratting); 
    console.log(eparchart);   
    var data1 = {
        labels: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10],
        datasets: [{
            label: "Rattings",
            backgroundColor: [
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)',
                'rgba(95, 190, 170, 0.99)'
            ],
            hoverBackgroundColor: [
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)',
                'rgba(26, 188, 156, 0.88)'
            ],
            data: eparchart,
        }
         ]
    };

    var bar = document.getElementById("barChart").getContext('2d');
    var myBarChart = new Chart(bar, {
        type: 'bar',
        data: data1,
        options: {
            barValueSpacing: 20
        }
    });
});
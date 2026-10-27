(function () {
  function readChartData() {
    var el = document.getElementById('csr-dashboard-chart-data');
    if (!el) {
      return {};
    }

    try {
      return JSON.parse(el.getAttribute('data-chart') || '{}');
    } catch (e) {
      return {};
    }
  }

  function hasValues(values) {
    if (!values || !values.length) {
      return false;
    }
    return values.some(function (value) {
      return Number(value) !== 0;
    });
  }

  function formatAmount(value) {
    value = Number(value) || 0;
    return value.toLocaleString('en-IN');
  }

  function renderNoData(canvasId) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) {
      return;
    }
    var context = canvas.getContext('2d');
    context.clearRect(0, 0, canvas.width, canvas.height);
    context.font = '14px Arial';
    context.fillStyle = '#777';
    context.textAlign = 'center';
    context.fillText('No data', canvas.width / 2, canvas.height / 2);
  }

  function makeBar(canvasId, labels, datasets) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) {
      return;
    }

    var values = [];
    datasets.forEach(function (dataset) {
      values = values.concat(dataset.data || []);
    });

    if (!labels || !labels.length || !hasValues(values)) {
      renderNoData(canvasId);
      return;
    }

    new Chart(canvas.getContext('2d'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: datasets.length > 1
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        },
        tooltips: {
          mode: 'index',
          intersect: false
        }
      }
    });
  }

  function makeLine(canvasId, labels, datasets) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) {
      return;
    }

    var values = [];
    datasets.forEach(function (dataset) {
      values = values.concat(dataset.data || []);
    });

    if (!labels || !labels.length || !hasValues(values)) {
      renderNoData(canvasId);
      return;
    }

    new Chart(canvas.getContext('2d'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: datasets
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: datasets.length > 1
        },
        scales: {
          yAxes: [{
            ticks: {
              beginAtZero: true
            }
          }]
        }
      }
    });
  }

  function makeDoughnut(canvasId, labels, values, showLegendValues) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) {
      return;
    }

    if (!labels || !labels.length || !hasValues(values)) {
      renderNoData(canvasId);
      return;
    }

    new Chart(canvas.getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: values,
          backgroundColor: ['#ffb39f', '#ffc184', '#54c8cc', '#55cdb0', '#8d92a1'],
          borderColor: '#ffffff',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutoutPercentage: 45,
        legend: {
          display: true,
          position: 'bottom',
          labels: {
            generateLabels: function (chart) {
              var chartData = chart.data || {};
              var dataset = chartData.datasets && chartData.datasets.length ? chartData.datasets[0] : {};
              return (chartData.labels || []).map(function (label, index) {
                var value = dataset.data && dataset.data[index] ? dataset.data[index] : 0;
                var color = dataset.backgroundColor && dataset.backgroundColor[index] ? dataset.backgroundColor[index] : '#cccccc';
                var meta = chart.getDatasetMeta(0);
                var arc = meta.data && meta.data[index] ? meta.data[index] : {};
                return {
                  text: showLegendValues ? label + ' - ' + formatAmount(value) : label,
                  fillStyle: color,
                  strokeStyle: color,
                  lineWidth: 0,
                  hidden: isNaN(value) || arc.hidden,
                  index: index
                };
              });
            }
          }
        }
      }
    });
  }

  function makeHorizontalBar(canvasId, labels, values) {
    var canvas = document.getElementById(canvasId);
    if (!canvas) {
      return;
    }

    if (!labels || !labels.length || !hasValues(values)) {
      renderNoData(canvasId);
      return;
    }

    new Chart(canvas.getContext('2d'), {
      type: 'horizontalBar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Approved Amount',
          data: values,
          backgroundColor: '#54c8cc',
          borderColor: '#38aeb2',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
          display: false
        },
        scales: {
          xAxes: [{
            ticks: {
              beginAtZero: true,
              callback: function (value) {
                return formatAmount(value);
              }
            }
          }]
        },
        tooltips: {
          callbacks: {
            label: function (tooltipItem) {
              return 'Approved Amount - ' + formatAmount(tooltipItem.xLabel);
            }
          }
        }
      }
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery) {
      $('#csrDashboardFySelect').on('change', function () {
        $('#csrDashboardYearForm').submit();
      });
    } else {
      var fySelect = document.getElementById('csrDashboardFySelect');
      if (fySelect) {
        fySelect.addEventListener('change', function () {
          document.getElementById('csrDashboardYearForm').submit();
        });
      }
    }

    var data = readChartData();
    var obligation = data.obligationSpentUnspent || {};
    var spentTrend = data.spentTrend || {};
    var budgetFlow = data.budgetFlow || {};
    var sectorAllocation = data.sectorAllocation || {};
    var locationAllocation = data.locationAllocation || {};

    makeLine('csr-obligation-chart', obligation.labels || [], [{
      label: 'Obligation',
      data: obligation.obligation || [],
      borderColor: '#aebed7',
      backgroundColor: 'rgba(174, 190, 215, 0.12)',
      pointBackgroundColor: '#aebed7',
      fill: false,
      lineTension: 0.35
    }, {
      label: 'Spent',
      data: obligation.spent || [],
      borderColor: '#ff914d',
      backgroundColor: 'rgba(255, 145, 77, 0.12)',
      pointBackgroundColor: '#ff914d',
      fill: false,
      lineTension: 0.35
    }, {
      label: 'Unspent',
      data: obligation.unspent || [],
      borderColor: '#2ed8b6',
      backgroundColor: 'rgba(46, 216, 182, 0.12)',
      pointBackgroundColor: '#2ed8b6',
      fill: false,
      lineTension: 0.35
    }]);

    makeDoughnut('csr-spent-trend-chart', spentTrend.labels || [], spentTrend.spent || [], true);

    makeBar('csr-budget-flow-chart', budgetFlow.labels || [], [{
      label: 'Obligation',
      data: budgetFlow.obligation || [],
      backgroundColor: '#5fc0ad'
    }, {
      label: 'Approved',
      data: budgetFlow.approved || [],
      backgroundColor: '#5b9be8'
    }, {
      label: 'Spent',
      data: budgetFlow.spent || [],
      backgroundColor: '#c9c9c9'
    }]);

    makeDoughnut('csr-sector-allocation-chart', sectorAllocation.labels || [], sectorAllocation.values || [], true);

    makeHorizontalBar('csr-location-allocation-chart', locationAllocation.labels || [], locationAllocation.values || []);

    document.querySelectorAll('.csr-progress-bar').forEach(function (bar) {
      var progress = Number(bar.getAttribute('data-progress') || 0);
      if (progress < 0) {
        progress = 0;
      }
      if (progress > 100) {
        progress = 100;
      }
      bar.style.width = progress + '%';
    });
  });
}());

(function (window, document) {
  var colors = ['#4099ff', '#2ed8b6', '#ffb64d', '#ff5370', '#7759de', '#00bcd4'];
  var tooltip;

  function getTooltip() {
    if (!tooltip) {
      tooltip = document.createElement('div');
      tooltip.className = 'mfg-chart-tooltip';
      tooltip.style.position = 'fixed';
      tooltip.style.zIndex = '99999';
      tooltip.style.pointerEvents = 'none';
      tooltip.style.padding = '6px 9px';
      tooltip.style.borderRadius = '4px';
      tooltip.style.background = 'rgba(17,24,39,.94)';
      tooltip.style.color = '#fff';
      tooltip.style.fontSize = '12px';
      tooltip.style.lineHeight = '1.35';
      tooltip.style.boxShadow = '0 8px 20px rgba(15,23,42,.22)';
      tooltip.style.display = 'none';
      document.body.appendChild(tooltip);
    }
    return tooltip;
  }

  function bindTooltip(el, label, value) {
    el.addEventListener('mousemove', function (event) {
      var tip = getTooltip();
      tip.innerHTML = '<strong>' + escapeHtml(label) + '</strong><br>' + escapeHtml(value);
      tip.style.left = (event.clientX + 12) + 'px';
      tip.style.top = (event.clientY + 12) + 'px';
      tip.style.display = 'block';
    });
    el.addEventListener('mouseleave', function () {
      getTooltip().style.display = 'none';
    });
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, function (char) {
      return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
    });
  }

  function clear(el) {
    if (el) {
      el.innerHTML = '';
    }
  }

  function maxValue(values) {
    var max = 0;
    values.forEach(function (value) {
      max = Math.max(max, Number(value) || 0);
    });
    return max || 1;
  }

  function svgEl(name, attrs) {
    var el = document.createElementNS('http://www.w3.org/2000/svg', name);
    Object.keys(attrs || {}).forEach(function (key) {
      el.setAttribute(key, attrs[key]);
    });
    return el;
  }

  function addText(svg, x, y, value, size, anchor) {
    var text = svgEl('text', {
      x: x,
      y: y,
      'font-size': size || 11,
      'text-anchor': anchor || 'middle',
      fill: '#475467'
    });
    text.textContent = value;
    svg.appendChild(text);
  }

  function addYAxis(svg, margin, chartH, max) {
    for (var tick = 0; tick <= 4; tick++) {
      var value = Math.round((max / 4) * tick);
      var y = margin.top + chartH - (chartH / 4) * tick;
      svg.appendChild(svgEl('line', { x1: margin.left - 4, y1: y, x2: margin.left, y2: y, stroke: '#98a2b3' }));
      addText(svg, margin.left - 8, y + 4, value, 10, 'end');
      if (tick > 0) {
        svg.appendChild(svgEl('line', { x1: margin.left, y1: y, x2: svg.getAttribute('viewBox').split(' ')[2] - margin.right, y2: y, stroke: '#eef2f7', 'stroke-dasharray': '3 3' }));
      }
    }
  }

  function renderBar(selector, labels, series, options) {
    var el = document.querySelector(selector);
    if (!el) { return; }
    clear(el);

    options = options || {};
    var rows = Array.isArray(series[0]) ? series : [series];
    var flat = [];
    rows.forEach(function (row) { flat = flat.concat(row); });

    var width = Math.max(el.clientWidth || 640, 320);
    var height = el.clientHeight || 260;
    var margin = { top: 18, right: 20, bottom: 52, left: 44 };
    var chartW = width - margin.left - margin.right;
    var chartH = height - margin.top - margin.bottom;
    var max = maxValue(flat);
    var groupW = chartW / Math.max(labels.length, 1);
    var barW = options.barWidth || Math.max((groupW - 14) / rows.length, 8);
    var svg = svgEl('svg', { width: '100%', height: height, viewBox: '0 0 ' + width + ' ' + height });

    svg.appendChild(svgEl('line', { x1: margin.left, y1: margin.top + chartH, x2: width - margin.right, y2: margin.top + chartH, stroke: '#d0d5dd' }));
    svg.appendChild(svgEl('line', { x1: margin.left, y1: margin.top, x2: margin.left, y2: margin.top + chartH, stroke: '#d0d5dd' }));
    addYAxis(svg, margin, chartH, max);

    labels.forEach(function (label, i) {
      rows.forEach(function (row, rowIndex) {
        var value = Number(row[i]) || 0;
        var barH = (value / max) * chartH;
        var x = margin.left + i * groupW + (groupW - (barW * rows.length)) / 2 + rowIndex * barW;
        var y = margin.top + chartH - barH;
        var rect = svgEl('rect', {
          x: x,
          y: y,
          width: Math.max(barW - 2, 4),
          height: Math.max(barH, 2),
          rx: 3,
          fill: colors[rowIndex % colors.length]
        });
        bindTooltip(rect, label, value);
        svg.appendChild(rect);
      });

      var shortLabel = String(label).length > 14 ? String(label).substring(0, 12) + '..' : label;
      addText(svg, margin.left + i * groupW + groupW / 2, height - 22, shortLabel, 10, 'middle');
    });

    el.appendChild(svg);
  }

  function renderLine(selector, labels, series) {
    var el = document.querySelector(selector);
    if (!el) { return; }
    clear(el);

    var width = Math.max(el.clientWidth || 640, 320);
    var height = el.clientHeight || 260;
    var margin = { top: 18, right: 22, bottom: 44, left: 44 };
    var chartW = width - margin.left - margin.right;
    var chartH = height - margin.top - margin.bottom;
    var flat = [];
    series.forEach(function (row) { flat = flat.concat(row); });
    var max = maxValue(flat);
    var svg = svgEl('svg', { width: '100%', height: height, viewBox: '0 0 ' + width + ' ' + height });

    svg.appendChild(svgEl('line', { x1: margin.left, y1: margin.top + chartH, x2: width - margin.right, y2: margin.top + chartH, stroke: '#d0d5dd' }));
    svg.appendChild(svgEl('line', { x1: margin.left, y1: margin.top, x2: margin.left, y2: margin.top + chartH, stroke: '#d0d5dd' }));
    addYAxis(svg, margin, chartH, max);

    series.forEach(function (row, rowIndex) {
      var points = row.map(function (value, i) {
        return {
          x: margin.left + (labels.length <= 1 ? chartW / 2 : (i / (labels.length - 1)) * chartW),
          y: margin.top + chartH - ((Number(value) || 0) / max) * chartH,
          value: value,
          label: labels[i]
        };
      });
      var d = points.map(function (point, index) {
        return (index === 0 ? 'M' : 'L') + point.x + ' ' + point.y;
      }).join(' ');
      svg.appendChild(svgEl('path', { d: d, fill: 'none', stroke: colors[rowIndex % colors.length], 'stroke-width': 3 }));
      points.forEach(function (point) {
        var circle = svgEl('circle', { cx: point.x, cy: point.y, r: 5, fill: colors[rowIndex % colors.length] });
        bindTooltip(circle, point.label, point.value);
        svg.appendChild(circle);
      });
    });

    labels.forEach(function (label, i) {
      var x = margin.left + (labels.length <= 1 ? chartW / 2 : (i / (labels.length - 1)) * chartW);
      addText(svg, x, height - 18, label, 10, 'middle');
    });

    el.appendChild(svg);
  }

  function renderPie(selector, labels, values) {
    var el = document.querySelector(selector);
    if (!el) { return; }
    clear(el);

    var width = Math.max(el.clientWidth || 420, 300);
    var height = el.clientHeight || 240;
    var radius = Math.min(width, height) / 2 - 30;
    var cx = width / 2;
    var cy = height / 2 - 8;
    var total = values.reduce(function (sum, value) { return sum + (Number(value) || 0); }, 0) || 1;
    var startAngle = -Math.PI / 2;
    var svg = svgEl('svg', { width: '100%', height: height, viewBox: '0 0 ' + width + ' ' + height });

    values.forEach(function (value, i) {
      var numericValue = Number(value) || 0;
      var angle = (numericValue / total) * Math.PI * 2;
      var endAngle = startAngle + angle;
      var x1 = cx + radius * Math.cos(startAngle);
      var y1 = cy + radius * Math.sin(startAngle);
      var x2 = cx + radius * Math.cos(endAngle);
      var y2 = cy + radius * Math.sin(endAngle);
      var largeArc = angle > Math.PI ? 1 : 0;
      var d = ['M', cx, cy, 'L', x1, y1, 'A', radius, radius, 0, largeArc, 1, x2, y2, 'Z'].join(' ');
      var slice = svgEl('path', { d: d, fill: colors[i % colors.length] });
      bindTooltip(slice, labels[i] || '', numericValue);
      svg.appendChild(slice);
      startAngle = endAngle;
    });

    svg.appendChild(svgEl('circle', { cx: cx, cy: cy, r: Math.max(radius * 0.58, 30), fill: '#fff' }));

    values.forEach(function (value, i) {
      if (i > 3) { return; }
      var x = 18 + i * Math.max(width / 4, 90);
      var y = height - 18;
      svg.appendChild(svgEl('rect', { x: x, y: y - 9, width: 9, height: 9, fill: colors[i % colors.length] }));
      addText(svg, x + 14, y, labels[i] || '', 10, 'start');
    });

    el.appendChild(svg);
  }

  window.mfgCharts = {
    bar: renderBar,
    line: renderLine,
    pie: renderPie
  };

  function parseChartData() {
    var holder = document.getElementById('mfgDashboardChartData');
    if (!holder) { return null; }
    try {
      return JSON.parse(holder.textContent || holder.innerText || '{}');
    } catch (error) {
      return null;
    }
  }

  function renderAdmin(data) {
    renderLine('#adminProductionTrend', data.trendLabels, [data.trendRequest, data.trendProduced]);
    renderPie('#adminStatusPie', data.statusLabels, data.statusSeries);
    renderBar('#adminStockBar', data.stockLabels, data.stockSeries, { barWidth: 38 });
    renderPie('#adminQcPie', data.qcLabels, data.qcSeries);
  }

  function renderPlanner(data) {
    renderBar('#plannerFunnelBar', data.planningLabels, data.planningSeries, { barWidth: 38 });
    renderPie('#plannerReadinessPie', data.readinessLabels, data.readinessSeries);
    renderLine('#plannerTrendLine', data.trendLabels, [data.trendRequest, data.trendProduced]);
    renderBar('#plannerProductBar', data.productLabels, data.productSeries, { barWidth: 38 });
  }

  function renderSupervisor(data) {
    renderBar('#supervisorWoBar', data.orderLabels, [data.plannedSeries, data.completedSeries], { barWidth: 18 });
    renderPie('#supervisorStatusPie', data.statusLabels, data.statusSeries);
    renderPie('#supervisorCompletionPie', data.completionLabels, data.completionSeries);
    renderBar('#supervisorMaterialBar', data.materialLabels, data.materialSeries, { barWidth: 38 });

    var fill = document.querySelector('.mfg-progress-fill[data-progress]');
    if (fill) {
      fill.style.width = Math.min(Number(fill.getAttribute('data-progress')) || 0, 100) + '%';
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    var data = parseChartData();
    var page = document.querySelector('.mfg-dashboard[data-dashboard]');
    if (!data || !page) { return; }

    if (page.getAttribute('data-dashboard') === 'admin') {
      renderAdmin(data);
    } else if (page.getAttribute('data-dashboard') === 'planner') {
      renderPlanner(data);
    } else if (page.getAttribute('data-dashboard') === 'supervisor') {
      renderSupervisor(data);
    }
  });
})(window, document);

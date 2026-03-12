// assets/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
// ---------- Historical Chart ----------
fetch('/invoice/procedure/data/')
.then(res => res.json())
.then(json => {

  const rawData = json.data;
// ==========================
// HISTORICAL DONUT CHART
// ==========================
if (document.getElementById("pieChart")) {

  const sorted = [...rawData].sort((a,b)=>b.total_revenue-a.total_revenue);

  const topCount = 7;
  const topData = sorted.slice(0,topCount);
  const otherValue = sorted.slice(topCount).reduce((s,d)=>s+d.total_revenue,0);

  const labels = topData.map(d=>d.procedure_name);
  const values = topData.map(d=>d.total_revenue);

  if(otherValue>0){
    labels.push("Other");
    values.push(otherValue);
  }

  const total = values.reduce((a,b)=>a+b,0);

  const ctx = document.getElementById("pieChart").getContext("2d");

  new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: labels,
      datasets: [{
        data: values,
        backgroundColor: ['#FFF7CD','#FDC3A1','#FB9B8F','#F57799','#FFD580','#FFB6B9','#C1E1C1','#D3C6A0'],
        borderColor: "#fff",
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '50%', // donut size
      plugins: {
        legend: { position: "bottom" },
        tooltip: {
          callbacks: {
            label: function(ctx){
              const percent = ((ctx.raw / total) * 100).toFixed(1);
              return ctx.label + ": " + percent + "%";
            }
          }
        },
        datalabels: {
          color: '#333',
          anchor: 'end',
          align: 'end',
          formatter: function(value, ctx) {
            const percent = ((value / total) * 100).toFixed(1);
            return percent + '%';
          }
        }
      }
    },
    plugins: [ChartDataLabels]
  });
}

// ==========================
// PREDICTED DONUT CHART
// ==========================
if (document.getElementById("predictedPieChart")) {

  fetch("python_script/forecast_finance.json")
    .then(res => res.json())
    .then(forecastData => {

      const june = forecastData.future_forecast.find(
        f => new Date(f.ds).getMonth()===5 && new Date(f.ds).getFullYear()===2026
      );

      const predictedRevenue = june.yhat;
      const totalHistoricalRevenue = rawData.reduce((s,d)=>s+d.total_revenue,0);

      const predicted = rawData.map(p => {
        const percentage = p.total_revenue / totalHistoricalRevenue;
        const procedureRevenue = predictedRevenue * percentage;
        const avgPrice = p.total_revenue / (p.count || 1);
        const predictedCount = Math.round(procedureRevenue / avgPrice);
        return { procedure_name: p.procedure_name, predictedCount };
      });

      predicted.sort((a,b)=>b.predictedCount - a.predictedCount);

      const top7 = predicted.slice(0,7);
      const otherTotal = predicted.slice(7).reduce((s,d)=>s+d.predictedCount,0);
      if(otherTotal>0) top7.push({ procedure_name: "Other", predictedCount: otherTotal });

      const labels = top7.map(d=>d.procedure_name);
      const values = top7.map(d=>d.predictedCount);
      const total = values.reduce((a,b)=>a+b,0);

      const ctx = document.getElementById("predictedPieChart").getContext("2d");

      new Chart(ctx, {
        type: "doughnut",
        data: {
          labels: labels,
          datasets: [{
            data: values,
            backgroundColor: ['#FFF7CD','#FDC3A1','#FB9B8F','#F57799','#FFD580','#FFB6B9','#C1E1C1','#D3C6A0'],
            borderColor: "#fff",
            borderWidth: 2
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '50%',
          plugins: {
            legend: { position: "bottom" },
            tooltip: {
              callbacks: {
                label: function(ctx){
                  return ctx.label + ": " + ctx.raw + " procedures (" + ((ctx.raw/total)*100).toFixed(1) + "%)";
                }
              }
            },
            datalabels: {
              color: '#333',
              anchor: 'end',
              align: 'end',
              formatter: function(value) {
                return value; // shows number outside slices
              }
            }
          }
        },
        plugins: [ChartDataLabels]
      });
    });
}
})
.catch(err=>console.error(err));
// ===============================
// PROFESSIONAL FINANCE FORECAST
// ===============================

if (document.getElementById("predictionChart")) {

    fetch("python_script/forecast_finance.json")
    .then(res => res.json())
    .then(data => {

        const validation = data.validation_2025;
        const future = data.future_forecast;
        const recommendations = data.recommendations;

        // ===============================
        // MERGE VALIDATION + FUTURE
        // ===============================
        const allData = [...validation, ...future];

        const labels = allData.map(v =>
            new Date(v.ds).toLocaleString('default', { month: 'short', year: 'numeric' })
        );

        const actual = allData.map(v => v.actual ?? null);
        const predicted = allData.map(v => v.predicted ?? v.yhat);
        const lower = allData.map(v => v.yhat_lower ?? null);
        const upper = allData.map(v => v.yhat_upper ?? null);

        // ANOMALIES MARKERS
        const anomalies = allData.map(v => (v.anomaly ? v.actual : null));

        const ctx = document.getElementById("predictionChart").getContext("2d");

        // ===============================
        // GRADIENT FOR FORECAST AREA
        // ===============================
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, "rgba(39,174,96,0.3)");
        gradient.addColorStop(1, "rgba(39,174,96,0)");

        const rangeGradient = ctx.createLinearGradient(0, 0, 0, 400);
        rangeGradient.addColorStop(0, "rgba(52,152,219,0.15)");
        rangeGradient.addColorStop(1, "rgba(52,152,219,0)");

        // ===============================
        // CREATE CHART
        // ===============================
        new Chart(ctx, {
            type: "line",
            data: {
                labels: labels,
                datasets: [
                    // ACTUAL REVENUE
                    {
                        label: "Actual Revenue",
                        data: actual,
                        borderColor: "#2E86DE",
                        backgroundColor: "#2E86DE",
                        borderWidth: 3,
                        tension: 0.3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        order: 1
                    },
                    // PREDICTED REVENUE
                    {
                        label: "Predicted Revenue",
                        data: predicted,
                        borderColor: "#27AE60",
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        borderDash: [6, 4],
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        order: 2
                    },
                    
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: "top",
                        labels: {
                            font: { size: 13 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context){
                                let value = context.parsed.y;
                                if(value === null) return "";
                                return `${context.dataset.label}: Rs ${value.toLocaleString()}`;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: "📊 Monthly Revenue Forecast ",
                        font: { size: 18, weight: '600' },
                        padding: { top: 10, bottom: 20 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: { color: "rgba(0,0,0,0.05)" },
                        ticks: {
                            callback: function(value) {
                                return "Rs " + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });

        // ===============================
        // DISPLAY RECOMMENDATIONS
        // ===============================
        const recContainer = document.getElementById("forecastRecommendations");
        if(recContainer && recommendations.length > 0){
            recContainer.innerHTML = "<h3>💡 Recommendations:</h3><ul>" + 
                recommendations.map(r => `<li>${r}</li>`).join("") + 
                "</ul>";
        }

    })
    .catch(err => console.error("Error loading forecast data:", err));
}

  const canvas = document.getElementById('regChart');
  canvas.height = canvas.parentElement.offsetHeight; // fill parent div height

  if (!canvas) {
    console.warn('regChart canvas not found!');
    return;
  }


console.log('chartLabels:', window.chartLabels);
console.log('chartData:', window.chartData);



  const ctx = canvas.getContext('2d');

  // Example: if PHP injected the data as JSON in a <script> before this file:
  // window.chartLabels = ["2019-01", "2019-02", ...];
  // window.chartData = [50, 60, ...];

  const labels = window.chartLabels || [];
  const dataPoints = window.chartData || [];


  // Create gradient for fill
  const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
  gradient.addColorStop(0, 'rgba(240, 135, 135, 0.5)'); // your rose color, semi-transparent
  gradient.addColorStop(1, 'rgba(240, 135, 135, 0)');

  const chart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [
        {
          label: 'Registrations',
          data: dataPoints,
          fill: true,
          backgroundColor: gradient,
          borderColor: 'rgba(240, 135, 135, 1)',
          borderWidth: 2,
          tension: 0.4,
          pointRadius: 3,
          pointBackgroundColor: 'rgba(240, 135, 135, 1)',
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: {
          title: {
            display: true,
            text: 'Month (YYYY-MM)',
          },
          ticks: {
            color: getComputedStyle(canvas).getPropertyValue('--text-muted') || '#666'
          },
        },
        y: {
          title: {
            display: true,
            text: 'Number of Registrations',
          },
          ticks: {
            color: getComputedStyle(canvas).getPropertyValue('--text-muted') || '#666'
          },
        },
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: (context) => {
              let value = context.parsed.y;
              return value + ' registrations';
            }
          }
        },
        legend: {
          display: false
        }
      },
      animation: {
        duration: 1000,
        easing: 'easeOutQuad'
      }
    }
  });

});

// // assets/js/chart.js

// document.addEventListener('DOMContentLoaded', function () {

 
//  const canvass = document.getElementById("barChart");
//   if (!canvass) return;

//   fetch("/patient/pareto")
//     .then(res => res.json())
//     .then(json => {

//       if (json.status !== "success") return;

//       const labels = json.data.chart.labels;
//       const revenues = json.data.chart.revenues;
//       const vipPatients = json.data.vip_patients;
//       const insight = json.insight;

//       const ctx = canvass.getContext("2d");

//       if (window.paretoBarChart) window.paretoBarChart.destroy();

//       /* ---------- COLOR THEME ---------- */

//       const root = getComputedStyle(document.documentElement);

//       const darkPeach =
//         root.getPropertyValue('--color-rose').trim() || "#ee6a6a";

//       const lightPeach =
//         root.getPropertyValue('--color-peach').trim() || "#f5ac82";

//       const gradient = ctx.createLinearGradient(0, 0, 0, 400);
//       gradient.addColorStop(0, darkPeach);
//       gradient.addColorStop(1, lightPeach);

//       /* ---------- PARETO CALCULATION ---------- */

//       const total = revenues.reduce((a, b) => a + b, 0);

//       let cumulative = 0;

//       const cumulativePercent = revenues.map(v => {
//         cumulative += v;
//         return ((cumulative / total) * 100).toFixed(2);
//       });

//       /* ---------- DOME BAR PLUGIN ---------- */

//       const DomeBar = {
//         id: "domeBar",
//         beforeDatasetsDraw(chart) {

//           const { ctx } = chart;
//           const meta = chart.getDatasetMeta(0);

//           ctx.save();

//           meta.data.forEach(bar => {

//             const { x, y, base, width } = bar;
//             const r = width / 2;

//             ctx.beginPath();

//             ctx.moveTo(x - width / 2, y + r);
//             ctx.arc(x, y + r, r, Math.PI, 0);
//             ctx.lineTo(x + width / 2, base);
//             ctx.lineTo(x - width / 2, base);

//             ctx.closePath();

//             ctx.fillStyle = gradient;
//             ctx.fill();
//           });

//           ctx.restore();
//         }
//       };

//       /* ---------- CHART ---------- */

//       window.paretoBarChart = new Chart(ctx, {

//         data: {
//           labels: labels,
//           datasets: [

//             {
//               type: "bar",
//               data: revenues,
//               backgroundColor: "transparent",
//               barPercentage: 0.95,
//               categoryPercentage: 0.98
//             },

//             {
//               type: "line",
//               data: cumulativePercent,
//               borderColor: darkPeach,
//               backgroundColor: darkPeach,
//               yAxisID: "y1",
//               tension: 0.35,
//               pointRadius: 4
//             }

//           ]
//         },

//         options: {

//           responsive: true,
//           maintainAspectRatio: false,

//           plugins: {

//             legend: { display: false },

//             tooltip: {
//               callbacks: {

//                 label: function(context) {

//                   if (context.dataset.type === "line") {
//                     return "Cumulative: " + context.raw + "%";
//                   }

//                   const patient = vipPatients[context.dataIndex];

//                   return [
//                     "Revenue: " + Number(patient.revenue).toLocaleString(),
//                     "Visits: " + patient.visits
//                   ];
//                 }
//               }
//             }
//           },

//           scales: {

//             x: {
//               grid: { display: false },
//               border: { display: false }
//             },

//             y: {
//               beginAtZero: true,
//               grid: { color: "rgba(0,0,0,0.05)", borderDash: [3,3] },
//               ticks: {
//                 callback: v => Number(v).toLocaleString()
//               },
//               border: { display: false }
//             },

//             y1: {
//               position: "right",
//               min: 0,
//               max: 100,
//               grid: { display: false },
//               ticks: {
//                 callback: v => v + "%"
//               }
//             }
//           }
//         },

//         plugins: [DomeBar]
//       });

//       /* ---------- INSIGHT ---------- */

 
//   const insightBox = document.getElementById("paretoInsight");
//   const loyalPatients = json.data.loyal_patients;

//   // Add insight text + "See More" link
//   insightBox.innerHTML = `
//     📊 <strong>Pareto Insight:</strong> ${insight} 
//     <a href="#" id="seeMorePareto" style="margin-left:10px; text-decoration:underline; color:#ee6a6a; cursor:pointer;">See More</a>
//   `;

//   // Modal elements
//   const modal = document.getElementById("paretoModal");
//   const tbody = document.getElementById("paretoPatientTable");
//   const closeBtn = document.getElementById("paretoModalClose");

//   // Populate table rows dynamically with loyal_patients
//  // Clear table
// tbody.innerHTML = "";

// // Add rows dynamically
// loyalPatients.forEach(p => {
//   const tr = document.createElement("tr");

//   // Name cell
//   const nameTd = document.createElement("td");
//   nameTd.textContent = p.patient_name;
//   nameTd.style.padding = "8px";
//   nameTd.style.borderBottom = "1px solid #eee";
//   nameTd.style.cursor = "pointer";
//   nameTd.style.color = "#007bff";

//   // Attach click handler to open profile in new tab
//   nameTd.addEventListener("click", () => {
//     window.open(`/patient/profile/${p.patient_id}`, "_blank");
//   });

//   // Revenue cell
//   const revTd = document.createElement("td");
//   revTd.textContent = Number(p.revenue).toLocaleString();
//   revTd.style.padding = "8px";
//   revTd.style.borderBottom = "1px solid #eee";
//   revTd.style.textAlign = "right";

//   // Visits cell
//   const visitsTd = document.createElement("td");
//   visitsTd.textContent = p.visits;
//   visitsTd.style.padding = "8px";
//   visitsTd.style.borderBottom = "1px solid #eee";
//   visitsTd.style.textAlign = "right";

//   // Append cells to row
//   tr.appendChild(nameTd);
//   tr.appendChild(revTd);
//   tr.appendChild(visitsTd);

//   // Append row to tbody
//   tbody.appendChild(tr);
// });

//   // Show modal on "See More"
//   document.getElementById("seeMorePareto").addEventListener("click", e => {
//     e.preventDefault();
//     modal.style.display = "flex";
//   });

//   // Close modal via cross
//   closeBtn.addEventListener("click", () => {
//     modal.style.display = "none";
//   });

//   // Click outside modal to close
//   modal.addEventListener("click", e => {
//     if (e.target === modal) modal.style.display = "none";
//   });

// })
// .catch(err => console.error(err));

  
// // ---------- Historical Chart ----------
// fetch('/invoice/procedure/data/')
// .then(res => res.json())
// .then(json => {

//   const rawData = json.data;
// // ==========================
// // HISTORICAL DONUT CHART
// // ==========================
// if (document.getElementById("pieChart")) {

//   const sorted = [...rawData].sort((a,b)=>b.total_revenue-a.total_revenue);

//   const topCount = 7;
//   const topData = sorted.slice(0,topCount);
//   const otherValue = sorted.slice(topCount).reduce((s,d)=>s+d.total_revenue,0);

//   const labels = topData.map(d=>d.procedure_name);
//   const values = topData.map(d=>d.total_revenue);

//   if(otherValue>0){
//     labels.push("Other");
//     values.push(otherValue);
//   }

//   const total = values.reduce((a,b)=>a+b,0);

//   const ctx = document.getElementById("pieChart").getContext("2d");

//   new Chart(ctx, {
//     type: "doughnut",
//     data: {
//       labels: labels,
//       datasets: [{
//         data: values,
//         backgroundColor: ['#FFF7CD','#FDC3A1','#FB9B8F','#F57799','#FFD580','#FFB6B9','#C1E1C1','#D3C6A0'],
//         borderColor: "#fff",
//         borderWidth: 2
//       }]
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       cutout: '50%', // donut size
//       plugins: {
//         legend: { position: "bottom" },
//         tooltip: {
//           callbacks: {
//             label: function(ctx){
//               const percent = ((ctx.raw / total) * 100).toFixed(1);
//               return ctx.label + ": " + percent + "%";
//             }
//           }
//         },
//         datalabels: {
//           color: '#333',
//           anchor: 'center',
//     align: 'center',
//           formatter: function(value, ctx) {
//             const percent = ((value / total) * 100).toFixed(1);
//             return percent + '%';
//           }
//         }
//       }
//     },
//     plugins: [ChartDataLabels]
//   });
// }

// // ==========================
// // PREDICTED DONUT CHART
// // ==========================
// if (document.getElementById("predictedPieChart")) {

//   fetch("python_script/forecast_finance.json")
//     .then(res => res.json())
//     .then(forecastData => {

//       const june = forecastData.future_forecast.find(
//         f => new Date(f.ds).getMonth()===5 && new Date(f.ds).getFullYear()===2026
//       );

//       const predictedRevenue = june.yhat;
//       const totalHistoricalRevenue = rawData.reduce((s,d)=>s+d.total_revenue,0);

//       const predicted = rawData.map(p => {
//         const percentage = p.total_revenue / totalHistoricalRevenue;
//         const procedureRevenue = predictedRevenue * percentage;
//         const avgPrice = p.total_revenue / (p.count || 1);
//         const predictedCount = Math.round(procedureRevenue / avgPrice);
//         return { procedure_name: p.procedure_name, predictedCount };
//       });

//       predicted.sort((a,b)=>b.predictedCount - a.predictedCount);

//       const top7 = predicted.slice(0,7);
//       const otherTotal = predicted.slice(7).reduce((s,d)=>s+d.predictedCount,0);
//       if(otherTotal>0) top7.push({ procedure_name: "Other", predictedCount: otherTotal });

//       const labels = top7.map(d=>d.procedure_name);
//       const values = top7.map(d=>d.predictedCount);
//       const total = values.reduce((a,b)=>a+b,0);

//       const ctx = document.getElementById("predictedPieChart").getContext("2d");

//       new Chart(ctx, {
//         type: "doughnut",
//         data: {
//           labels: labels,
//           datasets: [{
//             data: values,
//             backgroundColor: ['#FFF7CD','#FDC3A1','#FB9B8F','#F57799','#FFD580','#FFB6B9','#C1E1C1','#D3C6A0'],
//             borderColor: "#fff",
//             borderWidth: 2
//           }]
//         },
//         options: {
//           responsive: true,
//           maintainAspectRatio: false,
//           cutout: '50%',
//           plugins: {
//             legend: { position: "bottom" },
//             tooltip: {
//               callbacks: {
//                 label: function(ctx){
//                   return ctx.label + ": " + ctx.raw + " procedures (" + ((ctx.raw/total)*100).toFixed(1) + "%)";
//                 }
//               }
//             },
//             datalabels: {
//               color: '#333',
//               anchor: 'center',
//     align: 'center',
//               formatter: function(value) {
//                 return value; // shows number outside slices
//               }
//             }
//           }
//         },
//         plugins: [ChartDataLabels]
//       });
//     });
// }
// })
// .catch(err=>console.error(err));
// // ===============================
// // PROFESSIONAL FINANCE FORECAST
// // ===============================

// if (document.getElementById("predictionChart")) {

//     fetch("python_script/forecast_finance.json")
//     .then(res => res.json())
//     .then(data => {

//         const validation = data.validation_2025;
//         const future = data.future_forecast;
//         const recommendations = data.recommendations;

//         // ===============================
//         // MERGE VALIDATION + FUTURE
//         // ===============================
//         const cleanValidation = validation.filter(v => {
//     const month = new Date(v.ds).toISOString().slice(0, 7);
//     return month !== "2026-05";
// });
//  const allDataRaw = [...validation, ...future];

//         const allData = allDataRaw.filter(v => {
//             const date = new Date(v.ds);
//             return date >= new Date("2025-06-01");
//         });

//         const labels = allData.map(v =>
//             new Date(v.ds).toLocaleString('default', { month: 'short', year: 'numeric' })
//         );

//         const actual = allData.map(v => v.actual ?? null);
//         const predicted = allData.map(v => v.predicted ?? v.yhat);
//         const lower = allData.map(v => v.yhat_lower ?? null);
//         const upper = allData.map(v => v.yhat_upper ?? null);
//         const anomalies = allData.map(v => (v.anomaly ? v.actual : null));

//         const ctx = document.getElementById("predictionChart").getContext("2d");

//         // ===============================
//         // GRADIENT FOR FORECAST AREA
//         // ===============================
//         const gradient = ctx.createLinearGradient(0, 0, 0, 400);
//         gradient.addColorStop(0, "rgba(39,174,96,0.3)");
//         gradient.addColorStop(1, "rgba(39,174,96,0)");

//         const rangeGradient = ctx.createLinearGradient(0, 0, 0, 400);
//         rangeGradient.addColorStop(0, "rgba(52,152,219,0.15)");
//         rangeGradient.addColorStop(1, "rgba(52,152,219,0)");

//         // ===============================
//         // CREATE CHART
//         // ===============================
//        new Chart(ctx, {
//   type: "line",
//   data: {
//     labels: labels,
//     datasets: [

//       // ACTUAL REVENUE
//       {
//         label: "Actual Revenue",
//         data: actual,
//         borderColor: "#ee6a6a",
//         backgroundColor: "#ee6a6a",
//         borderWidth: 3,
//         tension: 0.35,
//         pointRadius: 5,
//         pointHoverRadius: 8,
//         pointBackgroundColor: "#fff",
//         pointBorderWidth: 2,
//         pointBorderColor: "#ee6a6a"
//       },

//       // PREDICTED REVENUE
//      {
//   label: "Predicted Revenue",
//   data: predicted,
//   borderColor: "#2E86DE",   // blue prediction line
//   backgroundColor: gradient,
//   borderDash: [6,5],
//   borderWidth: 3,
//   tension: 0.4,
//   fill: true,
//   pointRadius: 4,
//   pointHoverRadius: 7,
//   pointBackgroundColor: "#fff",
//   pointBorderWidth: 2,
//   pointBorderColor: "#2E86DE"   // blue points
// }
//     ]
//   },

//   options: {
//     responsive: true,
//     maintainAspectRatio:false,

//     interaction: {
//       mode: "index",
//       intersect: false
//     },

//     plugins: {

//       legend: {
//         position: "bottom",
//         labels: {
//           color: "#333",
//           font: {
//             family: "Poppins",
//             size: 13
//           },
//           boxWidth: 12
//         }
//       },

//       tooltip: {
//         backgroundColor:"#fff",
//         titleColor:"#333",
//         bodyColor:"#333",
//         borderColor:"#eee",
//         borderWidth:1,
//         padding:12,
//         callbacks: {
//           label: function(context){
//             let value = context.parsed.y;
//             if(value===null) return "";
//             return `${context.dataset.label}: Rs ${value.toLocaleString()}`;
//           }
//         }
//       },

//       title: {
//         display: true,
//         text: "Monthly Revenue Forecast",
//         color:"#333",
//         font: {
//           family:"Poppins",
//           size:18,
//           weight:"600"
//         },
//         padding:{bottom:15}
//       }

//     },

//     scales: {

//       y: {
//         grid: {
//           color: "rgba(0,0,0,0.05)"
//         },
//         ticks: {
//           color:"#666",
//           callback: function(value){
//             return "Rs " + value.toLocaleString();
//           }
//         }
//       },

//       x: {
//         grid: {
//           display:false
//         },
//         ticks:{
//           color:"#666"
//         }
//       }

//     }
//   }
// });
//         // ===============================
//         // DISPLAY RECOMMENDATIONS
//         // ===============================
//         // ===============================
// // DISPLAY RECOMMENDATIONS
// // ===============================
// const recContainer = document.getElementById("forecastRecommendations");

// if (recContainer && recommendations.length > 0) {

//     let html = "<h3 class='rec-title'>💡 Recommendations</h3>";

//     html += recommendations.map(r => {

//         let cls = "recommendation-stable";

//         if (r.toLowerCase().includes("decline") || r.toLowerCase().includes("lower")) {
//             cls = "recommendation-decline";
//         } 
//         else if (r.toLowerCase().includes("growth") || r.toLowerCase().includes("high")) {
//             cls = "recommendation-growth";
//         }

//         return `<div class="recommendation-card ${cls}">${r}</div>`;

//     }).join("");

//     recContainer.innerHTML = html;
// }

//     })
//     .catch(err => console.error("Error loading forecast data:", err));
// }

//   const canvas = document.getElementById('regChart');
//   canvas.height = canvas.parentElement.offsetHeight; // fill parent div height

//   if (!canvas) {
//     console.warn('regChart canvas not found!');
//     return;
//   }


// console.log('chartLabels:', window.chartLabels);
// console.log('chartData:', window.chartData);



//   const ctx = canvas.getContext('2d');

//   // Example: if PHP injected the data as JSON in a <script> before this file:
//   // window.chartLabels = ["2019-01", "2019-02", ...];
//   // window.chartData = [50, 60, ...];

//   const labels = window.chartLabels || [];
//   const dataPoints = window.chartData || [];


//   // Create gradient for fill
//   const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
//   gradient.addColorStop(0, 'rgba(240, 135, 135, 0.5)'); // your rose color, semi-transparent
//   gradient.addColorStop(1, 'rgba(240, 135, 135, 0)');

//   const chart = new Chart(ctx, {
//     type: 'line',
//     data: {
//       labels: labels,
//       datasets: [
//         {
//           label: 'Registrations',
//           data: dataPoints,
//           fill: true,
//           backgroundColor: gradient,
//           borderColor: 'rgba(240, 135, 135, 1)',
//           borderWidth: 2,
//           tension: 0.4,
//           pointRadius: 3,
//           pointBackgroundColor: 'rgba(240, 135, 135, 1)',
//         },
//       ],
//     },
//     options: {
//       responsive: true,
//       maintainAspectRatio: false,
//       scales: {
//         x: {
//           title: {
//             display: true,
//             text: 'Month (YYYY-MM)',
//           },
//           ticks: {
//             color: getComputedStyle(canvas).getPropertyValue('--text-muted') || '#666'
//           },
//         },
//         y: {
//           title: {
//             display: true,
//             text: 'Number of Registrations',
//           },
//           ticks: {
//             color: getComputedStyle(canvas).getPropertyValue('--text-muted') || '#666'
//           },
//         },
//       },
//       plugins: {
//         tooltip: {
//           callbacks: {
//             label: (context) => {
//               let value = context.parsed.y;
//               return value + ' registrations';
//             }
//           }
//         },
//         legend: {
//           display: false
//         }
//       },
//       animation: {
//         duration: 1000,
//         easing: 'easeOutQuad'
//       }
//     }
//   });

// });
// assets/js/chart.js

document.addEventListener('DOMContentLoaded', function () {

 
 const canvass = document.getElementById("barChart");
  if (!canvass) return;

  fetch("/patient/pareto")
    .then(res => res.json())
    .then(json => {

      if (json.status !== "success") return;

      const labels = json.data.chart.labels;
      const revenues = json.data.chart.revenues;
      const vipPatients = json.data.vip_patients;
      const insight = json.insight;

      const ctx = canvass.getContext("2d");

      if (window.paretoBarChart) window.paretoBarChart.destroy();

      /* ---------- COLOR THEME ---------- */

      const root = getComputedStyle(document.documentElement);

      const darkPeach =
        root.getPropertyValue('--color-rose').trim() || "#ee6a6a";

      const lightPeach =
        root.getPropertyValue('--color-peach').trim() || "#f5ac82";

      const gradient = ctx.createLinearGradient(0, 0, 0, 400);
      gradient.addColorStop(0, darkPeach);
      gradient.addColorStop(1, lightPeach);

      /* ---------- PARETO CALCULATION ---------- */

      const total = revenues.reduce((a, b) => a + b, 0);

      let cumulative = 0;

      const cumulativePercent = revenues.map(v => {
        cumulative += v;
        return ((cumulative / total) * 100).toFixed(2);
      });

      /* ---------- DOME BAR PLUGIN ---------- */

      const DomeBar = {
        id: "domeBar",
        beforeDatasetsDraw(chart) {

          const { ctx } = chart;
          const meta = chart.getDatasetMeta(0);

          ctx.save();

          meta.data.forEach(bar => {

            const { x, y, base, width } = bar;
            const r = width / 2;

            ctx.beginPath();

            ctx.moveTo(x - width / 2, y + r);
            ctx.arc(x, y + r, r, Math.PI, 0);
            ctx.lineTo(x + width / 2, base);
            ctx.lineTo(x - width / 2, base);

            ctx.closePath();

            ctx.fillStyle = gradient;
            ctx.fill();
          });

          ctx.restore();
        }
      };

      /* ---------- CHART ---------- */

      window.paretoBarChart = new Chart(ctx, {

        data: {
          labels: labels,
          datasets: [

            {
              type: "bar",
              data: revenues,
              backgroundColor: "transparent",
              barPercentage: 0.95,
              categoryPercentage: 0.98
            }

            // {
            //   type: "line",
            //   data: cumulativePercent,
            //   borderColor: darkPeach,
            //   backgroundColor: darkPeach,
            //   yAxisID: "y1",
            //   tension: 0.35,
            //   pointRadius: 4
            // }

          ]
        },

        options: {

          responsive: true,
          maintainAspectRatio: false,

          plugins: {

            legend: { display: false },

            tooltip: {
              callbacks: {

                label: function(context) {

                  if (context.dataset.type === "line") {
                    return "Cumulative: " + context.raw + "%";
                  }

                  const patient = vipPatients[context.dataIndex];

                  return [
                    "Revenue: " + Number(patient.revenue).toLocaleString(),
                    "Visits: " + patient.visits
                  ];
                }
              }
            }
          },

          scales: {

            x: {
              grid: { display: false },
              border: { display: false }
            },

            y: {
              beginAtZero: true,
              grid: { color: "rgba(0,0,0,0.05)", borderDash: [3,3] },
              ticks: {
                callback: v => Number(v).toLocaleString()
              },
              border: { display: false }
            },

            y1: {
              position: "right",
              min: 0,
              max: 100,
              grid: { display: false },
              ticks: {
                callback: v => v + "%"
              }
            }
          }
        },

        plugins: [DomeBar]
      });

      /* ---------- INSIGHT ---------- */

 
  const insightBox = document.getElementById("paretoInsight");
  const loyalPatients = json.data.loyal_patients;

  // Add insight text + "See More" link
  insightBox.innerHTML = `
     <strong>Insight:</strong> ${insight} 
    <a href="#" id="seeMorePareto" style="margin-left:10px; text-decoration:underline; color:#ee6a6a; cursor:pointer;">See More</a>
  `;

  // Modal elements
  const modal = document.getElementById("paretoModal");
  const tbody = document.getElementById("paretoPatientTable");
  const closeBtn = document.getElementById("paretoModalClose");

  // Populate table rows dynamically with loyal_patients
 // Clear table
tbody.innerHTML = "";

// Add rows dynamically
loyalPatients.forEach(p => {
  const tr = document.createElement("tr");

  // Name cell
  const nameTd = document.createElement("td");
  nameTd.textContent = p.patient_name;
  nameTd.style.padding = "8px";
  nameTd.style.borderBottom = "1px solid #eee";
  nameTd.style.cursor = "pointer";
  nameTd.style.color = "#007bff";

  // Attach click handler to open profile in new tab
  nameTd.addEventListener("click", () => {
    window.open(`/patient/profile/${p.patient_id}`, "_blank");
  });

  // Revenue cell
  const revTd = document.createElement("td");
  revTd.textContent = Number(p.revenue).toLocaleString();
  revTd.style.padding = "8px";
  revTd.style.borderBottom = "1px solid #eee";
  revTd.style.textAlign = "right";

  // Visits cell
  const visitsTd = document.createElement("td");
  visitsTd.textContent = p.visits;
  visitsTd.style.padding = "8px";
  visitsTd.style.borderBottom = "1px solid #eee";
  visitsTd.style.textAlign = "right";

  // Append cells to row
  tr.appendChild(nameTd);
  tr.appendChild(revTd);
  tr.appendChild(visitsTd);

  // Append row to tbody
  tbody.appendChild(tr);
});

  // Show modal on "See More"
  document.getElementById("seeMorePareto").addEventListener("click", e => {
    e.preventDefault();
    modal.style.display = "flex";
  });

  // Close modal via cross
  closeBtn.addEventListener("click", () => {
    modal.style.display = "none";
  });

  // Click outside modal to close
  modal.addEventListener("click", e => {
    if (e.target === modal) modal.style.display = "none";
  });

})
.catch(err => console.error(err));

  
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
          anchor: 'center',
    align: 'center',
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

      const june = forecastData.validation_2025.find(f =>
  new Date(f.ds).getMonth() === 5 &&
  new Date(f.ds).getFullYear() === 2026
);

const predictedRevenue = june?.predicted;
     
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
              anchor: 'center',
    align: 'center',
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
        borderColor: "#ee6a6a",
        backgroundColor: "#ee6a6a",
        borderWidth: 3,
        tension: 0.35,
        pointRadius: 5,
        pointHoverRadius: 8,
        pointBackgroundColor: "#fff",
        pointBorderWidth: 2,
        pointBorderColor: "#ee6a6a"
      },

      // PREDICTED REVENUE
     {
  label: "Predicted Revenue",
  data: predicted,
  borderColor: "#2E86DE",   // blue prediction line
  backgroundColor: gradient,
  borderDash: [6,5],
  borderWidth: 3,
  tension: 0.4,
  fill: true,
  pointRadius: 4,
  pointHoverRadius: 7,
  pointBackgroundColor: "#fff",
  pointBorderWidth: 2,
  pointBorderColor: "#2E86DE"   // blue points
}
    ]
  },

  options: {
    responsive: true,
    maintainAspectRatio:false,

    interaction: {
      mode: "index",
      intersect: false
    },

    plugins: {

      legend: {
        position: "bottom",
        labels: {
          color: "#333",
          font: {
            family: "Poppins",
            size: 13
          },
          boxWidth: 12
        }
      },

      tooltip: {
        backgroundColor:"#fff",
        titleColor:"#333",
        bodyColor:"#333",
        borderColor:"#eee",
        borderWidth:1,
        padding:12,
        callbacks: {
          label: function(context){
            let value = context.parsed.y;
            if(value===null) return "";
            return `${context.dataset.label}: Rs ${value.toLocaleString()}`;
          }
        }
      },

      title: {
        display: true,
        text: "Monthly Revenue Forecast",
        color:"#333",
        font: {
          family:"Poppins",
          size:18,
          weight:"600"
        },
        padding:{bottom:15}
      }

    },

    scales: {

      y: {
        grid: {
          color: "rgba(0,0,0,0.05)"
        },
        ticks: {
          color:"#666",
          callback: function(value){
            return "Rs " + value.toLocaleString();
          }
        }
      },

      x: {
        grid: {
          display:false
        },
        ticks:{
          color:"#666"
        }
      }

    }
  }
});
        // ===============================
        // DISPLAY RECOMMENDATIONS
        // ===============================
        // ===============================
// DISPLAY RECOMMENDATIONS
// ===============================
const recContainer = document.getElementById("forecastRecommendations");

if (recContainer && recommendations.length > 0) {

    let html = "<h3 class='rec-title'>💡 Recommendations</h3>";

    html += recommendations.map(r => {

        let cls = "recommendation-stable";

        if (r.toLowerCase().includes("decline") || r.toLowerCase().includes("lower")) {
            cls = "recommendation-decline";
        } 
        else if (r.toLowerCase().includes("growth") || r.toLowerCase().includes("high")) {
            cls = "recommendation-growth";
        }

        return `<div class="recommendation-card ${cls}">${r}</div>`;

    }).join("");

    recContainer.innerHTML = html;
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
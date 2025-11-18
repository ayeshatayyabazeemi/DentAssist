// assets/js/chart.js

document.addEventListener('DOMContentLoaded', function () {
  const canvas = document.getElementById('regChart');
  if (!canvas) {
    console.warn('regChart canvas not found!');
    return;
  }

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

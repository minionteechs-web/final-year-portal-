document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('[data-nav-toggle]');
  const nav = document.querySelector('[data-nav]');
  if (toggle && nav) {
    toggle.addEventListener('click', () => nav.classList.toggle('is-open'));
  }

  // smooth scroll for internal links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) target.scrollIntoView({behavior: 'smooth'});
  });
});

document.querySelectorAll('[data-chart]').forEach((canvas) => {
    const raw = canvas.getAttribute('data-chart');
    if (!raw || typeof Chart === 'undefined') return;

    let config;
    try {
      config = JSON.parse(raw);
    } catch (e) {
      return;
    }

    const type = config.type || 'radar';
    const labels = config.labels || [];
    const values = config.values || [];
    const title = config.title || '';

    const green = '#0B3D2E';
    const gold = '#C6A75E';

    const commonOptions = {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: type === 'bar' ? false : true, labels: { color: '#14201B' } },
        title: title ? { display: true, text: title, color: green, font: { family: 'Cormorant Garamond', size: 16 } } : { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => `${ctx.parsed.r ?? ctx.parsed.y ?? ctx.raw} / 5`
          }
        }
      }
    };

    if (type === 'radar') {
      new Chart(canvas, {
        type: 'radar',
        data: {
          labels,
          datasets: [{
            label: 'Average rating',
            data: values,
            fill: true,
            backgroundColor: 'rgba(198, 167, 94, 0.28)',
            borderColor: green,
            pointBackgroundColor: gold,
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: green,
            borderWidth: 2
          }]
        },
        options: {
          ...commonOptions,
          scales: {
            r: {
              min: 0,
              max: 5,
              ticks: { stepSize: 1, backdropColor: 'transparent', color: '#5C6B63' },
              grid: { color: 'rgba(11, 61, 46, 0.12)' },
              angleLines: { color: 'rgba(11, 61, 46, 0.12)' },
              pointLabels: { color: green, font: { size: 11, family: 'Source Sans 3' } }
            }
          }
        }
      });
    } else {
      new Chart(canvas, {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Average rating',
            data: values,
            backgroundColor: 'rgba(11, 61, 46, 0.82)',
            borderRadius: 8,
            maxBarThickness: 42
          }]
        },
        options: {
          ...commonOptions,
          scales: {
            y: {
              min: 0,
              max: 5,
              ticks: { stepSize: 1, color: '#5C6B63' },
              grid: { color: 'rgba(11, 61, 46, 0.08)' }
            },
            x: {
              ticks: { color: green, maxRotation: 45, minRotation: 0, font: { size: 11 } },
              grid: { display: false }
            }
          }
        }
      });
    }
  });
});

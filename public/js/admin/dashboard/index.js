document.addEventListener("DOMContentLoaded", function () {
    // Check if chart element exists
    var chartElement = document.getElementById('material-completion-chart');
    if (chartElement && typeof Chart !== 'undefined' && typeof materialStats !== 'undefined') {
        var ctx = chartElement.getContext('2d');

        // Create gradients for bars
        var gradients = [
            createGradient(ctx, '#667eea', '#764ba2'),  // Purple
            createGradient(ctx, '#11998e', '#38ef7d'),  // Green
            createGradient(ctx, '#3498db', '#2980b9'),  // Blue
            createGradient(ctx, '#f093fb', '#f5576c'),  // Pink
            createGradient(ctx, '#fa709a', '#fee140')   // Orange
        ];

        var materialChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: materialStats.labels,
                datasets: [{
                    label: 'Tingkat Penyelesaian',
                    data: materialStats.data,
                    backgroundColor: gradients,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart',
                    onComplete: function () {
                        // Add fade-in effect
                        chartElement.style.opacity = 1;
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: '#f0f0f0',
                            lineWidth: 1,
                        },
                        ticks: {
                            callback: function (value) {
                                return value + '%';
                            },
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#666'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '600'
                            },
                            color: '#333',
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function (context) {
                                return 'Penyelesaian: ' + context.raw + '%';
                            },
                            title: function (context) {
                                return context[0].label;
                            }
                        }
                    }
                }
            }
        });

        // Initial opacity for animation
        chartElement.style.opacity = 0;
        chartElement.style.transition = 'opacity 0.5s ease-in';
    }
});

// Helper function to create gradient
function createGradient(ctx, color1, color2) {
    var gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, color1);
    gradient.addColorStop(1, color2);
    return gradient;
}

// Add loading animation
window.addEventListener('load', function () {
    const cards = document.querySelectorAll('.dashboard-layout .card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

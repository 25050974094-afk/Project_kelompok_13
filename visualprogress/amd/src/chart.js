define([], function() {
    return {
        init: function(completed, notcompleted) {
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js';

            script.onload = function() {
                const progressCanvas = document.getElementById('progressChart');

                if (progressCanvas) {
                    new Chart(progressCanvas, {
                        type: 'doughnut',
                        data: {
                            labels: ['Selesai', 'Belum Selesai'],
                            datasets: [{
                                data: [completed, notcompleted],
                                backgroundColor: ['#16A34A', '#DC2626'],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Progress Aktivitas'
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                const activityCanvas = document.getElementById('activityChart');

                if (activityCanvas) {
                    new Chart(activityCanvas, {
                        type: 'bar',
                        data: {
                            labels: ['Materi', 'Quiz', 'Tugas', 'Forum'],
                            datasets: [{
                                label: 'Progress Aktivitas',
                                data: [100, 80, 40, 25],
                                backgroundColor: ['#16A34A', '#16A34A', '#F59E0B', '#DC2626']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                title: {
                                    display: true,
                                    text: 'Status Aktivitas'
                                },
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    max: 100
                                }
                            }
                        }
                    });
                }
            };

            document.head.appendChild(script);
        }
    };
});
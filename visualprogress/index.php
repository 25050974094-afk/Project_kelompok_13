<?php

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);
$context = context_course::instance($courseid);

require_login($course);

$PAGE->set_url(new moodle_url('/local/visualprogress/index.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title('Visual Progress Dashboard');
$PAGE->set_heading($course->fullname);
$PAGE->requires->css('/local/visualprogress/styles.css');

// DATA DUMMY
$completed = 7;
$notcompleted = 3;
$total = $completed + $notcompleted;

$percentage = 0;
if ($total > 0) {
    $percentage = round(($completed / $total) * 100);
}


echo $OUTPUT->header();

echo html_writer::tag('h2', 'Visual Progress Dashboard', ['class' => 'dashboard-title']);

echo html_writer::tag(
    'p',
    'Pantau perkembangan belajar siswa secara visual pada LMS Moodle',
    ['class' => 'dashboard-subtitle']
);

// CARD STATISTIK
echo html_writer::start_div('progress-cards');

echo html_writer::div(
    '<p>Progress Keseluruhan</p><h3>' . $percentage . '%</h3><span>Dari total aktivitas</span>',
    'progress-card'
);

echo html_writer::div(
    '<p>Aktivitas Selesai</p><h3>' . $completed . '</h3><span>Aktivitas</span>',
    'progress-card'
);

echo html_writer::div(
    '<p>Belum Selesai</p><h3>' . $notcompleted . '</h3><span>Aktivitas</span>',
    'progress-card'
);

echo html_writer::div(
    '<p>Total Aktivitas</p><h3>' . $total . '</h3><span>Aktivitas</span>',
    'progress-card'
);

echo html_writer::end_div();

// AREA GRAFIK
echo html_writer::start_div('dashboard-charts');

echo html_writer::start_div('chart-card');
echo html_writer::tag('h3', 'Progress Aktivitas');
echo html_writer::tag('canvas', '', ['id' => 'progressChart']);
echo html_writer::end_div();

echo html_writer::start_div('chart-card');
echo html_writer::tag('h3', 'Status Aktivitas');
echo html_writer::tag('canvas', '', ['id' => 'activityChart']);
echo html_writer::end_div();

echo html_writer::end_div();
// TABEL DETAIL AKTIVITAS
echo html_writer::start_div('activity-table-card');

echo html_writer::tag('h3', 'Detail Aktivitas Pembelajaran');

echo html_writer::start_tag('table', ['class' => 'activity-table']);

echo html_writer::start_tag('thead');
echo html_writer::start_tag('tr');
echo html_writer::tag('th', 'No');
echo html_writer::tag('th', 'Nama Aktivitas');
echo html_writer::tag('th', 'Jenis');
echo html_writer::tag('th', 'Status');
echo html_writer::tag('th', 'Progress');
echo html_writer::end_tag('tr');
echo html_writer::end_tag('thead');

echo html_writer::start_tag('tbody');

$activities = [
    ['Materi Pengenalan LMS', 'Page', 'Selesai', '100%'],
    ['Quiz Pertemuan 1', 'Quiz', 'Selesai', '100%'],
    ['Tugas Individu', 'Assignment', 'Belum Selesai', '0%'],
    ['Forum Diskusi', 'Forum', 'Selesai', '100%'],
];

$no = 1;

foreach ($activities as $activity) {
    $statusclass = $activity[2] === 'Selesai' ? 'badge-success' : 'badge-danger';

    echo html_writer::start_tag('tr');
    echo html_writer::tag('td', $no);
    echo html_writer::tag('td', $activity[0]);
    echo html_writer::tag('td', $activity[1]);
    echo html_writer::tag(
        'td',
        html_writer::span($activity[2], 'status-badge ' . $statusclass)
    );
    echo html_writer::tag('td', $activity[3]);
    echo html_writer::end_tag('tr');

    $no++;
}

echo html_writer::end_tag('tbody');
echo html_writer::end_tag('table');
echo html_writer::end_div();

echo html_writer::tag('script', '', [
    'src' => 'https://cdn.jsdelivr.net/npm/chart.js'
]);

echo html_writer::script("
document.addEventListener('DOMContentLoaded', function () {
    const completed = " . $completed . ";
    const notcompleted = " . $notcompleted . ";

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
});
");

echo $OUTPUT->footer();
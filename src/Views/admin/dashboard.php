<?php
if (!isset($_SESSION['username'])) {
    header('location: /admin/login');
}
?>

<h1 class="dashboard-title">Dashboard</h1>

<div class="metrics-container">
    <a href="/admin/student/list" style="text-decoration: none;">
    <div class="metric-card visitors">
        <div class="metric-info">
            <div class="metric-title">Students</div>
            <div class="metric-value"><?= $counts['student_count'] ?></div>
        </div>
        <div class="metric-icon">👥</div>
    </div>
    </a>
    <a href="/admin/question/list" style="text-decoration: none;">

    <div class="metric-card sales">
        <div class="metric-info">
            <div class="metric-title">Questions</div>
            <div class="metric-value"><?= $counts['question_count'] ?></div>
        </div>
        <div class="metric-icon">📊</div>
    </div>
    </a>
    <a href="/admin/teacher/list" style="text-decoration: none;">

    <div class="metric-card subscribers">
        <div class="metric-info">
            <div class="metric-title">Teachers</div>
            <div class="metric-value"><?= $counts['teacher_count'] ?></div>
        </div>
        <div class="metric-icon">📰</div>
    </div>
    </a>
    <a href="/admin/reports" style="text-decoration: none;">

    <div class="metric-card orders">
        <div class="metric-info">
            <div class="metric-title">Reports</div>
            <div class="metric-value"><?= $counts['report_count'] ?></div>
        </div>
        <div class="metric-icon">✓</div>
    </div>
    </a>
</div>
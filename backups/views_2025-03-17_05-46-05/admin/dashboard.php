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
    <?php if ($_SESSION['role'] == 1) { ?>
        <a href="/admin/teacher/list" style="text-decoration: none;">

            <div class="metric-card subscribers">
                <div class="metric-info">
                    <div class="metric-title">Teachers</div>
                    <div class="metric-value"><?= $counts['teacher_count'] ?></div>
                </div>
                <div class="metric-icon">📰</div>
            </div>
        </a>
    <?php } ?>
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

<div class="quick-actions">
    <h3>Quick Actions</h3>
    <div class="action-buttons">
        <button onclick="window.location='/admin/question/add'" class="action-btn">
            <i class="fas fa-plus"></i> Add Question
        </button>
        <button onclick="window.location='/admin/quiz/add'" class="action-btn">
            <i class="fas fa-file"></i> Create Mock Test
        </button>
        <?php if ($_SESSION['role'] == 1) { ?>

        <button onclick="window.location='/admin/teacher/add'" class="action-btn">
            <i class="fas fa-user-plus"></i> Add Teacher
        </button>
        <?php } ?>
        <button onclick="window.location='/admin/reports'" class="action-btn">
            <i class="fas fa-flag"></i> View Reports
        </button>
    </div>
</div>

<!-- Analytics Section -->
<div class="dashboard-grid">
    <!-- Recent Activities -->
    <div class="dashboard-card">
        <h3>Recent Activities</h3>
        <div class="activity-list">
            <?php foreach ($recentActivities as $activity): ?>
                <div class="activity-item">
                    <span class="activity-icon"><?= $activity['icon'] ?></span>
                    <div class="activity-details">
                        <p class="activity-text"><?= $activity['description'] ?></p>
                        <span class="activity-time"><?= $activity['time'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
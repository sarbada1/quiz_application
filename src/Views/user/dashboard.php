<div class="container dashboard py-4 ">
    <div class="welcome-section mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 mb-0">My Exam Dashboard</h1>
                <p class="text-muted lead">Welcome back, <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="date-display">
                    <i class="far fa-calendar-alt"></i> <?= date('F d, Y') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card bg-gradient-primary text-white">
                <div class="stat-card-body">
                    <div class="stat-card-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5 class="stat-card-title">In Progress</h5>
                        <p class="stat-card-value"><?= count($examData['in_progress']) ?> Exams</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient-info text-white">
                <div class="stat-card-body">
                    <div class="stat-card-icon">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5 class="stat-card-title">Upcoming</h5>
                        <p class="stat-card-value"><?= count($examData['upcoming']) ?> Exams</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card bg-gradient-success text-white">
                <div class="stat-card-body">
                    <div class="stat-card-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-card-info">
                        <h5 class="stat-card-title">Completed</h5>
                        <p class="stat-card-value">
                            <?php
                            $completedCount = 0;
                            foreach ($examData['past'] as $exam) {
                                if ($exam['has_attempted']) $completedCount++;
                            }
                            echo $completedCount;
                            ?> Exams
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- In Progress Exams -->
    <div class="card shadow-sm mb-4 border-0 rounded">
        <div class="card-header bg-gradient-danger text-white position-relative">
            <div class="d-flex align-items-center">
                <i class="fas fa-play-circle mr-2"></i>
                <h3 class="mb-0">Exams in Progress</h3>
            </div>
            <div class="header-indicator"></div>
        </div>
        <div class="card-body">
            <?php if (empty($examData['in_progress'])): ?>
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/5058/5058432.png" alt="No exams" class="empty-state-img">
                    <p>No exams are currently in progress</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($examData['in_progress'] as $exam): ?>
                        <div class="col-md-4 mb-3">
                            <div class="exam-card h-100 border-left-danger">
                                <div class="exam-card-header">
                                    <h5 class="mb-0"><?= htmlspecialchars($exam['title']) ?></h5>
                                    <span class="badge badge-danger">Live</span>
                                </div>
                                <div class="exam-card-body">
                                    <p class="exam-description"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                                    <div class="exam-details">
                                        <div class="detail-item">
                                            <i class="far fa-clock"></i>
                                            <span><?= $exam['duration'] ?? 0 ?> minutes</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="far fa-calendar-times"></i>
                                            <span>Ends: <?= date('h:i A', strtotime($exam['end_time'])) ?></span>
                                        </div>
                                    </div>

                                    <div class="countdown-container">
                                        <p class="countdown-label">Time Remaining:</p>
                                        <div class="countdown-display" data-end="<?= $exam['end_time'] ?>">
                                            <div class="countdown-segment hours">
                                                <span class="digits">00</span>
                                                <span class="unit">hours</span>
                                            </div>
                                            <div class="countdown-segment minutes">
                                                <span class="digits">00</span>
                                                <span class="unit">min</span>
                                            </div>
                                            <div class="countdown-segment seconds">
                                                <span class="digits">00</span>
                                                <span class="unit">sec</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="exam-card-footer">
                                    <a href="/realexam/take/<?= $exam['id'] ?>" class="btn btn-danger btn-block">
                                        <i class="fas fa-pen-alt mr-1"></i> Start Exam Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="card shadow-sm mb-4 border-0 rounded">
        <div class="card-header bg-gradient-primary text-white position-relative">
            <div class="d-flex align-items-center">
                <i class="fas fa-calendar-alt mr-2"></i>
                <h3 class="mb-0">Upcoming Exams</h3>
            </div>
            <div class="header-indicator"></div>
        </div>
        <div class="card-body">
            <?php if (empty($examData['upcoming'])): ?>
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/6028/6028541.png" alt="No exams" class="empty-state-img">
                    <p>No upcoming exams scheduled</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php
                    foreach ($examData['upcoming'] as $exam): ?>
                        <div class="col-md-4 mb-3">
                            <div class="exam-card h-100 border-left-primary">
                                <div class="exam-card-header">
                                    <h5 class="mb-0"><?= htmlspecialchars($exam['title']) ?></h5>
                                    <span class="badge badge-primary">Upcoming</span>
                                </div>
                                <div class="exam-card-body">
                                    <p class="exam-description"><?= htmlspecialchars($exam['description'] ?? '') ?></p>
                                    <div class="exam-details">
                                        <div class="detail-item">
                                            <i class="far fa-clock"></i>
                                            <span><?= $exam['duration'] ?? 0 ?> minutes</span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="far fa-calendar-check"></i>
                                            <span>Starts: <?= date('M d, h:i A', strtotime($exam['start_time'])) ?></span>
                                        </div>
                                    </div>

                                    <div class="countdown-container">
                                        <p class="countdown-label">Starting In:</p>
                                        <div class="countdown-display" data-end="<?= $exam['start_time'] ?>">
                                            <div class="countdown-segment days">
                                                <span class="digits">00</span>
                                                <span class="unit">days</span>
                                            </div>
                                            <div class="countdown-segment hours">
                                                <span class="digits">00</span>
                                                <span class="unit">hours</span>
                                            </div>
                                            <div class="countdown-segment minutes">
                                                <span class="digits">00</span>
                                                <span class="unit">min</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="exam-card-footer">
                                    <button class="btn btn-outline-primary btn-block" disabled>
                                        <i class="fas fa-hourglass-half mr-1"></i> Scheduled
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Past Exams -->
    <div class="card shadow-sm mb-4 border-0 rounded">
        <div class="card-header bg-gradient-secondary text-white position-relative">
            <div class="d-flex align-items-center">
                <i class="fas fa-history mr-2"></i>
                <h3 class="mb-0">Exam History</h3>
            </div>
            <div class="header-indicator"></div>
        </div>
        <div class="card-body">
            <?php if (empty($examData['past'])): ?>
                <div class="empty-state">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486754.png" alt="No history" class="empty-state-img">
                    <p>No exam history yet</p>
                </div>
            <?php else: ?>
                <div class="table-responsive custom-table-container">
                    <table class="table custom-table">
                        <thead>
                            <tr>
                                <th>Exam Title</th>
                                <th>Date</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examData['past'] as $exam): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="exam-icon mr-2">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div>
                                                <strong><?= htmlspecialchars($exam['title']) ?></strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($exam['created_at'] ?? 'now')) ?></td>
                                    <td><?= $exam['duration'] ?? 0 ?> min</td>
                                    <td>
    <?php if ($exam['has_attempted']): ?>
        <?php if (isset($exam['is_published']) && $exam['is_published']): ?>
            <a href="/exam/results/<?= $exam['attempt_id'] ?>" class="btn btn-sm btn-outline-info">
                <i class="fas fa-chart-bar mr-1"></i> View Results
            </a>
        <?php else: ?>
            <span class="badge badge-warning">Results Pending</span>
        <?php endif; ?>
    <?php else: ?>
        <span class="text-muted">N/A</span>
    <?php endif; ?>
</td>
                               
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>


            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include this in your document head -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<style>
    :root {
        --primary: #4e73df;
        --secondary: #858796;
        --success: #1cc88a;
        --info: #36b9cc;
        --warning: #f6c23e;
        --danger: #e74a3b;
        --light: #f8f9fc;
        --dark: #5a5c69;
    }



    .welcome-section {
        padding: 15px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .date-display {
        background: #f8f9fc;
        padding: 8px 15px;
        border-radius: 10px;
        font-size: 14px;
        color: var(--secondary);
        display: inline-block;
    }

    /* Gradient backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(87deg, #4e73df 0, #224abe 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(87deg, #1cc88a 0, #13855c 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(87deg, #36b9cc 0, #258391 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(87deg, #f6c23e 0, #dda20a 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(87deg, #e74a3b 0, #be2617 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(87deg, #858796 0, #60616f 100%);
    }

    /* Stats cards */
    .stat-card {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: transform 0.3s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-card-body {
        padding: 20px;
        display: flex;
        align-items: center;
    }

    .stat-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 24px;
    }

    .stat-card-title {
        margin-bottom: 5px;
        font-size: 16px;
        font-weight: normal;
        opacity: 0.8;
    }

    .stat-card-value {
        margin-bottom: 0;
        font-size: 24px;
        font-weight: bold;
    }

    /* Card design */
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .card-header {
        padding: 15px 20px;
        border-bottom: none;
        font-weight: 500;
    }

    .header-indicator {
        height: 3px;
        width: 50px;
        background: rgba(255, 255, 255, 0.3);
        position: absolute;
        bottom: 0;
        left: 20px;
    }

    /* Exam cards */
    .exam-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: all 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .exam-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
    }

    .border-left-danger {
        border-left: 5px solid var(--danger);
    }

    .border-left-primary {
        border-left: 5px solid var(--primary);
    }

    .exam-card-header {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .exam-card-body {
        padding: 15px;
        flex: 1;
    }

    .exam-card-footer {
        padding: 15px;
        border-top: 1px solid #f0f0f0;
    }

    .exam-description {
        min-height: 40px;
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .exam-details {
        margin-bottom: 15px;
    }

    .detail-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        color: #495057;
        font-size: 0.9rem;
    }

    .detail-item i {
        width: 20px;
        margin-right: 8px;
        color: #6c757d;
    }

    /* Countdown styling */
    .countdown-container {
        background: #f8f9fc;
        padding: 12px;
        border-radius: 8px;
        margin-top: 15px;
    }

    .countdown-label {
        margin-bottom: 8px;
        font-weight: 500;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .countdown-display {
        display: flex;
        justify-content: space-between;
    }

    .countdown-segment {
        text-align: center;
        background: #fff;
        border-radius: 8px;
        padding: 8px 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        flex: 1;
        margin: 0 3px;
    }

    .countdown-segment .digits {
        font-size: 1.5rem;
        font-weight: bold;
        display: block;
        color: #333;
    }

    .countdown-segment .unit {
        font-size: 0.7rem;
        color: #6c757d;
        text-transform: uppercase;
    }

    /* Empty state */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 0;
        color: #6c757d;
    }

    .empty-state-img {
        width: 120px;
        height: 120px;
        margin-bottom: 20px;
        opacity: 0.7;
    }

    /* Table styling */
    .custom-table-container {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .custom-table {
        margin-bottom: 0;
    }

    .custom-table thead th {
        background: #f8f9fc;
        border-top: none;
        font-weight: 600;
        padding: 15px;
        color: #495057;
    }

    .custom-table tbody tr {
        transition: all 0.2s;
    }

    .custom-table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .custom-table td {
        padding: 15px;
        vertical-align: middle;
    }

    .exam-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fc;
        color: #5a5c69;
    }

    .status-pill {
        padding: 5px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        font-size: 0.8rem;
        color: white;
    }

    .status-pill i {
        margin-right: 5px;
    }

    .bg-success {
        background-color: var(--success) !important;
    }

    .bg-danger {
        background-color: var(--danger) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const countdownDisplays = document.querySelectorAll('.countdown-display');

        // Update all countdowns every second
        setInterval(function() {
            countdownDisplays.forEach(function(display) {
                const endTime = new Date(display.dataset.end).getTime();
                const now = new Date().getTime();
                const timeRemaining = endTime - now;

                if (timeRemaining <= 0) {
                    // If countdown is done
                    display.innerHTML = '<div class="countdown-segment now"><span class="digits">Now!</span></div>';

                    // Refresh the page to update exam status
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                } else {
                    // Calculate time units
                    const days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

                    // Update days if present
                    const daysSegment = display.querySelector('.days');
                    if (daysSegment) {
                        daysSegment.querySelector('.digits').textContent = String(days).padStart(2, '0');
                    }

                    // Update hours if present
                    const hoursSegment = display.querySelector('.hours');
                    if (hoursSegment) {
                        hoursSegment.querySelector('.digits').textContent = String(hours).padStart(2, '0');
                    }

                    // Update minutes if present
                    const minutesSegment = display.querySelector('.minutes');
                    if (minutesSegment) {
                        minutesSegment.querySelector('.digits').textContent = String(minutes).padStart(2, '0');
                    }

                    // Update seconds if present
                    const secondsSegment = display.querySelector('.seconds');
                    if (secondsSegment) {
                        secondsSegment.querySelector('.digits').textContent = String(seconds).padStart(2, '0');
                    }

                    // Add pulsing effect when time is running low (less than 5 minutes)
                    if (timeRemaining < 5 * 60 * 1000) {
                        display.classList.add('time-critical');
                    }
                }
            });
        }, 1000);
    });
</script>
<div class="exam-list-container">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-4 mb-0">Available Exams</h1>
                <p class="text-muted lead">Find and take your scheduled exams</p>
            </div>
            
            <div class="d-flex">
                <a href="/student/dashboard" class="btn btn-outline-primary mr-2">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
                <button onclick="location.reload()" class="btn btn-primary">
                    <i class="fas fa-sync-alt mr-1"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Status filters -->
        <div class="exam-filters mb-4">
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-dark active filter-btn" data-filter="all">
                    All Exams
                </button>
                <button type="button" class="btn btn-outline-success filter-btn" data-filter="in_progress">
                    In Progress
                </button>
                <button type="button" class="btn btn-outline-primary filter-btn" data-filter="waiting">
                    Upcoming
                </button>
                <button type="button" class="btn btn-outline-secondary filter-btn" data-filter="ended">
                    Past
                </button>
            </div>
            
            <div class="search-box">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="form-control" id="examSearch" placeholder="Search exams...">
            </div>
        </div>
        
        <?php if (empty($exams)): ?>
        <div class="empty-state">
            <img src="https://cdn-icons-png.flaticon.com/512/6028/6028541.png" alt="No exams" class="empty-state-img">
            <h3>No Exams Available</h3>
            <p>There are currently no exams available for you.</p>
        </div>
        <?php else: ?>
        <div class="row" id="examCards">
            <?php foreach ($exams as $exam): ?>
            <div class="col-lg-4 col-md-6 mb-4 exam-card-wrapper" data-status="<?= $exam['status'] ?>">
                <div class="exam-card h-100 <?= getExamCardClass($exam['status']) ?>">
                    <div class="ribbon <?= getExamRibbonClass($exam['status']) ?>">
                        <span><?= getExamStatusText($exam['status']) ?></span>
                    </div>
                    
                    <div class="exam-card-header">
                        <h5 class="mb-0"><?= htmlspecialchars($exam['title']) ?></h5>
                    </div>
                    
                    <div class="exam-card-body">
                        <div class="exam-meta">
                            <div class="exam-meta-item">
                                <i class="far fa-clock"></i>
                                <span><?= $exam['duration'] ?? $exam['time'] ?? 0 ?> min</span>
                            </div>
                            
                            <?php if ($exam['has_attempted']): ?>
                            <div class="exam-meta-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Completed</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <p class="exam-description">
                            <?= htmlspecialchars($exam['description'] ?? 'No description available') ?>
                        </p>
                        
                        <div class="exam-status-box <?= getExamStatusBoxClass($exam['status']) ?>">
                            <?php if ($exam['status'] === 'in_progress'): ?>
                                <div class="status-icon">
                                    <i class="fas fa-play-circle pulse"></i>
                                </div>
                                <div class="status-content">
                                    <div class="status-title">Live Now</div>
                                    <div class="status-info">
                                        Ends: <?= date('h:i A', strtotime($exam['end_time'])) ?>
                                    </div>
                                    <?php if ($exam['end_time']): ?>
                                    <div class="mini-countdown" data-end="<?= $exam['end_time'] ?>">
                                        <span class="hrs">00</span>:<span class="mins">00</span>:<span class="secs">00</span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                            <?php elseif ($exam['status'] === 'waiting' && $exam['start_time']): ?>
                                <div class="status-icon">
                                    <i class="fas fa-hourglass-half"></i>
                                </div>
                                <div class="status-content">
                                    <div class="status-title">Starting Soon</div>
                                    <div class="status-info">
                                        Starts: <?= date('M d, h:i A', strtotime($exam['start_time'])) ?>
                                    </div>
                                </div>
                                
                            <?php elseif ($exam['status'] === 'ended'): ?>
                                <div class="status-icon">
                                    <i class="fas fa-flag-checkered"></i>
                                </div>
                                <div class="status-content">
                                    <div class="status-title">Exam Completed</div>
                                    <div class="status-info">
                                        This exam has ended
                                    </div>
                                </div>
                                
                            <?php else: ?>
                                <div class="status-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="status-content">
                                    <div class="status-title">Not Scheduled</div>
                                    <div class="status-info">
                                        Check back later
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="exam-card-footer">
                        <?php if ($exam['has_attempted']): ?>
                            <a href="/exam/results/<?= $exam['id'] ?>" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-line mr-1"></i> View Results
                            </a>
                        <?php elseif ($exam['status'] === 'in_progress'): ?>
                            <a href="/realexam/take/<?= $exam['id'] ?>" class="btn btn-success btn-block start-exam-btn">
                                <i class="fas fa-pen-alt mr-1"></i> Start Exam Now
                            </a>
                        <?php elseif ($exam['status'] === 'waiting'): ?>
                            <button class="btn btn-outline-primary btn-block" disabled>
                                <i class="fas fa-clock mr-1"></i> Waiting to Start
                            </button>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-block" disabled>
                                <i class="fas fa-lock mr-1"></i> Not Available
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
// Helper functions for exam card styling
function getExamCardClass($status) {
    switch ($status) {
        case 'in_progress': return 'active-exam';
        case 'waiting': return 'upcoming-exam';
        case 'ended': return 'past-exam';
        default: return 'inactive-exam';
    }
}

function getExamRibbonClass($status) {
    switch ($status) {
        case 'in_progress': return 'ribbon-success';
        case 'waiting': return 'ribbon-primary';
        case 'ended': return 'ribbon-secondary';
        default: return 'ribbon-warning';
    }
}

function getExamStatusText($status) {
    switch ($status) {
        case 'in_progress': return 'LIVE';
        case 'waiting': return 'UPCOMING';
        case 'ended': return 'ENDED';
        default: return 'PENDING';
    }
}

function getExamStatusBoxClass($status) {
    switch ($status) {
        case 'in_progress': return 'status-live';
        case 'waiting': return 'status-upcoming';
        case 'ended': return 'status-ended';
        default: return 'status-pending';
    }
}
?>

<style>
:root {
    --primary: #4e73df;
    --success: #1cc88a;
    --info: #36b9cc;
    --warning: #f6c23e;
    --danger: #e74a3b;
    --secondary: #858796;
    --light: #f8f9fc;
    --dark: #5a5c69;
}

.exam-list-container {
    background-color: #f8f9fc;
    min-height: 100vh;
    padding: 20px 0;
}

/* Exam filters */
.exam-filters {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.filter-btn.active {
    background-color: var(--dark);
    color: white;
}

.search-box {
    position: relative;
    max-width: 250px;
    width: 100%;
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

#examSearch {
    padding-left: 35px;
    border-radius: 30px;
}

/* Exam cards */
.exam-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    background: white;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.exam-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.active-exam {
    border-top: 4px solid var(--success);
}

.upcoming-exam {
    border-top: 4px solid var(--primary);
}

.past-exam {
    border-top: 4px solid var(--secondary);
}

.inactive-exam {
    border-top: 4px solid var(--warning);
}

/* Ribbon */
.ribbon {
    position: absolute;
    right: -5px;
    top: 10px;
    z-index: 1;
    overflow: hidden;
    width: 100px;
    height: 100px;
    text-align: right;
}

.ribbon span {
    font-size: 10px;
    font-weight: bold;
    color: #FFF;
    text-align: center;
    line-height: 20px;
    transform: rotate(45deg);
    width: 120px;
    display: block;
    position: absolute;
    top: 25px;
    right: -25px;
    padding: 0;
    box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
}

.ribbon span::before {
    content: "";
    position: absolute;
    left: 0px;
    top: 100%;
    z-index: -1;
    border-width: 3px;
    border-style: solid;
    border-color: transparent;
}

.ribbon span::after {
    content: "";
    position: absolute;
    right: 0px;
    top: 100%;
    z-index: -1;
    border-width: 3px;
    border-style: solid;
    border-color: transparent;
}

.ribbon-success span {
    background: linear-gradient(var(--success) 0%, #149e6b 100%);
}

.ribbon-success span::before {
    border-left-color: #106e4a;
    border-top-color: #106e4a;
}

.ribbon-success span::after {
    border-right-color: #106e4a;
    border-top-color: #106e4a;
}

.ribbon-primary span {
    background: linear-gradient(var(--primary) 0%, #2e59d9 100%);
}

.ribbon-primary span::before {
    border-left-color: #1c3fad;
    border-top-color: #1c3fad;
}

.ribbon-primary span::after {
    border-right-color: #1c3fad;
    border-top-color: #1c3fad;
}

.ribbon-secondary span {
    background: linear-gradient(var(--secondary) 0%, #60616f 100%);
}

.ribbon-secondary span::before {
    border-left-color: #393a43;
    border-top-color: #393a43;
}

.ribbon-secondary span::after {
    border-right-color: #393a43;
    border-top-color: #393a43;
}

.ribbon-warning span {
    background: linear-gradient(var(--warning) 0%, #f4b30d 100%);
}

.ribbon-warning span::before {
    border-left-color: #c29008;
    border-top-color: #c29008;
}

.ribbon-warning span::after {
    border-right-color: #c29008;
    border-top-color: #c29008;
}

/* Card components */
.exam-card-header {
    padding: 20px 20px 10px;
}

.exam-card-body {
    padding: 10px 20px 20px;
    flex: 1;
}

.exam-card-footer {
    padding: 15px 20px;
    background-color: rgba(0,0,0,0.02);
    border-top: 1px solid rgba(0,0,0,0.05);
}

.exam-meta {
    display: flex;
    margin-bottom: 15px;
}

.exam-meta-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
    color: #6c757d;
    margin-right: 15px;
}

.exam-meta-item i {
    margin-right: 5px;
}

.exam-description {
    color: #5a5c69;
    margin-bottom: 15px;
    line-height: 1.5;
    font-size: 0.95rem;
}

/* Status boxes */
.exam-status-box {
    display: flex;
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
}

.status-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    margin-right: 15px;
    background: rgba(255,255,255,0.25);
}

.status-icon i {
    font-size: 20px;
}

.status-content {
    flex: 1;
}

.status-title {
    font-weight: 600;
    margin-bottom: 3px;
}

.status-info {
    font-size: 0.85rem;
    opacity: 0.9;
}

.status-live {
    background: linear-gradient(45deg, rgba(28,200,138,0.15) 0%, rgba(28,200,138,0.25) 100%);
    border-left: 3px solid var(--success);
}

.status-live .status-icon {
    color: var(--success);
}

.status-upcoming {
    background: linear-gradient(45deg, rgba(78,115,223,0.15) 0%, rgba(78,115,223,0.25) 100%);
    border-left: 3px solid var(--primary);
}

.status-upcoming .status-icon {
    color: var(--primary);
}

.status-ended {
    background: linear-gradient(45deg, rgba(133,135,150,0.15) 0%, rgba(133,135,150,0.25) 100%);
    border-left: 3px solid var(--secondary);
}

.status-ended .status-icon {
    color: var(--secondary);
}

.status-pending {
    background: linear-gradient(45deg, rgba(246,194,62,0.15) 0%, rgba(246,194,62,0.25) 100%);
    border-left: 3px solid var(--warning);
}

.status-pending .status-icon {
    color: var(--warning);
}

/* Button styles */
.start-exam-btn {
    animation: pulse-green 2s infinite;
}

@keyframes pulse-green {
    0% {
        box-shadow: 0 0 0 0 rgba(28, 200, 138, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(28, 200, 138, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(28, 200, 138, 0);
    }
}

/* Animation */
.pulse {
    animation: pulse-animation 2s infinite;
}

@keyframes pulse-animation {
    0% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
    100% {
        opacity: 1;
    }
}

/* Mini countdown */
.mini-countdown {
    background: rgba(255,255,255,0.3);
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8rem;
    font-weight: 600;
    margin-top: 5px;
}

/* Empty state */
.empty-state {
    text-align: center;
    padding: 60px 0;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.empty-state-img {
    width: 120px;
    height: 120px;
    margin-bottom: 20px;
    opacity: 0.7;
}

.empty-state h3 {
    margin-bottom: 10px;
    color: #5a5c69;
}

.empty-state p {
    color: #858796;
    max-width: 500px;
    margin: 0 auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .exam-filters {
        flex-direction: column;
    }
    
    .btn-group {
        margin-bottom: 15px;
        width: 100%;
        display: flex;
    }
    
    .search-box {
        max-width: 100%;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    const examCards = document.querySelectorAll('.exam-card-wrapper');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const filter = this.dataset.filter;
            
            // Show/hide cards based on filter
            examCards.forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
    
    // Search functionality
    const searchInput = document.getElementById('examSearch');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        examCards.forEach(card => {
            const title = card.querySelector('h5').textContent.toLowerCase();
            const description = card.querySelector('.exam-description').textContent.toLowerCase();
            
            if (title.includes(searchTerm) || description.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Mini countdowns for in-progress exams
    const miniCountdowns = document.querySelectorAll('.mini-countdown');
    
    function updateMiniCountdowns() {
        miniCountdowns.forEach(countdown => {
            const endTime = new Date(countdown.dataset.end).getTime();
            const now = new Date().getTime();
            const timeRemaining = endTime - now;
            
            if (timeRemaining <= 0) {
                countdown.innerHTML = 'Ended';
                setTimeout(() => location.reload(), 3000);
            } else {
                const hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);
                
                countdown.querySelector('.hrs').textContent = String(hours).padStart(2, '0');
                countdown.querySelector('.mins').textContent = String(minutes).padStart(2, '0');
                countdown.querySelector('.secs').textContent = String(seconds).padStart(2, '0');
            }
        });
    }
    
    if (miniCountdowns.length > 0) {
        // Initial update
        updateMiniCountdowns();
        
        // Update every second
        setInterval(updateMiniCountdowns, 1000);
    }
});
</script>
<?php
$unreadReportsCount = $_SESSION['unreadReportsCount'] ?? 0;
?>
<main>
    <?php if (isset($_SESSION['message'])): ?>
        <div id="alert" class="alert mt-20 w-75 alert-<?= $_SESSION['status'] ?>" role="alert">
            <button type="button" class="closealert" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= $_SESSION['message'] ?>
        </div>
        <?php unset($_SESSION['message']);
        unset($_SESSION['status']); ?>
    <?php endif; ?>
    <div class="container mt-20">
        <div class="profile-header">
            <button class="edit-btn" id="editBtn"><i class="fas fa-pen"></i> Edit</button>
            <div class="user-info">
                <i class="fas fa-user-circle user-avatar"></i>
                <div>
                    <h1><?= $user['username'] ?></h1>
                    <p>Student</p>
                    <p><a href="mailto:sanjelsarbada12@gmail.com"><i class="fas fa-envelope"></i> <?= $user['email'] ?></a></p>
                    <p><a href="tel:9861992016"><i class="fas fa-phone"></i> <?= $user['phone'] ?? 'unknown' ?></a></p>
                </div>
            </div>
        </div>
        <ul class="nav-tabs">
            <li><a href="#profile" class="active">Profile</a></li>
            <li><a href="#quiz-history">Quiz History</a></li>
            <li><a href="#mocktest-history">Mock Test History</a></li>
            <li><a href="#notifications" id="notification">Question Reports & Notifications 
                <?php if ($unreadReportsCount > 0): ?>
                        <span class="badge"><?= $unreadReportsCount ?></span>
                    <?php endif; ?></a></li>
        </ul>

        <div id="profile" class="tab-content active">
            <div class="info-grid">
                <div class="info-card w-100">
                    <h3>Personal Information</h3>
                    <div class="info-item">
                        <i class="fas fa-calendar mr-5" style=" color: #0066cc;"></i> Age: <?= $userinfo['age'] ?? 'unknown' ?>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-university mr-5" style=" color: #0066cc;"></i> College: <?= $userinfo['college'] ?? 'unknown' ?>
                    </div>
                </div>
                <div class="info-card w-100">
                    <h3>Location</h3>
                    <div class="info-item">
                        <i class="fas fa-globe mr-5" style=" color: #0066cc;"></i> Country: Nepal
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt mr-5" style=" color: #0066cc;"></i> Address: <?= $userinfo['address'] ?? 'unknown' ?>
                    </div>
                </div>
            </div>
            <div class="info-card">
                <h3>Contact Information</h3>
                <div class="info-item">
                    <i class="fas fa-phone mr-5" style=" color: #0066cc;"></i> Phone: <?= $user['phone'] ?? 'unknown' ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope mr-5" style=" color: #0066cc;"></i> Email: <?= $user['email'] ?? 'unknown' ?>
                </div>
            </div>
        </div>

        <div id="quiz-history" class="tab-content">
            <h2>Quiz History</h2>
            <?php if (!empty($quizHistory)): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Quiz Name</th>
                            <th>Score</th>
                            <th>Correct</th>
                            <th>Total Questions</th>
                            <th>Time Taken</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizHistory as $attempt): ?>
                            <tr>
                                <td><?= htmlspecialchars($attempt['title'] ?? 'N/A') ?></td>
                                <?php
                                $score = ($attempt['correct_answers'] * 100) / $attempt['total_questions'];
                                ?>
                                <td>
                                    <?= $score ?>%
                                </td>
                                <td><?= $attempt['correct_answers'] ?? 0 ?></td>
                                <td><?= $attempt['total_questions'] ?? 0 ?></td>
                                <td>
                                    <?php
                                    if (!empty($attempt['start_time']) && !empty($attempt['end_time'])) {
                                        $duration = strtotime($attempt['end_time']) - strtotime($attempt['start_time']);
                                        echo floor($duration / 60) . ':' . str_pad($duration % 60, 2, '0', STR_PAD_LEFT);
                                    } else {
                                        echo 'N/A';
                                    }
                                    ?>
                                </td>
                                <td><?= !empty($attempt['start_time']) ? date('M d, Y H:i', strtotime($attempt['start_time'])) : 'N/A' ?></td>
                                <td><span class="status-badge status-<?= strtolower($attempt['status'] ?? 'unknown') ?>">
                                        <?= ucfirst($attempt['status'] ?? 'Unknown') ?>
                                    </span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No quiz attempts found.</p>
            <?php endif; ?>
        </div>


        <div id="mocktest-history" class="tab-content">
            <h2>Mock Test History</h2>
            <?php if (!empty($mocktestHistory)): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Test Name</th>
                            <th>Score</th>
                            <th>Total Marks</th>
                            <th>Obtained Marks</th>

                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($mocktestHistory as $attempt): ?>
                            <tr>
                                <td><?= htmlspecialchars($attempt['name']) ?></td>
                                <td><?= number_format($attempt['score'], 1) ?>%</td>
                                <td><?= $attempt['total_marks'] ?></td>
                                <td><?= $attempt['obtained_marks'] ?></td>

                                <td><?= date('M d, Y H:i', strtotime($attempt['start_time'])) ?></td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-data">No mock test attempts found.</p>
            <?php endif; ?>
        </div>

        <div id="notifications" class="tab-content">
            <h2>Question Reports & Notifications</h2>
            <?php if (!empty($reports)): ?>
                <div class="notifications-list">
                    <?php foreach ($reports as $report): ?>
                        <div class="notification-item <?= $report['status'] ?>">
                            <div class="notification-icon">
                                <?php if ($report['status'] === 'pending'): ?>
                                    <i class="fas fa-clock"></i>
                                <?php elseif ($report['status'] === 'reviewed'): ?>
                                    <i class="fas fa-eye"></i>
                                <?php else: ?>
                                    <i class="fas fa-check-circle"></i>
                                <?php endif; ?>
                            </div>
                            <div class="notification-content">
                                <div class="notification-header">
                                    <span class="status-badge <?= $report['status'] ?>">
                                        <?= ucfirst($report['status']) ?>
                                    </span>
                                    <span class="notification-time"><?= date('M d, Y H:i', strtotime($report['created_at'])) ?></span>
                                </div>
                                <p class="notification-text">
                                    <?php if ($report['status'] === 'pending'): ?>
                                        You have just submitted your report for the question: "<?= htmlspecialchars($report['question_text']) ?>".
                                    <?php elseif ($report['status'] === 'reviewed'): ?>
                                        Your report for the question: "<?= htmlspecialchars($report['question_text']) ?>" has been reviewed.
                                    <?php else: ?>
                                        Your report for the question: "<?= htmlspecialchars($report['question_text']) ?>" has been resolved.
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-data">No reports found.</p>
            <?php endif; ?>
        </div>

    </div>

    <div class="edit-form" id="editForm">
        <span class="close-btn" id="closeBtn">&times;</span>
        <h2>Edit Profile</h2>
        <form method="post">
            <label for="firstName">Full Name</label>
            <input type="text" id="firstName" name="username" value="<?= $user['username'] ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= $user['email'] ?? '' ?>">

            <label for="college">College</label>
            <input type="text" id="college" name="college" value="<?= $userinfo['college'] ?? '' ?>" placeholder="Enter your college">

            <label for="age">Age</label>
            <input type="text" id="age" name="age" value="<?= $userinfo['age'] ?? '' ?>" placeholder="Enter your age">

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?= $userinfo['address'] ?? '' ?>" placeholder="Enter your address">

            <button type="submit">Submit</button>
        </form>
    </div>
</main>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.nav-tabs a');
        const tabContents = document.querySelectorAll('.tab-content');
        const editBtn = document.getElementById('editBtn');
        const editForm = document.getElementById('editForm');
        const closeBtn = document.getElementById('closeBtn');

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                this.classList.add('active');
                document.querySelector(this.getAttribute('href')).classList.add('active');
            });
        });

        editBtn.addEventListener('click', function() {
            editForm.classList.add('active');
        });

        closeBtn.addEventListener('click', function() {
            editForm.classList.remove('active');
        });

        const closealertButtons = document.querySelectorAll('.closealert'); // Select all close buttons

        closealertButtons.forEach(closealertButton => {
            closealertButton.addEventListener('click', () => {
                const successAlert = closealertButton.parentElement; // Get the parent alert element
                successAlert.style.display = 'none';
            });
        });
    });
</script>
<style>
    .notification-item {
        background: white;
        border-radius: 8px;
        margin-bottom: 15px;
        padding: 15px;
        display: flex;
        gap: 15px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .notification-item.pending {
        border-left: 4px solid #ffc107;
    }

    .notification-item.reviewed {
        border-left: 4px solid #17a2b8;
    }

    .notification-item.resolved {
        border-left: 4px solid #28a745;
    }

    .notification-icon {
        font-size: 1.5rem;
        color: #6c757d;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .status-badge.pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-badge.reviewed {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-badge.resolved {
        background: #d4edda;
        color: #155724;
    }

    .status-completed {
        background: #e8f5e9;
        color: #2e7d32;
    }
    #notification {
        position: relative;
        display: inline-block;
    }
    .badge {
        position: absolute;
        top: 0;
        right: -10px;
        background-color: red;
        color: white;
        border-radius: 50%;
        padding: 5px 10px;
        font-size: 10px;
    }
</style>
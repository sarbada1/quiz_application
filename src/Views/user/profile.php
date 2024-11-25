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
                    <p><a href="tel:9861992016"><i class="fas fa-phone"></i> <?= $userinfo['phone'] ?? 'unknown' ?></a></p>
                </div>
            </div>
        </div>
        <ul class="nav-tabs">
            <li><a href="#profile" class="active">Profile</a></li>
            <li><a href="#quiz-history">Quiz History</a></li>
            <li><a href="#mocktest-history">Mock Test History</a></li>
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
                    <i class="fas fa-phone mr-5" style=" color: #0066cc;"></i> Phone: <?= $userinfo['phone'] ?? 'unknown' ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope mr-5" style=" color: #0066cc;"></i> Email: <?= $user['email'] ?? 'unknown' ?>
                </div>
            </div>
        </div>

        <div id="quiz-history" class="tab-content">
            <h2>Quiz History</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Quiz Name</th>
                        <th>Score</th>
                        <th>Correct</th>
                        <th>Wrong</th>
                        <th>Total Questions</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizHistory as $attempt): ?>
                        <tr>
                            <td><?= htmlspecialchars($attempt['quiz_title']) ?></td>
                            <td><?= number_format($attempt['score'], 1) ?>%</td>
                            <td><?= $attempt['correct_answers'] ?></td>
                            <td><?= $attempt['wrong_answers'] ?></td>
                            <td><?= $attempt['total_questions'] ?></td>
                            <td><?= date('M d, Y H:i', strtotime($attempt['attempted_at'])) ?></td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="mocktest-history" class="tab-content">
            <h2>Mock Test History</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Test Name</th>
                        <th>Score</th>
                        <th>Correct</th>
                        <th>Wrong</th>
                        <th>Unattempted</th>
                        <th>Time Taken</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mocktestHistory as $attempt): ?>
                        <tr>
                            <td><?= htmlspecialchars($attempt['name']) ?></td>
                            <td><?= number_format($attempt['score'], 1) ?>%</td>
                            <td><?= $attempt['correct_answers'] ?></td>
                            <td><?= $attempt['wrong_answers'] ?></td>
                            <td><?= $attempt['unattempted'] ?></td>
                            <td><?= gmdate('H:i:s', $attempt['time_taken']) ?></td>
                            <td><?= date('M d, Y H:i', strtotime($attempt['completed_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="edit-form" id="editForm">
        <span class="close-btn" id="closeBtn">&times;</span>
        <h2>Edit Profile</h2>
        <form method="post">
            <label for="firstName">Full Name</label>
            <input type="text" id="firstName" name="username" value="<?= $user['username'] ?>">

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= $user['email'] ?>">

            <label for="college">College</label>
            <input type="text" id="college" name="college" value="<?= $userinfo['college'] ?>" placeholder="Enter your college">

            <label for="age">Age</label>
            <input type="text" id="age" name="age" value="<?= $userinfo['age'] ?>" placeholder="Enter your age">

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?= $userinfo['address'] ?>" placeholder="Enter your address">

            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?= $userinfo['phone'] ?>">

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
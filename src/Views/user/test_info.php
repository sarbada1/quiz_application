<!-- Main content container -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= $_SESSION['status'] ?>">
        <?= $_SESSION['message'] ?>
    </div>
    <?php 
    unset($_SESSION['message']);
    unset($_SESSION['status']);
    endif; 
?>
<div class="test-mock-container">
    <?php if (!empty($mocktests)) : ?>
        <div class="programs-grid">
            <?php foreach ($mocktests as $mocktest) : ?>
                <div class="program-card">
                    <a href="/mocktest/<?=$mocktest['slug']?>"><h2><?= htmlspecialchars($mocktest['name']) ?></h2></a>
                    <?php
                    $examDate = !empty($mocktest['date']) ? DateTime::createFromFormat('Y-m-d', $mocktest['date'])->format('d F, Y') : 'N/A';
                    ?>
                    <p>Exam Date: <?= htmlspecialchars($examDate) ?> at <?= htmlspecialchars($mocktest['exam_time'] ?? 'N/A') ?></p>
                    <p>Available Seats: <?= htmlspecialchars($mocktest['available_seats'] ?? 'N/A') ?></p>
                    <?php if (!empty($mocktest['available_seats']) && $mocktest['available_seats'] > 0): ?>
                        <?php if (isset($isLoggedIn) && $isLoggedIn): ?>
                            <button type="button" class="btn btn-primary" onclick="registerForExam(<?= $mocktest['id'] ?>)">Register for this Exam</button>
                        <?php else: ?>
                            <div id="quizModal" class="modal">
                                <div class="modal-content">
                                    <span class="close" data-modal="quizModal">&times;</span>
                                    <h2><?= htmlspecialchars($mocktest['name']) ?></h2>
                                    <p>Please log in to register for the exam.</p>
                                    <button class="login-btn" id="startQuizzing">Login</button>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="no-seats">No available seats</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p class="no-programs">No test programs available.</p>
    <?php endif; ?>
</div>

<script>
function registerForExam(mocktestId) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "/mocktest/register/" + mocktestId, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            alert("Successfully registered for the exam.");
            location.reload();
        }
    };
    xhr.send();
}

document.getElementById('startQuizzing').addEventListener('click', function() {
    window.location.href = '/login';
});
</script>

<?php include __DIR__ . '/auth/login.php'; ?>
<?php include __DIR__ . '/auth/register.php'; ?>
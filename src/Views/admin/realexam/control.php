<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Exam Control Panel: <?= htmlspecialchars($exam['title']) ?></h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Exam Information</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <th>Exam ID:</th>
                                            <td><?= $exam['id'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Title:</th>
                                            <td><?= htmlspecialchars($exam['title']) ?></td>
                                        </tr>
                                        <tr>
                                            <th>Time Limit:</th>
                                            <td><?= $exam['time'] ?> minutes</td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td id="exam-status">Not Started</td>
                                        </tr>
                                        <tr>
                                            <th>Time Remaining:</th>
                                            <td id="time-remaining">--:--:--</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Exam Controls</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exam-duration">Exam Duration (minutes):</label>
                                        <input type="number" id="exam-duration" class="form-control" 
                                               value="<?= $exam['time'] ?>" min="1" max="240">
                                    </div>
                                    
                                    <div class="mt-4">
                                        <button id="start-exam-btn" class="btn btn-primary btn-lg btn-block">
                                            Start Exam
                                        </button>
                                        <button id="end-exam-btn" class="btn btn-danger btn-lg btn-block mt-3" disabled>
                                            End Exam
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Student Participation</h5>
                                </div>
                                <div class="card-body">
                                    <div id="participation-stats">
                                        <p>Waiting for students to connect...</p>
                                    </div>
                                    <div id="submission-log" class="mt-4">
                                        <h6>Submission Log:</h6>
                                        <ul id="submission-list" class="list-group">
                                            <!-- Submissions will be added here dynamically -->
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const websocket = new WebSocket('<?= $websocket_url ?>');
    const examId = <?= $exam['id'] ?>;
    let examInProgress = false;
    let countdownInterval;
    let endTime;
    
    // WebSocket event handlers
    websocket.onopen = function(e) {
        console.log("Connected to WebSocket server");
    };
    
    websocket.onmessage = function(e) {
        const data = JSON.parse(e.data);
        
        switch(data.type) {
            case 'student_joined':
                updateParticipationStats(data);
                break;
            case 'student_submitted':
                addSubmissionLog(data);
                updateParticipationStats(data);
                break;
        }
    };
    
    websocket.onerror = function(e) {
        console.error("WebSocket error:", e);
    };
    
    websocket.onclose = function(e) {
        console.log("WebSocket connection closed");
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    };
    
    function updateParticipationStats(data) {
        const statsElement = document.getElementById('participation-stats');
        if (data.total_participants !== undefined) {
            statsElement.innerHTML = `
                <p><strong>Connected students:</strong> ${data.total_participants}</p>
                <p><strong>Active students:</strong> ${data.total_participants - (data.submitted_count || 0)}</p>
                <p><strong>Submissions received:</strong> ${data.submitted_count || 0}</p>
            `;
        }
    }
    
    function addSubmissionLog(data) {
        const list = document.getElementById('submission-list');
        const item = document.createElement('li');
        item.className = 'list-group-item';
        
        const timestamp = new Date().toLocaleTimeString();
        item.innerHTML = `<strong>${timestamp}</strong>: Student ID ${data.user_id} submitted their exam. ${data.remaining_students} students still active.`;
        
        list.prepend(item);
    }
    
    function startExam() {
        const duration = parseInt(document.getElementById('exam-duration').value);
        if (isNaN(duration) || duration <= 0) {
            alert('Please enter a valid duration.');
            return;
        }
        
        // Calculate end time
        const durationInSeconds = duration * 60;
        endTime = Date.now() + (durationInSeconds * 1000);
        
        // Send start command to WebSocket server
        websocket.send(JSON.stringify({
            type: 'admin_start_exam',
            exam_id: examId,
            duration: durationInSeconds
        }));
        
        // Update UI
        document.getElementById('exam-status').textContent = 'In Progress';
        document.getElementById('start-exam-btn').disabled = true;
        document.getElementById('end-exam-btn').disabled = false;
        examInProgress = true;
        
        // Start countdown
        startCountdown(durationInSeconds);
    }
    
    function endExam() {
        if (!confirm('Are you sure you want to end the exam now? This will force all students to submit.')) {
            return;
        }
        
        // Send end command to WebSocket server
        websocket.send(JSON.stringify({
            type: 'admin_end_exam',
            exam_id: examId
        }));
        
        // Update UI
        document.getElementById('exam-status').textContent = 'Ended';
        document.getElementById('start-exam-btn').disabled = true;
        document.getElementById('end-exam-btn').disabled = true;
        document.getElementById('time-remaining').textContent = '00:00:00';
        examInProgress = false;
        
        // Stop countdown
        if (countdownInterval) {
            clearInterval(countdownInterval);
        }
    }
    
    function startCountdown(durationInSeconds) {
        const timerElement = document.getElementById('time-remaining');
        let remainingSeconds = durationInSeconds;
        
        updateTimerDisplay();
        
        countdownInterval = setInterval(function() {
            remainingSeconds--;
            
            if (remainingSeconds <= 0) {
                clearInterval(countdownInterval);
                endExam();
            } else {
                updateTimerDisplay();
            }
        }, 1000);
        
        function updateTimerDisplay() {
            const hours = Math.floor(remainingSeconds / 3600);
            const minutes = Math.floor((remainingSeconds % 3600) / 60);
            const seconds = remainingSeconds % 60;
            
            timerElement.textContent = 
                String(hours).padStart(2, '0') + ':' +
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        }
    }
    
    // Event listeners
    document.getElementById('start-exam-btn').addEventListener('click', startExam);
    document.getElementById('end-exam-btn').addEventListener('click', endExam);
});
</script>


    <div class="quiz-container">
        <h1>Available Quizzes</h1>
        <ul class="quiz-list">
            <?php
            foreach($quiz as $quizdata){
                echo '<li class="quiz-item">';
                echo '<a href="/quiz/' . ($quizdata['slug']) . '" class="quiz-name text-none">' . htmlspecialchars($quizdata['title']) . '</a>';
                echo '</li>';
            }
            ?>
        </ul>
    </div>

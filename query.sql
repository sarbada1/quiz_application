-- Active: 1731304931591@@localhost@3306
create DATABASE quiz_system;

use quiz_system;
CREATE TABLE `usertype` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role` VARCHAR(255) NOT NULL
);

CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL,
    `password` VARCHAR(255) NOT NULL,
    `usertype_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usertype_id`) REFERENCES `usertype` (`id`) ON DELETE CASCADE
);

CREATE TABLE `user_info` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `phone` VARCHAR(255) NULL,
    `age` VARCHAR(255) NULL,
    `college` VARCHAR(255) NULL,
    `address` VARCHAR(255) NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);






CREATE TABLE `question_type` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL
);

CREATE TABLE `level` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `level` VARCHAR(255) NOT NULL
);

ALTER TABLE question_type add COLUMN slug VARCHAR(255) null;

ALTER TABLE question_type
add COLUMN time_per_question VARCHAR(255) null;

CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `parent_id` BIGINT UNSIGNED NULL -- Ensure UNSIGNED here
);

CREATE TABLE `programmes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) -- Foreign Key Constraint
);

drop table programmes;

CREATE TABLE `programmes_mock_test` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `time` INT(50) NOT NULL,
    `program_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`program_id`) REFERENCES `programmes` (`id`) -- Foreign Key Constraint
);

drop table programmes_mock_test_questions;
drop table programmes_mock_test_answers;

CREATE TABLE `programmes_mock_test_questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `qid` BIGINT UNSIGNED NULL,
    `programmes_mock_test_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`programmes_mock_test_id`) REFERENCES `programmes_mock_test` (`id`), -- Foreign Key Constraint
    FOREIGN KEY (`qid`) REFERENCES `questions` (`id`) -- Foreign Key Constraint
);

CREATE TABLE `programmes_chapter` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `program_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`program_id`) REFERENCES `programmes` (`id`) -- Foreign Key Constraint
);

drop Table programmes;

ALTER TABLE categories add COLUMN slug VARCHAR(255) null;
ALTER TABLE programmes add COLUMN slug VARCHAR(255) null;
ALTER TABLE programmes_mock_test add COLUMN slug VARCHAR(255) null;

CREATE TABLE `quizzes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL, -- Ensure UNSIGNED here
    `user_id` BIGINT UNSIGNED NOT NULL, -- Ensure UNSIGNED here
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`), -- Foreign Key Constraint
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) -- Foreign Key Constraint
);

ALTER TABLE quizzes add COLUMN slug VARCHAR(255) null;

CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` BIGINT UNSIGNED NULL,
    `question_text` TEXT NOT NULL,
    `question_type` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
    FOREIGN KEY (`question_type`) REFERENCES `question_type` (`id`)
);

CREATE TABLE `answers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `answer` TEXT NOT NULL,
    `reason` TEXT NULL,
    `isCorrect` BOOLEAN NOT NULL,
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`)
);

CREATE TABLE `user_quizzes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `quiz_id` BIGINT UNSIGNED NOT NULL,
    `score` INT NOT NULL,
    `completed_at` TIMESTAMP NOT NULL,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
);

CREATE TABLE `quiz_result` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_quiz_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `selected_answer_id` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`user_quiz_id`) REFERENCES `user_quizzes` (`id`),
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
    FOREIGN KEY (`selected_answer_id`) REFERENCES `answers` (`id`)
);

CREATE TABLE `quiz_exam` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_quiz_id` BIGINT UNSIGNED NOT NULL,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `selected_answer_id` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`user_quiz_id`) REFERENCES `user_quizzes` (`id`),
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
    FOREIGN KEY (`selected_answer_id`) REFERENCES `answers` (`id`)
);

CREATE TABLE exams (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    number_of_questions INT NULL,
    duration INT NULL,
    is_active BOOLEAN NULL DEFAULT FALSE,
    full_marks INT NULL,
    pass_marks INT NULL,
    student_count INT NULL DEFAULT 0,
    FOREIGN KEY (created_by) REFERENCES users (id)
);

CREATE TABLE mock_test_attempts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    mock_test_id BIGINT UNSIGNED NOT NULL,
    total_questions INT NOT NULL,
    correct_answers INT NOT NULL,
    wrong_answers INT NOT NULL,
    unattempted INT NOT NULL,
    score DECIMAL(5,2) NOT NULL,
    time_taken INT NOT NULL, -- in seconds
    completion_status ENUM('completed', 'incomplete') NOT NULL,
    started_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    INDEX idx_user_mock (user_id, mock_test_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (mock_test_id) REFERENCES programmes_mock_test(id)
);

CREATE TABLE mock_test_answers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    attempt_id INT  NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NOT NULL,
    is_correct BOOLEAN NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES mock_test_attempts(id),
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (answer_id) REFERENCES answers(id)
);

-- Create reports table
CREATE TABLE question_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    reason ENUM('no_correct_answer', 'multiple_correct', 'unclear', 'other') NOT NULL,
    description TEXT NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
-- Create saved_mock_test_progress table
CREATE TABLE saved_mock_test_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    mock_test_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    selected_answer_id BIGINT UNSIGNED NOT NULL,
    remaining_time INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (mock_test_id) REFERENCES programmes_mock_test(id),
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (selected_answer_id) REFERENCES answers(id)
);
DROP Table answers;

DROP Table questions;

select c.id, c.name, c.parent_id, IFNULL(pp.name, 'Top Category') as category_name
from categories c
    LEFT JOIN categories pp ON c.parent_id = pp.id;

SELECT quizzes.*, categories.name
from quizzes
    join categories on categories.id = quizzes.category_id;

SELECT questions.*, question_type.`type`, quizzes.title
from
    questions
    join quizzes on quizzes.id = questions.quiz_id
    join question_type on question_type.id = questions.question_type

select * from quizzes;

ALTER TABLE quizzes ADD COLUMN difficulty_level BIGINT UNSIGNED;

ALTER TABLE quizzes
ADD FOREIGN KEY (difficulty_level) REFERENCES level (id);

SELECT q.id, q.question_text, qt.type, qt.time_per_question, qt.slug as question_type_slug
FROM questions q
    JOIN question_type qt ON q.question_type = qt.id
WHERE
    q.quiz_id = 1;

SELECT q.*
from questions q
    join quizzes qz on qz.id = q.quiz_id
where
    qz.difficulty_level = 1
    and quiz_id = 1;

SELECT p.*, c.name as cname
from programmes as p
    join categories as c on c.id = p.category_id;

select * from programmes;

SELECT quizzes.*, categories.name
from quizzes
    join categories on categories.id = quizzes.category_id;

SELECT
    q.*,
    c.name AS category_name,
    l.level AS difficulty_name,
    (
        SELECT COUNT(*)
        FROM questions
        WHERE
            quiz_id = q.id
    ) AS question_count
FROM
    quizzes q
    JOIN categories c ON q.category_id = c.id
    JOIN level l ON q.difficulty_level = l.id
WHERE
    q.slug = 'general-knowledge-quiz';

    SELECT q.id, q.question_text, qt.type, qt.time_per_question, qt.slug as question_type_slug
                FROM questions q
                JOIN question_type qt ON q.question_type = qt.id
                WHERE q.quiz_id = 1;



                SELECT 
            pmtq.id AS mock_test_question_id,
            q.id AS question_id,
            q.question_text,
            a.id AS answer_id,
            a.answer,
            a.isCorrect,
            a.reason
        FROM programmes_mock_test_questions pmtq
        JOIN questions q ON pmtq.qid = q.id
        JOIN answers a ON q.id = a.question_id
        WHERE pmtq.programmes_mock_test_id =1
        ORDER BY pmtq.id, a.id;

         SELECT 
    pmtq.id AS mock_test_question_id,
    q.id AS question_id,
    q.question_text,
    GROUP_CONCAT(a.answer ORDER BY a.id SEPARATOR ', ') AS answers,
    GROUP_CONCAT(a.isCorrect ORDER BY a.id SEPARATOR ', ') AS isCorrect,
    GROUP_CONCAT(a.reason ORDER BY a.id SEPARATOR ', ') AS reasons
FROM programmes_mock_test_questions pmtq
JOIN questions q ON pmtq.qid = q.id
JOIN answers a ON q.id = a.question_id
WHERE pmtq.programmes_mock_test_id = :mockTestId
GROUP BY pmtq.id, q.id;


select * from programmes_mock_test_questions as pmtq left join questions as q on q.id=pmtq.qid left join answers as a on q.id=a.question_id where pmtq.programmes_mock_test_id=1;
INSERT INTO `usertype` (`role`) 
VALUES 
    ('admin'),
    ('teacher'),
    ('student');


INSERT INTO
    `users` (
        `username`,
        `email`,
        `password`,
        `usertype_id`,
        `created_at`
    )
VALUES (
        'admin',
        'admin@gmail.com',
        '$2y$10$QBljYqooa3C6s5UE6ZQc1.azAhTRXfWSgm6HQkkW/3Q6XVorLge3i',
        1,
        NOW()
    );

-- insert categories
INSERT INTO `categories` (`name`, `slug`, `parent_id`) VALUES 
('Science', 'science', NULL),
('Mathematics', 'mathematics', NULL),
('Physics', 'physics', 1),
('Chemistry', 'chemistry', 1),
('Biology', 'biology', 1),
('Algebra', 'algebra', 2),
('Calculus', 'calculus', 2),
('Geometry', 'geometry', 2),
('History', 'history', NULL),
('World History', 'world-history', 9),
('Modern History', 'modern-history', 9),
('Literature', 'literature', NULL),
('English Literature', 'english-literature', 12),
('French Literature', 'french-literature', 12),
('Computer Science', 'computer-science', NULL),
('Programming', 'programming', 15),
('Data Science', 'data-science', 15),
('Artificial Intelligence', 'artificial-intelligence', 15),
('Arts', 'arts', NULL),
('Painting', 'painting', 19),
('Sculpture', 'sculpture', 19),
('Music', 'music', NULL),
('Classical Music', 'classical-music', 22),
('Jazz Music', 'jazz-music', 22);

INSERT INTO `question_type` (`type`, `slug`, `time_per_question`) VALUES 
('Multiple Choice', 'multiple-choice', '30'),
('True or False', 'true-or-false', '15'),
('Short Answer', 'short-answer', '45'),
('Fill in the Blank', 'fill-in-the-blank', '40'),
('Matching', 'matching', '60'),
('Essay', 'essay', '120');

INSERT INTO `level` (`level`) VALUES 
('Beginner'),
('Intermediate'),
('Advanced'),
('Expert');


INSERT INTO `quizzes` (`title`, `description`, `category_id`, `user_id`, `slug`, `difficulty_level`) VALUES
('General Knowledge Quiz', 'A fun quiz covering various topics like science, history, and more.', 1, 1, 'general-knowledge-quiz', 1),
('Advanced Math Quiz', 'A challenging quiz for advanced mathematics enthusiasts.', 2, 2, 'advanced-math-quiz', 3),
('World History Quiz', 'Test your knowledge about the history of the world.', 9, 3, 'world-history-quiz', 2),
('Literature Quiz', 'A quiz about famous literary works and authors.', 12, 1, 'literature-quiz', 2),
('Computer Science Quiz', 'A quiz for computer science lovers with topics ranging from algorithms to programming languages.', 15, 4, 'computer-science-quiz', 3),
('Music Theory Quiz', 'A quiz about the fundamentals of music theory and musical compositions.', 22, 5, 'music-theory-quiz', 2);

INSERT INTO `questions` (`quiz_id`, `question_text`, `question_type`) VALUES
(1, 'What is the capital of France?', 1),  -- Multiple Choice question for Quiz 1
(2, 'Solve for x: 2x + 3 = 7', 2),       -- True/False question for Quiz 2
(3, 'Who was the first president of the United States?', 1),  -- Multiple Choice question for Quiz 3
(4, 'What is the atomic number of Oxygen?', 2),  -- True/False question for Quiz 4
(5, 'Which programming language is known as the "mother of all languages"?', 1); -- Multiple Choice question for Quiz 5

INSERT INTO `answers` (`question_id`, `answer`, `reason`, `isCorrect`) VALUES
(1, 'Paris', 'Paris is the capital of France.', TRUE),    -- Correct answer for Question 1
(1, 'Berlin', 'Berlin is the capital of Germany.', FALSE), -- Incorrect answer for Question 1
(1, 'Madrid', 'Madrid is the capital of Spain.', FALSE),  -- Incorrect answer for Question 1
(2, 'x = 2', 'Solving the equation 2x + 3 = 7 gives x = 2.', TRUE), -- Correct answer for Question 2
(2, 'x = 3', 'This is an incorrect value for x in the equation 2x + 3 = 7.', FALSE), -- Incorrect answer for Question 2
(3, 'George Washington', 'George Washington was the first president of the United States.', TRUE), -- Correct answer for Question 3
(3, 'Abraham Lincoln', 'Abraham Lincoln was the 16th president of the United States, not the first.', FALSE), -- Incorrect answer for Question 3
(4, '8', 'The atomic number of Oxygen is 8.', TRUE),  -- Correct answer for Question 4
(4, '10', 'This is incorrect as Oxygen has the atomic number 8, not 10.', FALSE),  -- Incorrect answer for Question 4
(5, 'Fortran', 'Fortran is considered the mother of all programming languages.', TRUE), -- Correct answer for Question 5
(5, 'C', 'C is not considered the "mother of all languages".', FALSE);  -- Incorrect answer for Question 5


INSERT INTO `programmes` (`name`, `slug`, `description`, `category_id`) VALUES
('IOE Preparation Test', 'ioe-preparation-test', 'A comprehensive test designed to prepare students for the IOE entrance exam.', 1),
('GRE Preparation Test', 'gre-preparation-test', 'Test series to help students prepare for the Graduate Record Examination (GRE).', 2),
('IELTS Preparation Test', 'ielts-preparation-test', 'Practice test for the International English Language Testing System (IELTS).', 12),
('JEE Main Preparation Test', 'jee-main-preparation-test', 'Test series for students preparing for the JEE Main engineering entrance exam.', 2),
('Civil Services Examination Mock Test', 'civil-services-exam-mock-test', 'Mock tests for students preparing for civil services exams.', 9),
('SAT Test Preparation', 'sat-test-preparation', 'Practice tests for students planning to take the SAT exam for college admissions.', 2),
('Medical Entrance Test Preparation', 'medical-entrance-test-preparation', 'Test series for students preparing for medical entrance exams like NEET.', 1);


INSERT INTO `programmes_mock_test` (`name`, `time`, `program_id`) VALUES
('Test 1 - IOE Preparation', 60, 1),  -- Mock Test 1 for IOE Preparation Test
('Test 2 - IOE Preparation', 75, 1),  -- Mock Test 2 for IOE Preparation Test
('Mock Test - GRE Preparation', 120, 2),  -- Mock Test for GRE Preparation Test
('Chapterwise Test - GRE Preparation', 90, 2),  -- Chapterwise Test for GRE Preparation Test
('Test 1 - IELTS Preparation', 60, 3),  -- Test 1 for IELTS Preparation Test
('Mock Test - IELTS Preparation', 90, 3),  -- Mock Test for IELTS Preparation Test
('Test 1 - JEE Main Preparation', 120, 4),  -- Test 1 for JEE Main Preparation Test
('Chapterwise Test - JEE Main Preparation', 90, 4),  -- Chapterwise Test for JEE Main Preparation Test
('Mock Test - Civil Services Exam', 150, 5),  -- Mock Test for Civil Services Examination
('Test 1 - SAT Test Preparation', 90, 6),  -- Test 1 for SAT Test Preparation
('Chapterwise Test - SAT Test Preparation', 60, 6),  -- Chapterwise Test for SAT Test Preparation
('Mock Test - Medical Entrance', 180, 7);  -- Mock Test for Medical Entrance Test


INSERT INTO `programmes_mock_test_questions` (`qid`, `programmes_mock_test_id`) VALUES
(1, 1),  -- Question 1 from 'Test 1 - IOE Preparation' (programmes_mock_test_id = 1)
(2, 1),  -- Question 2 from 'Test 1 - IOE Preparation' (programmes_mock_test_id = 1)
(3, 2),  -- Question 3 from 'Test 2 - IOE Preparation' (programmes_mock_test_id = 2)
(4, 2),  -- Question 4 from 'Test 2 - IOE Preparation' (programmes_mock_test_id = 2)
(5, 3),  -- Question 5 from 'Mock Test - GRE Preparation' (programmes_mock_test_id = 3)
(1, 3),  -- Question 1 from 'Mock Test - GRE Preparation' (programmes_mock_test_id = 3)
(2, 4),  -- Question 2 from 'Chapterwise Test - GRE Preparation' (programmes_mock_test_id = 4)
(3, 4),  -- Question 3 from 'Chapterwise Test - GRE Preparation' (programmes_mock_test_id = 4)
(4, 5),  -- Question 4 from 'Test 1 - IELTS Preparation' (programmes_mock_test_id = 5)
(5, 5),  -- Question 5 from 'Test 1 - IELTS Preparation' (programmes_mock_test_id = 5)
(1, 6),  -- Question 1 from 'Mock Test - IELTS Preparation' (programmes_mock_test_id = 6)
(2, 6);  -- Question 2 from 'Mock Test - IELTS Preparation' (programmes_mock_test_id = 6)

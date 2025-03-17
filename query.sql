-- Active: 1741512521136@@127.0.0.1@3306@quiz_system
create DATABASE quiz_system;

use quiz_system;

SELECT COUNT(*) FROM mocktest_registrations 
            WHERE user_id = :user_id AND mocktest_id = :mocktest_id;
CREATE TABLE `usertype` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `role` VARCHAR(255) NOT NULL
);
SHOW CREATE TABLE questions;
SHOW CREATE TABLE question_tags;
SHOW CREATE TABLE tags;
CREATE TABLE `users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NULL,
    `password` VARCHAR(255) NOT NULL,
    `usertype_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`usertype_id`) REFERENCES `usertype` (`id`) ON DELETE CASCADE
);
ALTER TABLE users 
ADD COLUMN otp VARCHAR(6) NULL,
ADD COLUMN otp_attempts INT DEFAULT 0,
ADD COLUMN otp_expires TIMESTAMP NULL;
ALTER TABLE users 
ADD COLUMN is_verified TINYINT(1) DEFAULT 0;
ALTER TABLE users 
ADD COLUMN last_otp_sent TIMESTAMP NULL;
SELECT COUNT(*) FROM mock_test_attempts 
                WHERE user_id = :user_id AND mock_test_id = :mocktest_id;
CREATE TABLE `user_info` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `phone` VARCHAR(255) NULL,
    `age` VARCHAR(255) NULL,
    `college` VARCHAR(255) NULL,
    `address` VARCHAR(255) NULL,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);
CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

ALTER TABLE `question_tags` 
ADD UNIQUE INDEX `question_tag_unique` (`question_id`, `tag_id`);
CREATE TABLE `question_type` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL
);

CREATE TABLE `level` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `level` VARCHAR(255) NOT NULL
);

ALTER TABLE users add COLUMN phone VARCHAR(255) null;

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
ALTER TABLE programmes_mock_test 
MODIFY COLUMN no_of_student INT DEFAULT NULL,
ADD COLUMN current_students INT DEFAULT 0;

drop table questions;

DELIMITER //
CREATE TRIGGER after_mocktest_registration
AFTER INSERT ON mocktest_registrations
FOR EACH ROW
BEGIN
    UPDATE programmes_mock_test 
    SET current_students = current_students + 1
    WHERE id = NEW.mocktest_id;
END//
DELIMITER ;

ALTER TABLE mocktest_registrations
ADD CONSTRAINT check_student_limit 
CHECK (
    (SELECT current_students 
     FROM programmes_mock_test 
     WHERE id = mocktest_id) < 
    (SELECT no_of_student 
     FROM programmes_mock_test 
     WHERE id = mocktest_id)
);
drop table programmes_mock_test_questions;

drop table programmes_mock_test_answers;

CREATE TABLE `programmes_mock_test_questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `qid` BIGINT UNSIGNED NULL,
    `quiz_id` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`), -- Foreign Key Constraint
    FOREIGN KEY (`qid`) REFERENCES `questions` (`id`) -- Foreign Key Constraint
);
drop table programmes_mock_test_questions;
TRUNCATE TABLE answers;

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
ALTER TABLE programmes_mock_test add COLUMN no_of_student VARCHAR(255) null;
ALTER TABLE programmes_mock_test add COLUMN date DATE null;
ALTER TABLE programmes_mock_test add COLUMN exam_time TIME null;




SET FOREIGN_KEY_CHECKS = 0;
truncate table answers;
CREATE TABLE questions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    question_text TEXT NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    difficulty_level int NULL,
    marks INT NOT NULL DEFAULT 1,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);
CREATE TABLE previous_year_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_text TEXT NOT NULL,
    year INT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id)
);
CREATE TABLE previous_year_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    answer TEXT NOT NULL,
    isCorrect BOOLEAN NOT NULL,
    FOREIGN KEY (question_id) REFERENCES previous_year_questions(id)
);
CREATE TABLE question_tags (
    question_id BIGINT UNSIGNED NOT NULL,
    tag_id int  NOT NULL,
    PRIMARY KEY (question_id, tag_id),
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
CREATE TABLE quiz_questions (
    quiz_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    question_order INT NOT NULL,
    PRIMARY KEY (quiz_id, question_id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id),
    FOREIGN KEY (question_id) REFERENCES questions(id)
);
CREATE TABLE quizzes (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    type ENUM('mock', 'previous_year', 'quiz', 'real_exam') NOT NULL,
    total_marks INT NOT NULL,
    duration INT NOT NULL, -- in minutes
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
ALTER TABLE quizzes
ADD COLUMN no_of_student INT NULL;
CREATE TABLE quiz_sets (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    set_name VARCHAR(255) NOT NULL,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);
 SELECT * FROM questions 
            WHERE quiz_id =9 AND year = 2019

drop table quiz_sets;
ALTER TABLE quizzes 
MODIFY COLUMN status ENUM('draft', 'published', 'archived') DEFAULT 'draft';
CREATE TABLE quiz_tags (
    quiz_id BIGINT UNSIGNED NOT NULL,
    tag_id int  NOT NULL,
    PRIMARY KEY (quiz_id, tag_id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
SELECT 
    q.*,
    c.name AS category_name,
    l.level AS difficulty_name,
    COUNT(DISTINCT qu.id) AS question_count
FROM quizzes q
JOIN quiz_categories qc ON q.id = qc.quiz_id
JOIN categories c ON qc.category_id = c.id
LEFT JOIN questions qu ON c.id = qu.category_id
LEFT JOIN level l ON qu.difficulty_level = l.id
WHERE q.slug = :slug
AND q.type = 'quiz'
GROUP BY q.id, c.id, l.id;

SELECT 
                    q.id,
                    q.title,
                    q.slug,
                    q.description,
                    q.type,
                    q.total_marks,
                    q.duration,
                    q.status,
                    GROUP_CONCAT(DISTINCT t.name) as tags,
                    GROUP_CONCAT(DISTINCT c.name) as categories
                FROM quizzes q
                LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
                LEFT JOIN tags t ON qt.tag_id = t.id
                LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
                LEFT JOIN categories c ON qc.category_id = c.id
                GROUP BY q.id
                ORDER BY q.created_at DESC;

                SELECT qc.*, c.name 
            FROM quiz_categories qc
            JOIN categories c ON qc.category_id = c.id
            WHERE qc.quiz_id = :quiz_id;

SELECT 
                    q.id,
                    q.title,
                    q.slug,
                    q.description,
                    q.type,
                    q.total_marks,
                    q.duration,
                    q.status,
                    GROUP_CONCAT(DISTINCT t.name) as tags,
                    GROUP_CONCAT(DISTINCT c.name) as categories
                FROM quizzes q
                LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
                LEFT JOIN tags t ON qt.tag_id = t.id
                LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
                LEFT JOIN categories c ON qc.category_id = c.id
                where q.type = 'mock'
                GROUP BY q.id
                ORDER BY q.created_at DESC

CREATE TABLE quiz_categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    quiz_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    marks_allocated INT NOT NULL,
    number_of_questions INT NOT NULL,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
SELECT * FROM category_types ORDER BY id DESC;
TRUNCATE table answers;
CREATE TABLE `category_types` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL
);
ALTER TABLE categories 
ADD COLUMN category_type_id BIGINT UNSIGNED NULL,
ADD FOREIGN KEY (category_type_id) REFERENCES category_types(id);
CREATE TABLE `answers` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `answer` TEXT NOT NULL,
    `reason` TEXT NULL,
    `isCorrect` BOOLEAN NOT NULL,
    FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) on DELETE CASCADE
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
ALTER TABLE questions
ADD COLUMN question_type ENUM('quiz', 'mock', 'previous_year', 'real_exam') NOT NULL DEFAULT 'quiz',
ADD COLUMN year INT NULL;
drop table mock_test_answers;
ALTER TABLE mock_test_attempts 
ADD COLUMN correct_answers INT DEFAULT 0,
ADD COLUMN wrong_answers INT DEFAULT 0,
ADD COLUMN attempted_questions INT DEFAULT 0,
ADD COLUMN total_questions INT DEFAULT 0;
CREATE TABLE mock_test_attempts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    set_id BIGINT UNSIGNED NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    total_marks DECIMAL(10,2) DEFAULT 0,
    obtained_marks DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (set_id) REFERENCES quiz_sets(id)
);
SELECT 
                mta.question_id,
                mta.answer_id as selected_answer_id,
                q.question_text,
                q.marks,
                a.id as answer_id,
                a.answer as answer_text,
                a.isCorrect as is_correct,
                a.reason,
                CASE WHEN a.id = mta.answer_id THEN true ELSE false END as is_selected
            FROM mock_test_answers mta
            JOIN questions q ON mta.question_id = q.id 
            JOIN answers a ON q.id = a.question_id
            WHERE mta.attempt_id = 42
            ORDER BY mta.question_id, a.id;
CREATE TABLE mock_test_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    marks_obtained DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (attempt_id) REFERENCES mock_test_attempts(id),
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (answer_id) REFERENCES answers(id)
);

SELECT 
                    mta.id as attempt_id,
                    mt.set_name as name,
                    mta.start_time,
                    mta.end_time,
                    mta.total_marks,
                    mta.obtained_marks,
                    ((mta.obtained_marks / NULLIF(mta.total_marks, 0)) * 100) as score
                FROM mock_test_attempts mta
                JOIN quiz_sets mt ON mta.set_id = mt.id
                join quizzes as q on q.id=mt.quiz_id
                WHERE mta.user_id = :user_id and q.`type`='mock' ;
-- Create reports table
CREATE TABLE previous_question_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    reason ENUM(
        'no_correct_answer',
        'multiple_correct',
        'unclear',
        'other'
    ) NOT NULL,
    description TEXT NOT NULL,
    status ENUM(
        'pending',
        'reviewed',
        'resolved'
    ) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (question_id) REFERENCES questions (id),
    FOREIGN KEY (user_id) REFERENCES users (id)
);
SELECT 
    q.id,
    q.title,
    q.type,
    q.status,
    c.name AS category_name,
    l.level AS difficulty_name,
    COUNT(DISTINCT qu.id) AS question_count
FROM quizzes q
JOIN quiz_categories qc ON q.id = qc.quiz_id
JOIN categories c ON qc.category_id = c.id
LEFT JOIN questions qu ON c.id = qu.category_id
LEFT JOIN level l ON qu.difficulty_level = l.id
WHERE c.id = 4
GROUP BY 
    q.id,
    q.title,
    q.type,
    q.status,
    c.name,
    l.level;
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
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (mock_test_id) REFERENCES programmes_mock_test (id),
    FOREIGN KEY (question_id) REFERENCES questions (id),
    FOREIGN KEY (selected_answer_id) REFERENCES answers (id)
);

-- Drop existing table if exists
DROP TABLE IF EXISTS user_quiz_history;

-- Create quiz_attempts table with nullable columns
CREATE TABLE quiz_attempts (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    quiz_id BIGINT UNSIGNED NOT NULL,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NULL,
    total_marks INT DEFAULT 0,
    obtained_marks DECIMAL(5,2) DEFAULT 0,
    total_questions INT DEFAULT 0,  
    attempted_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    status ENUM('started', 'in_progress', 'completed', 'abandoned') DEFAULT 'started',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (quiz_id) REFERENCES quizzes(id) ON DELETE CASCADE
);
SELECT 
                    mta.id as attempt_id,
                    mt.title as name,
                    mta.start_time,
                    mta.end_time,
                    mta.total_marks,
                    mta.obtained_marks,
                    mta.total_questions,
                    mta.attempted_questions,
                    mta.correct_answers,
                    (mta.attempted_questions - mta.correct_answers) as wrong_answers,
                    (mta.total_questions - mta.attempted_questions) as unattempted,
                    mta.status,
                    ((mta.obtained_marks / NULLIF(mta.total_marks, 0)) * 100) as score
                FROM mock_test_attempts mta
                JOIN quizzes mt ON mta.quiz_id = mt.id
                WHERE mta.user_id = :user_id 
                AND mt.type = 'mock'
                AND mta.status = 'completed'
                ORDER BY mta.start_time DESC;
CREATE TABLE user_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    answer_id BIGINT UNSIGNED NOT NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (answer_id) REFERENCES answers(id)
);

    CREATE TABLE quiz_attempt_answers (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NULL,
    selected_option_id BIGINT UNSIGNED NULL,
    is_correct BOOLEAN DEFAULT FALSE,
    marks_obtained DECIMAL(5,2) DEFAULT 0,
    time_taken INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (selected_option_id) REFERENCES question_options(id) ON DELETE SET NULL
);
CREATE TABLE test_reviews (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    question_id BIGINT UNSIGNED NOT NULL,
    user_answer_id BIGINT UNSIGNED NOT NULL,
    correct_answer_id BIGINT UNSIGNED NOT NULL,
    marks_obtained DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (attempt_id) REFERENCES mock_test_attempts(id),
    FOREIGN KEY (question_id) REFERENCES questions(id),
    FOREIGN KEY (user_answer_id) REFERENCES answers(id),
    FOREIGN KEY (correct_answer_id) REFERENCES answers(id)
);
CREATE TABLE category_performance (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    attempt_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    total_questions INT DEFAULT 0,
    attempted_questions INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    total_marks DECIMAL(5,2) DEFAULT 0,
    obtained_marks DECIMAL(5,2) DEFAULT 0,
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);
CREATE TABLE user_quiz_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    quiz_id BIGINT UNSIGNED NOT NULL,
    attempt_id INT NOT NULL,
    score DECIMAL(5, 2) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users (id),
    FOREIGN KEY (quiz_id) REFERENCES quizzes (id),
    FOREIGN KEY (attempt_id) REFERENCES quiz_attempts (id)
);
CREATE TABLE mocktest_registrations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    mocktest_id BIGINT UNSIGNED NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (mocktest_id) REFERENCES programmes_mock_test(id),
    UNIQUE KEY (user_id, mocktest_id)
);
CREATE TABLE `subject_tests` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `program_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `time` INT NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`program_id`) REFERENCES `programmes` (`id`)
);

WITH RECURSIVE category_tree AS (
        -- Get subjects (first level children)
        SELECT c.*, 0 as level
        FROM categories c 
        WHERE c.parent_id = :categoryId
        
        UNION ALL
        SELECT c2.*, ct.level + 1
        FROM categories c2
        INNER JOIN category_tree ct ON c2.parent_id = ct.id
        WHERE ct.level < 1
    )
    SELECT ct.*, 
        (SELECT COUNT(*) FROM questions q 
         JOIN quizzes qz ON q.quiz_id = qz.id 
         WHERE qz.category_id = ct.id) as question_count
    FROM category_tree ct
    ORDER BY level, name;
DROP Table quiz_answers;
SELECT 
                    q.id, 
                    q.question_text,
                    GROUP_CONCAT(DISTINCT a.id) as answer_ids,
                    GROUP_CONCAT(DISTINCT a.answer) as answers,
                    GROUP_CONCAT(DISTINCT a.isCorrect) as correct_answers
                FROM questions q
                JOIN quiz_categories qc ON q.category_id = qc.category_id
                JOIN answers a ON q.id = a.question_id
                WHERE qc.quiz_id = 8
                GROUP BY q.id
                ORDER BY RAND();
DROP Table user_quiz_history;

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
    join question_type on question_type.id = questions.question_type;

    select * from quizzes;

select qu.* from quizzes q join categories as c on c.id =q.category_id join questions qu on qu.quiz_id=q.id join answers a on a.question_id=qu.id where q.category_id=5;
SELECT q.id, q.title, q.description, q.slug ,                       
(SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS question_count
FROM quizzes q
WHERE q.category_id = :category_id;

SELECT 
 q.id,
q.question_text,
JSON_ARRAYAGG(
 JSON_OBJECT(
'id', a.id,
'text', a.answer,
 'correct_answer', a.isCorrect,
  'reason', COALESCE(a.reason, '')
                        )
                    ) as answers
                FROM questions q
                LEFT JOIN answers a ON q.id = a.question_id
                WHERE q.quiz_id = 7
                GROUP BY q.id
                ORDER BY RAND()
                LIMIT 5;

                SELECT 
                    qa.question_id,
                    qa.answer_id as selected_answer_id,
                    qa.is_correct as question_correct,
                    q.question_text,
                    q.id as qid,
                    a.id as aid,
                    a.answer as answer_text,
                    a.isCorrect as is_correct,
                    a.reason
                FROM quiz_answers qa
                JOIN questions q ON qa.question_id = q.id
                JOIN answers a ON q.id = a.question_id
                WHERE qa.attempt_id = 234
                ORDER BY qa.question_order;
ALTER TABLE quizzes ADD COLUMN difficulty_level BIGINT UNSIGNED;
WITH user_rank AS (
    SELECT count(*)+1 as user_rank
    FROM mock_test_attempts mta
    WHERE mta.mock_test_id = 1 
    AND mta.score > 42.69
    AND mta.completion_status = 'completed'
    group by mta.user_id
)
SELECT min(user_rank) 
FROM user_rank;

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
WHERE
    q.quiz_id = 1;

SELECT
    pmtq.id AS mock_test_question_id,
    q.id AS question_id,
    q.question_text,
    a.id AS answer_id,
    a.answer,
    a.isCorrect,
    a.reason
FROM
    programmes_mock_test_questions pmtq
    JOIN questions q ON pmtq.qid = q.id
    JOIN answers a ON q.id = a.question_id
WHERE
    pmtq.programmes_mock_test_id = 1
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

select *
from
    programmes_mock_test_questions as pmtq
    left join questions as q on q.id = pmtq.qid
    left join answers as a on q.id = a.question_id
where
    pmtq.programmes_mock_test_id = 1;

INSERT INTO
    `usertype` (`role`)
VALUES ('admin'),
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
INSERT INTO
    `categories` (`name`, `slug`, `parent_id`)
VALUES ('Science', 'science', NULL),
    (
        'Mathematics',
        'mathematics',
        NULL
    ),
    ('Physics', 'physics', 1),
    ('Chemistry', 'chemistry', 1),
    ('Biology', 'biology', 1),
    ('Algebra', 'algebra', 2),
    ('Calculus', 'calculus', 2),
    ('Geometry', 'geometry', 2),
    ('History', 'history', NULL),
    (
        'World History',
        'world-history',
        9
    ),
    (
        'Modern History',
        'modern-history',
        9
    ),
    (
        'Literature',
        'literature',
        NULL
    ),
    (
        'English Literature',
        'english-literature',
        12
    ),
    (
        'French Literature',
        'french-literature',
        12
    ),
    (
        'Computer Science',
        'computer-science',
        NULL
    ),
    (
        'Programming',
        'programming',
        15
    ),
    (
        'Data Science',
        'data-science',
        15
    ),
    (
        'Artificial Intelligence',
        'artificial-intelligence',
        15
    ),
    ('Arts', 'arts', NULL),
    ('Painting', 'painting', 19),
    ('Sculpture', 'sculpture', 19),
    ('Music', 'music', NULL),
    (
        'Classical Music',
        'classical-music',
        22
    ),
    (
        'Jazz Music',
        'jazz-music',
        22
    );

INSERT INTO
    `question_type` (
        `type`,
        `slug`,
        `time_per_question`
    )
VALUES (
        'Multiple Choice',
        'multiple-choice',
        '30'
    ),
    (
        'True or False',
        'true-or-false',
        '15'
    ),
    (
        'Short Answer',
        'short-answer',
        '45'
    ),
    (
        'Fill in the Blank',
        'fill-in-the-blank',
        '40'
    ),
    ('Matching', 'matching', '60'),
    ('Essay', 'essay', '120');

INSERT INTO
    `level` (`level`)
VALUES ('Beginner'),
    ('Intermediate'),
    ('Advanced'),
    ('Expert');

INSERT INTO
    `quizzes` (
        `title`,
        `description`,
        `category_id`,
        `user_id`,
        `slug`,
        `difficulty_level`
    )
VALUES (
        'General Knowledge Quiz',
        'A fun quiz covering various topics like science, history, and more.',
        1,
        1,
        'general-knowledge-quiz',
        1
    ),
    (
        'Advanced Math Quiz',
        'A challenging quiz for advanced mathematics enthusiasts.',
        2,
        2,
        'advanced-math-quiz',
        3
    ),
    (
        'World History Quiz',
        'Test your knowledge about the history of the world.',
        9,
        3,
        'world-history-quiz',
        2
    ),
    (
        'Literature Quiz',
        'A quiz about famous literary works and authors.',
        12,
        1,
        'literature-quiz',
        2
    ),
    (
        'Computer Science Quiz',
        'A quiz for computer science lovers with topics ranging from algorithms to programming languages.',
        15,
        4,
        'computer-science-quiz',
        3
    ),
    (
        'Music Theory Quiz',
        'A quiz about the fundamentals of music theory and musical compositions.',
        22,
        5,
        'music-theory-quiz',
        2
    );

INSERT INTO
    `questions` (
        `quiz_id`,
        `question_text`,
        `question_type`
    )
VALUES (
        1,
        'What is the capital of France?',
        1
    ), -- Multiple Choice question for Quiz 1
    (
        2,
        'Solve for x: 2x + 3 = 7',
        2
    ), -- True/False question for Quiz 2
    (
        3,
        'Who was the first president of the United States?',
        1
    ), -- Multiple Choice question for Quiz 3
    (
        4,
        'What is the atomic number of Oxygen?',
        2
    ), -- True/False question for Quiz 4
    (
        5,
        'Which programming language is known as the "mother of all languages"?',
        1
    );
-- Multiple Choice question for Quiz 5
INSERT INTO
    `answers` (
        `question_id`,
        `answer`,
        `reason`,
        `isCorrect`
    )
VALUES (
        1,
        'Paris',
        'Paris is the capital of France.',
        TRUE
    ), -- Correct answer for Question 1
    (
        1,
        'Berlin',
        'Berlin is the capital of Germany.',
        FALSE
    ), -- Incorrect answer for Question 1
    (
        1,
        'Madrid',
        'Madrid is the capital of Spain.',
        FALSE
    ), -- Incorrect answer for Question 1
    (
        2,
        'x = 2',
        'Solving the equation 2x + 3 = 7 gives x = 2.',
        TRUE
    ), -- Correct answer for Question 2
    (
        2,
        'x = 3',
        'This is an incorrect value for x in the equation 2x + 3 = 7.',
        FALSE
    ), -- Incorrect answer for Question 2
    (
        3,
        'George Washington',
        'George Washington was the first president of the United States.',
        TRUE
    ), -- Correct answer for Question 3
    (
        3,
        'Abraham Lincoln',
        'Abraham Lincoln was the 16th president of the United States, not the first.',
        FALSE
    ), -- Incorrect answer for Question 3
    (
        4,
        '8',
        'The atomic number of Oxygen is 8.',
        TRUE
    ), -- Correct answer for Question 4
    (
        4,
        '10',
        'This is incorrect as Oxygen has the atomic number 8, not 10.',
        FALSE
    ), -- Incorrect answer for Question 4
    (
        5,
        'Fortran',
        'Fortran is considered the mother of all programming languages.',
        TRUE
    ), -- Correct answer for Question 5
    (
        5,
        'C',
        'C is not considered the "mother of all languages".',
        FALSE
    );
-- Incorrect answer for Question 5
INSERT INTO
    `programmes` (
        `name`,
        `slug`,
        `description`,
        `category_id`
    )
VALUES (
        'IOE Preparation Test',
        'ioe-preparation-test',
        'A comprehensive test designed to prepare students for the IOE entrance exam.',
        1
    ),
    (
        'GRE Preparation Test',
        'gre-preparation-test',
        'Test series to help students prepare for the Graduate Record Examination (GRE).',
        2
    ),
    (
        'IELTS Preparation Test',
        'ielts-preparation-test',
        'Practice test for the International English Language Testing System (IELTS).',
        12
    ),
    (
        'JEE Main Preparation Test',
        'jee-main-preparation-test',
        'Test series for students preparing for the JEE Main engineering entrance exam.',
        2
    ),
    (
        'Civil Services Examination Mock Test',
        'civil-services-exam-mock-test',
        'Mock tests for students preparing for civil services exams.',
        9
    ),
    (
        'SAT Test Preparation',
        'sat-test-preparation',
        'Practice tests for students planning to take the SAT exam for college admissions.',
        2
    ),
    (
        'Medical Entrance Test Preparation',
        'medical-entrance-test-preparation',
        'Test series for students preparing for medical entrance exams like NEET.',
        1
    );

INSERT INTO
    `programmes_mock_test` (`name`, `time`, `program_id`)
VALUES (
        'Test 1 - IOE Preparation',
        60,
        1
    ), -- Mock Test 1 for IOE Preparation Test
    (
        'Test 2 - IOE Preparation',
        75,
        1
    ), -- Mock Test 2 for IOE Preparation Test
    (
        'Mock Test - GRE Preparation',
        120,
        2
    ), -- Mock Test for GRE Preparation Test
    (
        'Chapterwise Test - GRE Preparation',
        90,
        2
    ), -- Chapterwise Test for GRE Preparation Test
    (
        'Test 1 - IELTS Preparation',
        60,
        3
    ), -- Test 1 for IELTS Preparation Test
    (
        'Mock Test - IELTS Preparation',
        90,
        3
    ), -- Mock Test for IELTS Preparation Test
    (
        'Test 1 - JEE Main Preparation',
        120,
        4
    ), -- Test 1 for JEE Main Preparation Test
    (
        'Chapterwise Test - JEE Main Preparation',
        90,
        4
    ), -- Chapterwise Test for JEE Main Preparation Test
    (
        'Mock Test - Civil Services Exam',
        150,
        5
    ), -- Mock Test for Civil Services Examination
    (
        'Test 1 - SAT Test Preparation',
        90,
        6
    ), -- Test 1 for SAT Test Preparation
    (
        'Chapterwise Test - SAT Test Preparation',
        60,
        6
    ), -- Chapterwise Test for SAT Test Preparation
    (
        'Mock Test - Medical Entrance',
        180,
        7
    );
-- Mock Test for Medical Entrance Test
INSERT INTO
    `programmes_mock_test_questions` (
        `qid`,
        `programmes_mock_test_id`
    )
VALUES (1, 1), -- Question 1 from 'Test 1 - IOE Preparation' (programmes_mock_test_id = 1)
    (2, 1), -- Question 2 from 'Test 1 - IOE Preparation' (programmes_mock_test_id = 1)
    (3, 2), -- Question 3 from 'Test 2 - IOE Preparation' (programmes_mock_test_id = 2)
    (4, 2), -- Question 4 from 'Test 2 - IOE Preparation' (programmes_mock_test_id = 2)
    (5, 3), -- Question 5 from 'Mock Test - GRE Preparation' (programmes_mock_test_id = 3)
    (1, 3), -- Question 1 from 'Mock Test - GRE Preparation' (programmes_mock_test_id = 3)
    (2, 4), -- Question 2 from 'Chapterwise Test - GRE Preparation' (programmes_mock_test_id = 4)
    (3, 4), -- Question 3 from 'Chapterwise Test - GRE Preparation' (programmes_mock_test_id = 4)
    (4, 5), -- Question 4 from 'Test 1 - IELTS Preparation' (programmes_mock_test_id = 5)
    (5, 5), -- Question 5 from 'Test 1 - IELTS Preparation' (programmes_mock_test_id = 5)
    (1, 6), -- Question 1 from 'Mock Test - IELTS Preparation' (programmes_mock_test_id = 6)
    (2, 6);
-- Question 2 from 'Mock Test - IELTS Preparation' (programmes_mock_test_id = 6)
INSERT INTO
    `questions` (
        `quiz_id`,
        `question_text`,
        `question_type`
    )
VALUES (
        1,
        'What is the largest planet in our solar system?',
        1
    ),
    (
        1,
        'What is the capital of Australia?',
        1
    ),
    (
        1,
        'Who wrote the play "Romeo and Juliet"?',
        1
    ),
    (
        1,
        'What is the smallest country in the world by land area?',
        1
    ),
    (
        1,
        'How many continents are there on Earth?',
        1
    ),
    (
        1,
        'What is the chemical symbol for water?',
        1
    ),
    (
        1,
        'What year did the Titanic sink?',
        1
    ),
    (
        1,
        'What is the hardest natural substance on Earth?',
        1
    ),
    (
        1,
        'Which planet is known as the Red Planet?',
        1
    ),
    (
        1,
        'Who painted the Mona Lisa?',
        1
    ),
    (
        1,
        'What is the currency of Japan?',
        1
    ),
    (
        1,
        'What is the tallest mountain in the world?',
        1
    ),
    (
        1,
        'In which year did World War II end?',
        1
    ),
    (
        1,
        'What is the smallest ocean in the world?',
        1
    ),
    (
        1,
        'How many bones are there in the human body?',
        1
    ),
    (
        1,
        'What is the capital of Canada?',
        1
    ),
    (
        1,
        'What is the most widely spoken language in the world?',
        1
    ),
    (
        1,
        'Which element has the chemical symbol "O"?',
        1
    ),
    (
        1,
        'Who developed the theory of relativity?',
        1
    ),
    (
        1,
        'What is the largest desert in the world?',
        1
    );

INSERT INTO
    `answers` (
        `question_id`,
        `answer`,
        `isCorrect`
    )
VALUES (1, 'Earth', FALSE),
    (1, 'Mars', FALSE),
    (1, 'Jupiter', TRUE),
    (1, 'Saturn', FALSE),
    (2, 'Sydney', FALSE),
    (2, 'Melbourne', FALSE),
    (2, 'Canberra', TRUE),
    (2, 'Brisbane', FALSE),
    (3, 'Charles Dickens', FALSE),
    (
        3,
        'William Shakespeare',
        TRUE
    ),
    (3, 'George Orwell', FALSE),
    (3, 'Mark Twain', FALSE),
    (4, 'Monaco', FALSE),
    (4, 'Vatican City', TRUE),
    (4, 'Malta', FALSE),
    (4, 'San Marino', FALSE),
    (5, '5', FALSE),
    (5, '6', FALSE),
    (5, '7', TRUE),
    (5, '8', FALSE),
    (6, 'CO2', FALSE),
    (6, 'O2', FALSE),
    (6, 'H2O', TRUE),
    (6, 'H2SO4', FALSE),
    (7, '1912', TRUE),
    (7, '1920', FALSE),
    (7, '1918', FALSE),
    (7, '1905', FALSE),
    (8, 'Gold', FALSE),
    (8, 'Iron', FALSE),
    (8, 'Diamond', TRUE),
    (8, 'Silver', FALSE),
    (9, 'Venus', FALSE),
    (9, 'Mars', TRUE),
    (9, 'Jupiter', FALSE),
    (9, 'Saturn', FALSE),
    (10, 'Vincent van Gogh', FALSE),
    (10, 'Leonardo da Vinci', TRUE),
    (10, 'Pablo Picasso', FALSE),
    (10, 'Claude Monet', FALSE),
    (11, 'Won', FALSE),
    (11, 'Peso', FALSE),
    (11, 'Yen', TRUE),
    (11, 'Rupee', FALSE),
    (12, 'K2', FALSE),
    (12, 'Mount Everest', TRUE),
    (12, 'Kangchenjunga', FALSE),
    (12, 'Makalu', FALSE),
    (13, '1940', FALSE),
    (13, '1942', FALSE),
    (13, '1945', TRUE),
    (13, '1948', FALSE),
    (14, 'Indian Ocean', FALSE),
    (14, 'Pacific Ocean', FALSE),
    (14, 'Arctic Ocean', TRUE),
    (14, 'Atlantic Ocean', FALSE),
    (15, '200', FALSE),
    (15, '206', TRUE),
    (15, '220', FALSE),
    (15, '215', FALSE),
    (16, 'Toronto', FALSE),
    (16, 'Vancouver', FALSE),
    (16, 'Ottawa', TRUE),
    (16, 'Montreal', FALSE),
    (17, 'English', TRUE),
    (17, 'Mandarin', FALSE),
    (17, 'Spanish', FALSE),
    (17, 'Hindi', FALSE),
    (18, 'H', FALSE),
    (18, 'O', TRUE),
    (18, 'C', FALSE),
    (18, 'N', FALSE),
    (19, 'Isaac Newton', FALSE),
    (19, 'Albert Einstein', TRUE),
    (19, 'Galileo Galilei', FALSE),
    (19, 'Nikola Tesla', FALSE),
    (20, 'Sahara Desert', TRUE),
    (20, 'Gobi Desert', FALSE),
    (20, 'Kalahari Desert', FALSE),
    (20, 'Arabian Desert', FALSE);

SELECT 
                        qa.question_id,
                        qa.answer_id as selected_id,
                        qa.is_correct,
                        q.question_text,
                        (
                            SELECT JSON_ARRAYAGG(
                                JSON_OBJECT(
                                    'id', a.id,
                                    'answer_text', a.answer,
                                    'is_correct', a.isCorrect,
                                    'explanation', COALESCE(a.reason, '')
                                )
                            )
                            FROM answers a
                            WHERE a.question_id = q.id
                        ) as all_answers
                    FROM quiz_answers qa
                    JOIN questions q ON qa.question_id = q.id
                    WHERE qa.attempt_id = :attempt_id
                    ORDER BY qa.id;

SELECT
    qa.question_id,
    qa.answer_id as selected_answer_id,
    qa.is_correct,
    q.question_text,
    q.id as qid,
    a.id as aid,
    a.answer as answer_text,
    a.isCorrect as is_correct,
    a.reason
FROM
    quiz_answers qa
    JOIN questions q ON qa.question_id = q.id
    JOIN answers a ON q.id = a.question_id
WHERE
    qa.attempt_id = 47
ORDER BY qa.question_order;

SELECT mta.*, pmt.name, (
        mta.total_questions - (
            mta.correct_answers + mta.wrong_answers
        )
    ) as unattempted, TIMESTAMPDIFF(
        SECOND, mta.started_at, mta.completed_at
    ) as time_taken
FROM
    mock_test_attempts mta
    JOIN programmes_mock_test pmt ON mta.mock_test_id = pmt.id
WHERE
    mta.user_id = 6;


     SELECT 
            c1.*, 
            (SELECT GROUP_CONCAT(c2.id, ':', c2.name, ':', c2.slug)
             FROM categories c2 
             WHERE c2.category_id = c1.id) as children
        FROM categories c1
        WHERE c1.category_id = 0;

   SELECT 
    q.id,
    q.title,
    q.slug,
    q.description,
    q.type,
    q.total_marks,
    q.duration,
    q.status,
    GROUP_CONCAT(DISTINCT t.name) as tags,
    GROUP_CONCAT(DISTINCT c.name) as categories,
    (SELECT COUNT(*) FROM quiz_sets WHERE quiz_id = q.id) as set_count
FROM quizzes q
LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
LEFT JOIN tags t ON qt.tag_id = t.id
LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
LEFT JOIN categories c ON qc.category_id = c.id
WHERE q.type = :type
GROUP BY q.id
ORDER BY q.created_at DESC;

SELECT 
                q.question_text,
                mta.answer_id as selected_answer_id,
                mta.is_correct,
                GROUP_CONCAT(
                    JSON_OBJECT(
                        'id', a.id,
                        'answer_text', a.answer,
                        'is_correct', a.isCorrect
                    )
                ) as answers
            FROM mock_test_answers mta
            JOIN questions q ON mta.question_id = q.id
            JOIN answers a ON q.id = a.question_id
            WHERE mta.attempt_id = :attempt_id
            GROUP BY q.id, mta.answer_id, mta.is_correct
            ORDER BY q.id;

            truncate table mock_test_attempts;
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);


select * from previous_year_questions  pyq join quizzes q on q.id=pyq.quiz_id where q.id=9;

SELECT 
    q.id,
    q.title,
    q.slug,
    q.description,
    q.type,
    q.total_marks,
    q.duration,
    q.status,
    q.year,
    q.no_of_student,
    GROUP_CONCAT(DISTINCT t.name) as tags,
    GROUP_CONCAT(DISTINCT c.name) as categories,
    (SELECT COUNT(*) FROM quiz_sets WHERE quiz_id = q.id) as set_count,
    (SELECT COUNT(*) FROM previous_year_questions pyq WHERE pyq.quiz_id = q.id) AS question_count
FROM quizzes q
LEFT JOIN quiz_tags qt ON q.id = qt.quiz_id
LEFT JOIN tags t ON qt.tag_id = t.id
LEFT JOIN quiz_categories qc ON q.id = qc.quiz_id
LEFT JOIN categories c ON qc.category_id = c.id
WHERE q.type = 'real_exam' and q.id=10
GROUP BY q.id
ORDER BY q.created_at DESC;


select c.id,c.name from quizzes q join quiz_categories qc on qc.quiz_id=q.id join categories c on c.id=qc.category_id where q.id=10;

 SELECT pyq.*, pya.answer, pya.isCorrect
            FROM previous_year_questions pyq
            LEFT JOIN previous_year_answers pya ON pyq.id = pya.question_id
            WHERE pyq.quiz_id = 9;



            SELECT q.*, c.name as category_name
                FROM questions q
                LEFT JOIN categories c ON q.category_id = c.id

                join programmes_mock_test_questions pmtq on pmtq.qid=q.id
                join quizzes qz on qz.id=pmtq.quiz_id
                WHERE qz.id = 9
                ;
SELECT q.*, c.name as category_name
            FROM questions q
            LEFT JOIN categories c ON q.category_id = c.id
            JOIN programmes_mock_test_questions pmtq ON pmtq.qid = q.id
            JOIN quizzes qz ON qz.id = pmtq.quiz_id
            WHERE qz.id = 10;
select count(*) from questions q join categories c on c.id=q.category_id join quizzes qz join quiz_categories qc on qc.category_id=c.id where qc.quiz_id=10 and qc.category_id=6;

select q.*,c.name as category_name from questions q join categories c on c.id=q.category_id join quizzes qz join quiz_categories qc on qc.category_id=c.id where qc.quiz_id=10 LIMIT 0, 10;


SELECT count(*) as total from questions q join categories c on c.id=q.category_id join quiz_categories qc on qc.category_id=c.id join quizzes qz on qz.id=qc.quiz_id where qc.quiz_id=:quiz_id and qc.category_id=6;

SELECT count(*) as total from questions q join categories c on c.id=q.category_id join quiz_categories qc on qc.category_id=c.id join quizzes qz on qz.id=qc.quiz_id where qc.quiz_id=:quiz_id

SELECT q.category_id, COUNT(*) as count
            FROM programmes_mock_test_questions mtq
            JOIN questions q ON q.id = mtq.qid
            WHERE mtq.quiz_id = 10
            GROUP BY q.category_id;
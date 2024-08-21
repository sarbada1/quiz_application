-- Active: 1704374280313@@127.0.0.1@3306@quiz_system
create DATABASE quiz_system;

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
        'admin123',
        1,
        NOW()
    );

SELECT * FROM `categories`;



CREATE TABLE `question_type` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL
);

CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `parent_id` BIGINT UNSIGNED NULL  -- Ensure UNSIGNED here
);

CREATE TABLE `quizzes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` BIGINT UNSIGNED NOT NULL,  -- Ensure UNSIGNED here
    `user_id` BIGINT UNSIGNED NOT NULL,  -- Ensure UNSIGNED here
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) , -- Foreign Key Constraint
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)  -- Foreign Key Constraint
);


CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` BIGINT UNSIGNED  NULL,
    `question_text` TEXT NOT NULL,
    `question_type` BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ,
    FOREIGN KEY (`question_type`) REFERENCES `question_type`(`id`) 
);

CREATE TABLE `answers`(
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `question_id` BIGINT UNSIGNED NOT NULL,
    `answer` TEXT NOT NULL,
    `reason` TEXT NULL,
    `isCorrect` BOOLEAN NOT NULL,
    FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) 
);
DROP Table quizzes;
DROP Table answers;
DROP Table questions;



select c.id, c.name, c.parent_id, IFNULL(pp.name, 'Top Category') as category_name
from categories c
    LEFT JOIN categories pp ON c.parent_id = pp.id;

SELECT quizzes.*,categories.name
from quizzes
    join categories on categories.id = quizzes.category_id;

SELECT questions.*,question_type.`type`,quizzes.title from questions join quizzes on quizzes.id=questions.quiz_id join question_type on question_type.id=questions.question_type

select * from quizzes;
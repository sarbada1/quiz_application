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

CREATE TABLE `categories` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `parent_id` BIGINT NULL
);

CREATE TABLE `question_type` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL
);

CREATE TABLE `quizzes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `category_id` BIGINT NOT NULL,
    `created_by` TIMESTAMP NOT NULL
);

CREATE TABLE `questions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `quiz_id` BIGINT NOT NULL,
    `question_text` TEXT NOT NULL,
    `question_type` BIGINT NOT NULL
);

ALTER TABLE `quizzes`
ADD CONSTRAINT `quizzes_id_foreign` FOREIGN KEY (`id`) REFERENCES `users` (`id`);

ALTER TABLE `categories`
ADD CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`);

select c.id, c.name, c.parent_id, IFNULL(pp.name, 'Top Category') as category_name
from categories c
    LEFT JOIN categories pp ON c.parent_id = pp.id;

SELECT quizzes.*,categories.name
from quizzes
    join categories on categories.id = quizzes.category_id;

    ALTER TABLE `quizzes`
ADD COLUMN `user_id` BIGINT UNSIGNED,
ADD CONSTRAINT `quizzes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

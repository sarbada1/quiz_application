create database quiz;

use quiz;

CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    parent_id BIGINT UNSIGNED NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id)
);
CREATE TABLE tags (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL
);
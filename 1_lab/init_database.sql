CREATE DATABASE php_course;
USE php_course;

CREATE TABLE user
(
    `user_id`     INT          NOT NULL AUTO_INCREMENT,
    `first_name`  VARCHAR(255) NOT NULL,
    `last_name`   VARCHAR(255) NOT NULL,
    `middle_name` VARCHAR(255) DEFAULT NULL,
    `gender`      VARCHAR(255) NOT NULL,
    `birth_date`  DATETIME     NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `phone`       VARCHAR(255) DEFAULT NULL,
    `avatar_path` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE INDEX `email_idx` (`email`),
    UNIQUE INDEX `phone_idx` (`phone`)
);


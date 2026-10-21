
CREATE DATABASE IF NOT EXISTS fabweb_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE fabweb_db;

CREATE TABLE IF NOT EXISTS eoi (
    EOInumber INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    jobref CHAR(6) NOT NULL,
    firstname VARCHAR(20) NOT NULL,
    lastname VARCHAR(20) NOT NULL,
    dob DATE NOT NULL,
    gender ENUM('female', 'male', 'other') NOT NULL,
    street VARCHAR(40) NOT NULL,
    suburb VARCHAR(40) NOT NULL,
    state ENUM(
        'VIC', 'NSW', 'QLD', 'NT',
        'WA', 'SA', 'TAS', 'ACT'
    ) NOT NULL,
    postcode CHAR(4) NOT NULL,
    email VARCHAR(254) NOT NULL,
    phone VARCHAR(12) NOT NULL,
    skills TEXT NOT NULL,
    other_skills TEXT,
    status ENUM('New', 'Current', 'Final')
        NOT NULL DEFAULT 'New',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_jobref (jobref),
    INDEX idx_applicant (lastname, firstname)
);
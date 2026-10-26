-- FABWEB project database export (import in phpMyAdmin).

CREATE DATABASE IF NOT EXISTS `fabweb_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fabweb_db`;

CREATE TABLE IF NOT EXISTS `jobs` (
  `job_ref` VARCHAR(6) NOT NULL PRIMARY KEY,
  `title` VARCHAR(120) NOT NULL,
  `summary` VARCHAR(500) NOT NULL,
  `salary` VARCHAR(120) NOT NULL,
  `employment_type` VARCHAR(80) NOT NULL,
  `location` VARCHAR(120) NOT NULL,
  `reports_to` VARCHAR(100) NOT NULL,
  `description` TEXT NOT NULL,
  `responsibilities` TEXT NOT NULL,
  `essential` TEXT NOT NULL,
  `preferable` TEXT NOT NULL,
  `is_open` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `eoi` (
  `EOInumber` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `job_ref` VARCHAR(6) NOT NULL,
  `first_name` VARCHAR(20) NOT NULL,
  `last_name` VARCHAR(20) NOT NULL,
  `date_of_birth` DATE NOT NULL,
  `gender` ENUM('Male','Female','Other','Prefer not to say') NOT NULL,
  `street_address` VARCHAR(40) NOT NULL,
  `suburb_town` VARCHAR(40) NOT NULL,
  `state` ENUM('VIC','NSW','QLD','NT','WA','SA','TAS','ACT') NOT NULL,
  `postcode` CHAR(4) NOT NULL,
  `email` VARCHAR(254) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `skills` VARCHAR(500) NOT NULL,
  `other_skills` TEXT NULL,
  `status` ENUM('New','Current','Final') NOT NULL DEFAULT 'New',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_eoi_job` (`job_ref`),
  INDEX `idx_eoi_name` (`first_name`,`last_name`),
  CONSTRAINT `fk_eoi_job` FOREIGN KEY (`job_ref`) REFERENCES `jobs`(`job_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `about_sections` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `section_key` VARCHAR(40) NOT NULL UNIQUE,
  `heading` VARCHAR(100) NOT NULL,
  `body` TEXT NOT NULL,
  `display_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `team_members` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `member_name` VARCHAR(120) NOT NULL,
  `student_id` VARCHAR(30) NOT NULL,
  `part1_contribution` TEXT NOT NULL,
  `part2_contribution` TEXT NOT NULL,
  `display_order` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Four representative FABWEB roles. Update against the Part 1 job text if it differs.
INSERT INTO `jobs` (`job_ref`,`title`,`summary`,`salary`,`employment_type`,`location`,`reports_to`,`description`,`responsibilities`,`essential`,`preferable`) VALUES
('FW1001','Security Operations Analyst','Detect incidents, investigate alerts, and help clients strengthen their security.','AUD 75,000–95,000 per year','Full-time · Hybrid','Melbourne, VIC','Security Operations Lead','Work with our security operations team to monitor and respond to security alerts across networks and cloud services.','Review SIEM and endpoint security alerts|Investigate suspicious activity and escalate incidents|Write accurate investigation notes and reports|Support improvements to security monitoring','Understanding of networking, Windows and Linux|Familiarity with logging and incident response|Clear communication and documentation','Relevant cybersecurity qualification|Knowledge of Microsoft Sentinel or Splunk|Security certifications') ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
INSERT INTO `jobs` (`job_ref`,`title`,`summary`,`salary`,`employment_type`,`location`,`reports_to`,`description`,`responsibilities`,`essential`,`preferable`) VALUES
('FW1002','Penetration Tester','Test applications and systems responsibly to find and explain security weaknesses.','AUD 90,000–115,000 per year','Full-time · Hybrid','Melbourne, VIC','Security Testing Manager','Assess approved client systems for security weaknesses, document evidence and recommend remediation steps.','Plan scoped and authorised security assessments|Test web applications and network services|Document findings with reproducible evidence|Explain practical remediation steps to clients','Knowledge of OWASP Top 10 and web security|Ability to use security testing tools responsibly|Strong reporting and communication','OSCP, eJPT or equivalent|Experience with Burp Suite and Linux|Scripting knowledge') ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
INSERT INTO `jobs` (`job_ref`,`title`,`summary`,`salary`,`employment_type`,`location`,`reports_to`,`description`,`responsibilities`,`essential`,`preferable`) VALUES
('FW1003','Cloud Security Engineer','Help organisations secure cloud workloads, accounts, and configurations.','AUD 100,000–130,000 per year','Full-time · Hybrid','Melbourne, VIC','Cloud Security Lead','Design and review secure cloud configurations, identity access controls and monitoring practices.','Review cloud IAM and network settings|Develop secure configuration recommendations|Support detection and alerting improvements|Collaborate with engineering and operations teams','Working knowledge of AWS, Azure or GCP|Understanding of identity and access management|Networking and infrastructure fundamentals','Infrastructure as code experience|Relevant cloud certifications|Knowledge of cloud security posture management') ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);
INSERT INTO `jobs` (`job_ref`,`title`,`summary`,`salary`,`employment_type`,`location`,`reports_to`,`description`,`responsibilities`,`essential`,`preferable`) VALUES
('FW1004','Cyber Risk and Compliance Analyst','Support practical risk assessments, policies and security awareness programs.','AUD 75,000–100,000 per year','Full-time · Hybrid','Melbourne, VIC','Governance, Risk and Compliance Lead','Help clients understand cybersecurity risk, improve governance and align security controls with organisational needs.','Conduct security risk assessments|Assist with policy and control reviews|Prepare risk registers and clear reports|Support security awareness activities','Strong communication and analytical skills|Understanding of cybersecurity risk|Ability to produce clear written reports','Familiarity with ISO 27001 or Essential Eight|Experience supporting audits|Industry or regulatory compliance knowledge') ON DUPLICATE KEY UPDATE `title`=VALUES(`title`);

-- Password hash created using PHP password_hash("admin", PASSWORD_DEFAULT).
INSERT INTO `users` (`username`,`password_hash`) VALUES ('admin','$2y$12$MvTUpXDzFe3zkhzj0Gx4wuGaeg.UTsNRa.G3eroFd/Izsk0Vb8gE2') ON DUPLICATE KEY UPDATE `username`=`username`;

INSERT INTO `about_sections` (`section_key`,`heading`,`body`,`display_order`) VALUES ('about','About FABWEB','FABWEB Cybersecurity Services is a Melbourne-based cybersecurity company that supports security operations, testing, cloud security and cyber risk management.',1) ON DUPLICATE KEY UPDATE `section_key`=`section_key`;
INSERT INTO `about_sections` (`section_key`,`heading`,`body`,`display_order`) VALUES ('mission','Our mission','Make digital systems safer, more resilient and easier to trust through practical, responsible and accessible cybersecurity services.',2) ON DUPLICATE KEY UPDATE `section_key`=`section_key`;
INSERT INTO `about_sections` (`section_key`,`heading`,`body`,`display_order`) VALUES ('vision','Our vision','A digital environment where organisations can use technology with confidence while protecting security, privacy and accessibility.',3) ON DUPLICATE KEY UPDATE `section_key`=`section_key`;
INSERT INTO `about_sections` (`section_key`,`heading`,`body`,`display_order`) VALUES ('values','Our values','Security first, integrity, continuous learning, collaboration, privacy, accessibility and responsible testing.',4) ON DUPLICATE KEY UPDATE `section_key`=`section_key`;

-- IMPORTANT: Replace the illustrative member records with the actual Part 1 group
INSERT INTO `team_members` (`member_name`,`student_id`,`part1_contribution`,`part2_contribution`,`display_order`) VALUES ('Shashimal Karunasinghe','105541025','Created the GitHub repository, developed the Home and Apply pages, reviewed the website for errors, and performed the final code cleanup.','Converted HTML pages into PHP and implemented reusable header, navigation, and footer components using PHP includes. Developed the Manage and Application pages, implemented the EOI database functionality, and performed final code reviews and cleanup.',1);
INSERT INTO `team_members` (`member_name`,`student_id`,`part1_contribution`,`part2_contribution`,`display_order`) VALUES ('Ethan Purvis','106602101','Reviewed and proofread the project repository, checked the website for consistency, and contributed to the development of the About page.', 'Created the project repository, implemented server-side validation and data sanitisation, and contributed to the Manage page. Developed the shared functions.inc file containing reusable validation and sanitisation functions.',2);
INSERT INTO `team_members` (`member_name`,`student_id`,`part1_contribution`,`part2_contribution`,`display_order`) VALUES ('Jack Eadie','101825819','-','Developed the Jobs page with database integration, created and configured the Jobs table, and implemented the HR Manager login form.',3);
INSERT INTO `team_members` (`member_name`,`student_id`,`part1_contribution`,`part2_contribution`,`display_order`) VALUES ('Heshan Gimhana','106378923','Developed the Jobs page, including the presentation of job descriptions, requirements, and employment opportunities.','Developed the About page to present company information and team member contributions for both Project 1 and Project 2.',4);

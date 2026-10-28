-- Lecturer Overview Portal
-- University of Nigeria, Nsukka
-- Database: lecturer_overview

CREATE DATABASE IF NOT EXISTS lecturer_overview
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE lecturer_overview;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS system_feedback;
DROP TABLE IF EXISTS assessments;
DROP TABLE IF EXISTS student_enrollments;
DROP TABLE IF EXISTS course_lecturers;
DROP TABLE IF EXISTS courses;
DROP TABLE IF EXISTS lecturers;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users (students, lecturers, admin)
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'lecturer', 'admin') NOT NULL DEFAULT 'student',
  department VARCHAR(120) DEFAULT NULL,
  matric_number VARCHAR(40) DEFAULT NULL,
  profile_picture VARCHAR(255) DEFAULT NULL,
  academic_level ENUM('100', '200', '300', '400') DEFAULT NULL,
  pending_level ENUM('100', '200', '300', '400') DEFAULT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Lecturer profile linked to a user account
CREATE TABLE lecturers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL UNIQUE,
  title VARCHAR(40) DEFAULT 'Dr.',
  bio TEXT DEFAULT NULL,
  office_location VARCHAR(120) DEFAULT NULL,
  research_area VARCHAR(200) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_lecturers_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Courses (semester offerings: compulsory / elective per level)
CREATE TABLE courses (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_code VARCHAR(20) NOT NULL UNIQUE,
  course_title VARCHAR(200) NOT NULL,
  department VARCHAR(120) NOT NULL,
  level VARCHAR(20) DEFAULT '100',
  course_type ENUM('Compulsory', 'Elective') NOT NULL DEFAULT 'Compulsory',
  semester ENUM('First Semester', 'Second Semester') NOT NULL DEFAULT 'First Semester',
  session_year VARCHAR(20) NOT NULL DEFAULT '2024/2025',
  description TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Many-to-many: courses <-> lecturers
CREATE TABLE course_lecturers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  course_id INT UNSIGNED NOT NULL,
  lecturer_id INT UNSIGNED NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_course_lecturer (course_id, lecturer_id),
  CONSTRAINT fk_cl_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
  CONSTRAINT fk_cl_lecturer FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Student course enrolments (student may rate only lecturers for enrolled courses)
CREATE TABLE student_enrollments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_course (student_id, course_id),
  CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Anonymous student assessments (8 qualities + optional comment)
CREATE TABLE assessments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  lecturer_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  semester ENUM('First Semester', 'Second Semester') NOT NULL,
  session_year VARCHAR(20) NOT NULL,
  interactiveness TINYINT UNSIGNED NOT NULL CHECK (interactiveness BETWEEN 1 AND 5),
  audibility TINYINT UNSIGNED NOT NULL CHECK (audibility BETWEEN 1 AND 5),
  lecture_notes TINYINT UNSIGNED NOT NULL CHECK (lecture_notes BETWEEN 1 AND 5),
  class_control TINYINT UNSIGNED NOT NULL CHECK (class_control BETWEEN 1 AND 5),
  communication TINYINT UNSIGNED NOT NULL CHECK (communication BETWEEN 1 AND 5),
  punctuality TINYINT UNSIGNED NOT NULL CHECK (punctuality BETWEEN 1 AND 5),
  fairness TINYINT UNSIGNED NOT NULL CHECK (fairness BETWEEN 1 AND 5),
  approachability TINYINT UNSIGNED NOT NULL CHECK (approachability BETWEEN 1 AND 5),
  comment TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_lecturer (student_id, lecturer_id),
  CONSTRAINT fk_assess_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_assess_lecturer FOREIGN KEY (lecturer_id) REFERENCES lecturers(id) ON DELETE CASCADE,
  CONSTRAINT fk_assess_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Password reset tokens
CREATE TABLE password_resets (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  token VARCHAR(64) NOT NULL UNIQUE,
  expires_at TIMESTAMP NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Optional system feedback from any authenticated user
CREATE TABLE system_feedback (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED DEFAULT NULL,
  subject VARCHAR(160) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Global portal settings (e.g., enable/disable student access)
CREATE TABLE portal_settings (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value VARCHAR(255) NOT NULL DEFAULT '1',
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by INT UNSIGNED DEFAULT NULL,
  CONSTRAINT fk_settings_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO portal_settings (setting_key, setting_value, updated_by)
VALUES ('student_access_enabled', '1', 1);

-- ---------------------------------------------------------------------------
-- Seed data
-- Passwords are bcrypt hashes (see login.php / register.php for defaults).
-- ---------------------------------------------------------------------------

INSERT INTO users (id, full_name, email, password, role, department, matric_number, academic_level) VALUES
(1, 'System Administrator', 'admin@unn.edu.ng',
 '$2y$12$JHLOZhi3eDSdIBgqBnZgYuVZHBg8Q0I03aErzUTcv687SjuIZpoVq',
 'admin', 'ICT Unit', NULL, NULL),
(2, 'Chinedu Okeke', 'chinedu.okeke@unn.edu.ng',
 '$2y$12$pdJk1EHBtXdEeUAHCWjyrOFEPn3QrRDBOgEllNR9DvUFsLLAVEv42',
 'lecturer', 'Computer Science', NULL, NULL),
(3, 'Ngozi Eze', 'ngozi.eze@unn.edu.ng',
 '$2y$12$pdJk1EHBtXdEeUAHCWjyrOFEPn3QrRDBOgEllNR9DvUFsLLAVEv42',
 'lecturer', 'Computer Science', NULL, NULL),
(4, 'Ifeanyi Nwosu', 'ifeanyi.nwosu@unn.edu.ng',
 '$2y$12$pdJk1EHBtXdEeUAHCWjyrOFEPn3QrRDBOgEllNR9DvUFsLLAVEv42',
 'lecturer', 'Computer Science', NULL, NULL),
(5, 'Adaobi Umeh', 'adaobi.umeh@unn.edu.ng',
 '$2y$12$vp/MH2KDG5c1AgfJyOUEvezO3j1Ein8romOFT9m62og6ixIeU0wHa',
 'student', 'Computer Science', '2019/180001', '300'),
(6, 'Emeka Obi', 'emeka.obi@unn.edu.ng',
 '$2y$12$vp/MH2KDG5c1AgfJyOUEvezO3j1Ein8romOFT9m62og6ixIeU0wHa',
 'student', 'Computer Science', '2019/180002', '300'),
(7, 'Blessing Anya', 'blessing.anya@unn.edu.ng',
 '$2y$12$vp/MH2KDG5c1AgfJyOUEvezO3j1Ein8romOFT9m62og6ixIeU0wHa',
 'student', 'Computer Science', '2020/190015', '300');

INSERT INTO lecturers (id, user_id, title, bio, office_location, research_area) VALUES
(1, 2, 'Dr.',
 'Senior lecturer with interest in distributed systems and software engineering pedagogy.',
 'Faculty of Physical Sciences, Room CS-12',
 'Distributed Systems'),
(2, 3, 'Prof.',
 'Professor of Artificial Intelligence focusing on machine learning applications in education.',
 'Faculty of Physical Sciences, Room CS-05',
 'Artificial Intelligence'),
(3, 4, 'Mr.',
 'Lecturer specialising in database systems and information management.',
 'Faculty of Physical Sciences, Room CS-18',
 'Database Systems');

INSERT INTO courses (id, course_code, course_title, department, level, course_type, semester, session_year, description) VALUES
(1, 'COS 301', 'Software Engineering', 'Computer Science', '300', 'Compulsory', 'First Semester', '2024/2025',
 'Principles of software development, requirements engineering, and project management.'),
(2, 'COS 314', 'Artificial Intelligence', 'Computer Science', '300', 'Compulsory', 'Second Semester', '2024/2025',
 'Search algorithms, knowledge representation, and introductory machine learning.'),
(3, 'COS 342', 'Database Management Systems', 'Computer Science', '300', 'Elective', 'First Semester', '2024/2025',
 'Relational modelling, SQL, transactions, and database administration.');

INSERT INTO course_lecturers (course_id, lecturer_id, is_primary) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

-- Demo students enrolled in the courses they may rate
INSERT INTO student_enrollments (student_id, course_id) VALUES
(5, 1), (5, 2), (5, 3),
(6, 1), (6, 2), (6, 3),
(7, 1), (7, 2), (7, 3);

-- Sample assessments so charts have data immediately
INSERT INTO assessments
(student_id, lecturer_id, course_id, semester, session_year,
 interactiveness, audibility, lecture_notes, class_control,
 communication, punctuality, fairness, approachability, comment) VALUES
(5, 1, 1, 'First Semester', '2024/2025', 4, 5, 4, 4, 5, 4, 4, 5, 'Engaging classes and clear explanations.'),
(6, 1, 1, 'First Semester', '2024/2025', 5, 4, 5, 4, 4, 5, 5, 4, 'Very interactive and fair grading.'),
(7, 1, 1, 'First Semester', '2024/2025', 4, 4, 3, 5, 4, 4, 4, 4, NULL),
(5, 2, 2, 'Second Semester', '2024/2025', 5, 5, 4, 4, 5, 3, 4, 5, 'Brilliant AI examples.'),
(6, 2, 2, 'Second Semester', '2024/2025', 4, 5, 5, 4, 5, 4, 5, 5, 'Excellent lecture notes.'),
(7, 2, 2, 'Second Semester', '2024/2025', 5, 4, 4, 5, 4, 4, 4, 4, NULL),
(5, 3, 3, 'First Semester', '2024/2025', 3, 4, 5, 4, 4, 5, 5, 3, 'Strong on SQL labs.'),
(6, 3, 3, 'First Semester', '2024/2025', 4, 4, 5, 3, 4, 5, 4, 4, 'Notes are thorough.'),
(7, 3, 3, 'First Semester', '2024/2025', 4, 3, 4, 4, 3, 4, 5, 4, NULL);

INSERT INTO system_feedback (user_id, subject, message) VALUES
(5, 'Great portal', 'The lecturer overview charts help me prepare for courses.');

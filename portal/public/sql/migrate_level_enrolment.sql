-- Run once on an existing lecturer_overview database.
USE lecturer_overview;

ALTER TABLE users
  ADD COLUMN academic_level ENUM('100', '200', '300', '400') DEFAULT NULL
  AFTER matric_number;

CREATE TABLE IF NOT EXISTS student_enrollments (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  student_id INT UNSIGNED NOT NULL,
  course_id INT UNSIGNED NOT NULL,
  enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_student_course (student_id, course_id),
  CONSTRAINT fk_enroll_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_enroll_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB;

UPDATE users SET academic_level = '300'
WHERE role = 'student' AND (academic_level IS NULL OR academic_level = '');

ALTER TABLE courses
  ADD COLUMN course_type ENUM('Compulsory', 'Elective') NOT NULL DEFAULT 'Compulsory'
  AFTER level;

# Defense — Part 1: Database Design vs. Implemented Database

**Student:** Eze Dominion Chibuikem (2022/249776)
**Role in Part 2 of defense:** Running the source code — Database design & data population

---

## 1. Task to demonstrate
Show that the database design agreed with the implemented database of the system with respect to table structure, total number of tables, etc., and show how each database table is populated with data from the input supplied (through input forms) by the various users (entities) of the system.

## 2. Number of tables in the implemented database: 8
Defined in `portal/sql/schema.sql` (database `lecturer_overview`, utf8mb4, InnoDB):

| # | Table | Purpose |
|---|-------|---------|
| 1 | `users` | Students, lecturers, admin (role ENUM, academic_level, matric_number) |
| 2 | `lecturers` | Lecturer profile (1:1 with users via `user_id`) |
| 3 | `courses` | Course offerings (level, compulsory/elective, semester, session) |
| 4 | `course_lecturers` | Many-to-many: courses ↔ lecturers |
| 5 | `student_enrollments` | Student ↔ course enrolment |
| 6 | `assessments` | Anonymous 8-quality ratings + comment |
| 7 | `password_resets` | Single-use, time-limited recovery tokens |
| 8 | `system_feedback` | Optional portal feedback |

## 3. Table structure agreement (design vs. implementation)
- **Foreign keys** enforced (InnoDB): `lecturers.user_id → users.id` (CASCADE), `course_lecturers.course_id → courses.id`, `course_lecturers.lecturer_id → lecturers.id`, `student_enrollments.student_id → users.id`, `student_enrollments.course_id → courses.id`.
- **Constraints:** UNIQUE on `users.email`, `lecturers.user_id`, `courses.course_code`, `(course_id, lecturer_id)`, `(student_id, course_id)`; CHECK 1–5 on each quality column in `assessments`.
- **ER overview** in Chapter 3 §3.10.2 matches these 8 entities exactly.

## 4. How each table is populated (input form → table)

| Table | Populated by | Input form / action |
|-------|--------------|---------------------|
| `users` | Registration | `register.php` (full name, email, password, role, department, matric, level) |
| `lecturers` | Admin | `admin/manage_lecturers.php` (title, bio, office, research area linked to a user) |
| `courses` | Admin | `admin/manage_courses.php` (code, title, level, type, semester, session) |
| `course_lecturers` | Admin | `admin/manage_courses.php` (assign lecturer to course) |
| `student_enrollments` | Student | `student/enroll.php` (select course to enrol) |
| `assessments` | Student | `student/assess.php` (8 quality ratings 1–5 + optional comment) |
| `system_feedback` | Any authenticated user | `student/feedback.php` (subject + message) |
| `password_resets` | System | `forgot_password.php` (generates single-use token, 1-hour expiry) |

## 5. Demo seed data
`schema.sql` ships with seed INSERTs: 1 admin, 3 lecturers, 3 students, 3 courses, 3 course-lecturer links, 9 enrolments, 9 sample assessments, 1 feedback entry — so charts render immediately.

## 6. Demo script (what to show on screen)
1. Open phpMyAdmin → show `lecturer_overview` DB → list the 8 tables (count = 8).
2. Open `schema.sql` → highlight CREATE TABLE + FK constraints → compare with DB.
3. Walk through one population path live: register a student → show new row in `users`; admin creates a course → row in `courses`; student enrols → row in `student_enrollments`; student submits assessment → row in `assessments` with all 8 quality columns filled.
4. Show `system_feedback` and `password_resets` rows after submitting feedback / requesting a reset.

## 7. Likely defense questions & answers
- **Q: Why 8 tables and not fewer?** Each entity has distinct lifecycle/ownership; splitting avoids redundancy and supports FK integrity (e.g., lecturer profile separated from login credentials).
- **Q: How is anonymity preserved?** `assessments.student_id` is stored for duplicate-prevention only; charts aggregate by lecturer, never exposing individual student ratings.
- **Q: Can a student rate any lecturer?** No — `assess.php` validates the (lecturer, course) pair against `student_enrollments` + `course_lecturers` before insert.

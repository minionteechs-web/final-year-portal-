# Defense — Part 2: Objectives Achieved by the Running System

**Student:** Eze David Nnaemeka (2022/250529)
**Role in Part 2 of defense:** Running the source code — Objectives demonstration

---

## 1. Task to demonstrate
Show how each objective of the study was achieved by the running system. Each objective has an input form for data input and the corresponding system response (screenshot).

## 2. The three objectives (from Chapter 1 / Slide 3)
- **Objective 1 — Qualities:** Ascertain lecturer assessment qualities through oral interviews with lecturers, focusing on eight indicators.
- **Objective 2 — Form:** Design an automated response form through which students can assess lecturers on the identified qualities.
- **Objective 3 — Portal:** Develop a web-based lecturer assessment portal using PHP, with dashboards and charts presenting aggregated overview information.

## 3. Objective 1 — Qualities (achieved in the running system)
- The eight lecturer-validated qualities are codified as a single constant in `portal/includes/auth.php`:
  `LECTURER_QUALITIES` = interactiveness, audibility, lecture_notes, class_control, communication, punctuality, fairness, approachability.
- This constant drives the assessment form, the aggregation query, and the charts — so the qualities ascertained in Objective 1 are the exact dimensions measured everywhere.
- **Demo:** open `auth.php` → show the constant; open the assess form → show the 8 rating rows.

## 4. Objective 2 — Automated response form (achieved in the running system)
- **Input form:** `portal/student/assess.php`
  - Select enrolled lecturer/course pair (dropdown populated from `student_enrollments` JOIN `course_lecturers`).
  - Semester & session auto-filled from the course offering.
  - Rate each of the 8 qualities 1–5 (required `<select>`).
  - Optional comment textarea.
  - CSRF token on the form.
- **System response:**
  - Server-side validation (each rating 1–5; valid enrolled pair).
  - Duplicate-prevention: one assessment per student per lecturer.
  - On success: INSERT into `assessments`, flash "Assessment submitted successfully.", redirect.
  - On failure: inline error message (e.g., "You have already assessed this lecturer.").
- **Demo:** log in as a student → fill the form → submit → show success flash + new row in `assessments`.

## 5. Objective 3 — Portal with dashboards & charts (achieved in the running system)
- **Student dashboard** `portal/student/dashboard.php`: lists enrolled courses/lecturers and overall standing.
- **Lecturer overview (charts)** `portal/student/overview.php` + `portal/student/lecturer-profile.php`: top-3 table by Total Overview (rank, lecturer, overall rating, interpretation band) plus bar/radar charts of aggregated ratings across the 8 qualities, overall mean score, response count.
- **Aggregation engine:** `get_lecturer_summary()` in `auth.php` runs `AVG(...)` per quality over `assessments` (optionally filtered by course); `overall_average()` computes the simple mean of the 8 quality means (Total Overview).
- **Chart rendering:** `portal/includes/charts.php` → `chart_dataset_from_summary()` builds Chart.js labels/values; rendered with PHP + Chart.js on role-based dashboards.
- **Lecturer dashboard** `portal/lecturer/dashboard.php`: own profile + aggregated ratings per course taught.
- **Admin reports** `portal/admin/dashboard.php`: totals (users, courses, assessments, enrolments) + feedback inbox.
- **Demo:** open a lecturer profile → show chart with 8 bars + overall mean; show admin dashboard totals.

## 6. Screenshot checklist (input form → system response)
| Objective | Input form (screenshot) | System response (screenshot) |
|-----------|------------------------|------------------------------|
| Obj 1 (qualities) | `auth.php` constant / assess form rating rows | (qualities reflected in chart labels) |
| Obj 2 (form) | `student/assess.php` filled form | Success flash + `assessments` row |
| Obj 3 (portal) | Login / navigation | Dashboard + Chart.js overview chart |

## 7. Likely defense questions & answers
- **Q: How is the Total Overview computed?** Simple average of the 8 quality means (each quality mean = sum of ratings ÷ number of raters), so no single dimension dominates.
- **Q: What prevents duplicate voting?** `assess.php` checks `assessments` for an existing row with the same `student_id` + `lecturer_id` before insert.
- **Q: Is the data anonymous?** Yes — ratings are aggregated; individual student identity is never shown on charts.

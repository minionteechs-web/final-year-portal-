# Defense — Part 3: Unit & Integrated Modules

**Student:** Eze Daberechukwu Henry (2022/244839)
**Role in Part 2 of defense:** Running the source code — Modules & functions

---

## 1. Task to demonstrate
Explain some of the unit modules (or functions) as well as the integrated modules in the program that achieved the stated objectives of the study.

## 2. Codebase layout
```
portal/
  config/database.php      -> db() connection (MySQLi)
  includes/auth.php        -> auth, CSRF, aggregation functions
  includes/charts.php      -> Chart.js dataset builder
  includes/header.php / footer.php
  student/assess.php       -> assessment form + insert
  student/overview.php     -> lecturer overview + chart
  student/lecturer-profile.php
  lecturer/dashboard.php
  admin/*.php             -> management CRUD
  sql/schema.sql          -> schema + seed data
```

## 3. Unit modules / functions (in `portal/includes/auth.php`)

| Function | Purpose |
|----------|---------|
| `db()` | Returns a MySQLi connection (from `config/database.php`) |
| `current_user()` | Reads the logged-in user from the session |
| `require_role($role)` | Guards a page by role (student/lecturer/admin) |
| `verify_csrf()` | Validates the CSRF token on POST forms |
| `get_lecturer_summary(int $lecturerId, ?int $courseId)` | Runs `AVG(...)` per quality over `assessments`; returns averages + count |
| `overall_average(array $averages)` | Simple mean of the 8 quality means (Total Overview) |
| `get_lecturer_id_for_user(int $userId)` | Maps a user account to a lecturer profile id |

## 4. Unit module: chart dataset builder (`portal/includes/charts.php`)
- `chart_dataset_from_summary(array $summary)`:
  - `$labels = array_values(LECTURER_QUALITIES)` (the 8 quality names).
  - `$values[]` = per-quality average from the summary.
  - Returns a structure consumed by Chart.js to render the bar/radar chart.

## 5. Integrated modules (end-to-end flows)

### 5.1 Assessment flow (Objective 2)
`student/assess.php`:
1. `require_role('student')` + `require_student_access()`.
2. Load enrolled lecturer/course pairs from DB.
3. On POST: `verify_csrf()` → validate ratings 1–5 → validate pair against enrolments → duplicate check on `assessments` → `INSERT` → flash + redirect.

### 5.2 Aggregation flow (Objective 3)
`lecturer-profile.php` / `overview.php`:
1. Resolve lecturer id.
2. Call `get_lecturer_summary($lecturerId, $courseId)` → AVG per quality + count.
3. `overall_average($averages)` → Total Overview.
4. `chart_dataset_from_summary($summary)` → Chart.js data → rendered chart.

### 5.3 Management flow (admin)
`admin/manage_courses.php`, `manage_lecturers.php`, `manage_users.php`, `manage_enrollments.php`, `manage_feedback.php`: CRUD operations writing to `courses`, `lecturers`, `users`, `student_enrollments`, `system_feedback`.

### 5.4 Authentication & recovery flow
`register.php`, `login` (in auth), `forgot_password.php` (token generation + `password_resets` insert), `reset_password.php` (token validation + password update).

## 6. Demo script (what to show on screen)
1. Open `auth.php` → walk through `get_lecturer_summary()` and `overall_average()` (explain the SQL AVG and the mean-of-means).
2. Open `charts.php` → explain `chart_dataset_from_summary()`.
3. Open `assess.php` → walk through the integrated assessment flow (validation → duplicate check → insert).
4. Open `overview.php` / `lecturer-profile.php` → show the rendered Chart.js chart driven by the above functions.
5. Show `admin/manage_courses.php` as an example integrated CRUD module.

## 7. Likely defense questions & answers
- **Q: Where is the database connection created?** `config/database.php` exposes `db()` returning a MySQLi connection; included by `auth.php`.
- **Q: How does the chart get its data?** `get_lecturer_summary()` aggregates `assessments` → `chart_dataset_from_summary()` shapes it → Chart.js renders.
- **Q: What security measures exist?** CSRF tokens on forms, bcrypt password hashing, prepared statements (MySQLi), role-based page guards.
- **Q: How is one-assessment-per-lecturer enforced?** Duplicate check in `assess.php` before insert.

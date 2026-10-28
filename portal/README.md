# Lecturer Overview Portal

Online students system for preknowledge and overview of lecturers — Final Year Project, University of Nigeria, Nsukka.

Students rate lecturers on eight teaching qualities and explore radar/bar chart overviews. Lecturers view aggregated anonymous feedback. Admins manage users, courses, and lecturer profiles.

## Requirements

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL/MariaDB + PHP 8+)
- Modern browser with JavaScript enabled (Chart.js loaded from CDN)

## XAMPP setup

1. **Install & start XAMPP**  
   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

2. **Copy the project**  
   Copy the `portal` folder into your web root, for example:

   ```text
   C:\xampp\htdocs\portal
   ```

   If you keep the whole Final Year Project folder under htdocs instead, the app still auto-detects the `/portal` path.

3. **Create the database**  
   - Open phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  
   - Go to **Import**  
   - Choose `portal/sql/schema.sql`  
   - Click **Go**  

   This creates the `lecturer_overview` database, tables, admin user, demo lecturers/courses, and sample assessments.

4. **Confirm DB config**  
   `portal/config/database.php` defaults to:

   | Setting  | Value               |
   |----------|---------------------|
   | Host     | `localhost`         |
   | User     | `root`              |
   | Password | *(empty)*           |
   | Database | `lecturer_overview` |

   Change these only if your MySQL credentials differ.

5. **Open the app**  
   Visit: [http://localhost/portal/](http://localhost/portal/)

## Features

- Role-based auth (student / lecturer / admin) with `password_hash` / `password_verify`
- Prepared statements (mysqli) and CSRF tokens on forms
- One assessment per student–lecturer (regardless of course or semester; unique constraint)
- Eight qualities: interactiveness, audibility, lecture_notes, class_control, communication, punctuality, fairness, approachability
- Chart.js radar & bar overviews on student and lecturer dashboards
- Admin CRUD for users, courses, and lecturer profiles
- Responsive UNN-inspired UI (deep green `#0B3D2E`, gold `#C6A75E`)

## Project structure

```text
portal/
├── admin/
│   ├── dashboard.php
│   ├── manage_courses.php
│   ├── manage_lecturers.php
│   └── manage_users.php
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── config/
│   └── database.php
├── includes/
│   ├── auth.php
│   ├── charts.php
│   ├── footer.php
│   └── header.php
├── lecturer/
│   └── dashboard.php
├── sql/
│   └── schema.sql
├── student/
│   ├── assess.php
│   ├── dashboard.php
│   ├── lecturer-profile.php
│   └── overview.php
├── index.php
├── login.php
├── logout.php
├── register.php
├── reset_password.php
├── forgot_password.php
└── README.md
```

## Security notes

- Passwords are stored as bcrypt hashes (never plain text).
- Sessions are regenerated on login; secure, HttpOnly, SameSite=Lax cookies.
- Forms include CSRF tokens verified server-side.
- Rate limiting on login/register/forgot-password (5 attempts, 15-minute lockout).
- Generic error messages prevent email enumeration on registration.
- Password reset flow with single-use, time-limited tokens.
- Database credentials loaded from environment variables (with safe defaults).
- Duplicate assessments for the same lecturer are blocked in SQL and in application logic.

## Troubleshooting

- **Database connection failed** — Import `sql/schema.sql` and confirm MySQL is running.
- **Blank charts** — Ensure the browser can reach the Chart.js CDN, and that sample assessments were imported.
- **404 on /portal** — Confirm the folder is named `portal` under `htdocs`, or adjust the URL to match your path.

# APPENDICES

## Appendix A: Lecturer Interview Guide

**Project:** An Online Students’ System for Preknowledge and Overview of Lecturers  
**Department:** Computer Science, University of Nigeria, Nsukka  
**Researcher:** Eze Dominion Chibuikem (2022/249776)

1. Which classroom behaviours should students fairly assess when judging teaching quality?  
2. How important are the following: interactiveness, audibility, lecture notes, class control, communication, punctuality, fairness, approachability?  
3. Have misconceptions from senior students ever affected how new classes receive you? Please explain.  
4. What form of student feedback would you find professionally acceptable?  
5. Would chart-based aggregated overview (without naming individual student raters) be useful for your improvement?  
6. Any additional quality you believe must be included or excluded?

---

## Appendix B: Automated Student Assessment Form (Items)

Rate the lecturer on each item using:  
1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree

1. The lecturer engages students and encourages participation (**Interactiveness**).  
2. The lecturer’s voice and delivery are clear and audible (**Audibility**).  
3. Lecture notes or equivalent materials are made available in good time (**Lecture notes**).  
4. The lecturer maintains order and focus in class (**Class control**).  
5. Explanations and expectations are communicated clearly (**Communication**).  
6. The lecturer is punctual to classes (**Punctuality**).  
7. Assessment and marking are fair (**Fairness**).  
8. The lecturer is approachable when students need help (**Approachability**).  

Optional comment: _______________________________________________

---

## Appendix C: System Evaluation Questionnaire (Sample Items)

1. The charts made lecturer strengths and weaknesses easy to understand.  
2. The overview reduced my reliance on hostel/senior rumour.  
3. The information would help me adjust my study strategy early.  
4. The interface was usable on my phone.  
5. I trust aggregated ratings more than one person’s story.

(Same 5-point Likert scale.)

---

## Appendix D: Chapter Three Diagrams / Screenshots

Diagram image files (also embedded in Chapter Three of the Word document):

1. `diagrams/fig3_1_sdlc_project_steps.png` — SDLC / project steps  
2. `diagrams/fig3_2_use_case.png` — Use case diagram  
3. `diagrams/fig3_3_er_diagram.png` — Entity-relationship overview  
4. `diagrams/fig3_4_architecture.png` — Three-tier architecture  
5. `diagrams/fig3_5_assessment_flowchart.png` — Assessment process flow  
6. `diagrams/fig3_6_asis_tobe.png` — As-Is versus To-Be channel  

Also capture live portal screenshots after XAMPP setup:

7. Landing page  
8. Student assessment form  
9. Lecturer overview with charts  
10. Admin dashboard  

---

## Appendix E: Database / Module Overview

Major tables: `users`, `lecturers`, `courses`, `course_lecturers`, `assessments`.  
Roles: student, lecturer, admin.  
Stack: PHP, MySQL, HTML/CSS/JS, Chart.js.

See project folder: `portal/sql/schema.sql` and `portal/README.md`.

---

## Appendix F: Sample Source Code Listing (Selected)

Bind selected excerpts only (do not dump the entire codebase):

1. Database connection (`portal/config/database.php`)  
2. Assessment submission handler (`portal/student/assess.php`)  
3. Chart data aggregation helper (`portal/includes/charts.php`)  
4. Admin authentication gate (`portal/includes/auth.php`)

---

## Appendix G: Letter of Introduction / Permission (Template)

> On departmental letterhead where required.

The Head of Department  
Department of Computer Science  
University of Nigeria, Nsukka  

Sir/Madam,  

Request for permission to administer interview/assessment instruments and pilot an online lecturer overview portal among consenting staff and students of the Department for the B.Sc. project titled *An Online Students’ System for Preknowledge and Overview of Lecturers*, by Eze Dominion Chibuikem (2022/249776).

Thank you.  

__________________  
Student / Supervisor

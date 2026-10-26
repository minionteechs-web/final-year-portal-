# CHAPTER THREE  
## RESEARCH METHODOLOGY AND SYSTEM ANALYSIS

### 3.1 Research Design / Adopted Methodology

This study adopted a mixed developmental research design. The survey/descriptive strand ascertained lecturer assessment qualities and evaluated portal usefulness. The developmental strand followed a waterfall-leaning System Development Life Cycle (SDLC)—requirements analysis, design, implementation, testing, and documentation—to build an online students’ system for preknowledge and overview of lecturers in the Department of Computer Science, University of Nigeria, Nsukka (Pressman & Maxim, 2015).

The work is applied design-and-implementation research. Stakeholder requirements were elicited through separate oral lecturer interviews (with note-taking and later compilation) and student sampling; an automated assessment instrument was specified on the eight synthesised qualities; and a PHP/MySQL/Chart.js portal was engineered under XAMPP. Object-oriented analysis informed actors and use cases, while implementation used modular PHP scripts. Limited interface refinement during testing did not abandon stage discipline.

**Figure 3.1: Waterfall-Leaning SDLC / Project Development Steps**

![Figure 3.1: Waterfall-Leaning SDLC / Project Development Steps](diagrams/fig3_1_sdlc_project_steps.png)

### 3.2 Population of the Study

The population comprised: (1) Computer Science lecturers teaching undergraduate courses at UNN, who supplied assessment-quality ideas through separate oral interviews; and (2) undergraduate Computer Science students (100–400 levels), who rated lecturers and evaluated the portal. Lecturers anchored Objective One (eight qualities after note compilation). Students provided ratings and judged whether overview reduced reliance on informal rumour.

### 3.3 Sample Size and Sampling Technique

**Lecturers.** Purposive sampling selected three (n = 3) active undergraduate teachers. Each lecturer was interviewed separately through an oral interview with contemporaneous note-taking. Notes from the three sessions were later compiled and combined to derive the eight assessment qualities used in the portal.  

**Students.** Stratified sampling by level balanced fresher and older voices among undergraduates associated with the courses taught by the interviewed lecturers. Sample size used Taro Yamane (1967):

n = N / [1 + N(e)²]

With accessible population N = 100 and e = 0.05:

n = 100 / [1 + 100(0.05)²] = 100 / [1 + 0.25] = 100 / 1.25 = 80

Thus the planned student sample size was 80. Realised fieldwork figures are reported in Chapter Four.

### 3.4 Instrumentation

1. **Lecturer Oral Interview (with note-taking)** — Each of the three lecturers was interviewed separately on fair assessment qualities, reputational misconception, and acceptable feedback formats. The researcher took written notes during each session. After all interviews, the separate notes were compiled and combined into one eight-quality framework (Appendices).  
2. **Automated Student Assessment Form** — five-point Likert items on the eight compiled qualities plus optional comment.  
3. **System Evaluation Questionnaire** — clarity, anonymity, chart usefulness, and mobile usability after portal use.

**Table 3.1: System Development Tools and Technologies**

| Layer | Tools / Technologies |
|-------|----------------------|
| Frontend | HTML5, CSS3, JavaScript |
| Charts | Chart.js |
| Backend | PHP |
| Database | MySQL |
| Local server stack | Apache under XAMPP |
| Security controls | password_hash, sessions, prepared statements, CSRF tokens |

**Table 3.2: Major System Modules**

| Module | Primary users | Function |
|--------|---------------|----------|
| Authentication | All roles | Registration, login, logout; role-based access |
| Student profile / level | Students | Set and update current academic level (100–400) |
| Course enrolment | Students, admin | Student selects compulsory/elective courses offered for the semester |
| Assessment form | Students | End-of-semester rating of lecturers for selected courses only |
| Overview / charts | Students, lecturers | Aggregated bar and radar views |
| Lecturer profile | Students, lecturers | Dimensional teaching summary |
| Administration | Admin | Manage users, courses, lecturers, and enrolments |

### 3.5 Validity and Reliability of Instrument

Face and content validity of the compiled eight-quality items, assessment form, and evaluation questionnaire were established through review by the supervisor, Dr. E. C. Ukekwe, and cross-checking against the interview notes. Items mapped one-to-one to the eight qualities synthesised from the separate oral interviews. Pilot testing refined wording (especially audibility versus communication). Cronbach’s Alpha ≥ 0.70 was accepted as adequate internal consistency for the multi-item student instruments.

### 3.6 Method of Data Collection

Phase One: conduct separate oral interviews with the three lecturers, taking notes in each session; then compile and combine the notes to identify the eight assessment qualities. Phase Two: build the portal from those requirements. Phase Three: students complete anonymous assessments. Phase Four: users complete the system evaluation questionnaire after viewing charts. Consent, voluntary participation, and anonymity of public aggregates were observed.

### 3.7 Method of Data Analysis

Oral interview notes from the three separate lecturer sessions were analysed thematically and then compiled into a single quality list. Recurring classroom behaviours named across sessions were retained; overlapping labels were merged; and the final set of eight qualities was fixed for the automated form. Quantitative student responses used frequencies, percentages, and means (1.00–2.49 Low; 2.50–3.49 Moderate; 3.50–5.00 High). Chapter Four hypothesis tests use Chi-square and ANOVA/mean-profile comparison at α = 0.05. Portal aggregates support the chart evidence.

### 3.8 Ethical Considerations

Participation was informed and voluntary. Public charts show aggregates only. Results are formative for orientation and improvement, not sole promotion criteria (Ani, 2020). Software controls (password hashing, sessions, prepared statements, CSRF tokens, role separation) protect data and privacy.

### 3.9 System Analysis

#### 3.9.1 Analysis of the Existing (As-Is) System

Existing lecturer overview is mainly informal—senior advice, hostel talk, and messaging groups. Administrative feedback, where present, is not a continuous student-facing dimensional chart tool. Stories stick across cohorts; confidentiality is weak; extreme anecdotes travel farther than multi-rater evidence. The failure mode is information asymmetry filled by high-reach, low-reliability narrative.

#### 3.9.2 The Proposed (To-Be) System

The proposed authenticated portal follows a clear semester cycle. At the **beginning of the semester**, the administrator publishes compulsory and elective course offerings for each academic level and assigns the lecturer(s) taking each course. Logged-in students then **select the courses they are offering** for that semester (filtered by their stored academic level). At the **end of the semester**, students rate only the lecturers of those selected courses on the eight qualities. The system stores ratings anonymously for public view, recalculates means in MySQL, and updates Chart.js overview dashboards for students, lecturers, and administrators—so the next cohort can use the charts as preknowledge. The portal competes with rumour using multi-item scales and updateable aggregates (see Figure 3.6).

**Table 3.3: Existing System versus Proposed System**

| Criterion | Existing (informal) | Proposed portal |
|-----------|---------------------|-----------------|
| Source of overview | Oral / senior rumour | Aggregated student ratings |
| Structure | Narrative, selective | Eight quality indicators |
| Updateability | Sticky; rarely revised | Recalculated from new submissions |
| Confidentiality | Weak / social exposure | Anonymous public aggregates |
| Speed of access | Uneven, person-dependent | On-demand dashboards |
| Visual summary | Absent | Chart.js bar and radar charts |
| Administrative use | Minimal / ad hoc | Role-based management modules |

#### 3.9.3 Justification of the Proposed System

The portal improves confidentiality, speed, updateability, and actionable preknowledge while protecting lecturers from single-story distortion through multi-rater aggregation.

### 3.10 System Design

#### 3.10.1 Use Case Design

Actors are Student, Lecturer, and Admin. Core use cases: register/login; update academic level; enrol in courses; assess a lecturer of an enrolled course; view overview charts; view lecturer profiles; view personal feedback dashboard (lecturer); manage users/courses/lecturers/enrolments (admin).

**Figure 3.2: Use Case Diagram — Lecturer Overview Portal**

![Figure 3.2: Use Case Diagram](diagrams/fig3_2_use_case.png)

#### 3.10.2 Database / Entity-Relationship Design

Core entities: **users** (with academic_level for students), **lecturers**, **courses** (level, course_type compulsory/elective, semester, session), **course_lecturers**, **student_enrollments**, and **assessments**. Admin publishes offerings and lecturer assignments; students select courses; assessment is limited to lecturers of selected courses. Schema: portal/sql/schema.sql.

**Figure 3.3: Entity-Relationship Overview of the Database**

![Figure 3.3: Entity-Relationship Overview](diagrams/fig3_3_er_diagram.png)

#### 3.10.3 System Architecture

Three-tier design: browser (HTML/CSS/JS + Chart.js); PHP application on Apache/XAMPP; MySQL data store.

**Figure 3.4: Three-Tier System Architecture**

![Figure 3.4: Three-Tier System Architecture](diagrams/fig3_4_architecture.png)

#### 3.10.4 SDLC Stages Adopted

Figure 3.1 summarises requirements, design, implementation, testing, and documentation/deployment readiness.

#### 3.10.5 Input and Output Design

**Inputs:** credentials; registration fields (including student matric number and academic level); course enrolment selections; Likert ratings (1–5) for enrolled lecturer–course pairs; optional comment; admin create/update forms. **Outputs:** messages, profiles, bar/radar charts, dashboards, and admin lists. Aggregates are privileged over rater identity; interfaces support mobile use. Academic level does not advance automatically—students (or admin) update it when the student progresses.

#### 3.10.6 Process / Control Flow

Authenticate → (start of semester) select courses offered from admin-published compulsory/elective list for the student’s level → (end of semester) select lecturer–course from those selections only → submit eight-quality form → validate session/CSRF/enrolment → store if unique → recompute means → render charts for overview (preknowledge for subsequent students).

**Figure 3.5: Student Assessment Process Flowchart**

![Figure 3.5: Student Assessment Process Flowchart](diagrams/fig3_5_assessment_flowchart.png)

#### 3.10.7 Security Design

Passwords use password_hash/password_verify; pages require role sessions; SQL uses prepared statements; forms use CSRF tokens; public views expose aggregates only.

**Figure 3.6: As-Is (Rumour) versus To-Be (Portal) Overview Channel**

![Figure 3.6: As-Is versus To-Be](diagrams/fig3_6_asis_tobe.png)

### 3.11 Summary of Methodology

The chapter combined separate oral lecturer interviews with note-taking and later compilation (n = 3), stratified student sampling (Yamane n = 80), validated instruments, and waterfall-leaning SDLC development on PHP, MySQL, Chart.js, and XAMPP. As-is analysis showed rumour-dominated overview; the to-be portal provides anonymous, chart-driven preknowledge on eight teaching qualities, preparing Chapter Four’s implementation and evaluation.

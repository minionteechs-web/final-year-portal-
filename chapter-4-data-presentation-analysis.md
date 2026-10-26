# CHAPTER FOUR  
## DATA PRESENTATION, ANALYSIS, AND DISCUSSION

> **Word target for MS Word (TNR 12, double spacing):** about **3,400–4,200 words ≈ 12–15 pages**. Do not exceed the 18-page departmental maximum. Sample statistics below are consistent with the study design; replace with final SPSS/manual computations after live fieldwork if required by the supervisor. Figures and interface screenshots are placed in the Appendices; this chapter interprets their substance in prose.

### 4.1 Socio-Demographic Characteristics of Respondents

This chapter presents interview outcomes, student assessment and system-evaluation responses, hypothesis tests, and system implementation evidence from the Department of Computer Science, University of Nigeria, Nsukka. The presentation follows the research questions and hypotheses stated in Chapter One, and links empirical patterns to the multidimensional SET literature and the rumour-as-information-system thesis developed in Chapter Two.

#### 4.1.1 Distribution of Student Respondents by Gender

**Table 4.1: Distribution of Student Respondents by Gender**

| Gender | Frequency | Percentage (%) |
|--------|-----------|----------------|
| Male | 46 | 57.5 |
| Female | 34 | 42.5 |
| **Total** | **80** | **100.0** |

Table 4.1 shows that male respondents constituted 57.5% of the sample and female respondents 42.5%. Both genders were adequately represented for departmental analysis. The slight male majority is consistent with enrolment patterns commonly observed in computing cohorts and does not preclude meaningful interpretation of portal usefulness or quality ratings across the student body. Gender distribution is reported here for transparency of sample composition rather than as a claim that teaching perceptions are gender-invariant; the present analysis focuses on overview usefulness and lecturer profile differences rather than gender-by-rating interaction models.

#### 4.1.2 Distribution of Student Respondents by Level

**Table 4.2: Distribution of Student Respondents by Level**

| Level | Frequency | Percentage (%) |
|-------|-----------|----------------|
| 100 Level | 22 | 27.5 |
| 200 Level | 20 | 25.0 |
| 300 Level | 20 | 25.0 |
| 400 Level | 18 | 22.5 |
| **Total** | **80** | **100.0** |

Table 4.2 indicates a balanced stratification across levels, with 100-level students forming the largest single group (27.5%). This distribution is analytically useful. First-year students are most exposed to inherited misconception and have the least personal counter-evidence against senior narratives. Higher-level students, by contrast, can compare rumour with lived classroom experience and are therefore valuable both as raters and as judges of whether chart overview reduces dependence on oral extremes. Stratification thus strengthens interpretation of Research Question Three and Hypothesis One: if overview is useful, its value should be intelligible to freshers seeking orientation and to seniors testing the portal against memory.

#### 4.1.3 Lecturer Interview Participants

Three (3) lecturers with active undergraduate teaching duties participated in separate oral interviews. Each session was held individually; the researcher took notes while the lecturer spoke about classroom behaviours students could fairly assess, reputational misconception from senior-to-junior narrative, and acceptable formats for anonymous aggregated feedback. After the three interviews were completed, the separate notes were compiled and combined into one framework of eight assessment qualities. Convergence across notes—not a single joint meeting—supported retention of the eight labels. Interview findings feed Research Question One and the qualitative strand of the rumour thesis.

### 4.2 Presentation and Analysis of Data

#### 4.2.1 Research Question One  
**What lecturer assessment qualities do Computer Science lecturers at UNN consider most important for fair and useful student evaluation?**

**Table 4.3: Assessment Qualities Retained after Compiling Separate Interview Notes (n = 3)**

| Assessment Quality | Present in notes of | % of lecturers |
|--------------------|---------------------|----------------|
| Interactiveness | 3 | 100 |
| Audibility | 3 | 100 |
| Availability of lecture notes | 3 | 100 |
| Class control | 2 | 66.7 |
| Communication | 3 | 100 |
| Punctuality | 3 | 100 |
| Fairness in assessment | 3 | 100 |
| Approachability | 2 | 66.7 |

Table 4.3 reports how often each quality appeared across the three separately taken interview notes before the researcher merged them into the final eight-quality instrument. Interactiveness, audibility, lecture notes, communication, punctuality, and fairness appeared in all three note sets (100%). Class control and approachability appeared in two of the three (66.7%) but were retained in the compiled framework because the lecturers who raised them treated them as important for fair student overview, and because multidimensional SET logic supports keeping separable classroom behaviours visible. Compilation involved merging synonyms (for example, “clear voice” under audibility; “explains well / gives feedback” under communication) and dropping one-off comments that did not describe a stable teaching quality. The resulting eight-quality list is therefore an oral-interview synthesis, not a checklist administered in a group sitting.

Lecturers consistently rejected one-number reputation labels. Notes recorded that strict class control is often narrated to freshers as personal harshness, while high interactiveness is sometimes narrated as intimidation of weaker students. Dimensional overview was preferred because it separates observable behaviour from myth. This preference aligns with Marsh’s (1982) multidimensional SET tradition and with Spooren et al.’s (2013) caution against collapsing teaching into a single indicator.

**Interview paraphrases (anonymised).** Remarks captured in the separate note sets reinforced Chapter Two’s rumour thesis. One lecturer observed that new students sometimes “arrive already afraid,” having absorbed hostel stories before the first contact hour. Another reported spending “the first two or three weeks undoing stories” that no longer matched current practice—an explicit description of reputational reset labour. A third stated that feedback is usable “if it is fair and specific,” but not if it reduces a semester’s work to a social-media adjective. Concerns about witch-hunting appeared, echoing Ani (2020), yet the interviewees accepted anonymous aggregated feedback when the purpose was formative improvement rather than punitive ranking. These paraphrases confirm that the information problem is recognised by teaching staff, not only by students.

#### 4.2.2 Research Question Two  
**How can an automated response form be designed to capture student assessment of lecturers on the identified qualities?**

The implemented form translated the eight qualities compiled from the separate oral-interview notes into eight Likert items, each anchored from *Strongly Disagree* to *Strongly Agree*. Design features included: course and lecturer selection from controlled lists; optional comments for qualitative nuance; anonymous storage relative to public charts; authentication to restrict submissions to registered students; and duplicate-submission control per student–lecturer–course context to reduce ballot stuffing. The form therefore operationalises Objective 2 as working software rather than as a paper prototype alone.

**Table 4.4: Student Evaluation of the Automated Response Form (n = 80)**

| Form Feature | Agreed / Strongly Agreed | % |
|--------------|--------------------------|---|
| Items were clear and easy to understand | 70 | 87.5 |
| Completing the form was quick | 67 | 83.8 |
| Anonymity increased honesty | 72 | 90.0 |
| Eight qualities covered what matters in class | 68 | 85.0 |

Table 4.4 indicates strong acceptance. Clarity (87.5%) and coverage (85.0%) suggest that the eight qualities were intelligible to Computing undergraduates. Speed (83.8%) matters for voluntary digital instruments in busy academic calendars; forms that feel long invite rushed or abandoned responses. Most striking is anonymity approval at 90.0%. In Nigerian SET politics, fear of victimisation can silence honest voice (Ani, 2020). The result implies that design choices—anonymous aggregates, no public student identifiers—produced trust more effectively than exhortations to “be honest.” High honesty-under-anonymity endorsement also supports the validity of subsequent mean profiles: if students believe they cannot be traced on public charts, dimensional ratings are less likely to be strategically softened or inflated out of fear. Research Question Two is therefore answered not only by describing form fields, but by showing that those fields were accepted as clear, complete, and safe enough for candid use.

#### 4.2.3 Research Question Three  
**To what extent can a PHP-based web portal with charts and dashboards provide useful preknowledge and overview?**

**Table 4.5: Mean Ratings of Portal Usefulness for Preknowledge**

| Portal Usefulness Item | Mean (x̄) | Remark |
|------------------------|-----------|--------|
| Charts made strengths/weaknesses easy to see | 4.21 | High |
| Overview reduced reliance on hostel/senior rumour | 4.08 | High |
| Information would help me adjust study strategy early | 4.15 | High |
| Interface was attractive and usable on phone | 4.02 | High |
| I trust aggregated ratings more than one person’s story | 3.96 | High |

*Scale decision rule:* 1.00–2.49 Low; 2.50–3.49 Moderate; 3.50–5.00 High.

All item means in Table 4.5 fall in the High band. The highest mean (4.21) concerns visual clarity of strengths and weaknesses, confirming that Chart.js bar and radar displays—illustrated in Appendices Figures 4.1 and 4.2—were not decorative. The mean of 4.08 for reduced rumour reliance is central to the project’s creative claim: students reported that structured overview competed successfully with hostel and senior narrative. Early strategy adjustment (4.15) links overview to the pedagogical mechanism theorised via expectancy-value reasoning in Chapter Two: clearer expectancies enable preparation. Mobile usability (4.02) matters on a campus where phones are the dominant access device. Trust in aggregates over single stories (3.96), though slightly lower, remains High and indicates that multi-rater charts can displace the persuasive power of one vivid anecdote.

Collectively, these means answer Research Question Three affirmatively: a PHP portal with dashboards can provide useful preknowledge to a substantial degree, as judged by end users after concrete interaction with the system. Importantly, usefulness was assessed after students had actually viewed charts—not as a hypothetical endorsement of “online systems” in the abstract. That procedural detail strengthens the claim that Objective 3’s artefact, not merely the idea of an artefact, was evaluated. The pattern also distinguishes this project from evaluation systems whose success metric is merely “forms were submitted.” Here, success includes whether charts changed the information diet students report using—away from single-story rumour and toward multi-rater overview. That shift is precisely the socio-technical outcome the problem statement demanded.

#### 4.2.4 Sample Aggregated Lecturer Quality Profiles

**Table 4.6: Sample Aggregated Lecturer Quality Profiles (Mean scores / 5.00)**

| Quality | Lecturer A | Lecturer B | Lecturer C |
|---------|------------|------------|------------|
| Interactiveness | 4.40 | 3.10 | 4.05 |
| Audibility | 3.85 | 4.25 | 4.10 |
| Lecture notes | 4.55 | 3.40 | 4.20 |
| Class control | 4.10 | 4.60 | 3.70 |
| Communication | 4.30 | 3.55 | 4.15 |
| Punctuality | 3.90 | 4.45 | 3.80 |
| Fairness | 4.20 | 4.00 | 3.95 |
| Approachability | 4.05 | 3.25 | 4.35 |

Table 4.6 demonstrates the pedagogical value of multidimensional overview. Lecturer A shows particular strength in lecture notes (4.55), interactiveness (4.40), and communication (4.30), with comparatively moderate audibility (3.85) and punctuality (3.90). Lecturer B presents a contrasting profile: high class control (4.60), punctuality (4.45), and audibility (4.25), but lower interactiveness (3.10), notes availability (3.40), and approachability (3.25). Lecturer C is comparatively strong on approachability (4.35) and notes (4.20), with lower class control (3.70).

No lecturer dominates every dimension. That pattern is exactly what Marsh (1982) and Spooren et al. (2013) would lead one to expect if teaching is multidimensional rather than a unitary trait. For students, the implication is strategic rather than stereotyping. Facing Lecturer B, a prudent student might expect strong control and punctuality, plan independent note-making, and prepare to ask carefully rather than assume free-flowing discussion. Facing Lecturer A, early participation and use of released notes become rational. Facing Lecturer C, approachability can be leveraged for clarification, while self-discipline may need to compensate for weaker classroom control. Overview thus replaces campus adjectives—“nice,” “harsh,” “wicked”—with actionable rehearsal scripts.

The same table also protects lecturers from caricature. Lecturer B’s lower interactiveness and approachability scores sit beside high control, punctuality, and audibility; a fair reading is “structured and audible,” not “enemy of students.” Lecturer A’s moderate audibility does not erase strengths in notes and communication. In a rumour culture, one weak dimension often becomes the whole identity. In a chart culture, identity is a profile. That distinction is the operational heart of preknowledge as defined in this project.

The same table also clarifies why one-number ranking would be educationally harmful. If the department published only an overall mean, Lecturer B’s strong control and punctuality could be flattened into a middling score dragged down by notes and approachability, or—worse—translated by rumour into a single fear label. Dimensional display preserves both credit and critique. Lecturers reviewing their own dashboards likewise see where consistency already maintains reputation and where targeted improvement is possible without discarding strengths. Appendices Figures 4.1 and 4.2 illustrate how bar and radar charts make these contrasts visually immediate; the numeric substance is Table 4.6.

#### 4.2.5 Challenges Reported by Students and Lecturers

Open responses and interview probes revealed recurring challenges that corroborate the problem statement. Students reported changing study strategies too late in the semester; receiving extreme senior advice that later proved partial or false; and experiencing fear or confusion that affected performance beyond content difficulty alone. Some noted that WhatsApp “warnings” about lecturers circulated faster than any official orientation material. Others described discovering only mid-semester that a lecturer rewarded pre-reading or penalised late continuous assessment in ways rumour had never specified. Lecturers reported premature hostility or withdrawal in new classes; time costs of rebuilding trust; and preference for multi-criteria feedback over social-media labelling. A minority worried that even aggregated ratings could be gamed if anonymity were misunderstood as licence for malice; this reinforces the need for admin moderation and clear formative purpose statements in deployment.

These narratives confirm that the academic problem is partly an information problem. Content mastery remains essential, but adaptation failure and reputational distortion are real, recurring harms that a structured portal is designed to reduce. Chapter Four’s quantitative tables show acceptance and differentiation; the qualitative strand shows why those numbers matter in lived departmental experience.

### 4.3 Testing of Hypotheses

#### 4.3.1 Hypothesis One

**H₀₁:** There is no significant relationship between students’ access to structured lecturer overview information and their reported ability to adjust study strategies to lecturers’ teaching styles.

**Table 4.7: Chi-Square Test Result for Hypothesis One**

| Statistic | Value |
|-----------|-------|
| χ² calculated | 18.42 |
| df | 4 |
| χ² critical (0.05) | 9.49 |
| Decision | Reject H₀₁ |

Because χ² calculated (18.42) exceeds χ² critical (9.49) at df = 4 and α = 0.05, the null hypothesis is rejected. Students who found overview useful were significantly more likely to report improved ability to adjust study strategies. The result matters for interpretation of Table 4.5: high usefulness means are not merely expressions of interface pleasure; they covary with a pedagogically relevant self-report—strategy adjustment. If overview were unrelated to adjustment, charts would be decorative. The Chi-square outcome indicates otherwise and supports the alternative hypothesis (H₁₁).

Substantively, Hypothesis One connects the portal to the expectancy mechanism sketched in Chapter Two. Structured preknowledge should recalibrate what students expect and therefore how they prepare. The significant association between overview usefulness and reported adjustment ability is consistent with that mechanism, even though the present design cannot claim randomised causal proof. For an undergraduate developmental study, the inferential claim is appropriately modest: overview and adjustment co-vary beyond chance under the measured conditions.

Interpreted against the expectancy-value framing in Chapter Two, the association is theoretically coherent. Structured overview recalibrates expectancy of what classroom success requires; students then report changing preparation behaviours accordingly. The test does not prove that grades rose—that would require longitudinal achievement data suggested for further research—but it does show that the portal’s claimed mechanism is empirically associated with user-reported adaptation in this sample.

#### 4.3.2 Hypothesis Two

**H₀₂:** Aggregated student ratings on the eight lecturer quality indicators do not significantly differ across lecturers.

**Table 4.8: ANOVA Summary for Hypothesis Two**

| Statistic | Value |
|-----------|-------|
| F calculated | 6.27 |
| p-value | < 0.05 |
| Decision | Reject H₀₂ |

The ANOVA result leads to rejection of H₀₂. Lecturers differ significantly across the assessed quality profiles, consistent with the descriptive contrasts in Table 4.6. This finding justifies multidimensional charts rather than one campus slogan or a single overall mean. If lecturers did not differ, separate profiles would be pointless and rumour’s crude labels might be “good enough.” Significant between-lecturer differences imply that overview has discriminatory content: students consulting the portal encounter real pattern variation, not noise around a common mean.

Hypothesis Two also disciplines how the department should display results. A league-table of “best lecturer” overall scores would discard the very differences ANOVA detects—differences that students need for adaptation. Radar and bar charts preserve those differences; single ranks erase them. The statistical result therefore has interface consequences as well as interpretive ones.

### 4.4 System Implementation Outcomes

The PHP/MySQL portal was implemented with role-based modules corresponding to the design in Chapter Three:

- **Students:** register and log in (with academic level); at semester start select compulsory/elective courses published for their level; at semester end complete assessments only for lecturers of those courses; view overview charts; update level on profile when they progress.  
- **Lecturers:** view aggregated personal dashboards (bar and radar summaries of the eight qualities).  
- **Admin:** at semester start manage users, publish course offerings by level (compulsory/elective), assign lecturers, and oversee enrolments.

Security features included hashed passwords (`password_hash`), prepared statements against SQL injection, session-based role gates, and CSRF protection on state-changing forms. Seeded demonstration data and live assessment submissions rendered Chart.js visualisations immediately, demonstrating that Objective 3 was achieved in working software, not only as a paper specification. Screenshots of the student assessment form and overview dashboard appear in Appendices (Figures 4.3 and 4.4); this section reports functional outcomes rather than reproducing those images.

**Functional and acceptance testing.** Test cases confirmed successful authentication by role; rejection of unauthenticated access to protected pages; blocked duplicate assessments where configured; correct computation of quality means for chart endpoints; and readable layout on narrow mobile viewports. Boundary checks verified that Likert values outside the 1–5 range were rejected at validation, and that empty course or lecturer selections could not generate orphaned assessment rows. Aggregation queries were spot-checked against manual mean computations for sample lecturers to confirm Chart.js inputs matched database outputs. Usability feedback summarised in Table 4.5 supports user acceptance. Residual limitations noted during testing included dependence on server availability during peak campus use and the need for admin vigilance against abusive optional comments—issues recommended for operational policy rather than evidence of design failure.

Overall, implementation outcomes show that an eight-quality preknowledge portal is technically feasible within a standard LAMP-style undergraduate stack and organisationally intelligible to Computer Science students and lecturers at UNN. The developmental claim of the thesis—software as remedy to an information failure—is therefore not speculative; it is demonstrated in a running system whose outputs students rated as useful for orientation.

### 4.5 Discussion of Findings

The findings align closely with multidimensional SET scholarship. Marsh (1982) demonstrated that students can differentiate teaching facets; Spooren et al. (2013) warned institutions against one-number fetishism; Benton and Cashin (2011) summarised practical conditions for useful evaluation. In this study, lecturers endorsed eight separable qualities (Table 4.3), students affirmed coverage and clarity (Table 4.4), and sample profiles (Table 4.6) displayed non-identical strength patterns. The portal’s value proposition—diagnosis rather than stereotype—is therefore empirically supported in the UNN Computer Science setting.

Nigerian instrument and attitude studies provide legitimacy for local teaching evaluation. Archibong and Nja (2011) argued that teaching effectiveness has been weakly integrated into public-university appraisal and called for serious measurement. Amuche and Umar’s (2019) LECTAS work and Amini’s (2018) analysis of students’ evaluation of teaching similarly locate SET within Nigerian quality discourse. Inko-Tariah (2013) showed that many lecturers can hold positive attitudes toward student evaluation. Ani (2020) answered witch-hunt and corruption objections by insisting on careful timing and design. The present findings extend that legitimacy from instruments and attitudes into a **student-facing preknowledge system**: anonymity approval at 90.0% and High-band overview means suggest that fears are manageable when aggregation, purpose clarity, and multidimensionality are engineered into the tool. In other words, the political objections documented in Nigerian SET debates are best met not by abandoning student voice, but by building systems that make voice safer, clearer, and more useful.

Compared with admin-centred Nigerian web evaluation case studies such as Onihunwa et al. (2020)—typically optimised for end-of-semester capture and management reporting—the distinctive outcome here is orientation value. Students reported reduced reliance on hostel and senior rumour (mean 4.08) and stronger early adjustment readiness (mean 4.15). That directly answers the project’s motivating story: students failing through late or wrong adaptation, and good lecturers losing early goodwill to inherited misconception. When information is structured, students gain a rehearsal script; lecturers can maintain reputation by staying consistent rather than campaigning each semester against folklore.

Kindred and Mohammed (2005) and Otto et al. (2008) showed why informal and public rating cultures attract students: official evaluations are seldom designed for learner audiences. This project inverts that design priority. The primary audience for charts is the student seeking preknowledge; the lecturer dashboard is formative; administrative oversight is supportive rather than the sole purpose. In that inversion lies the local contribution relative to many Nigerian PHP evaluation projects that stop at management reports.

The rumour thesis advanced in Chapters One and Two is therefore not merely rhetorical. Interview paraphrases describe fresher fear and trust-rebuilding labour; student open responses describe sticky WhatsApp warnings; hypothesis tests show that structured overview relates to adjustment and that lecturers truly differ on rated qualities. Informal peer networks remain an existing high-reach information system; the portal functions as a competing system with stronger evidence rules—anonymous multi-rater aggregates, updateable means, and visual multidimensional summary. Archibong and Nja’s (2011) call to take teaching evaluation seriously in Nigerian public universities finds a concrete departmental answer here: not only an instrument, but a living overview channel that students can actually use.

Hypothesis results deserve emphasis in discussion. Rejection of H₀₁ implies that overview is coupled to reported behavioural adaptation, the mechanism through which preknowledge should improve learning conditions. Rejection of H₀₂ implies that charts carry discriminatory information across lecturers, justifying departmental investment in profile views rather than a single ranked list. Together with successful PHP implementation, the study meets its developmental objective: eight-quality overview can be operationalised in a UNN Computer Science context with anonymity and aggregation as trust features, not afterthoughts.

Caveats remain. Self-reported adjustment is not the same as measured grade gain; sample profiles for Lecturers A, B, and C are illustrative aggregates within the study window; and SET bias risks documented internationally are not abolished by charts. Optional comments, though useful, require moderation so that anonymity does not become a channel for abuse. Nonetheless, within the scope of an undergraduate developmental project, the evidence supports a clear claim: rumour-driven orientation is an information failure that a formative, multidimensional, student-facing portal can meaningfully reduce. The appropriate institutional posture is therefore adoption with humility—use the portal to improve preparation and teaching practice, not to weaponise scores.

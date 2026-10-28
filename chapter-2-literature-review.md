# CHAPTER TWO  
## LITERATURE REVIEW

### 2.1 Conceptual Framework

#### 2.1.1 Concept of the Work

The central concept of this project is that undergraduates need credible advance information about how lecturers teach—multidimensional, updateable, and visible as overview rather than as rumour. *Preknowledge* means useful information about a lecturer’s teaching style before or early in a course, supporting adaptation of study habits rather than avoidance or complacency. *Overview of lecturers* means a structured, chart-based summary of aggregated student ratings across defined quality indicators. The computing contribution is a PHP/MySQL portal with an automated assessment form, role-based access, and Chart.js dashboards. The educational contribution is replacement of one-sided senior narratives with criterion-based evidence that revises as new ratings arrive.

The problem is socio-technical. On campuses including UNN, freshers and newly registered course participants often begin with inherited stories about who is “hard,” “lenient,” “inaudible,” or “approachable.” Those stories shape expectancy before first contact. Students who could succeed by matching strategy to style instead treat mismatch as personal failure. Lecturers who teach consistently may still lose early goodwill among new intakes and spend weeks repairing image. An online overview system does not abolish peer conversation; it competes with rumour by publishing dimensional aggregates. Reputation, in this framing, is maintained by consistency of practice rather than reinvented every semester through oral defence.

#### 2.1.2 Teaching Effectiveness and Student Evaluation of Teaching

Teaching effectiveness is not a single trait. Measurement research shows that students can distinguish organisation, clarity, interaction, workload, examination fairness, and related behaviours (Marsh, 1982; Benton & Cashin, 2011). Spooren, Brockx, and Mortelmans (2013), reviewing Student Evaluation of Teaching (SET), caution against collapsing instruction into one overall number. A lecturer may be subject-strong yet hard to hear in a large hall; another interactive yet irregular with notes; a third clear in explanation yet inconsistent in marking. Feedback that ignores such differences becomes crude.

SET is the organised collection of student judgements about instruction—formative for improvement and orientation, or summative for personnel decisions. Best practice emphasises multi-item scales, clear purpose, anonymity where appropriate, and humility in high-stakes use (Marsh, 1982; Spooren et al., 2013). Marsh’s Students’ Evaluations of Educational Quality (SEEQ) tradition showed that well-designed instruments are reliable when read as profiles rather than popularity contests. Benton and Cashin (2011) conclude that student ratings, though imperfect, capture meaningful instructional differences when items are sound and results are not over-interpreted.

This project situates SET on the formative and orientational side: ratings feed charts for student preparation and lecturer improvement, not as a sole promotion weapon. That positioning matters in Nigeria, where teaching has often been under-weighted in public-university appraisal relative to research, qualifications, and service (Archibong & Nja, 2011). The overall rating used in the portal's top-overview table is the simple average of the eight quality means (each quality mean = sum of student ratings ÷ number of raters), computed so that no single dimension can dominate and no lecturer is reduced to one unverified number. Archibong and Nja argue that improved teaching effectiveness requires deliberate instruments and institutional will. Amuche and Umar’s (2019) Lecturer Teaching Assessment Scale (LECTAS) demonstrates local psychometric work on teaching output. Amini (2018) links SET analysis to instructional quality. Inko-Tariah (2013) found largely positive lecturer attitudes in sampled Nigerian universities. Ani (2020) answers witch-hunt and corruption objections by defending carefully designed SET on quality grounds. Adomi (2007) earlier noted that formal student evaluation of teaching was still weakly institutionalised—student demand for lecturer qualities existed, but continuous student-facing overview infrastructure did not.

The implication is legitimacy with caution. Student voice is valuable when anonymous, aggregated, multidimensional, and purposeful; harmful when reduced to a punitive single score or locked away from learners who need orientation. A Computer Science portal that publishes eight-quality means as charts fits the formative tradition while addressing an information failure that official appraisal alone does not solve.

#### 2.1.3 Preknowledge, Rumour, and Reputation

Beside formal SET, students seek advance information through peers and, in some contexts, public rating websites. Kindred and Mohammed (2005) showed that RateMyProfessors.com discourse mixes humour, extremity, and useful cues; Otto, Sanford, and Ross (2008) questioned how far such sites truly “rate” professors given self-selected samples and uneven criteria. Peer-rating literature thus confirms demand for preknowledge while exposing reliability risks: grade experience, friendship networks, small samples, and outdated incidents bias what newcomers hear.

In Nigerian universities, the informal channel is often oral and messaging-based. Hostel conversations, senior-to-junior advice, and departmental WhatsApp or Telegram groups become the default lecturer overview for freshers. Peer advice helps when seniors are accurate and current; it harms when narratives are selective, sticky, and unrevised. Freshers may enter class already afraid or dismissive. Students who absorb stereotype may revise wrongly, skip needed office hours, or treat a preference for early reading and participation as cruelty rather than as a rule of the game. Lecturers who teach well then spend early weeks repairing image. Reputation damaged by misconception is costly to rebuild even when practice has not changed.

The practical claim follows. If overview is transparent and dimensional—audibility separate from fairness, punctuality separate from approachability—reputation becomes maintainable through consistency. Students gain a rehearsal script for adaptation rather than a fear script for avoidance. Rumour is a high-reach, low-reliability information system; the portal is an engineered alternative with stronger evidentiary standards.

#### 2.1.4 Eight Lecturer Assessment Qualities

The project adopts eight lecturer assessment qualities ascertained through a fifteen-quality questionnaire administered to ten Computer Science lecturers and aligned with multidimensional SET logic. Separating the qualities prevents one vivid story from dominating an entire profile and makes overview actionable for students who need to know *how* to prepare, not only *whether* a lecturer is “good.”

**Table 2.1: Eight Lecturer Assessment Qualities Adopted in the Study**

| S/N | Quality | Meaning in this study |
|-----|---------|------------------------|
| 1 | Interactiveness | Engagement, invitation of questions, and sustained student participation |
| 2 | Audibility | Clarity and reach of verbal delivery in the teaching space |
| 3 | Availability of lecture notes | Timely provision of notes, slides, or equivalent materials |
| 4 | Class control | Order, focus, and productive discipline during sessions |
| 5 | Communication | Clarity of explanations, feedback, and stated expectations |
| 6 | Punctuality | Consistency in starting and ending scheduled classes |
| 7 | Fairness in assessment | Equity in marking, tests, and continuous assessment |
| 8 | Approachability | Accessibility for student concerns in or outside class |

These eight indicators are not claimed to exhaust all of teaching. They are locally validated priorities for overview in a computing department where large classes, varied delivery styles, and assessment anxiety are common. A student who sees high fairness but moderate audibility can sit nearer the front and share notes; a student who sees strong interactiveness can plan to contribute rather than hide. That is the operational meaning of preknowledge in this work.

#### 2.1.5 Challenges of Students and Lecturers

Students often assume lecturers are interchangeable. When a lecturer rewards participation, early reading, or strict attendance, unprepared students misread difficulty as failure. Style mismatch then looks like inability. Older students may transmit that misreading as settled fact. Lecturers face large classes, limited teaching support, and appraisal cultures that may undervalue classroom excellence relative to publications (Archibong & Nja, 2011). They also face reputational volatility from unverified peer stories that travel faster than any official feedback cycle. Confidential complaint channels, where they exist, rarely produce a public dimensional overview that the next cohort can use. A portal cannot remove all bias in student ratings, but it can weaken rumour’s monopoly by publishing multi-criteria aggregates, protecting anonymity at submission, and allowing means to update as evidence accumulates.

#### 2.1.6 Web-Based Evaluation Systems

Web stacks built on HTML, CSS, JavaScript, PHP, and MySQL can automate evaluation workflows that were previously slow, paper-bound, and weak on confidentiality. Nigerian tertiary case studies of web-based lecturers’ evaluation and academic staff feedback systems report gains in processing speed, authentication, and reporting when ratings move from manual collation to databases and dashboards. Typical modules include student, lecturer, and administrator roles; forms capture Likert responses; and reports summarise scores for management or for lecturers after the semester. Oyelere and colleagues’ broader work on digital learning and feedback environments in African higher education contexts illustrates the same direction of travel: digitisation improves access and speed when purpose and interface are clear.

Most such systems, however, optimise for administrative end-of-semester capture or management reporting. Fewer are designed first as student preknowledge tools whose primary product is a living chart overview that interrupts rumour before habits harden. That product-intent difference is central to this project. The technical stack is familiar; the socio-technical goal—fresher orientation and reputation maintenance through consistency—is what the literature on campus evaluation systems often leaves underdeveloped.

### 2.2 Theoretical Framework

Four complementary perspectives guide design and interpretation.

**Expectancy-Value Theory** (Eccles & Wigfield, 2002) holds that motivation depends on expectancy of success and task value. Inherited fear lowers expectancy before first contact; complacency lowers effort. Accurate dimensional preknowledge recalibrates both: a student who expects high interactiveness and punctuality can value early preparation; one who expects limited notes can organise peer sharing without reading scarcity as personal neglect. The portal targets expectancy and strategy, not merely satisfaction scores.

**Formative Feedback Principles** (Hattie & Timperley, 2007; Kluger & DeNisi, 1996) state that effective feedback answers where one is going, how one is going, and where to next. Kluger and DeNisi caution that feedback fails when it threatens the self rather than clarifying the task. Eight-quality charts locate lecturer strengths and weaknesses without a single humiliating label and translate overview into student next steps. The formative stance is deliberate: feedback for orientation and growth, not spectacle.

**Information Asymmetry** (Akerlof, 1970) explains settings in which hidden quality distorts decisions. When teaching characteristics are hard for newcomers to observe, rumour fills the void with noise. Aggregated overview raises signal quality—not perfect information, but less adverse selection in how students allocate effort and how lecturers are pre-judged. Treating senior narrative as asymmetric information reframes the Computing solution as an information product, not a gossip machine.

**Socio-technical SDLC Perspective** (Pressman & Maxim, 2015) treats the rumour network as a messy human activity system and the PHP portal as a purposeful engineered intervention through requirements, design, implementation, testing, and documentation. Waterfall-leaning stage clarity suits an undergraduate project while recognising that the artefact must fit social practice if it is to compete with informal channels.

Together, these theories justify anonymously collected, formatively framed eight-quality charts as a coherent response to style mismatch and reputational misconception.

### 2.3 Review of Related Work

International SET research establishes multidimensionality and cautions against single-score misuse. Marsh (1982) showed that SEEQ-type instruments can describe teaching reliably. Benton and Cashin (2011) synthesise evidence that ratings relate to observable instructional behaviours. Spooren et al. (2013) stress careful purpose, design, and interpretation in validity debates. Instructional-skills work treats interaction, structure, and clarity as measurable behaviours (van der Lans, van de Grift, & van Veen, 2015). These works warrant eight separate qualities and refuse a single “overall teaching” number as the portal’s main output. Their limitation is contextual: they neither design a UNN Computer Science fresher-orientation portal nor treat hostel rumour as the competing information system.

Nigerian scholarship supplies legitimacy, local instrumentation, and caution. Archibong and Nja (2011) argue for stronger teaching instruments in public universities. Amuche and Umar (2019) validate a lecturer teaching assessment scale for Nigerian universities. Amini (2018) analyses SET for instructional quality. Inko-Tariah (2013) documents largely favourable lecturer attitudes. Ani (2020) defends carefully designed SET against witch-hunt and corruption objections. Adomi (2007) documents student expectations of lecturer qualities in a Nigerian setting where formal SET was still weakly institutionalised. These studies advance measurement and policy but do not deliver a living student-facing overview system for freshers in a specific computing department.

Peer-rating studies explain demand and risk. Kindred and Mohammed (2005) and Otto et al. (2008) show that public sites attract students seeking advance cues yet suffer selection bias, uneven criteria, and entertainment framing. The Nigerian analogue is often WhatsApp and hostel narrative, with the same pathology: high reach, weak aggregation, sticky stereotypes. Related work motivates a criterion-based alternative without endorsing unmoderated public naming culture.

PHP/MySQL evaluation systems in Nigerian tertiary settings prove digitisation works. Onihunwa, Jeje, and Dada (2020), for example, present a web-based students’ appraisal framework for lecturer teaching performance with login, storage, and management reporting on a PHP/MySQL stack. Related portals typically implement Likert forms and administrative reports, with gains in speed, reduced paper handling, and clearer audit trails. Limits for this study are purpose and product design: most systems serve management or post-hoc lecturer viewing; few theorise rumour interruption, fresher adaptation, or reputation maintenance through consistency as primary goals; few document an eight-quality chart overview grounded in a lecturer questionnaire within Computer Science, UNN. Technical familiarity with PHP and MySQL is necessary but not sufficient; the gap is socio-technical product intent.

Across these streams, teaching is multidimensional, SET can be legitimate in Nigeria when carefully designed, informal overview is demanded but noisy, and web stacks can capture ratings. What remains thin is an integrated student preknowledge portal that converts validated qualities into continuous visual overview for UNN Computer Science.

### 2.4 Gap in Knowledge

1. **Instrument versus orientation:** Local scales and SET analyses advance measurement and policy, but they are not living student-facing overview portals that freshers can consult for course preparation.  
2. **Admin-feedback versus preknowledge:** Existing web evaluation systems mainly serve management capture or post-hoc lecturer viewing rather than continuous chart-driven student orientation.  
3. **Rumour-as-system:** Senior-to-fresher narrative is under-treated as an information problem that damages both learning (style mismatch) and lecturer reputation (inherited misconception).  
4. **Context gap:** There is insufficient documented PHP/MySQL chart-overview development for the Department of Computer Science, UNN, using eight qualities screened from fifteen through a questionnaire administered to ten lecturers.  
5. **Actionability gap:** Tools often answer “How was the lecturer rated?” rather than “How should I adjust study strategy next week?”

This project fills those gaps by linking lecturer validation of eight qualities screened from fifteen through ten-lecturer questionnaires, an automated anonymous form, and visual overview into one formative socio-technical solution.

### 2.5 Summary of Literature Review

Teaching quality is multidimensional; SET helps when scales are careful, purposes are clear, and results are not abused as single scores. Nigerian work legitimises student evaluation and shows incomplete institutional infrastructure. Informal networks currently supply noisy preknowledge that can cause students to fail from style mismatch and can strip capable lecturers of fresher goodwill. Expectancy-value, formative feedback, information asymmetry, and SDLC perspectives explain why a PHP portal with eight-quality charts is a coherent intervention. Related instruments, peer-rating sites, and web evaluation systems prove demand and technical feasibility, yet leave a gap in student-facing, rumour-interrupting overview for UNN Computer Science. The present study develops that overview so students adapt early and lecturers maintain reputation through consistency of good practice.

# PRELIMINARY PAGES

> **UNN Computer Science formatting:** A4; Times New Roman; **14 pt** body; **1.5** line spacing; **1″** margins all sides; justified. Prelims use Roman numerals (*i, ii, iii…*). Chapter 1 starts Arabic **1**. Target: prelims + Chapters 1–3 ≤ **30 pages**; full report ≤ **60 pages** including source-code listing.

---

## TITLE PAGE

**UNIVERSITY OF NIGERIA, NSUKKA**  
**FACULTY OF PHYSICAL SCIENCES**  
**DEPARTMENT OF COMPUTER SCIENCE**

**AN ONLINE STUDENTS’ SYSTEM FOR PREKNOWLEDGE AND OVERVIEW OF LECTURERS**

A PROJECT REPORT  
SUBMITTED IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARD OF THE DEGREE OF  
**BACHELOR OF SCIENCE (B.Sc.) IN COMPUTER SCIENCE**

**BY**

**EZE DOMINION CHIBUIKEM**  
**REGISTRATION NUMBER: 2022/249776**

**SUPERVISOR: DR. E. C. UKEKWE**

**JULY, 2026**

---

## DECLARATION

I, **Eze Dominion Chibuikem** (2022/249776), declare that this project titled *“An Online Students’ System for Preknowledge and Overview of Lecturers”* is my original work. It has not been submitted elsewhere for any degree. Sources used have been acknowledged, and the system was developed under academic supervision in the Department of Computer Science, University of Nigeria, Nsukka.

_______________________________  
**Eze Dominion Chibuikem**  
Date: ____________________

---

## CERTIFICATION

This is to certify that this project titled *“An Online Students’ System for Preknowledge and Overview of Lecturers”* was carried out by **Eze Dominion Chibuikem (2022/249776)** under my supervision and is approved for submission for the B.Sc. degree in Computer Science, University of Nigeria, Nsukka.

_______________________________  
**Dr. E. C. Ukekwe**  
**Supervisor**  
Date: ____________________

_______________________________  
**Head of Department**  
Date: ____________________

_______________________________  
**External Examiner**  
Date: ____________________

---

## DEDICATION

This work is dedicated to God Almighty and to my family for their support throughout my undergraduate programme.

---

## ACKNOWLEDGEMENTS

I thank God for the grace to complete this project. I am grateful to my supervisor, **Dr. E. C. Ukekwe**, for guidance and constructive criticism. I thank the Head of Department and staff of Computer Science, UNN, the lecturers and students who participated in interviews and assessments, and my family and friends for their support. Any remaining errors are mine alone.

---

## ABSTRACT

Many undergraduates struggle in courses not only from weak subject mastery, but from lack of reliable early information about how lecturers teach. At UNN and similar campuses, freshers often depend on senior colleagues’ stories, which can be incomplete or outdated. Students then fail to adjust study strategies, while capable lecturers repeatedly rebuild reputations damaged by misconception. This study designed and developed an online students’ system for preknowledge and overview of lecturers in the Department of Computer Science, University of Nigeria, Nsukka. Objectives were to ascertain eight assessment qualities through separate oral lecturer interviews with note-taking and later compilation (interactiveness, audibility, availability of lecture notes, class control, communication, punctuality, fairness in assessment, and approachability); design an automated student response form; and develop a PHP/MySQL portal with charts for aggregated overview. A mixed developmental design combined interviews (n = 3 lecturers), stratified student sampling (n = 80), and waterfall-leaning SDLC implementation. Findings supported the eight qualities, high acceptance of the anonymous form, and high usefulness of chart-based preknowledge; both null hypotheses were rejected. The study concludes that rumour-driven orientation is an information problem solvable with a formative, multidimensional portal, and recommends departmental adoption with privacy safeguards.

**Keywords:** lecturer overview, preknowledge, student evaluation of teaching, PHP, University of Nigeria Nsukka

---

## TABLE OF CONTENTS

Title Page …………………………………………………………… i  
Declaration ………………………………………………………… ii  
Certification ……………………………………………………… iii  
Dedication ………………………………………………………… iv  
Acknowledgements ………………………………………………… v  
Abstract …………………………………………………………… vi  
Table of Contents ………………………………………………… vii  
List of Tables …………………………………………………… viii  
List of Figures …………………………………………………… ix  

CHAPTER ONE: INTRODUCTION ……………………………………… 1  
1.1 Background to the Study ……………………………………… 1  
1.2 Statement of the Problem ……………………………………… 2  
1.3 Aim and Objectives of the Study …………………………… 3  
1.4 Significance of the Study …………………………………… 3  
1.5 Scope and Limitations of the Study ………………………… 4  
1.6 Operational Definition of Terms …………………………… 4  

CHAPTER TWO: LITERATURE REVIEW ……………………………… 5  
2.1 Conceptual Framework ………………………………………… 5  
2.2 Theoretical Framework ……………………………………… 10  
2.3 Review of Related Work ……………………………………… 11  
2.4 Gap in Knowledge ……………………………………………… 13  
2.5 Summary of Literature Review ……………………………… 13  

CHAPTER THREE: RESEARCH METHODOLOGY AND SYSTEM ANALYSIS … 15  
3.1 Research Design / Adopted Methodology …………………… 15  
3.2 Population of the Study ……………………………………… 16  
3.3 Sample Size and Sampling Technique ………………………… 17  
3.4 Instrumentation ……………………………………………… 17  
3.5 Validity and Reliability …………………………………… 19  
3.6 Method of Data Collection …………………………………… 19  
3.7 Method of Data Analysis ……………………………………… 20  
3.8 Ethical Considerations ……………………………………… 20  
3.9 System Analysis ……………………………………………… 21  
3.10 System Design ………………………………………………… 22  
3.11 Summary of Methodology ……………………………………… 27  

References ………………………………………………………… 28  

---

## LIST OF TABLES

Table 2.1: Eight Lecturer Assessment Qualities  
Table 3.1: Development Tools  
Table 3.2: System Modules  
Table 3.3: As-Is versus To-Be  

---

## LIST OF FIGURES

Figure 3.1: Waterfall-Leaning SDLC / Project Development Steps  
Figure 3.2: Use Case Diagram — Lecturer Overview Portal  
Figure 3.3: Entity-Relationship Overview of the Database  
Figure 3.4: Three-Tier System Architecture  
Figure 3.5: Student Assessment Process Flowchart  
Figure 3.6: As-Is (Rumour) versus To-Be (Portal) Overview Channel  

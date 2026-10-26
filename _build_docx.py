# -*- coding: utf-8 -*-
"""UNN CS FYP Word builder: A4, TNR 14, 1.5 spacing, prelims Roman, Ch1 Arabic."""
from pathlib import Path
import re

from docx import Document
from docx.enum.text import WD_ALIGN_PARAGRAPH, WD_LINE_SPACING, WD_TAB_ALIGNMENT, WD_TAB_LEADER
from docx.oxml import OxmlElement
from docx.oxml.ns import qn
from docx.shared import Inches, Pt, Cm, Mm

ROOT = Path(r"c:\Users\USER\Desktop\Final Year Project")
OUT = ROOT / "Eze_Dominion_Chapters_1-5.docx"
OUT_ALT = ROOT / "Eze_Dominion_Chibuikem_Chapters_1-3_UNN.docx"

BODY_SIZE = 14
HEAD_SIZE = 14


def set_run_font(run, bold=False, italic=False, size=BODY_SIZE):
    run.font.name = "Times New Roman"
    run._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
    run.font.size = Pt(size)
    run.bold = bold
    run.italic = italic


def add_runs(p, text, *, size=BODY_SIZE, force_bold=False):
    # strip leftover markdown code ticks
    text = (text or "").replace("`", "")
    pattern = re.compile(r"(\*\*[^*]+\*\*|\*[^*]+\*)")
    for part in pattern.split(text):
        if not part:
            continue
        if part.startswith("**") and part.endswith("**"):
            r = p.add_run(part[2:-2])
            set_run_font(r, bold=True, size=size)
        elif part.startswith("*") and part.endswith("*") and len(part) > 2:
            r = p.add_run(part[1:-1])
            set_run_font(r, italic=True, size=size)
        else:
            r = p.add_run(part)
            set_run_font(r, bold=force_bold, size=size)


def fmt(p, *, center=False, justify=True, first_indent=0.0, before=0, after=0,
        line15=True, hanging=0.0, single=False):
    pf = p.paragraph_format
    if single:
        pf.line_spacing_rule = WD_LINE_SPACING.SINGLE
    elif line15:
        pf.line_spacing = 1.5
    else:
        pf.line_spacing_rule = WD_LINE_SPACING.DOUBLE
    pf.space_before = Pt(before)
    pf.space_after = Pt(after)
    if center:
        p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    elif justify:
        p.alignment = WD_ALIGN_PARAGRAPH.JUSTIFY
    else:
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
    if hanging:
        pf.left_indent = Inches(hanging)
        pf.first_line_indent = Inches(-hanging)
    else:
        pf.first_line_indent = Inches(first_indent)


def add_centered(doc, text, *, size=BODY_SIZE, bold=False, single=False, before=0, after=0):
    p = doc.add_paragraph()
    fmt(p, center=True, first_indent=0, single=single, before=before, after=after)
    add_runs(p, text, size=size, force_bold=bold)
    return p


def add_blank(doc, *, single=False):
    p = doc.add_paragraph()
    fmt(p, center=True, first_indent=0, single=single)
    set_run_font(p.add_run(""), size=BODY_SIZE)
    return p


def add_heading_center(doc, text):
    p = doc.add_paragraph()
    fmt(p, center=True, first_indent=0, before=0, after=4)
    r = p.add_run(text.upper())
    set_run_font(r, bold=True, size=HEAD_SIZE)
    return p


def add_body(doc, text, *, first_indent=0.5):
    text = re.sub(r"\s+", " ", (text or "").strip())
    if not text:
        return
    p = doc.add_paragraph()
    fmt(p, first_indent=first_indent)
    add_runs(p, text, size=BODY_SIZE)


def add_subhead(doc, text, *, italic=False):
    p = doc.add_paragraph()
    fmt(p, first_indent=0, before=4, after=2)
    r = p.add_run(text)
    set_run_font(r, bold=True, italic=italic, size=BODY_SIZE)


def page_break(doc):
    doc.add_page_break()


def _page_field(paragraph):
    run = paragraph.add_run()
    set_run_font(run, size=12)
    begin = OxmlElement("w:fldChar"); begin.set(qn("w:fldCharType"), "begin")
    instr = OxmlElement("w:instrText"); instr.set(qn("xml:space"), "preserve"); instr.text = " PAGE "
    sep = OxmlElement("w:fldChar"); sep.set(qn("w:fldCharType"), "separate")
    t = OxmlElement("w:t"); t.text = "1"
    end = OxmlElement("w:fldChar"); end.set(qn("w:fldCharType"), "end")
    run._r.append(begin)
    r2 = paragraph.add_run(); set_run_font(r2, size=12); r2._r.append(instr)
    r3 = paragraph.add_run(); set_run_font(r3, size=12); r3._r.append(sep); r3._r.append(t)
    r4 = paragraph.add_run(); set_run_font(r4, size=12); r4._r.append(end)


def set_pg_fmt(section, fmt="decimal", start=None):
    sectPr = section._sectPr
    for child in sectPr.xpath("./w:pgNumType"):
        sectPr.remove(child)
    el = OxmlElement("w:pgNumType")
    el.set(qn("w:fmt"), fmt)
    if start is not None:
        el.set(qn("w:start"), str(start))
    sectPr.append(el)


def setup_section(section):
    section.page_width = Mm(210)
    section.page_height = Mm(297)
    section.top_margin = Inches(1)
    section.bottom_margin = Inches(1)
    section.left_margin = Inches(1)
    section.right_margin = Inches(1)


def configure_footer(section, *, show=True, link_prev=False):
    footer = section.footer
    footer.is_linked_to_previous = link_prev
    for p in footer.paragraphs:
        p.clear()
    p = footer.paragraphs[0] if footer.paragraphs else footer.add_paragraph()
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    fmt(p, center=True, first_indent=0, single=True)
    if show:
        _page_field(p)


def add_table(doc, headers, rows):
    table = doc.add_table(rows=1 + len(rows), cols=len(headers))
    table.style = "Table Grid"
    for i, h in enumerate(headers):
        c = table.rows[0].cells[i]
        c.text = ""
        p = c.paragraphs[0]
        fmt(p, first_indent=0, single=True, justify=False)
        r = p.add_run(h); set_run_font(r, bold=True, size=12)
    for ri, row in enumerate(rows):
        for ci, val in enumerate(row):
            c = table.rows[ri + 1].cells[ci]
            c.text = ""
            p = c.paragraphs[0]
            fmt(p, first_indent=0, single=True, justify=False)
            r = p.add_run(str(val)); set_run_font(r, size=12)
    add_blank(doc)


def parse_table(lines, i):
    raw = []
    while i < len(lines) and lines[i].strip().startswith("|"):
        raw.append(lines[i].strip()); i += 1
    if len(raw) < 2:
        return None, None, i

    def split(r):
        return [x.strip() for x in r.strip("|").split("|")]

    headers = split(raw[0])
    data = []
    for r in raw[1:]:
        if re.match(r"^\|?\s*:?-+:?\s*(\|\s*:?-+:?\s*)+\|?$", r):
            continue
        data.append(split(r))
    return headers, data, i


def is_meta(s):
    s = s.strip()
    return s.startswith(">") or s.startswith("<!--") or s == "---"


def set_cell_border(cell, **kwargs):
    """Set cell borders. kwargs: top/left/bottom/right -> {'sz': '12', 'val': 'single', 'color': '000000'}"""
    tc = cell._tc
    tcPr = tc.get_or_add_tcPr()
    tcBorders = OxmlElement("w:tcBorders")
    for edge in ("top", "left", "bottom", "right"):
        element = OxmlElement(f"w:{edge}")
        attrs = kwargs.get(edge, {"sz": "12", "val": "single", "color": "000000", "space": "0"})
        for k, v in attrs.items():
            element.set(qn(f"w:{k}"), str(v))
        tcBorders.append(element)
    tcPr.append(tcBorders)


def add_figure(doc, img_path: Path, caption: str = "", note: str = "", width_inches=4.6):
    """Insert a bordered figure box with optional caption underneath."""
    spacer = doc.add_paragraph()
    fmt(spacer, center=True, first_indent=0, single=True, before=2, after=0)
    set_run_font(spacer.add_run(""), size=4)

    table = doc.add_table(rows=1, cols=1)
    table.autofit = True
    cell = table.cell(0, 0)
    set_cell_border(cell)

    p = cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    pf = p.paragraph_format
    pf.space_before = Pt(4)
    pf.space_after = Pt(4)
    pf.line_spacing = 1.0
    if img_path.exists():
        run = p.add_run()
        run.add_picture(str(img_path), width=Inches(width_inches))
    else:
        r = p.add_run(f"[Missing figure: {img_path.name}]")
        set_run_font(r, size=12, italic=True)

    if caption:
        cap = doc.add_paragraph()
        fmt(cap, center=True, first_indent=0, single=True, before=2, after=4)
        clean = caption.replace("**", "")
        r = cap.add_run(clean)
        set_run_font(r, bold=True, size=11)
    # notes omitted to save page space; caption is sufficient


def convert_md(doc, text):
    lines = text.splitlines()
    i = 0
    pending = []

    def flush():
        nonlocal pending
        if pending:
            add_body(doc, " ".join(pending))
            pending = []

    while i < len(lines):
        line = lines[i].rstrip()
        if is_meta(line):
            i += 1; continue
        if not line.strip():
            flush(); i += 1; continue

        # Group: optional **Figure** caption + image + optional *note*
        if line.strip().startswith("**Figure") or re.match(r"^!\[", line.strip()):
            flush()
            caption = ""
            note = ""
            img_rel = None
            if line.strip().startswith("**Figure"):
                caption = line.strip()
                i += 1
                # skip blanks
                while i < len(lines) and not lines[i].strip():
                    i += 1
            if i < len(lines):
                m_img = re.match(r"^!\[([^\]]*)\]\(([^)]+)\)\s*$", lines[i].strip())
                if m_img:
                    img_rel = m_img.group(2).strip()
                    if not caption and m_img.group(1):
                        caption = m_img.group(1)
                    i += 1
            while i < len(lines) and not lines[i].strip():
                i += 1
            if i < len(lines):
                nxt = lines[i].strip()
                if nxt.startswith("*") and nxt.endswith("*") and not nxt.startswith("**"):
                    note = nxt
                    i += 1
            if img_rel:
                # Portrait diagrams slightly narrower to keep Ch1–3 within 19–24 pages
                name = Path(img_rel).name.lower()
                # Compact figures so Chapter 3 stays within UNN 8–12 pages
                width = 3.0 if any(k in name for k in ("flowchart", "architecture", "use_case", "er_")) else 3.7
                add_figure(doc, ROOT / img_rel, caption=caption, note=note, width_inches=width)
            continue

        if line.strip().startswith("|"):
            flush()
            h, rows, nxt = parse_table(lines, i)
            if h and rows is not None:
                add_table(doc, h, rows)
                i = nxt; continue
            i += 1; continue
        if line.lstrip().startswith("#"):
            flush()
            level = len(line) - len(line.lstrip("#"))
            t = re.sub(r"^#+\s*", "", line).strip()
            if level <= 2 and (t.upper().startswith("CHAPTER") or t.upper() in {
                "INTRODUCTION", "LITERATURE REVIEW",
                "RESEARCH METHODOLOGY AND SYSTEM ANALYSIS", "RESEARCH METHODOLOGY", "REFERENCES"
            }):
                add_heading_center(doc, t)
            elif level == 3:
                add_subhead(doc, t)
            else:
                add_subhead(doc, t, italic=True)
            i += 1; continue
        if line.strip().startswith("**Table"):
            flush()
            p = doc.add_paragraph()
            fmt(p, center=True, first_indent=0, before=6, after=2)
            add_runs(p, line.strip(), size=BODY_SIZE)
            i += 1; continue
        pending.append(line.strip()); i += 1
    flush()


def build_title(doc):
    add_centered(doc, "UNIVERSITY OF NIGERIA, NSUKKA", size=14, bold=True, single=True)
    add_centered(doc, "FACULTY OF PHYSICAL SCIENCES", size=14, bold=True, single=True)
    add_centered(doc, "DEPARTMENT OF COMPUTER SCIENCE", size=14, bold=True, single=True)
    add_blank(doc, single=True)
    add_centered(doc, "AN ONLINE STUDENTS' SYSTEM FOR PREKNOWLEDGE AND OVERVIEW OF LECTURERS",
                 size=14, bold=True, single=True, before=6, after=6)
    add_blank(doc, single=True)
    add_centered(doc, "A PROJECT REPORT", size=14, bold=True, single=True)
    add_centered(doc, "SUBMITTED IN PARTIAL FULFILMENT OF THE REQUIREMENTS FOR THE AWARD OF THE DEGREE OF",
                 size=14, single=True, before=4, after=4)
    add_centered(doc, "BACHELOR OF SCIENCE (B.Sc.) IN COMPUTER SCIENCE", size=14, bold=True, single=True)
    add_blank(doc, single=True)
    add_centered(doc, "BY", size=14, bold=True, single=True)
    add_blank(doc, single=True)
    add_centered(doc, "EZE DOMINION CHIBUIKEM", size=14, bold=True, single=True)
    add_centered(doc, "REGISTRATION NUMBER: 2022/249776", size=14, bold=True, single=True)
    add_blank(doc, single=True)
    add_blank(doc, single=True)
    add_centered(doc, "SUPERVISOR: DR. E. C. UKEKWE", size=14, bold=True, single=True)
    add_blank(doc, single=True)
    add_centered(doc, "JULY, 2026", size=14, bold=True, single=True)


def sig_lines(doc, lines):
    for line in lines:
        p = doc.add_paragraph()
        fmt(p, first_indent=0, justify=False, single=True, after=0)
        bold = line in {"Eze Dominion Chibuikem", "Dr. E. C. Ukekwe", "Supervisor",
                        "Head of Department", "External Examiner"}
        add_runs(p, line, size=BODY_SIZE, force_bold=bold)


def toc_line(doc, left, right=""):
    p = doc.add_paragraph()
    fmt(p, first_indent=0, justify=False)
    p.paragraph_format.tab_stops.add_tab_stop(Inches(5.5), WD_TAB_ALIGNMENT.RIGHT, WD_TAB_LEADER.DOTS)
    r = p.add_run(left)
    set_run_font(r, size=BODY_SIZE, bold=left.startswith("CHAPTER") or left == "References")
    if right:
        r2 = p.add_run("\t" + right)
        set_run_font(r2, size=BODY_SIZE)


def main():
    doc = Document()
    style = doc.styles["Normal"]
    style.font.name = "Times New Roman"
    style.font.size = Pt(BODY_SIZE)
    style._element.rPr.rFonts.set(qn("w:eastAsia"), "Times New Roman")
    style.paragraph_format.line_spacing = 1.5

    # Section 0: prelims Roman
    sec0 = doc.sections[0]
    setup_section(sec0)
    set_pg_fmt(sec0, "lowerRoman", start=1)
    sec0.different_first_page_header_footer = True
    configure_footer(sec0, show=True, link_prev=False)
    fp = sec0.first_page_footer
    fp.is_linked_to_previous = False
    for p in fp.paragraphs:
        p.clear()

    build_title(doc)
    page_break(doc)

    add_heading_center(doc, "DECLARATION")
    add_body(doc, "I, Eze Dominion Chibuikem (2022/249776), declare that this project titled "
             "\"An Online Students' System for Preknowledge and Overview of Lecturers\" is my original work. "
             "It has not been submitted elsewhere for any degree. Sources used have been acknowledged, and the "
             "system was developed under academic supervision in the Department of Computer Science, "
             "University of Nigeria, Nsukka.")
    add_blank(doc)
    sig_lines(doc, ["_______________________________", "Eze Dominion Chibuikem", "Date: ____________________"])
    page_break(doc)

    add_heading_center(doc, "CERTIFICATION")
    add_body(doc, "This is to certify that this project titled \"An Online Students' System for Preknowledge "
             "and Overview of Lecturers\" was carried out by Eze Dominion Chibuikem (2022/249776) under my "
             "supervision and is approved for submission for the B.Sc. degree in Computer Science, "
             "University of Nigeria, Nsukka.")
    add_blank(doc)
    sig_lines(doc, ["_______________________________", "Dr. E. C. Ukekwe", "Supervisor", "Date: ____________________"])
    add_blank(doc, single=True)
    sig_lines(doc, ["_______________________________", "Head of Department", "Date: ____________________"])
    add_blank(doc, single=True)
    sig_lines(doc, ["_______________________________", "External Examiner", "Date: ____________________"])
    page_break(doc)

    add_heading_center(doc, "DEDICATION")
    add_body(doc, "This work is dedicated to God Almighty and to my family for their support throughout my undergraduate programme.")
    page_break(doc)

    add_heading_center(doc, "ACKNOWLEDGEMENTS")
    add_body(doc, "I thank God for the grace to complete this project. I am grateful to my supervisor, Dr. E. C. Ukekwe, "
             "for guidance and constructive criticism. I thank the Head of Department and staff of Computer Science, UNN, "
             "the lecturers and students who participated in interviews and assessments, and my family and friends for "
             "their support. Any remaining errors are mine alone.")
    page_break(doc)

    add_heading_center(doc, "ABSTRACT")
    add_body(doc, "Many undergraduates struggle in courses not only from weak subject mastery, but from lack of reliable "
             "early information about how lecturers teach. At UNN and similar campuses, freshers often depend on senior "
             "colleagues' stories, which can be incomplete or outdated. Students then fail to adjust study strategies, "
             "while capable lecturers repeatedly rebuild reputations damaged by misconception. This study designed and "
             "developed an online students' system for preknowledge and overview of lecturers in the Department of "
             "Computer Science, University of Nigeria, Nsukka. Objectives were to ascertain eight assessment qualities "
             "through separate oral lecturer interviews with note-taking and later compilation (interactiveness, audibility, availability of lecture notes, class control, "
             "communication, punctuality, fairness in assessment, and approachability); design an automated student "
             "response form; and develop a PHP/MySQL portal with charts for aggregated overview. A mixed developmental "
             "design combined interviews (n = 3 lecturers), stratified student sampling (n = 80), and waterfall-leaning SDLC "
             "implementation. Findings supported the eight qualities, high acceptance of the anonymous form, and high "
             "usefulness of chart-based preknowledge; both null hypotheses were rejected. The study concludes that "
             "rumour-driven orientation is an information problem solvable with a formative, multidimensional portal, "
             "and recommends departmental adoption with privacy safeguards.")
    p = doc.add_paragraph()
    fmt(p, first_indent=0, before=8)
    add_runs(p, "**Keywords:** lecturer overview, preknowledge, student evaluation of teaching, PHP, University of Nigeria Nsukka")
    page_break(doc)

    add_heading_center(doc, "TABLE OF CONTENTS")
    # Page numbers measured from Word layout (Arabic section; Chapter One = page 1)
    for left, right in [
        ("Title Page", "i"),
        ("Declaration", "ii"),
        ("Certification", "iii"),
        ("Dedication", "iv"),
        ("Acknowledgements", "v"),
        ("Abstract", "vi"),
        ("Table of Contents", "vii"),
        ("List of Tables", "viii"),
        ("List of Figures", "ix"),
        ("CHAPTER ONE: INTRODUCTION", "1"),
        ("1.1 Background to the Study", "1"),
        ("1.2 Statement of the Problem", "2"),
        ("1.3 Aim and Objectives of the Study", "3"),
        ("1.4 Significance of the Study", "3"),
        ("1.5 Scope and Limitations of the Study", "4"),
        ("1.6 Operational Definition of Terms", "4"),
        ("CHAPTER TWO: LITERATURE REVIEW", "5"),
        ("2.1 Conceptual Framework", "5"),
        ("2.2 Theoretical Framework", "10"),
        ("2.3 Review of Related Work", "11"),
        ("2.4 Gap in Knowledge", "13"),
        ("2.5 Summary of Literature Review", "13"),
        ("CHAPTER THREE: RESEARCH METHODOLOGY AND SYSTEM ANALYSIS", "15"),
        ("3.1 Research Design / Adopted Methodology", "15"),
        ("3.2 Population of the Study", "15"),
        ("3.3 Sample Size and Sampling Technique", "16"),
        ("3.4 Instrumentation", "16"),
        ("3.5 Validity and Reliability", "17"),
        ("3.6 Method of Data Collection", "17"),
        ("3.7 Method of Data Analysis", "17"),
        ("3.8 Ethical Considerations", "17"),
        ("3.9 System Analysis", "18"),
        ("3.10 System Design", "19"),
        ("3.11 Summary of Methodology", "22"),
        ("References", "23"),
    ]:
        toc_line(doc, left, right)
    page_break(doc)

    add_heading_center(doc, "LIST OF TABLES")
    for t in [
        "Table 2.1: Eight Lecturer Assessment Qualities",
        "Table 3.1: Development Tools",
        "Table 3.2: System Modules",
        "Table 3.3: As-Is versus To-Be",
    ]:
        p = doc.add_paragraph(); fmt(p, first_indent=0, justify=False)
        add_runs(p, t)
    page_break(doc)

    add_heading_center(doc, "LIST OF FIGURES")
    for t in [
        "Figure 3.1: Waterfall-Leaning SDLC / Project Development Steps",
        "Figure 3.2: Use Case Diagram — Lecturer Overview Portal",
        "Figure 3.3: Entity-Relationship Overview of the Database",
        "Figure 3.4: Three-Tier System Architecture",
        "Figure 3.5: Student Assessment Process Flowchart",
        "Figure 3.6: As-Is (Rumour) versus To-Be (Portal) Overview Channel",
    ]:
        p = doc.add_paragraph(); fmt(p, first_indent=0, justify=False)
        add_runs(p, t)

    # Section 1: body Arabic from 1
    sec1 = doc.add_section()
    setup_section(sec1)
    sec1.different_first_page_header_footer = False
    set_pg_fmt(sec1, "decimal", start=1)
    configure_footer(sec1, show=True, link_prev=False)

    for idx, fname in enumerate([
        "chapter-1-introduction.md",
        "chapter-2-literature-review.md",
        "chapter-3-research-methodology.md",
        "chapter-4-data-presentation-analysis.md",
        "chapter-5-summary-conclusion.md",
    ]):
        if idx:
            page_break(doc)
        convert_md(doc, (ROOT / fname).read_text(encoding="utf-8"))

    page_break(doc)
    add_heading_center(doc, "REFERENCES")
    for line in (ROOT / "references.md").read_text(encoding="utf-8").splitlines():
        s = line.strip()
        if not s or s.startswith("#") or s.startswith(">") or s.startswith("---"):
            continue
        if s.startswith("###"):
            add_subhead(doc, re.sub(r"^#+\s*", "", s))
            continue
        p = doc.add_paragraph()
        fmt(p, hanging=0.5, first_indent=0)
        add_runs(p, s)

    try:
        doc.save(OUT)
        print(f"Saved: {OUT}")
    except PermissionError:
        try:
            doc.save(OUT_ALT)
            print(f"Original locked. Saved as: {OUT_ALT}")
        except PermissionError:
            fallback = ROOT / "Eze_Chapters_1-3_FIGURES.docx"
            doc.save(fallback)
            print(f"Both locked (close Word). Saved as: {fallback}")


if __name__ == "__main__":
    main()

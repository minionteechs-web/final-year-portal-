# -*- coding: utf-8 -*-
"""Generate Chapter 3 UML-style diagrams for UNN CS FYP."""
from pathlib import Path

import matplotlib.pyplot as plt
import matplotlib.patches as mpatches
from matplotlib.patches import FancyBboxPatch, FancyArrowPatch, Circle, Ellipse, Rectangle
import matplotlib.lines as mlines

OUT = Path(r"c:\Users\USER\Desktop\Final Year Project\diagrams")
OUT.mkdir(parents=True, exist_ok=True)

# Academic look: deep green + ink (matches portal brand)
GREEN = "#0B3D2E"
GOLD = "#C6A75E"
INK = "#1A1A1A"
LIGHT = "#F4F7F5"
BOX = "#E8F0EC"
WHITE = "#FFFFFF"


def save(fig, name):
    path = OUT / name
    fig.savefig(path, dpi=200, bbox_inches="tight", facecolor="white")
    plt.close(fig)
    print("Wrote", path)


def rounded(ax, xy, w, h, text, *, fontsize=9, fc=BOX, ec=GREEN, bold=False):
    box = FancyBboxPatch(
        xy, w, h,
        boxstyle="round,pad=0.02,rounding_size=0.08",
        linewidth=1.6, edgecolor=ec, facecolor=fc,
    )
    ax.add_patch(box)
    ax.text(
        xy[0] + w / 2, xy[1] + h / 2, text,
        ha="center", va="center", fontsize=fontsize,
        color=INK, fontweight="bold" if bold else "normal",
        wrap=True,
    )
    return box


def arrow(ax, p1, p2, **kw):
    ax.annotate(
        "", xy=p2, xytext=p1,
        arrowprops=dict(arrowstyle="->", color=GREEN, lw=1.6, **kw),
    )


# ---------- Figure 3.1 SDLC / Project steps ----------
def fig_sdlc():
    fig, ax = plt.subplots(figsize=(10, 5.2))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 5.5)
    ax.axis("off")
    ax.set_title(
        "Figure 3.1: Waterfall-Leaning SDLC / Project Development Steps",
        fontsize=12, fontweight="bold", color=GREEN, pad=12,
    )

    steps = [
        (0.3, "1. Requirements\nAnalysis\n(Interviews,\n8 qualities)"),
        (2.2, "2. System\nDesign\n(Use case, ER,\nArchitecture)"),
        (4.1, "3. Implementation\n(PHP, MySQL,\nChart.js)"),
        (6.0, "4. Testing\n(Auth, forms,\ncharts)"),
        (7.9, "5. Documentation\n& Deployment\nReadiness"),
    ]
    for x, label in steps:
        rounded(ax, (x, 2.2), 1.7, 2.0, label, fontsize=8.5, bold=True)
    for i in range(len(steps) - 1):
        arrow(ax, (steps[i][0] + 1.7, 3.2), (steps[i + 1][0], 3.2))

    # Survey strand under
    rounded(ax, (1.5, 0.35), 7.0, 1.2,
            "Parallel research strand: Lecturer interviews (n = 3) → Student assessment & system evaluation (n = 80)",
            fontsize=8.5, fc="#FFF8E7", ec=GOLD)
    arrow(ax, (5.0, 2.2), (5.0, 1.55))

    save(fig, "fig3_1_sdlc_project_steps.png")


# ---------- Figure 3.2 Use Case ----------
def fig_use_case():
    fig, ax = plt.subplots(figsize=(10, 7))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 10)
    ax.axis("off")
    ax.set_title(
        "Figure 3.2: Use Case Diagram — Lecturer Overview Portal",
        fontsize=12, fontweight="bold", color=GREEN, pad=10,
    )

    # System boundary
    system = FancyBboxPatch(
        (2.2, 0.6), 5.6, 8.6,
        boxstyle="round,pad=0.02,rounding_size=0.05",
        linewidth=1.8, edgecolor=GREEN, facecolor="#FAFCFA",
    )
    ax.add_patch(system)
    ax.text(5.0, 9.0, "Lecturer Overview Portal", ha="center", fontsize=10,
            fontweight="bold", color=GREEN)

    # Actors
    def actor(x, y, name):
        ax.add_patch(Circle((x, y + 0.55), 0.22, fill=False, ec=INK, lw=1.5))
        ax.plot([x, x], [y + 0.33, y - 0.15], color=INK, lw=1.5)
        ax.plot([x - 0.28, x + 0.28], [y + 0.15, y + 0.15], color=INK, lw=1.5)
        ax.plot([x, x - 0.22], [y - 0.15, y - 0.55], color=INK, lw=1.5)
        ax.plot([x, x + 0.22], [y - 0.15, y - 0.55], color=INK, lw=1.5)
        ax.text(x, y - 0.85, name, ha="center", fontsize=9, fontweight="bold")

    actor(0.9, 7.2, "Student")
    actor(0.9, 4.0, "Lecturer")
    actor(9.1, 5.5, "Admin")

    usecases = [
        (5.0, 7.8, "Register / Login"),
        (5.0, 6.7, "Assess lecturer\n(8 qualities)"),
        (5.0, 5.5, "View overview\ncharts"),
        (5.0, 4.3, "View lecturer\nprofile"),
        (5.0, 3.1, "View own\nfeedback dashboard"),
        (5.0, 1.8, "Manage users,\ncourses, lecturers"),
    ]
    for x, y, label in usecases:
        e = Ellipse((x, y), 2.8, 0.95, fill=True, facecolor=WHITE, edgecolor=GREEN, lw=1.5)
        ax.add_patch(e)
        ax.text(x, y, label, ha="center", va="center", fontsize=8)

    # Associations
    links = [
        ((1.15, 7.4), (3.6, 7.8)),
        ((1.15, 7.2), (3.6, 6.7)),
        ((1.15, 7.0), (3.6, 5.5)),
        ((1.15, 6.9), (3.6, 4.3)),
        ((1.15, 4.2), (3.6, 7.8)),
        ((1.15, 4.0), (3.6, 3.1)),
        ((1.15, 3.9), (3.6, 5.5)),
        ((8.85, 5.7), (6.4, 7.8)),
        ((8.85, 5.5), (6.4, 1.8)),
        ((8.85, 5.3), (6.4, 5.5)),
    ]
    for a, b in links:
        ax.plot([a[0], b[0]], [a[1], b[1]], color=INK, lw=0.9)

    save(fig, "fig3_2_use_case.png")


# ---------- Figure 3.3 ER ----------
def fig_er():
    fig, ax = plt.subplots(figsize=(11, 7.2))
    ax.set_xlim(0, 11)
    ax.set_ylim(0, 8)
    ax.axis("off")
    ax.set_title(
        "Figure 3.3: Entity-Relationship Overview of the Database",
        fontsize=12, fontweight="bold", color=GREEN, pad=10,
    )

    def entity(x, y, w, h, title, fields):
        # header
        ax.add_patch(Rectangle((x, y + h - 0.45), w, 0.45, fc=GREEN, ec=GREEN, lw=1.4))
        ax.text(x + w / 2, y + h - 0.22, title, ha="center", va="center",
                color=WHITE, fontsize=9, fontweight="bold")
        ax.add_patch(Rectangle((x, y), w, h - 0.45, fc=WHITE, ec=GREEN, lw=1.4))
        ax.text(x + 0.12, y + h - 0.7, fields, ha="left", va="top", fontsize=7.2,
                family="monospace", color=INK)

    entity(0.3, 5.0, 2.9, 2.7, "USERS",
           "PK id\nfull_name, email\npassword, role\nmatric_number\nacademic_level\n(100–400 students)")
    entity(3.5, 5.2, 2.8, 2.4, "LECTURERS",
           "PK id\nFK user_id\ntitle, bio\noffice_location\nresearch_area")
    entity(6.8, 5.2, 2.9, 2.4, "COURSES",
           "PK id\ncourse_code\ncourse_title\nlevel, semester\nsession_year")
    entity(3.4, 0.25, 4.2, 3.15, "ASSESSMENTS",
           "PK id\nFK student_id → users\nFK lecturer_id → lecturers\nFK course_id → courses\nsemester, session_year\neight quality scores (1–5)\ncomment (optional)\nUNIQUE(student,lecturer,\n course,semester,session)")
    entity(0.3, 2.55, 2.8, 1.9, "COURSE_LECTURERS",
           "PK id\nFK course_id\nFK lecturer_id\nis_primary")
    entity(8.0, 2.55, 2.7, 2.0, "STUDENT_\nENROLLMENTS",
           "PK id\nFK student_id\nFK course_id\nUNIQUE(student,\n course)")
    entity(0.3, 0.25, 2.8, 1.7, "PASSWORD_\nRESETS",
           "PK id\nFK user_id\ntoken (unique)\nexpires_at\nused_at")
    entity(8.0, 0.25, 2.7, 1.7, "SYSTEM_\nFEEDBACK",
           "PK id\nFK user_id\nsubject\nmessage\ncreated_at")

    # relationship lines
    ax.annotate("", xy=(3.5, 6.3), xytext=(3.2, 6.3),
                arrowprops=dict(arrowstyle="-", color=GOLD, lw=1.8))
    ax.text(3.25, 6.5, "1:1", fontsize=7, color=GOLD)
    ax.annotate("", xy=(6.8, 6.1), xytext=(6.3, 6.1),
                arrowprops=dict(arrowstyle="-", color=GOLD, lw=1.8))
    ax.annotate("", xy=(5.0, 5.2), xytext=(5.0, 3.4),
                arrowprops=dict(arrowstyle="-", color=GREEN, lw=1.6))
    ax.annotate("", xy=(4.0, 3.4), xytext=(2.0, 5.0),
                arrowprops=dict(arrowstyle="-", color=GREEN, lw=1.2))
    ax.annotate("", xy=(7.0, 3.4), xytext=(8.0, 5.2),
                arrowprops=dict(arrowstyle="-", color=GREEN, lw=1.2))
    ax.annotate("", xy=(3.4, 2.0), xytext=(3.1, 3.2),
                arrowprops=dict(arrowstyle="-", color=GOLD, lw=1.5))
    ax.annotate("", xy=(8.0, 3.4), xytext=(8.5, 5.2),
                arrowprops=dict(arrowstyle="-", color=GOLD, lw=1.4))
    ax.annotate("", xy=(3.4, 4.8), xytext=(1.7, 5.0),
                arrowprops=dict(arrowstyle="-", color=GOLD, lw=1.2))
    ax.text(0.3, 0.15, "Students rate only lecturers of enrolled courses; charts use anonymous aggregates.",
            fontsize=8, color=INK)

    save(fig, "fig3_3_er_diagram.png")


# ---------- Figure 3.4 Architecture ----------
def fig_architecture():
    fig, ax = plt.subplots(figsize=(9.5, 6.5))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 8)
    ax.axis("off")
    ax.set_title(
        "Figure 3.4: Three-Tier System Architecture",
        fontsize=12, fontweight="bold", color=GREEN, pad=10,
    )

    tiers = [
        (5.8, LIGHT, GREEN, "PRESENTATION TIER\n(Client Browser)\nHTML5 · CSS3 · JavaScript · Chart.js\nStudent / Lecturer / Admin UI"),
        (3.4, BOX, GREEN, "APPLICATION TIER\n(PHP on Apache / XAMPP)\nAuth · Assessment · Aggregation · Dashboards\nSessions · CSRF · Prepared Statements"),
        (1.0, "#EAF2FF", "#1F4E79", "DATA TIER\n(MySQL)\nusers · enrolments · lecturers · courses · assessments"),
    ]
    for y, fc, ec, text in tiers:
        rounded(ax, (1.5, y), 7.0, 1.9, text, fontsize=9, fc=fc, ec=ec, bold=True)

    arrow(ax, (5.0, 5.8), (5.0, 5.3))
    arrow(ax, (5.0, 3.4), (5.0, 2.9))
    ax.text(5.2, 5.45, "HTTP requests / responses", fontsize=8, color=INK)
    ax.text(5.2, 3.05, "SQL queries / results", fontsize=8, color=INK)

    save(fig, "fig3_4_architecture.png")


# ---------- Figure 3.5 Assessment process flow ----------
def fig_process():
    fig, ax = plt.subplots(figsize=(8.5, 9.5))
    ax.set_xlim(0, 8)
    ax.set_ylim(0, 11)
    ax.axis("off")
    ax.set_title(
        "Figure 3.5: Student Assessment Process Flowchart",
        fontsize=12, fontweight="bold", color=GREEN, pad=8,
    )

    def proc(y, text):
        rounded(ax, (2.2, y), 3.6, 0.75, text, fontsize=8.5)

    def decision(y, text):
        # diamond approximated as rotated square via FancyBbox or polygon
        d = mpatches.FancyBboxPatch(
            (2.5, y), 3.0, 0.9,
            boxstyle="round,pad=0.02,rounding_size=0.35",
            linewidth=1.6, edgecolor=GOLD, facecolor="#FFF8E7",
        )
        ax.add_patch(d)
        ax.text(4.0, y + 0.45, text, ha="center", va="center", fontsize=8)

    proc(9.7, "Start: Student opens portal")
    arrow(ax, (4.0, 9.7), (4.0, 9.35))
    proc(8.55, "Login / Register (role = student)")
    arrow(ax, (4.0, 8.55), (4.0, 8.2))
    decision(7.15, "Authenticated?")
    ax.text(5.7, 7.55, "No", fontsize=8, color=INK)
    ax.annotate("", xy=(6.6, 7.6), xytext=(5.5, 7.6),
                arrowprops=dict(arrowstyle="->", color=INK, lw=1.3))
    ax.text(6.3, 8.7, "Show error /\nretry login", fontsize=7.5, ha="center",
            bbox=dict(boxstyle="round", fc=WHITE, ec=INK))
    arrow(ax, (4.0, 7.15), (4.0, 6.8))
    ax.text(4.15, 6.9, "Yes", fontsize=8)
    proc(6.0, "Enrol in courses taken → select\nlecturer–course from enrolments only")
    arrow(ax, (4.0, 6.0), (4.0, 5.65))
    proc(4.85, "Rate 8 qualities (Likert 1–5)\n+ optional comment")
    arrow(ax, (4.0, 4.85), (4.0, 4.5))
    decision(3.45, "Enrolled, valid &\nunique submission?")
    ax.text(5.7, 3.85, "No", fontsize=8)
    ax.annotate("", xy=(6.6, 3.9), xytext=(5.5, 3.9),
                arrowprops=dict(arrowstyle="->", color=INK, lw=1.3))
    ax.text(6.3, 5.0, "Reject if not enrolled,\nduplicate, or invalid", fontsize=7.5, ha="center",
            bbox=dict(boxstyle="round", fc=WHITE, ec=INK))
    arrow(ax, (4.0, 3.45), (4.0, 3.1))
    ax.text(4.15, 3.2, "Yes", fontsize=8)
    proc(2.3, "Store in MySQL assessments")
    arrow(ax, (4.0, 2.3), (4.0, 1.95))
    proc(1.15, "Recompute means → update\nChart.js overview / dashboards")
    arrow(ax, (4.0, 1.15), (4.0, 0.8))
    proc(0.15, "End: Student / next cohort\nviews updated overview")

    save(fig, "fig3_5_assessment_flowchart.png")


# ---------- Figure 3.6 As-Is vs To-Be ----------
def fig_asis_tobe():
    fig, ax = plt.subplots(figsize=(10, 5.5))
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 6)
    ax.axis("off")
    ax.set_title(
        "Figure 3.6: As-Is (Rumour) versus To-Be (Portal) Overview Channel",
        fontsize=12, fontweight="bold", color=GREEN, pad=10,
    )

    rounded(ax, (0.4, 1.0), 4.0, 4.2,
            "AS-IS SYSTEM\n\n"
            "Senior / hostel stories\n↓\nSelective narrative\n↓\nFear or complacency\n↓\nStyle mismatch &\nreputational reset",
            fontsize=9, fc="#F8E8E8", ec="#8B2E2E", bold=False)
    rounded(ax, (5.6, 1.0), 4.0, 4.2,
            "TO-BE SYSTEM\n\n"
            "Anonymous 8-quality ratings\n↓\nMySQL aggregation\n↓\nChart.js overview\n↓\nInformed adaptation &\nreputation by consistency",
            fontsize=9, fc=BOX, ec=GREEN, bold=False)
    arrow(ax, (4.5, 3.1), (5.5, 3.1))
    ax.text(5.0, 3.4, "Replace", ha="center", fontsize=8, color=GOLD, fontweight="bold")

    save(fig, "fig3_6_asis_tobe.png")


if __name__ == "__main__":
    fig_sdlc()
    fig_use_case()
    fig_er()
    fig_architecture()
    fig_process()
    fig_asis_tobe()
    print("Done. Files in", OUT)

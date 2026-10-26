# -*- coding: utf-8 -*-
import sys
from pathlib import Path
sys.stdout.reconfigure(encoding="utf-8")
from docx import Document

ROOT = Path(r"c:\Users\USER\Desktop\Final Year Project")
doc = Document(str(ROOT / "Eze_Chapters_1-3_UPDATED.docx"))

spaced = []
ticks = []
for i, p in enumerate(doc.paragraphs):
    t = p.text
    if " - " in t:
        spaced.append((i, t[:140]))
    if "`" in t:
        ticks.append((i, t[:140]))

print("Spaced hyphen count:", len(spaced))
for x in spaced[:15]:
    print(" ", x)
print("Backtick count:", len(ticks))
for x in ticks[:15]:
    print(" ", x)
print("OK" if not spaced and not ticks else "ISSUES REMAIN")

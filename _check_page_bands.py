# -*- coding: utf-8 -*-
import re, sys
from pathlib import Path
sys.stdout.reconfigure(encoding="utf-8")
ROOT = Path(r"c:\Users\USER\Desktop\Final Year Project")
# UNN CS: TNR 14 + 1.5 on A4 ≈ ~240 words/page
WPP = 245  # TNR 14 + 1.5 on A4, 1" margins

def wc(fn):
    t = (ROOT / fn).read_text(encoding="utf-8")
    t = re.sub(r"^>.*$", "", t, flags=re.M)
    t = re.sub(r"<!--.*?-->", "", t, flags=re.S)
    return len(re.findall(r"[A-Za-z0-9][\w'-]*", t))

rows = [
    ("Prelims", "00-preliminary-pages.md", 6, 8, True),
    ("Ch1", "chapter-1-introduction.md", 4, 6, False),
    ("Ch2", "chapter-2-literature-review.md", 8, 12, False),
    ("Ch3", "chapter-3-research-methodology.md", 8, 12, False),
]
print(f"Basis: TNR 14 + 1.5 spacing ≈ {WPP} words/page (A4)")
print("-" * 64)
total = 0
ok = True
for label, fn, lo, hi, prelim in rows:
    w = wc(fn)
    p = w / WPP
    if prelim:
        # structural page breaks dominate
        est, band, v = "~6", f"{lo}-{hi}", "WITHIN"
        print(f"{label:<8} {w:5d} words  {est:>8}  band {band:<6} {v}")
        total += 6.0
    else:
        v = "WITHIN" if lo <= p <= hi else ("LOW" if p < lo else "HIGH")
        if v != "WITHIN":
            ok = False
        print(f"{label:<8} {w:5d} words  {p:5.1f} pp  band {lo}-{hi:<2}  {v}")
        total += p
print("-" * 64)
print(f"Estimated prelims + Ch1-3: ~{total:.1f} pages  (max 30)  -> {'OK' if total <= 30 and ok else 'ADJUST'}")
print(f"Overall status: {'COMPLIANT' if total <= 30 and ok else 'NEEDS TRIM'}")

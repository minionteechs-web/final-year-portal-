# -*- coding: utf-8 -*-
import sys, re
from pathlib import Path
sys.stdout.reconfigure(encoding="utf-8")
ROOT = Path(r"c:\Users\USER\Desktop\Final Year Project")
WPP = 245  # TNR 14, 1.5, A4

def words(fn):
    t = (ROOT / fn).read_text(encoding="utf-8")
    t = re.sub(r"^>.*$", "", t, flags=re.M)
    t = re.sub(r"!\[.*?\]\(.*?\)", "", t)
    return len(re.findall(r"[A-Za-z0-9][\w'-]*", t))

ch1 = words("chapter-1-introduction.md")
ch2 = words("chapter-2-literature-review.md")
ch3 = words("chapter-3-research-methodology.md")
figs = len(list((ROOT / "diagrams").glob("fig3_*.png")))

# Compact boxed figures (~0.33 page each at current Word widths)
p1 = ch1 / WPP
p2 = ch2 / WPP
p3 = ch3 / WPP + figs * 0.33
total = p1 + p2 + p3

print(f"Ch1: {ch1} words -> {p1:.1f} pp")
print(f"Ch2: {ch2} words -> {p2:.1f} pp")
print(f"Ch3: {ch3} words + {figs} figs -> {p3:.1f} pp")
print(f"TOTAL Ch1-3: {total:.1f} pages (limit 25) -> {'OK' if total <= 25 else 'OVER'}")

# Suggested TOC Arabic page starts
print("\nSuggested TOC Arabic starts:")
print(f"  Chapter One:   1")
print(f"  Chapter Two:   {1 + round(p1)}")
print(f"  Chapter Three: {1 + round(p1) + round(p2)}")
print(f"  References:    {1 + round(total)}")

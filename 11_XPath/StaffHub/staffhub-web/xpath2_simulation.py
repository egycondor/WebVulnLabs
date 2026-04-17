"""
XPath 2.0 simulation for StaffHub /api/lookup (blind injection lab).

libxml2/lxml only implements XPath 1.0. Students often probe for XPath 2 with
functions like lower-case(), matches(), and string-to-codepoints(). When the
raw lookup parameter mentions any of these function names, we preprocess the
*entire* XPath string by evaluating those calls in Python and inlining
constants, then hand the result to lxml as XPath 1.0-compatible syntax.

If a payload uses only XPath 1.0 (no simulated markers), we evaluate it
directly with lxml.

The "genuine XPath 1.0-only" fingerprinting case in the brief: expressions
that rely exclusively on XPath 1.0 features are evaluated by lxml only; they
do not trigger this preprocessor unless one of the XPath-2-style tokens is
present (case-sensitive substring checks per brief: lower-case, ...).
"""

from __future__ import annotations

import re
from typing import Match

# Markers that opt into XPath 2.0-style preprocessing before lxml evaluation.
XPATH2_MARKERS = ("lower-case(", "string-to-codepoints(", "matches(")


def _needs_xpath2_simulation(xpath: str) -> bool:
    return any(m in xpath for m in XPATH2_MARKERS)


def _replace_lower_case(xpath: str) -> str:
    pattern = re.compile(r"lower-case\s*\(\s*'([^']*)'\s*\)")

    def repl(m: Match[str]) -> str:
        return "'" + m.group(1).lower() + "'"

    prev = None
    out = xpath
    while prev != out:
        prev = out
        out = pattern.sub(repl, out)
    return out


def _replace_matches(xpath: str) -> str:
    """
    XPath 2 matches($s, $pattern) using Python `re` (XPath regex is not 1:1;
    sufficient for lab payloads with simple patterns).
    """
    pattern = re.compile(
        r"matches\s*\(\s*'((?:[^'\\]|\\.)*)'\s*,\s*'((?:[^'\\]|\\.)*)'\s*\)"
    )

    def repl(m: Match[str]) -> str:
        text = m.group(1)
        pat = m.group(2)
        try:
            ok = bool(re.search(pat, text))
        except re.error:
            ok = False
        return "true()" if ok else "false()"

    prev = None
    out = xpath
    while prev != out:
        prev = out
        out = pattern.sub(repl, out)
    return out


def _replace_string_to_codepoints(xpath: str) -> str:
    """
    Inline string-to-codepoints('..')[n] as a numeric literal for XPath 1.0.
    XPath uses 1-based indexing in predicates like ...[1].
    """
    pattern = re.compile(
        r"string-to-codepoints\s*\(\s*'([^']*)'\s*\)\s*\[\s*(\d+)\s*\]"
    )

    def repl(m: Match[str]) -> str:
        s = m.group(1)
        idx = int(m.group(2))
        codes = [ord(ch) for ch in s]
        if idx < 1 or idx > len(codes):
            return "0"  # out-of-range coerces comparisons to fail safely
        return str(codes[idx - 1])

    prev = None
    out = xpath
    while prev != out:
        prev = out
        out = pattern.sub(repl, out)
    return out


def preprocess_for_xpath2(xpath: str) -> str:
    """
    Apply innermost-style replacements until stable. Order: codepoints (may
    appear inside other constructs), matches, lower-case.
    """
    out = xpath
    out = _replace_string_to_codepoints(out)
    out = _replace_matches(out)
    out = _replace_lower_case(out)
    # Second pass for nested combinations
    out = _replace_string_to_codepoints(out)
    out = _replace_matches(out)
    out = _replace_lower_case(out)
    return out

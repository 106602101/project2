#!/usr/bin/env python3
"""Convert the FOUR original FABWEB HTML pages to PHP without losing page content.
Run in the existing GitHub project folder after copying this kit into that folder.
Only Python standard library is required.
"""
import html
import re
from pathlib import Path

BASE = Path(__file__).resolve().parent
FILES = ('index', 'jobs', 'apply', 'about')

def extract(pattern, text, name):
    m = re.search(pattern, text, flags=re.IGNORECASE | re.DOTALL)
    if not m:
        raise ValueError(f'{name} not found: original HTML markup may have changed')
    return m.group(1)

for page in FILES:
    original = BASE / (page + '.html')
    if not original.exists():
        print(f'SKIP {original.name}: copy the original GitHub HTML files here first')
        continue
    source = original.read_text(encoding='utf-8-sig')
    page_title = html.unescape(extract(r'<title[^>]*>(.*?)</title>', source, 'title')).strip()
    desc_match = re.search(r'<meta\s+name=["\']description["\']\s+content=["\'](.*?)["\']', source, flags=re.I | re.S)
    desc = html.unescape(desc_match.group(1)) if desc_match else 'FABWEB Cybersecurity Services.'
    style_blocks = re.findall(r'<style[^>]*>(.*?)</style>', source, flags=re.I | re.S)
    main_html = extract(r'(<main\b[^>]*>.*?</main\s*>)', source, 'main content')
    # Replace relative links to other pages; external links unaffected.
    main_html = re.sub(r'(?i)(?<=")((?:index|jobs|apply|about))\.html(?=(?:[#?][^"\']*)?")', r'\1.php', main_html)
    main_html = re.sub(r"(?i)(?<=')((?:index|jobs|apply|about))\.html(?=(?:[#?][^'\"]*)?')", r'\1.php', main_html)
    # PHP single quoted literal escaping.
    php = lambda s: "'" + s.replace('\\', '\\\\').replace("'", "\\'") + "'"
    prefix = ('<?php\n'
              + '$pageTitle = ' + php(page_title) + ';\n'
              + '$pageDescription = ' + php(desc) + ';\n'
              + '$pageStyles = ' + php('\n'.join(style_blocks)) + ';\n'
              + "require __DIR__ . '/includes/header.inc';\n?>\n")
    end = "\n<?php require __DIR__ . '/includes/footer.inc'; ?>\n"
    output = BASE / (page + '.php')
    output.write_text(prefix + main_html + end, encoding='utf-8')
    print(f'CREATED {output.name}: original <main> retained')
print('Done. Test at http://localhost/project2/index.php')

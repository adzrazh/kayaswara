# CV. Kayaswara Publishing - PRD

## Problem Statement
Website jasa penerbitan buku akademik untuk CV. Kayaswara, Indonesia.

## Brand
- **Company**: CV. Kayaswara, Indonesia
- **Address**: Jln. Sunan Kalijaga Timur 10, Kec. Larangan, Kota Tangerang, Banten
- **Phone**: 081213-169703
- **Email**: kayaswara.jurnal@gmail.com
- **Tracking Code**: KYSWR-DDMMYYYY-NNN

## Design System — Academic Press
| Token | Value |
|-------|-------|
| Primary | #1A3C5E (Academic Blue) |
| Accent | #B8860B (Academic Gold) |
| Background | #F7F5F0 (Warm Parchment) |
| Fonts | Cormorant Garamond + IBM Plex Sans |
| Radius | 0px (sharp corners) |

## What's Implemented
- [x] Full content transformation (OJS → Publishing)
- [x] CSS rewrite — Academic Blue + Gold palette, 100% consistent
- [x] Brand: CV. Kayaswara (0 SNADA refs remaining)
- [x] **File upload** on consultation form (drag & drop, 20MB max, PDF/DOC/DOCX/ODT/RTF/ZIP/RAR)
- [x] Admin can download uploaded manuscript files
- [x] DB schema updated with attachment_file + attachment_name columns
- [x] Migration SQL provided (migration_file_upload.sql)
- [x] Upload directory with .htaccess security
- [x] Company details in invoice, footer, header, install defaults

## Backlog
- P1: Upload real portfolio images
- P2: Blog content

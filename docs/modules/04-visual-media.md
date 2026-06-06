# Module 04 — Visual & Media System

## Purpose
Carousel copy generation, media library management, and Canva integration.
No image rendering in v1. Brandara generates copy, user designs in Canva.

## Screens
- `/create/carousel` — carousel copy generator
- `/media` — media library (accessible from composer and standalone)

## Carousel copy generator
Input: topic, post type, target platform
Output:
- Hook slide (title + 1-line hook)
- 4–8 content slides (headline + 2–3 lines of copy + visual direction note)
- CTA slide (call to action + next step)

Structure options: Problem-solution / Step-by-step / Listicle / Before-after / Case study

LinkedIn carousels: 8–12 slides optimal
Instagram carousels: 5–8 slides optimal

Output formatted for copy-paste into Canva.

## Media library
- Upload images via drag-drop or file picker
- All images stored per brand in `storage/app/tenants/{id}/media/`
- Searchable by filename, tag, date, campaign
- Platform compliance check on upload:
  - Instagram: min 320px, max 8MB, JPG/PNG
  - LinkedIn: max 20MB
  - Twitter: max 5MB, JPG/PNG/GIF/WEBP

Image compression: Intervention Image package
- Compress on upload to reduce storage
- Resize to max 2048px on longest side

## Canva integration
1. User generates carousel copy in Brandara
2. Clicks "Design in Canva"
3. Brandara passes:
   - Carousel text (slide by slide)
   - Brand Kit colours
   - Brand Kit fonts
4. Canva opens with template pre-populated
5. User designs, saves
6. Canva sends finished image back via webhook
7. Image lands in Brandara Media Library
8. Attaches to the scheduled post automatically

## Quote and testimonial card copy
- Founder quote card: extracts most shareable line from a longer post
- Client testimonial: formats raw feedback into visual-ready copy
- Motivational graphic: short sharp quote for visual design
- Each output includes visual direction note (background mood, colour suggestion)

## Database tables
- `media_files` — all media metadata

## Storage limits by plan
- Starter: 1 GB
- Pro: 5 GB
- Agency: 20 GB

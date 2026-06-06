# Brandara — Complete Colour System

## Core brand colours

```
--color-brand-violet:    #7C3AED   /* Primary — buttons, logo, active nav, links */
--color-brand-violet-dark: #6D28D9 /* Hover and pressed states */
--color-brand-violet-light: #F5F3FF /* Tint — stat card bg, tags, selections */
--color-brand-gold:      #F59E0B   /* Accent — CTAs, campaigns, highlights */
--color-brand-gold-dark: #D97706   /* Gold hover state */
```

## Sidebar — same in BOTH light and dark mode

```css
background: linear-gradient(165deg, #1B0D35 0%, #0E0720 100%);
```

Logo mark gradient:
```css
background: linear-gradient(135deg, #7C3AED, #A78BFA);
```

Active nav item:
```css
background: linear-gradient(90deg, rgba(124,58,237,0.5), rgba(124,58,237,0.2));
```

Inactive nav text: `rgba(255,255,255,0.45)`

## Light mode (default — all page backgrounds WHITE)

```
Page background:      #FFFFFF    /* Pure white — all pages */
Surface / panels:     #FAFBFF    /* Cards, inputs, side panels */
Border:               #E2E8F0    /* Card borders, dividers */
Title text:           #0F172A    /* All headings */
Body text:            #64748B    /* Labels, descriptions, secondary text */
```

## Dark mode (user toggle)

```
Page background:      #0F0B1E    /* Deep purple-black */
Cards / panels:       #1A1035    /* Dark purple tinted */
Card border:          #2D1F5E    /* Subtle purple border */
Title text:           #F1F5F9    /* Near white */
Muted text:           #6B5E8A    /* Muted purple-grey */
```

## Stat card colours — one unique colour per metric

Each metric card on the dashboard gets its own colour family:

```
Posts published:    bg #F5F3FF  value #6D28D9  (violet)
Reach:              bg #EFF6FF  value #1D4ED8  (blue)
Warm leads:         bg #FFFBEB  value #B45309  (amber)
Engagement rate:    bg #FFF1F2  value #BE123C  (rose)
Campaigns active:   bg #F0FDFA  value #0F766E  (teal)
WhatsApp messages:  bg #FFF7ED  value #9A3412  (orange)
AI Presence score:  bg #F5F3FF  value #7C3AED  (violet)
```

Dark mode stat cards — same values, deeper backgrounds:
```
Posts:  bg #1A1035  value #A78BFA
Reach:  bg #0C1A35  value #60A5FA
Leads:  bg #1C1000  value #FCD34D
Rate:   bg #1F0A12  value #FB7185
```

## Functional colours — consistent meaning

```
Success / live / connected:    #10B981   /* Emerald green */
Error / failed / disconnected: #EF4444   /* Red */
Warning / expiring / pending:  #F59E0B   /* Amber */
Info / neutral:                #3B82F6   /* Blue */
```

## Content pillar colours — default set

Each pillar gets a unique colour for the calendar and charts:
```
Pillar 1 (Thought Leadership): #7C3AED violet
Pillar 2 (Client Wins):        #3B82F6 blue
Pillar 3 (Personal Story):     #F59E0B amber
Pillar 4 (Product/Offer):      #10B981 emerald
Pillar 5 (Industry Insights):  #F43F5E rose
```

## Platform colours — use for platform badges and icons

```
LinkedIn:   #0077B5
X/Twitter:  #000000
Facebook:   #1877F2
Instagram:  gradient(#F58529, #DD2A7B, #8134AF)
Threads:    #000000
WhatsApp:   #25D366
TikTok:     #010101 with #FE2C55 + #25F4EE accents
```

## Tailwind config — add to tailwind.config.js

```js
module.exports = {
  theme: {
    extend: {
      colors: {
        brand: {
          violet:    '#7C3AED',
          'violet-dark': '#6D28D9',
          'violet-light': '#F5F3FF',
          gold:      '#F59E0B',
          'gold-dark': '#D97706',
        },
        sidebar: {
          from:  '#1B0D35',
          to:    '#0E0720',
        }
      }
    }
  },
  plugins: [require('daisyui')],
  daisyui: {
    themes: [{
      brandara: {
        "primary": "#7C3AED",
        "primary-content": "#ffffff",
        "secondary": "#F59E0B",
        "secondary-content": "#ffffff",
        "accent": "#10B981",
        "neutral": "#0F172A",
        "base-100": "#ffffff",
        "base-200": "#FAFBFF",
        "base-300": "#E2E8F0",
        "base-content": "#0F172A",
        "info": "#3B82F6",
        "success": "#10B981",
        "warning": "#F59E0B",
        "error": "#EF4444",
      }
    }]
  }
}
```

## Sidebar CSS — copy exactly

```css
.sidebar {
  background: linear-gradient(165deg, #1B0D35 0%, #0E0720 100%);
  position: relative;
  overflow: hidden;
}

/* Ambient glow top */
.sidebar::before {
  content: '';
  position: absolute;
  top: -40px; left: -40px;
  width: 160px; height: 160px;
  background: radial-gradient(circle, rgba(124,58,237,0.35) 0%, transparent 70%);
  pointer-events: none;
}

/* Ambient glow bottom */
.sidebar::after {
  content: '';
  position: absolute;
  bottom: -20px; right: -30px;
  width: 120px; height: 120px;
  background: radial-gradient(circle, rgba(167,139,250,0.15) 0%, transparent 70%);
  pointer-events: none;
}

.nav-item-active {
  background: linear-gradient(90deg, rgba(124,58,237,0.5), rgba(124,58,237,0.2));
  border-radius: 8px;
}

.logo-mark {
  background: linear-gradient(135deg, #7C3AED, #A78BFA);
  border-radius: 8px;
}
```

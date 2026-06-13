# Design System Inspired by Bridestory

## 1. Visual Theme & Atmosphere

Bridestory's design system embodies elegance, warmth, and accessibility for wedding planning. The aesthetic combines a soft, approachable color palette with clean typography and generous whitespace, creating an inviting experience that celebrates life's most important moments. The design prioritizes clarity and discoverability, guiding users through vendor discovery with confidence. Romantic accents in blush pink are balanced by strong contrast through deep blacks and sophisticated grays, establishing a premium yet friendly brand presence. The system emphasizes imagery and storytelling through generous card layouts and subtle shadows that create depth without overwhelming the content.

**Key Characteristics**

- Warm, romantic aesthetic with accessible contrast
- Clean typography hierarchy using Figtree family across all scales
- Generous whitespace and breathing room between content blocks
- Soft shadow treatments for gentle elevation and depth
- Pink and black color combination signaling both elegance and trust
- High-quality imagery as primary content driver
- Minimal borders and boundaries, favoring whitespace separation

## 2. Color Palette & Roles

### Primary
- **Bridestory Blue** (`#0000EE`): Primary interactive element, links, call-to-action accents; establishes brand identity and drives user engagement across the platform
- **Bridestory Blush** (`#EBA1A1`): Primary action buttons, CTAs, badges, highlights; softens intensity while maintaining prominence and emotional resonance

### Accent Colors
- **Coral Red** (`#FE5D51`): Critical alerts, promotional badges, secondary emphasis; draws attention to time-sensitive information and special offers

### Interactive
- **Link Blue** (`#0000EE`): Navigation links, text-based CTAs, underlines
- **Button Blush** (`#EBA1A1`): Primary button backgrounds, form submissions, confirmations
- **Button Blush Border** (`#EBA1A1`): Button outlines, secondary button borders

### Neutral Scale
- **Black** (`#000000`): Primary text, headings, high-contrast elements; ensures legibility and establishes hierarchy
- **Dark Charcoal** (`#252525`): Secondary text, metadata, subtle emphasis
- **Medium Gray** (`#555555`): Body text, supporting copy, labels
- **Light Gray** (`#848484`): Tertiary text, disabled states, footnotes
- **Pale Gray** (`#AAAAAA`): Placeholder text, very subtle information
- **Very Light Gray** (`#EEEEEE`): Borders, dividers, subtle background accents
- **Lightest Gray** (`#D9D8D8`): Input borders, container separators, faint rules

### Surface & Borders
- **White** (`#FFFFFF`): Card backgrounds, modal overlays, primary surfaces; provides clean content containers
- **Off-White Backgrounds** (`#FAFAFA`): Input field backgrounds, subtle section differentiation
- **Border Gray** (`#D9D8D8`): Subtle borders, input frames, container edges
- **Divider Gray** (`#EEEEEE`): Section dividers, lightweight rules

## 3. Typography Rules

### Font Family
**Primary:** Figtree Bold, Figtree SemiBold, Figtree
**Fallback Stack:** `'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif`

### Hierarchy

| Role | Font | Size | Weight | Line Height | Letter Spacing | Notes |
|------|------|------|--------|-------------|----------------|-------|
| Display / H1 | Figtree Bold | 32px | 700 | 40px | 0px | Page titles, hero sections |
| Heading / H2 | Figtree Bold | 30px | 400 | 38px | 0px | Section headings, major titles |
| Subheading / H3 | Figtree SemiBold | 20px | 400 | 26px | 0px | Subsection titles, card titles |
| Label / H4 | Figtree | 11px | 400 | 13px | 0px | Captions, metadata, mini labels |
| Body | Figtree | 16px | 400 | 30px | 0px | Main content, descriptions |
| Button | Figtree SemiBold | 14px | 500 | 18px | 0px | Button text, action labels |
| Input | Figtree | 14px | 400 | normal | 0px | Form input text, placeholders |
| Link | Figtree | 16px | 400 | 16px | 0px | Navigation links, hypertext |
| Caption | Figtree | 13px | 400 | 16px | 0px | Navigation, secondary labels |

### Principles
- Use Figtree exclusively across all scales for cohesive, modern aesthetic
- Maintain generous line heights (1.5x–2x font size) for readability and breathing room
- Weight hierarchy prioritizes contrast: Bold for headings, SemiBold for emphasis, Regular for body
- All heading scales use `line-height > font-size` to ensure comfortable reading rhythm
- Button text consistently uses SemiBold at 14px to maintain clarity in compact spaces

## 4. Component Stylings

### Buttons

#### Primary Button
- **Background:** `#EBA1A1`
- **Text Color:** `#FFFFFF`
- **Font:** Figtree Bold, 14px, weight 400
- **Padding:** `0px 0px 0px 0px`
- **Border Radius:** `4px`
- **Border:** `1px solid #EBA1A1`
- **Height:** `32px`
- **Line Height:** `21px`
- **Hover:** Background `#D9898A`, text `#FFFFFF`
- **Active:** Background `#C97576`, text `#FFFFFF`
- **Disabled:** Background `#D9D8D8`, text `#AAAAAA`, border `1px solid #D9D8D8`

#### Secondary Button (Outlined)
- **Background:** `#FFFFFF`
- **Text Color:** `#EBA1A1`
- **Font:** Figtree Bold, 14px, weight 400
- **Padding:** `0px 0px 0px 0px`
- **Border Radius:** `4px`
- **Border:** `1px solid #EBA1A1`
- **Height:** `32px`
- **Line Height:** `21px`
- **Hover:** Background `#FFF5F5`, text `#D9898A`, border `1px solid #D9898A`
- **Active:** Background `#FFECEC`, text `#C97576`, border `1px solid #C97576`

#### Ghost Button (Text-Only)
- **Background:** transparent
- **Text Color:** `#AAAAAA`
- **Font:** Figtree SemiBold, 15px, weight 400
- **Padding:** `0px 0px 0px 0px`
- **Border Radius:** `0px`
- **Border:** `0px none`
- **Height:** `18px`
- **Line Height:** `18px`
- **Hover:** Text Color `#555555`
- **Active:** Text Color `#252525`

#### Large CTA Button
- **Background:** `#EBA1A1`
- **Text Color:** `#FFFFFF`
- **Font:** Figtree Bold, 20px, weight 500
- **Padding:** `12px 4px 12px 4px`
- **Border Radius:** `10px`
- **Border:** `1px solid #EBA1A1`
- **Height:** `50px`
- **Width:** `366px` (responsive, full-width on mobile)
- **Line Height:** normal
- **Hover:** Background `#D9898A`, text `#FFFFFF`

#### Full-Width Action Button
- **Background:** `#EBA1A1`
- **Text Color:** `#FFFFFF`
- **Font:** Figtree SemiBold, 14px, weight 500
- **Padding:** `1px 6px 1px 6px`
- **Border Radius:** `5px`
- **Border:** `3px solid #EBA1A1`
- **Height:** `32px`
- **Width:** `100%`
- **Line Height:** `18px`
- **Hover:** Background `#D9898A`, border `3px solid #D9898A`

### Cards & Containers

#### Vendor Card
- **Background:** `#FFFFFF`
- **Text Color:** `#000000`
- **Border Radius:** `8px`
- **Border:** `0px none`
- **Box Shadow:** `rgba(0, 0, 0, 0.12) 0px 2px 10px 0px`
- **Height:** `287px`
- **Width:** `305px`
- **Padding:** `0px` (image-driven; padding applied to inner text sections)
- **Hover:** Box Shadow `rgba(0, 0, 0, 0.2) 0px 8px 24px 0px`, slight scale lift
- **Image Radius:** `8px 8px 0px 0px` (top corners only)

#### Standard Card (Elevated)
- **Background:** `#FFFFFF`
- **Text Color:** `#000000`
- **Border Radius:** `8px`
- **Border:** `0px none`
- **Box Shadow:** `rgba(0, 0, 0, 0.12) 0px 2px 10px 0px`
- **Padding:** `0px`

#### Modal/Overlay Card
- **Background:** `#FFFFFF`
- **Text Color:** `#000000`
- **Border Radius:** `16px`
- **Border:** `0px none`
- **Box Shadow:** `rgba(0, 0, 0, 0.3) 0px 8px 24px 0px`
- **Padding:** `0px` (child elements define internal spacing)

### Inputs & Forms

#### Text Input
- **Background:** `#FAFAFA`
- **Text Color:** `#000000`
- **Font:** Figtree, 14px, weight 400
- **Padding:** `0px 30px 0px 40px` (left icon space, right clearance)
- **Border Radius:** `5px`
- **Border:** `0px none` (appears borderless on light background)
- **Height:** `35px`
- **Line Height:** normal
- **Placeholder Color:** `#AAAAAA`
- **Focus:** Background `#FFFFFF`, outline `2px solid #0000EE`
- **Error:** Background `#FFF5F5`, border `1px solid #FE5D51`
- **Disabled:** Background `#EEEEEE`, text `#AAAAAA`

#### Search Input
- **Background:** `#FAFAFA`
- **Text Color:** `#000000`
- **Font:** Figtree, 14px, weight 400
- **Padding:** `0px 30px 0px 40px`
- **Border Radius:** `5px`
- **Height:** `35px`
- **Width:** `677.891px` (responsive, full-width on mobile)
- **Placeholder:** `Search`
- **Icon:** Search glass in `#AAAAAA`, positioned left at `12px`

### Navigation

#### Primary Navigation
- **Background:** transparent
- **Text Color:** `#000000`
- **Font:** Figtree, 13px, weight 400
- **Height:** `32px`
- **Padding:** `0px 16px` (between nav items)
- **Line Height:** `18px`
- **Active State:** Text `#0000EE`, underline `2px solid #0000EE`
- **Hover:** Text `#555555`

#### Navigation Item (with Badge)
- **Badge Background:** `#FE5D51`
- **Badge Color:** `#FFFFFF`
- **Badge Font:** Figtree SemiBold, 10px, weight 500
- **Badge Padding:** `2px 4px`
- **Badge Border Radius:** `0px 8px`
- **Positioned:** Top-right of label text

### Links

#### Text Link (Primary)
- **Background:** transparent
- **Text Color:** `#0000EE`
- **Font:** Figtree, 16px, weight 400
- **Height:** `40px`
- **Line Height:** `16px`
- **Hover:** Text Color `#0000EE`, underline `1px solid #0000EE`
- **Active:** Text Color `#0000EE`, underline `2px solid #0000EE`
- **Visited:** Text Color `#6B5B95`

#### Secondary Link (Subdued)
- **Background:** transparent
- **Text Color:** `#AAAAAA`
- **Font:** Figtree SemiBold, 15px, weight 400
- **Height:** `18px`
- **Line Height:** `18px`
- **Hover:** Text Color `#555555`

#### Link Button (Outlined)
- **Background:** `#FFFFFF`
- **Text Color:** `#EBA1A1`
- **Font:** Figtree Bold, 14px, weight 400
- **Height:** `32px`
- **Border Radius:** `4px`
- **Border:** `1px solid #EBA1A1`
- **Line Height:** `21px`
- **Hover:** Background `#FFF5F5`

### Badges

#### Category Badge
- **Background:** `#FFFFFF`
- **Text Color:** `#EBA1A1`
- **Font:** Figtree SemiBold, 12px, weight 500
- **Padding:** `6px 12px`
- **Border Radius:** `20px` (fully rounded)
- **Border:** `1px solid #EBA1A1`
- **Height:** `28px`

#### Status Badge (Alert)
- **Background:** `#FE5D51`
- **Text Color:** `#FFFFFF`
- **Font:** Figtree SemiBold, 10px, weight 500
- **Padding:** `2px 6px`
- **Border Radius:** `0px 8px`
- **Height:** `16px`

## 5. Layout Principles

### Spacing System

**Base Unit:** `4px`

**Scale:** `4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 72px, 80px, 104px`

**Usage Context:**
- `4px`: Micro-spacing within components (badge padding, icon margins)
- `8px`: Tight grouping (navigation item spacing, button icon gaps)
- `12px`: Component internal padding (cards, inputs)
- `16px`: Standard margin between sibling elements, navigation padding
- `20px`: Card internal content padding
- `24px`: Section internal padding, container edges
- `32px`: Medium section separation
- `40px`: Large internal padding, hero sections
- `72px`: Major section breaks (between "Venue in Singapore" and next section)
- `80px`: Top-level page sections
- `104px`: Maximum breathing room in feature sections

### Grid & Container

**Max Container Width:** `1200px` (desktop), `100%` minus `16px` margins (tablet/mobile)

**Column Strategy:** 
- Desktop: 4-column grid for vendor cards (at `305px` width each, with `16px` gutter)
- Tablet: 2-column grid
- Mobile: 1-column, full-width cards

**Section Pattern:**
- Hero banner: full-width, `80px` top margin, `40px` padding
- Content section: centered container, `80px` top margin, `24px` internal padding
- Card grid: 4 columns desktop, `16px` gutter between cards
- Footer: full-width, `24px` padding, background `#FAFAFA`

### Whitespace Philosophy

Bridestory prioritizes generous whitespace to create a premium, uncluttered aesthetic. Content breathes through strategic negative space rather than visual borders. Sections are separated vertically by multiples of the `16px` unit (`32px`, `80px`) to establish clear hierarchy and rhythm. Horizontal whitespace is equally important: card grids use consistent `16px` gaps, and text never spans the full viewport width. This approach reinforces elegance and ensures the marketplace feels curated rather than crowded.

### Border Radius Scale

- `4px`: Primary buttons, secondary buttons, input fields (sharp geometric precision)
- `5px`: Input fields, form elements (slight softness)
- `8px`: Cards, containers, elevated surfaces (subtle rounding for hierarchy)
- `8px 8px 0px 0px`: Card image tops (only top corners rounded; bottom aligns with card edges)
- `10px`: Large CTA buttons (more approachable, prominent call-to-action)
- `16px`: Modal overlays, drawers (maximum rounding for overlays separating from page)
- `20px`: Category badges, pill-shaped elements (fully rounded)

## 6. Depth & Elevation

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat (0) | No shadow, `box-shadow: none` | Navigation, body text, background sections, borders |
| Shallow (1) | `rgba(0, 0, 0, 0.12) 0px 2px 10px 0px` | Cards, vendor listings, subtle lift |
| Moderate (2) | `rgba(0, 0, 0, 0.3) 0px 8px 24px 0px` | Modal overlays, event popups, strong emphasis |
| Dropdown (3) | `rgba(37, 37, 37, 0.05) 0px 6px 12px 0px` | Dropdown menus, tooltips, light lift |
| Dropdown Alt (4) | `rgba(0, 0, 0, 0.1) 0px 8px 16px 0px` | Alternative dropdown treatment, deeper shadow |

**Shadow Philosophy:** Bridestory employs restrained shadow usage to suggest elevation without visual heaviness. The smallest shadow (Level 1) creates perceptible card separation from the page background; larger shadows are reserved for modals and overlays that must command attention. Shadows use black with low opacity (`0.05`–`0.3`) to maintain softness and elegance. On hover, cards lift slightly by increasing shadow intensity, signaling interactivity without jarring motion.

## 7. Do's and Don'ts

### Do
- Use `#EBA1A1` (Bridestory Blush) for all primary call-to-action buttons and interactive promotions
- Maintain minimum `16px` margin between major content sections to preserve whitespace hierarchy
- Apply `8px` or larger border radius to all card components for a modern, approachable aesthetic
- Write button text in Figtree SemiBold or Bold at `14px` or `20px` for clarity and prominence
- Stack heading sizes in strict order: H1 (32px) > H2 (30px) > H3 (20px) > H4 (11px)
- Use `#0000EE` (Bridestory Blue) exclusively for hyperlinks and primary text interactivity
- Provide high-contrast shadows (`0px 2px 10px`) on vendor cards to create visual separation
- Center container content with max-width `1200px` and symmetric side margins
- Pair imagery with minimal text overlay; let photos drive the story
- Use `#AAAAAA` placeholder text in inputs; ensure focus state includes `#0000EE` outline

### Don't
- Don't use `#FE5D51` (Coral Red) for primary buttons; reserve it for alerts and time-sensitive promotions
- Don't apply border-radius smaller than `4px` to interactive components; maintain minimum softness
- Don't exceed `80px` line-height on body text; preserve readability rhythm
- Don't mix Figtree with other typefaces; maintain typographic cohesion across all scales
- Don't apply shadows larger than Level 2 to non-modal components; preserve subtle hierarchy
- Don't use `#FFFFFF` text on light backgrounds; ensure WCAG AA contrast (7:1 minimum)
- Don't set card widths below `305px` or above `400px` in 4-column grids; maintain visual consistency
- Don't remove padding from form inputs; preserve `40px` left and `30px` right for icon and clearance space
- Don't use full-width containers on desktop; always constrain to `1200px` max with margins
- Don't apply font-weight heavier than 700; maintain hierarchy distinction with Figtree weights (400, 500, 700)

## 8. Responsive Behavior

### Breakpoints

| Breakpoint | Width | Key Changes |
|------------|-------|-------------|
| Desktop | 1200px+ | 4-column card grid, full navigation, max-width container `1200px`, `16px` side margins |
| Tablet | 768px–1199px | 2-column card grid, collapsible navigation menu, max-width container `100% - 24px`, `12px` side margins |
| Mobile | < 768px | 1-column full-width cards, hamburger navigation menu, stacked layout, `16px` side margins, bottom nav tabs |

### Touch Targets

- **Minimum Button Size:** `44px` height × `44px` width (accessible touch target per WCAG 2.1)
- **Minimum Link Size:** `40px` height for text links (navigation, CTA)
- **Button Padding Adjustment:** Mobile buttons increase padding to `12px 20px` (from `0px 0px`) to meet 44px touch target
- **Input Height:** Maintain `35px` height on desktop, increase to `44px` on mobile for comfortable touch interaction
- **Icon Spacing:** Minimum `8px` gap between icon and label for finger navigation clarity
- **Navigation Item Spacing:** Minimum `12px` horizontal padding between nav items (desktop); mobile nav items full-width with `16px` height minimum

### Collapsing Strategy

- **Navigation:** On mobile (< 768px), collapse primary navigation into hamburger menu; show only logo and login/register buttons in header
- **Card Grid:** 4 columns (desktop) → 2 columns (tablet) → 1 column (mobile); maintain `16px` gutter at all sizes
- **Text:** Body text remains `16px` across all breakpoints; responsive scaling occurs at heading levels only (H1 `32px` → `24px` on mobile)
- **Buttons:** Primary buttons scale from `366px` width (desktop) to `100% - 32px` margin (mobile); height remains `50px` or expands to `44px` minimum
- **Modals:** Maximum modal width `90vw` on mobile (max `600px` on desktop); maintain `24px` internal padding at all sizes
- **Images:** Vendor card images scale from `305px` width to `100%` width on mobile; aspect ratio preserved via `object-fit: cover`
- **Spacing:** Reduce margins by `50%` on mobile (e.g., `80px` → `40px` section gap; `24px` → `12px` internal padding)

## 9. Agent Prompt Guide

### Quick Color Reference

- **Primary CTA:** Bridestory Blush (`#EBA1A1`)
- **Background:** White (`#FFFFFF`) for cards/containers; Off-White (`#FAFAFA`) for inputs/light sections
- **Heading Text:** Black (`#000000`)
- **Body Text:** Medium Gray (`#555555`)
- **Links:** Bridestory Blue (`#0000EE`)
- **Placeholder/Disabled:** Pale Gray (`#AAAAAA`)
- **Alerts/Critical:** Coral Red (`#FE5D51`)
- **Borders/Dividers:** Light Gray (`#D9D8D8`) or Very Light Gray (`#EEEEEE`)
- **Shadows:** `rgba(0, 0, 0, 0.12)` (cards), `rgba(0, 0, 0, 0.3)` (modals)

### Iteration Guide

1. **Typography is Figtree-only:** All text uses Figtree Bold, SemiBold, or Regular; fallback to system sans-serif. Respect the hierarchy: 32px display → 30px headings → 20px subheadings → 16px body → 14px buttons/inputs.

2. **Button color defaults to Blush (#EBA1A1):** Primary buttons are always Blush with White text. Secondary buttons are White with Blush borders. Ghost buttons are transparent text-only. No other primary button colors exist.

3. **Cards have `8px` radius and light shadow:** All vendor cards, product cards, and elevated containers use `border-radius: 8px` and `box-shadow: rgba(0, 0, 0, 0.12) 0px 2px 10px 0px`. Image tops round to `8px 8px 0px 0px` only.

4. **Spacing follows the 4px scale:** Use `4px, 8px, 12px, 16px, 20px, 24px, 32px, 40px, 72px, 80px, 104px` exclusively. Section breaks are `80px` or `72px`. Internal padding is `20px` or `24px`. Never invent spacing values outside this scale.

5. **Max-width container is 1200px desktop, responsive on mobile:** Center all content with `max-width: 1200px` and `margin: 0 auto`. Tablet/mobile use full-width minus `12px`–`24px` margins. No content spans full viewport width.

6. **Input fields are #FAFAFA background, 35px tall, with 40px left padding:** Search and form inputs use `background: #FAFAFA`, `height: 35px`, `padding: 0px 30px 0px 40px`, `border-radius: 5px`, and `border: 0px`. Focus state adds `outline: 2px solid #0000EE`.

7. **Links are always #0000EE blue:** All text hyperlinks use `color: #0000EE`. Hover adds underline. Never use other colors for links unless semantic status (alerts use `#FE5D51`).

8. **Grid layout is 4 columns desktop, 2 tablet, 1 mobile:** Vendor cards are fixed `305px` width on desktop with `16px` gutter. Tablet uses 2 columns, mobile stacks 1 full-width column with responsive margins.

9. **Shadows increase on interactive states:** Buttons and cards lift on hover by increasing shadow depth (Level 1 → Level 2) or scaling slightly. Modals always use `rgba(0, 0, 0, 0.3) 0px 8px 24px 0px` shadow (deepest).

10. **Badges use Blush border with Blush text, White background:** Category badges are `background: #FFFFFF`, `color: #EBA1A1`, `border: 1px solid #EBA1A1`, `border-radius: 20px`, `padding: 6px 12px`. Alert badges are `background: #FE5D51`, `color: #FFFFFF`, `border-radius: 0px 8px`.
# Design System Inspired by Indonetwork

## 1. Visual Theme & Atmosphere

Indonetwork's design system embodies a professional B2B marketplace aesthetic grounded in trust, accessibility, and clarity. The visual language combines a bold, confident primary blue with energetic orange accents, creating a dynamic yet corporate atmosphere that appeals to business decision-makers across Indonesia. The interface prioritizes functional simplicity with generous whitespace, straightforward typography, and purposeful color usage that guides users through product discovery and vendor connections. The design reflects an approachable, industrious character—conveying reliability through clean layouts while maintaining visual interest through strategic color contrast and structured component hierarchies. This is a marketplace designed for serious business conversations, yet accessible enough for first-time B2B explorers.

**Key Characteristics**
- Bold primary blue (`#0D6EFD`) paired with vibrant orange (`#FC5000`) for maximum visual hierarchy
- Dark neutral base (`#212529`) for primary content and text, ensuring readability and professionalism
- Generous use of white (`#FFFFFF`) and light gray (`#F8F9FA`) for breathing room and content separation
- Rounded button treatments (`50px` radius) for approachable, modern interaction points
- Clean, icon-driven sidebar navigation with categorical organization
- Dropdown shadows and elevation for clear depth perception
- Status colors (`#198754` green, `#DC3545` red, `#FFC107` amber) for immediate feedback
- Consistent 6px and 8px border radius for contained form elements

## 2. Color Palette & Roles

### Primary
- **Primary Blue** (`#0D6EFD`): Main interactive elements, links, and focus states; establishes trust and professionalism in B2B context
- **Primary Orange** (`#FC5000`): High-energy CTAs and search buttons; drives immediate attention and action

### Accent Colors
- **Cyan** (`#0DCAF0`): Secondary interactive states and hover effects
- **Deep Blue** (`#2266CC`): Alternative primary for links and secondary buttons; adds depth to hierarchy

### Interactive
- **Button Blue** (`#4E85D6`): Standard button background for primary interactions; softer than primary blue for regular-weight CTAs
- **Orange CTA** (`#FC5000`): Search and dominant action buttons; high contrast for critical conversions

### Neutral Scale
- **Dark Text** (`#212529`): Primary body copy and headings; highest contrast for readability
- **Text Black** (`#000000`): Deep shadows and highest-contrast text (most used, 580 instances)
- **Medium Gray** (`#6C757D`): Secondary text, disabled states, and less emphatic content
- **Light Gray** (`#495057`): Subtle text and borders
- **Lighter Gray** (`#ADB5BD`): Inactive UI elements and very subtle borders
- **White** (`#FFFFFF`): Primary background and card surfaces
- **Off-White** (`#F8F9FA`): Subtle background differentiation for sections and hoverable areas
- **Light Border** (`#E9ECEF`): Dividing lines between content blocks
- **Mid Border** (`#DEE2E6`): Standard borders on inputs and card edges

### Surface & Borders
- **Border Gray** (`#DEE2E6`): Primary border color for inputs, cards, and container edges
- **Light Border** (`#DDDDDD`): Subtle dividers and secondary borders
- **Gray Divider** (`#C6C7C8`): Tertiary dividers used sparingly

### Semantic / Status
- **Success Green** (`#198754`): Confirmation messages, successful operations, and approval states
- **Error Red** (`#DC3545`): Error messages, deletions, and critical warnings
- **Warning Amber** (`#FFC107`): Alert states, caution messages, and non-critical warnings

## 3. Typography Rules

### Font Family
**Primary Font:** InFont (sans-serif), fallback stack: `InFont, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif`

**Secondary Font:** InFont (same family; variations through weight and size only)

### Hierarchy

| Role | Font | Size | Weight | Line Height | Letter Spacing | Notes |
|------|------|------|--------|-------------|----------------|-------|
| Display / H1 | InFont | 40px | 500 | 48px | 0px | Large page titles and hero headlines |
| Heading 2 | InFont | 20px | 700 | 20px | 0px | Section subheadings and card titles |
| Heading 3 | InFont | 28px | 500 | 42px | 0px | Prominent content headers |
| Heading 4 | InFont | 14px | 400 | 16.8px | 0px | Small section labels and component headings |
| Heading 5 | InFont | 20px | 500 | 30px | 0px | Product names and featured content titles |
| Body / Paragraph | InFont | 12px | 400 | 18px | 0px | Primary reading copy, product descriptions |
| Span / Label | InFont | 13px | 400 | 19.5px | 0px | Secondary labels, captions, sidebar items |
| Input / Form | InFont | 16px | 400 | 24px | 0px | User input in text fields and search boxes |

### Principles
- Weight is reserved for hierarchy emphasis; only H2 uses `700` weight, others rely on size and color contrast
- Generous line height ensures readability across compact marketplace layouts
- Size progression follows a clear scale: 12px → 13px → 14px → 16px → 20px → 28px → 40px
- Input text is notably larger (`16px`) to ensure accessibility on touch devices
- All typography uses the same font family to maintain cohesion; distinction achieved through weight, size, and color
- Body text (`12px`) is compact to maximize content density in product grids
- Line heights are consistently 1.5× or greater for comfortable reading

## 4. Component Stylings

### Buttons

#### Primary Button (Standard CTA)
- **Background:** `#4E85D6`
- **Text Color:** `#FFFFFF`
- **Font Size:** `13px`
- **Font Weight:** `400`
- **Padding:** `5px 15px`
- **Border Radius:** `50px`
- **Border:** `0px none`
- **Line Height:** `19.5px`
- **Hover State:** Darken background to `#3A5FA8`; maintain white text
- **Active State:** Further darken to `#2A4578`
- **Disabled State:** Reduce opacity to `0.6`; change cursor to `not-allowed`

#### Secondary Button (Large, Outlined)
- **Background:** `transparent`
- **Text Color:** `#FFFFFF`
- **Font Size:** `16px`
- **Font Weight:** `400`
- **Padding:** `6px 12px`
- **Border Radius:** `6px`
- **Border:** `1px solid transparent`
- **Line Height:** `24px`
- **Height:** `auto`
- **Hover State:** Add light background overlay (`rgba(255, 255, 255, 0.1)`)
- **Focus State:** Add outline `2px solid #0D6EFD`

#### Icon Button (Small, Discrete)
- **Background:** `transparent`
- **Text Color:** `#212529`
- **Font Size:** `14px`
- **Font Weight:** `400`
- **Padding:** `8px`
- **Border Radius:** `6px`
- **Border:** `0px`
- **Line Height:** `21px`
- **Width/Height:** `14px` minimum (expand container for touch safety)
- **Hover State:** Add background `rgba(0, 0, 0, 0.08)`
- **Active State:** Add background `rgba(0, 0, 0, 0.15)`

#### Search Button (Rounded Primary CTA)
- **Background:** `#FC5000`
- **Text Color:** `#FFFFFF`
- **Font Size:** `16px`
- **Font Weight:** `400`
- **Padding:** `6px 14.4px`
- **Border Radius:** `50px`
- **Border:** `1px solid transparent`
- **Line Height:** `24px`
- **Height:** `40px`
- **Hover State:** Darken to `#E83D00`; add subtle shadow `0px 2px 8px rgba(252, 80, 0, 0.3)`
- **Active State:** Further darken to `#C83200`

#### Login Button (White Outlined)
- **Background:** `transparent`
- **Text Color:** `#FFFFFF`
- **Font Size:** `14px`
- **Font Weight:** `700`
- **Padding:** `1px 6px`
- **Border Radius:** `50px`
- **Border:** `1px solid #DDDDDD`
- **Line Height:** `35px`
- **Height:** `38px`
- **Width:** `120px`
- **Hover State:** Add background `rgba(255, 255, 255, 0.15)`; brighten border to `#FFFFFF`
- **Focus State:** Add outline `2px solid #FFFFFF`

### Cards & Containers

#### Product Card
- **Background:** `#FFFFFF`
- **Border:** `1px solid #DEE2E6`
- **Border Radius:** `8px`
- **Padding:** `16px`
- **Box Shadow:** `0px 1px 3px rgba(0, 0, 0, 0.08)`
- **Image Top Radius:** `8px 8px 0px 0px` (images flush-fit to top)
- **Hover State:** Add shadow `0px 4px 12px rgba(0, 0, 0, 0.12)`; lift with `transform: translateY(-2px)`
- **Image Aspect Ratio:** Maintain 4:3 or 16:9 for consistency

#### Section Header Card
- **Background:** `#F8F9FA`
- **Border:** `1px solid #E9ECEF`
- **Border Radius:** `6px`
- **Padding:** `20px`
- **Margin Bottom:** `24px`
- **Heading Color:** `#212529`
- **Text Color:** `#495057`

#### Supplier Info Card
- **Background:** `#FFFFFF`
- **Border:** `1px solid #DEE2E6`
- **Border Radius:** `6px`
- **Padding:** `12px 16px`
- **Font Size:** `12px`
- **Line Height:** `18px`
- **Icon Color:** `#FFC107` (supplier badge)
- **Hover State:** Add subtle border color shift to `#0D6EFD`

### Inputs & Forms

#### Text Input (Search Style)
- **Background:** `transparent`
- **Text Color:** `#212529`
- **Font Size:** `16px`
- **Font Weight:** `400`
- **Padding:** `6px 12px 6px 24px`
- **Border Radius:** `6px 0px 0px 6px` (left rounded when paired with button)
- **Border:** `0px` (remove default)
- **Line Height:** `24px`
- **Height:** `40px`
- **Placeholder Color:** `#ADB5BD`
- **Focus State:** Add outline `2px solid #0D6EFD`; add background `#FFFFFF`
- **Disabled State:** Background `#E9ECEF`; text color `#6C757D`; cursor `not-allowed`

#### Dropdown Select
- **Background:** `#FFFFFF`
- **Text Color:** `#212529`
- **Font Size:** `14px`
- **Padding:** `8px 12px`
- **Border:** `1px solid #DEE2E6`
- **Border Radius:** `6px`
- **Line Height:** `21px`
- **Box Shadow on Open:** `rgba(0, 0, 0, 0.25) 0px 5px 5px 0px, rgba(0, 0, 0, 0.2) 0px 4px 8px 0px`
- **Hover State:** Border color `#0D6EFD`
- **Focus State:** Outline `2px solid #0D6EFD`; remove default browser styling

#### Checkbox / Radio
- **Size:** `16px × 16px`
- **Border Radius:** `4px` (checkbox), `50%` (radio)
- **Unchecked Border:** `2px solid #DEE2E6`
- **Checked Background:** `#0D6EFD`
- **Checked Border:** `2px solid #0D6EFD`
- **Focus State:** Add outline `2px solid #0D6EFD` with `2px` offset

### Navigation

#### Sidebar Category Navigation
- **Background:** `#FFFFFF`
- **Border Right:** `1px solid #E9ECEF`
- **Item Padding:** `12px 16px`
- **Item Font Size:** `13px`
- **Item Color:** `#212529`
- **Item Icon Color:** `#6C757D`
- **Hover State:** Background `#F8F9FA`; icon color `#0D6EFD`
- **Active State:** Left border `4px solid #0D6EFD`; background `#F0F5FF`
- **Icon Size:** `16px × 16px`

#### Top Navigation Bar
- **Background:** `#0D6EFD`
- **Height:** `80px` (including logo and controls)
- **Padding:** `16px 24px`
- **Logo Color:** `#FFFFFF`
- **Logo Font Size:** `20px`
- **Font Weight:** `700`
- **Link Color:** `#FFFFFF`
- **Link Font Size:** `13px`
- **Link Padding:** `0px 8px`
- **Hover State:** Background `rgba(255, 255, 255, 0.15)`
- **Right Align:** Controls (LOGIN button, language selector, social icons)

#### Breadcrumb Navigation
- **Text Color:** `#6C757D`
- **Link Color:** `#0D6EFD`
- **Font Size:** `12px`
- **Separator:** `/` in `#ADB5BD`
- **Padding:** `8px 0px`
- **Active State (Last Item):** Color `#212529`; font weight `500`

### Badges & Tags

#### Supplier Badge
- **Background:** `#FFC107`
- **Text Color:** `#000000`
- **Font Size:** `12px`
- **Font Weight:** `600`
- **Padding:** `4px 8px`
- **Border Radius:** `4px`
- **Display:** Positioned top-left corner of product cards

#### Status Badges
- **Success:** Background `#198754`, text `#FFFFFF`
- **Error:** Background `#DC3545`, text `#FFFFFF`
- **Warning:** Background `#FFC107`, text `#000000`
- **Info:** Background `#0DCAF0`, text `#000000`
- **Padding:** `6px 12px`
- **Border Radius:** `50px`
- **Font Size:** `12px`
- **Font Weight:** `600`

### Links

#### Inline Link (Header Context)
- **Background:** `transparent`
- **Text Color:** `#FFFFFF`
- **Font Size:** `13px`
- **Font Weight:** `400`
- **Padding:** `0px 8px`
- **Line Height:** `19.5px`
- **Hover State:** Add underline `2px solid #FFFFFF`
- **Active State:** Color `#0DCAF0`

#### Social Icon Link
- **Background:** `#FFFFFF`
- **Border:** `0px`
- **Border Radius:** `50%`
- **Width/Height:** `16px`
- **Icon Color:** `#000000`
- **Padding:** `0px`
- **Hover State:** Background `#0DCAF0`; icon color `#FFFFFF`
- **Focus State:** Outline `2px solid #0D6EFD` with `2px` offset

## 5. Layout Principles

### Spacing System

**Base Unit:** `4px`

**Scale Progression:**
- `4px`: Tight component spacing, icon padding
- `8px`: Small padding, minor margins between inline elements
- `12px`: Input field padding, small card padding
- `16px`: Standard card padding, medium margins
- `20px`: Large padding, section spacing
- `24px`: Major section margins, component gaps
- `80px`: Page-level margin, hero section spacing

**Usage Contexts:**
- **Component Internal:** `8px` to `16px` (inputs, buttons, card interiors)
- **Between Components:** `16px` to `24px` (adjacent cards, section spacing)
- **Section Separation:** `24px` to `80px` (hero to content flow, major layout breaks)
- **Whitespace (Breathing Room):** `20px` minimum horizontal padding on mobile, `24px` on desktop

### Grid & Container

**Max Width:** `1200px` for main content; allows comfortable reading and scanning

**Column Strategy:** 
- **Desktop:** 6-column grid with `16px` gutters; supports 1, 2, 3, or 6 column layouts
- **Tablet:** 4-column grid with `12px` gutters
- **Mobile:** Single column with `12px` horizontal margin

**Container Patterns:**
- **Hero Section:** Full width background (`#0D6EFD`), centered max-width container inside
- **Content Sections:** Padded container with max-width constraint and centered alignment
- **Product Grid:** Responsive grid (6 → 3 → 2 → 1 columns); `16px` gap between items
- **Sidebar + Main:** Sidebar fixed at `280px` width; main content fluid with `24px` margin

### Whitespace Philosophy

Whitespace is employed strategically to reduce cognitive load and guide user attention. Card-based layouts provide visual breathing room between product listings. Generous padding (`20px` to `24px`) separates major content sections, creating clear hierarchy and reducing visual fatigue. Horizontal margins on smaller screens (`12px` minimum) prevent content from feeling cramped. Sections are vertically spaced with consistent `24px` to `80px` margins, establishing a rhythm that helps users anticipate content transitions. This approach transforms dense product directories into scannable, organized experiences.

### Border Radius Scale

- **`4px`:** Checkboxes, small UI elements, tight components
- **`6px`:** Input fields, buttons (non-rounded), form controls, small cards
- **`8px`:** Product cards, modal windows, larger containers, image corners (`8px 8px 0px 0px` top, `0px 8px 8px 0px` variants for directional use)
- **`50px`:** Fully rounded buttons (login, search, primary actions), circular avatars, pill-shaped elements

## 6. Depth & Elevation

| Level | Treatment | Use |
|-------|-----------|-----|
| Flat / No Shadow | `box-shadow: none` | Cards on neutral backgrounds, text, inline elements |
| Raised | `box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.08)` | Product cards, subtle lift, default card state |
| Elevated | `box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.12)` | Card hover state, floating panels, slight prominence |
| Floating / Dropdown | `box-shadow: rgba(0, 0, 0, 0.25) 0px 5px 5px 0px, rgba(0, 0, 0, 0.2) 0px 4px 8px 0px` | Open dropdowns, modals, popovers, maximum depth |

**Shadow Philosophy:**
Shadows are used sparingly to create depth without visual clutter. The base layer uses no shadow; interaction states (hover, focus, open) introduce subtle elevation to signal interactivity. Dropdowns and modals use a stronger shadow combination to clearly separate overlapping content from the background. All shadows use black (`rgba(0, 0, 0, ...)`) at varying opacity levels to maintain tonal consistency. Shadow blur and spread values increase together with elevation level, creating a natural distance perspective. This conservative approach preserves the clean, professional aesthetic while maintaining visual clarity in complex layouts.

## 7. Do's and Don'ts

### Do

- **Use `#0D6EFD` for primary interactive elements** — links, focus states, primary buttons in secondary contexts; it establishes trust and draws attention appropriately
- **Pair `#FC5000` with `#FFFFFF` text** — search buttons and high-priority CTAs; this combination has excellent contrast and stands out in product-dense layouts
- **Maintain `16px` minimum font size in inputs** — ensures readability and usability on touch devices; form friction is a conversion killer
- **Group related products in card grids with consistent `16px` gaps** — the rhythm aids visual scanning and reduces cognitive overload
- **Apply `50px` border radius to all primary action buttons** — creates recognizable, tappable targets that feel approachable and modern
- **Use the `13px` span size for labels, captions, and sidebar text** — maintains visual hierarchy without overwhelming smaller content areas
- **Employ `#F8F9FA` background for section headers and secondary cards** — subtle contrast helps segment content without harsh visual breaks
- **Combine dark text (`#212529`) with white backgrounds** — maximizes readability for body copy and product descriptions
- **Implement the full dropdown shadow for modals and overlays** — ensures floating content is unambiguously separated from background
- **Use `#198754` green only for success states, `#DC3545` red only for errors, `#FFC107` amber only for warnings** — consistent semantic color usage aids rapid user comprehension

### Don't

- **Do not use `#0D6EFD` and `#FC5000` together without sufficient spacing** — the contrast can create visual vibration and reduce legibility; separate them by whitespace or neutral colors
- **Do not reduce text below `12px` in body copy** — marketplace content is dense; smaller text increases eyestrain and bounce rates
- **Do not apply shadows to every card** — reserve elevation for interactive or highlighted states; overuse flattens hierarchy
- **Do not use fewer than `8px` of padding inside input fields** — compressed inputs feel cramped and reduce touch accuracy
- **Do not mix border radius values inconsistently** — stick to the defined scale (`4px`, `6px`, `8px`, `50px`) to maintain visual cohesion
- **Do not place text directly on product images without contrast overlays** — ensure `#FFFFFF` text has a dark semi-transparent backdrop if overlaid
- **Do not use `#6C757D` text on anything lighter than `#F8F9FA` background** — insufficient contrast fails WCAG AA accessibility standards
- **Do not apply `#FFC107` warning color to critical errors** — reserve red (`#DC3545`) for high-urgency states; yellow is for non-blocking alerts
- **Do not create buttons wider than the content they control** — maintain proportional visual weight; oversized buttons feel awkward in dense layouts
- **Do not forget to include `:focus` states with `2px solid #0D6EFD` outline** — keyboard navigation and accessibility compliance depend on visible focus indicators
- **Do not use all-caps text in body copy** — small caps or title case maintains readability; all-caps reduces scanning speed

## 8. Responsive Behavior

### Breakpoints

| Name | Width | Key Changes |
|------|-------|-------------|
| **Mobile** | `<= 576px` | Single column, `12px` horizontal margin, `16px` card gap, sidebar collapses to hamburger menu, input full width, h1 reduces to `32px` |
| **Tablet** | `576px – 992px` | 2-column product grid, sidebar collapses, `14px` body text, `16px` gap, dropdown menus shift to accordion on small tablets |
| **Desktop** | `>= 992px` | 3 to 6 column grid depending on section, sidebar visible at `280px`, max-width container `1200px`, full top navigation, `16px` gap |
| **Large Desktop** | `>= 1400px` | Up to 6 columns on premium product feeds, additional whitespace (`24px` margins), wider sidebar becomes option |

### Touch Targets

- **Minimum Size:** `44px × 44px` for all interactive elements (buttons, links, icon buttons)
- **Recommended Size:** `48px × 48px` for mobile-primary interactions
- **Spacing Between Targets:** Minimum `8px` gap to prevent accidental adjacent taps
- **Text Links in Mobile Context:** Wrap in `16px` padding or expand touch area with pseudo-elements; avoid naked text links smaller than `16px` on touch screens
- **Icon Buttons:** Increase internal padding on mobile from `8px` to `12px` to meet minimum target size
- **Form Inputs:** Maintain `40px` height across all breakpoints; do not reduce below this for touch safety

### Collapsing Strategy

**Header Navigation:**
- **Desktop (`>= 992px`):** Horizontal top bar with full link text, social icons visible, right-aligned LOGIN button, language selector dropdown
- **Tablet (`576px – 992px`):** Compress spacing, abbreviate or stack navigation items, move some links to hamburger menu
- **Mobile (`< 576px`):** Hamburger menu button (three-line icon, `40px × 40px` minimum), collapse all header links into slide-out drawer, search bar full width below header

**Sidebar Navigation:**
- **Desktop:** Fixed `280px` sidebar on left; all category items visible; active state highlighted
- **Tablet:** Sidebar collapses to icon-only toggle; clicking toggle opens slide-out drawer over content
- **Mobile:** Sidebar hidden by default; accessible via hamburger menu; drawer slides in from left, covers full width or `85vw`

**Product Grid:**
- **Desktop:** `6` columns (or `3` per row in featured sections) with `16px` gap
- **Tablet:** `2` to `3` columns with `12px` gap; maintain card sizing consistency
- **Mobile:** `1` column full-width (minus `12px` margins); increase card padding to `12px` to maintain readability

**Forms & Inputs:**
- **Desktop:** Side-by-side layout for label + input; dropdown adjacent to search
- **Tablet:** Stack if space constrained; maintain `16px` input height
- **Mobile:** Full-width inputs; stack all form elements vertically; search button below input if space insufficient

**Modals & Overlays:**
- **Desktop:** Centered modal, max-width `600px`, `20px` margins from viewport edges
- **Tablet:** Centered modal, max-width `90vw`, `12px` margins
- **Mobile:** Full-screen modal or bottom-sheet; if bottom-sheet, leave `12px` margin at top and sides; no close button if swiped to dismiss is implemented

**Image Aspect Ratios:**
- **Desktop:** `4:3` or `16:9` maintained; cards display full preview
- **Tablet:** `1:1` square crops acceptable to save space; crop intelligently to center subject
- **Mobile:** `1:1` or `16:9`; optimize for vertical scrolling; lazy-load images below fold

## 9. Agent Prompt Guide

### Quick Color Reference

- **Primary CTA:** Primary Blue (`#0D6EFD`) for links, focus states, secondary contexts
- **Primary Action Button:** Button Blue (`#4E85D6`) for standard CTAs; Search Orange (`#FC5000`) for dominant actions
- **Background:** White (`#FFFFFF`) for cards/surfaces; Off-White (`#F8F9FA`) for subtle section differentiation
- **Heading Text:** Dark Text (`#212529`) for all headings and primary copy
- **Secondary Text:** Medium Gray (`#6C757D`) for descriptions and less emphatic content
- **Borders:** Border Gray (`#DEE2E6`) for inputs, dividers, and card edges
- **Success:** Success Green (`#198754`)
- **Error:** Error Red (`#DC3545`)
- **Warning:** Warning Amber (`#FFC107`)
- **Neutral Dark:** Black (`#000000`) for maximum contrast elements (most frequently used)

### Iteration Guide

1. **Always use `#0D6EFD` for interactive primary focus states and links; use `#FC5000` for search/dominant CTAs only** — this establishes a clear, predictable interactive hierarchy across the B2B marketplace
2. **Maintain minimum `16px` font size for input fields and body copy on mobile; do not reduce `13px` for labels without testing contrast** — accessibility and usability depend on readable text
3. **Apply `50px` border radius exclusively to pill-shaped buttons (login, search, primary actions); use `6px` for all form inputs and secondary buttons** — this visual distinction signals different interaction types
4. **Ensure all card shadows follow the elevation scale: no shadow for base, `0px 1px 3px rgba(0, 0, 0, 0.08)` for default, `0px 4px 12px rgba(0, 0, 0, 0.12)` for hover, and the full dropdown shadow for overlays** — consistent shadows create perceived depth without visual chaos
5. **Use `#F8F9FA` background for secondary content areas and section headers; never use it for primary white-space backgrounds** — subtle differentiation aids content scanning without harsh contrast
6. **Implement `#FFFFFF` text on `#0D6EFD` backgrounds exclusively in navigation and CTAs; never mix white text with light gray backgrounds** — contrast ratio compliance is non-negotiable
7. **Grid spacing is `16px` on desktop, `12px` on tablet, responsive on mobile; maintain this rhythm across all product lists and section layouts** — consistent gaps aid visual rhythm and scanning
8. **Every interactive element must have a visible `:focus` state with `2px solid #0D6EFD` outline and `2px` offset** — keyboard navigation and screen readers depend on clear focus indicators
9. **Product card styling: white background, `1px solid #DEE2E6` border, `8px` radius, `16px` padding, image top-flush with `8px 8px 0px 0px` radius, hover adds `0px 4px 12px` shadow and `-2px` vertical lift** — this component defines the marketplace aesthetic
10. **Mobile-first breakpoint strategy: single-column at mobile, 2–3 columns at tablet, 3–6 columns at desktop; compress whitespace as viewport shrinks but maintain minimum `12px` padding and `44px` touch targets** — responsive hierarchy ensures usability across devices while maximizing content density on larger screens
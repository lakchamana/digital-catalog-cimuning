# Changelog

## [Unreleased]

### Added
- Initial Laravel project scaffold.
- Tailwind CSS theme tokens for Cimuning UMKM visual system.
- Livewire and Alpine.js setup target for interactive UI.
- Public homepage with responsive navbar, hero search, popular categories, featured UMKM cards, CTA, and footer.
- Initial public placeholder routes for UMKM, products, registration, about, and contact pages.
- Project documentation folder and handoff notes.
- Core database migrations for user roles, categories, UMKMs, products, product images, UMKM contacts, and UMKM social links.
- Eloquent models and relationships for the core directory data.
- Idempotent seed data for admin, UMKM owners, categories, verified UMKMs, contacts, social links, and products.
- Database-aware public homepage, UMKM listing, product listing, category listing, and UMKM detail page.
- Product card component for product/service discovery.
- Livewire UMKM search component with shareable query string filters.
- UMKM filters for keyword, category, RW, verified status, services, sorting, and pagination size.
- Mobile filter bottom sheet with Alpine.js and loading skeleton state for UMKM discovery.
- Service badges on UMKM cards.
- Cimuning logo asset as the main navbar/footer logo and browser tab icon.
- Livewire product search component with shareable query string filters.
- Product filters for keyword, category, UMKM, price availability, sorting, and pagination size.
- Mobile filter bottom sheet and loading skeleton state for product discovery.
- Laravel Filament 5 admin panel at `/admin` for back office management.
- Role-based Filament access for admin and UMKM owner users.
- Filament resources for category, UMKM, and product CRUD.
- Admin-only UMKM verification actions for verified and revision status.
- Public disk upload fields for UMKM logo, UMKM cover, and product images.
- Dashboard stats widget for verified UMKM, pending verification, active products, and active categories.
- Feature tests for Filament access and UMKM owner product scoping.
- Polished public UMKM detail page with visual hero, logo display, service badges, sticky contact panel, Maps section, and mobile sticky CTA.
- Public UMKM registration form at `/daftar-umkm` with validation, optional logo/cover uploads, and pending review status.
- Reusable unique slug helper for UMKM, category, and product records.
- Feature tests for public UMKM registration, unique slug generation, invalid uploads, and pending visibility protection.
- Database notifications table for dashboard notifications.
- Filament notification bell with 30-second polling in the `/admin` panel.
- Central UMKM verification workflow for verified, need revision, and rejected status changes.
- Admin notification when a public UMKM registration is submitted.
- Owner notification when an assigned UMKM is verified, marked need revision, or rejected.
- Admin dashboard widget for UMKM that need review.
- Owner dashboard widget for assigned UMKM status.
- Feature tests for dashboard notifications and UMKM verification status changes.
- Lead event tracking for public WhatsApp and Google Maps CTA clicks.
- Filament lead analytics widgets for contact click totals and recent lead activity.
- Feature tests for lead redirects, public visibility protection, product lead relation, and owner lead scoping.
- Account-first UMKM owner registration through Filament `/admin/register`.
- Product-led homepage layout with product/service discovery prioritized above UMKM profile sections.
- Owner dashboard empty state action for completing a UMKM profile.
- Feature tests for owner registration, owner UMKM creation defaults, and homepage verified product visibility.
- First-visit public onboarding tutorial with Alpine.js and localStorage.
- Feature tests for onboarding markup on public pages and exclusion from lead redirect routes.
- Search-centric public navbar with large product/service search, compact discovery nav, and secondary informational links.
- Homepage carousel jumbotron with Alpine.js controls, dots, and mobile horizontal snap behavior.
- Category icon component and `/kategori` public index page for browsing all active categories.
- Additional seed categories: Pendidikan, Kesehatan, Laundry, Elektronik, Agribisnis, Properti/Rumah, Event & Catering, and Anak & Bayi.
- Interactive public walkthrough with step-by-step actions and localStorage key `cimuning_walkthrough_seen_v1`.
- Feature tests for navbar search, homepage carousel, category shortcuts, category index visibility, and walkthrough markup.
- Public accessibility polish for skip links, visible focus rings, nav current state, dialog ARIA attributes, and Livewire result announcements.
- Feature tests for public accessibility landmarks, filter drawers, live regions, and route rendering.

### Changed
- Replaced default Laravel welcome route with Cimuning UMKM homepage.
- Homepage now reads categories and featured UMKMs from the database when tables are available, with static fallback for pre-migration development.
- `/umkm` and `/produk` now support simple database-backed keyword search when seeded data is available.
- `/umkm` listing now uses Livewire instead of a plain GET form/controller query.
- `/kategori/{slug}` now renders the Livewire UMKM listing with an initial category filter.
- App branding changed to Cimuning Digital Hub.
- Homepage, navbar, footer, metadata, and environment app name now use the new brand direction.
- `/produk` listing now uses Livewire instead of a plain GET form/controller query.
- Admin dashboard direction now uses Filament as the back office layer, while public pages remain Blade and Livewire.
- `/umkm/{slug}` now eager-loads product images and renders uploaded UMKM/product images when available.
- Product cards now show uploaded product images and a WhatsApp inquiry CTA when the UMKM has WhatsApp.
- `/daftar-umkm` now renders the Livewire registration form instead of the MVP placeholder page.
- Filament category, UMKM, and product forms now auto-fill slugs from names while keeping slugs editable.
- UMKM admin verification workflow now includes a reject action and deactivates rejected/revision records.
- Public UMKM registration now notifies admin users after a pending submission is created.
- Filament UMKM verification table actions now use a shared workflow service and notify owners when an owner account is assigned.
- Public WhatsApp and Maps CTA links now pass through a lightweight tracking redirect before opening the external target.
- `/daftar-umkm` now acts as an account-first owner onboarding landing page instead of a guest submission form.
- New owner-created UMKMs are forced to pending and inactive until admin verification.
- Owner access can fill and revise their own UMKM profile, but cannot activate public visibility.
- Public layout now renders a lightweight onboarding dialog for first-time visitors.
- Homepage discovery now starts with a carousel and category icons under the search-centric navbar, closer to OLX-style browsing while staying directory-only.
- Public first-visit onboarding now behaves as an interactive walkthrough instead of a static intro dialog.
- Homepage carousel now uses horizontal-only `scrollTo` movement and pauses auto-advance when the carousel is outside the viewport.
- Public filter panels now avoid duplicate form control IDs between desktop and mobile variants.
- Public mobile drawer, filter bottom sheets, and walkthrough now expose clearer dialog semantics for assistive technology.

### Fixed
- PHPUnit dev dependency is now installed successfully after PHP `zip` became available, so `php artisan test` can run.
- Homepage carousel no longer jumps the page back up while users are scrolling through catalog sections.
- Homepage carousel prev/next controls are now positioned as cleaner floating controls with safer spacing from slide edges.

### Notes
- MVP remains a directory/catalog platform. Payment, checkout, cart, and transaction flows are intentionally excluded.
- MySQL/XAMPP is now active and user confirmed `php artisan migrate --seed` was run successfully.
- Local PHP CLI has required extensions for Filament install: `intl`, `zip`, `fileinfo`, `mbstring`, `openssl`, and `pdo_mysql`.
- Google Maps on public detail pages uses public Maps links/embed without an API key.
- Public UMKM registrations do not create owner accounts automatically; admin can assign an owner later from Filament.
- Dashboard notifications are database-only for this MVP; email, WhatsApp, and realtime broadcast notifications are deferred.
- Lead tracking records anonymous contact intent only; checkout, cart, payment, shipping, and internal transaction flows remain excluded.
- Guest UMKM submission is intentionally replaced by account-first onboarding; the old Livewire guest form is no longer rendered publicly.
- First-visit tutorial is available on public pages only; custom dashboard onboarding remains deferred.
- `/kategori` is the index for all active categories, while `/kategori/{slug}` remains the category-filtered UMKM listing.

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

### Changed
- Replaced default Laravel welcome route with Cimuning UMKM homepage.
- Homepage now reads categories and featured UMKMs from the database when tables are available, with static fallback for pre-migration development.
- `/umkm` and `/produk` now support simple database-backed keyword search when seeded data is available.
- `/umkm` listing now uses Livewire instead of a plain GET form/controller query.
- `/kategori/{slug}` now renders the Livewire UMKM listing with an initial category filter.
- App branding changed to Cimuning Digital Hub.
- Homepage, navbar, footer, metadata, and environment app name now use the new brand direction.
- `/produk` listing now uses Livewire instead of a plain GET form/controller query.

### Fixed
-

### Notes
- MVP remains a directory/catalog platform. Payment, checkout, cart, and transaction flows are intentionally excluded.
- MySQL/XAMPP is now active and user confirmed `php artisan migrate --seed` was run successfully.

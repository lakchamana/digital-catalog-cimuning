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

### Fixed
- PHPUnit dev dependency is now installed successfully after PHP `zip` became available, so `php artisan test` can run.

### Notes
- MVP remains a directory/catalog platform. Payment, checkout, cart, and transaction flows are intentionally excluded.
- MySQL/XAMPP is now active and user confirmed `php artisan migrate --seed` was run successfully.
- Local PHP CLI has required extensions for Filament install: `intl`, `zip`, `fileinfo`, `mbstring`, `openssl`, and `pdo_mysql`.

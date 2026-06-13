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

### Changed
- Replaced default Laravel welcome route with Cimuning UMKM homepage.
- Homepage now reads categories and featured UMKMs from the database when tables are available, with static fallback for pre-migration development.
- `/umkm` and `/produk` now support simple database-backed keyword search when seeded data is available.

### Fixed
-

### Notes
- MVP remains a directory/catalog platform. Payment, checkout, cart, and transaction flows are intentionally excluded.
- MySQL migration was not run locally because the MySQL service on `127.0.0.1:3306` refused the connection. Migrations and seeders were verified with SQLite in-memory.

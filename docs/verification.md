# Verification record

Verified on November 21, 2026 using PHP 8.2.12, CodeIgniter 4.7.6, SQLite3, GD, intl and BCMath. The HTTP integration script uses a real local server, cookies, CSRF fields, real multipart uploads and persisted data. All 37 checks in `test-results.json` passed.

The checks cover authentication redirects, login/logout, all six main pages, product/customer/staff create and edit, invalid product rejection, exact decimal sale totals, stock deduction, overselling rejection, stock preservation on rejection, walk-in customers, duplicate sale rejection, image re-encoding/delivery, executable upload rejection, logical deletion, retained history, own-account protection, authenticated media, wrong passwords and missing CSRF tokens.

Headless Microsoft Edge rendered the sign-in, dashboard, products, sale and staff form at 1440 pixels wide. Dashboard and sale pages were also inspected at 390 pixels wide. No browser JavaScript errors or mobile document overflow were observed. The test records are removed from the prepared local database after verification.

MySQL/MariaDB, a Docker build, multiple application instances and public hosting were not exercised in this environment. The conditional stock update and transaction are implemented to protect inventory, but no parallel-load benchmark was performed. No claim is made that hosted deployment requirements are complete.

Additional checks verified staff avatar cropping to exactly 256 × 256 pixels, rejection of zero quantities, sale-total overflow and rollback of its stock change. A separate clean database was created from migrations; the catalog seeder, interactive first-account command, stored password hash and all three sales foreign keys were verified.

# Release and submission

The source is available in the public [clayinthesky/subaan-pos repository](https://github.com/clayinthesky/subaan-pos). Reviewers can read the source without signing in. A public application URL has not yet been deployed.

## Repository maintenance

Commit changes to the main branch and push them to the existing origin remote. The `.gitignore` excludes `.env`, vendor dependencies, runtime databases, uploads and sessions. Keep `composer.lock`, migrations and seeders committed. Keep staff credentials out of the repository.

## PHP hosting release

1. Use PHP 8.2 or newer with intl, mbstring, bcmath, gd, fileinfo, mysqli or sqlite3, and session extensions. Use GD with JPEG/PNG/WebP support.
2. Install the committed source and run `composer install --no-dev --prefer-dist --optimize-autoloader`.
3. Configure the web document root as `public/` and enable rewriting. Configure persistent storage for `writable/`; use a managed MySQL database when storage cannot remain on one persistent instance.
4. Set the actual HTTPS base URL, production environment, and actual database credentials. Never put secrets in a public repository.
5. Run `php spark migrate`. Create a private staff account with `php spark pos:staff` through the host's console. Run the optional catalog seeder only if demonstration products are wanted.
6. Check the public sign-in page, authenticated management pages, a valid image upload, a sale, and an overselling rejection. Verify the history and remaining stock, restart the service, and confirm data persists.
7. Submit the real GitHub repository link and the real HTTPS app link. Give the professor access and staff credentials through a private channel, not in a public README.

## Acceptance demonstration

Create a product with stock 5, create a customer, create a second staff account with an avatar, and sign in as that staff member. Record a sale of 2 units for the customer. Verify stock 3 and a history entry showing the correct staff, customer, product and total. Try selling 4 units; verify a clear rejection and stock still 3. Record a walk-in sale of 1 unit and verify stock 2. Demonstrate editing and deleting management records; show that past transactions remain readable. Finally sign out and visit a management URL to show the authentication redirect.

## Operations

Back up the database and `writable/uploads` together. For SQLite, take a backup using SQLite's backup API or stop writes before copying the database. Do not discard the persistent Docker volume during an update. For a MySQL release, use the provider's consistent database backup facility. Monitor `writable/logs`, maintain writable directory permissions for only the web-service account, and update framework dependencies with regression checks before release.

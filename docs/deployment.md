# Release and submission

The source package is ready for a PHP-capable host. A repository URL and public app URL have not been created in this session. The available GitHub, Render and other hosting connectors were disabled by the workspace administrator. Sites accepts static or Worker artifacts and cannot directly run this CodeIgniter/PHP project.

## GitHub publication

Create a repository named `subaan-pos` in an authorized GitHub account. Open a terminal in this project, initialize Git if necessary, add the source, and commit it. Use the exact remote URL returned by GitHub when connecting the local repository, then push the main branch. The `.gitignore` excludes `.env`, vendor dependencies, runtime database files, uploads and sessions. Commit `composer.lock` and the migration/seeder files. Keep account credentials out of the repository.

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

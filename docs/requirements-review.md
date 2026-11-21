# Assignment requirements review

This review compares the implementation with the supplied IT0049 Midterm Project document, The Complete Point-of-Sale System, for Oracle M. Subaan, AC32. The document requests an application and two submission links. It does not contain a separate written question set or a finals rubric.

## Required application features

| Requirement | Status | Implementation and evidence |
| --- | --- | --- |
| CodeIgniter routing and MVC structure | Implemented | Explicit routes in `app/Config/Routes.php`; controllers, models and views in their framework directories. |
| Products, customers, users and sales schema | Implemented | `app/Database/Migrations/2026-11-21-000001_CreatePos.php` preserves the required fields and all three sales foreign keys. |
| Product list, add, edit and delete or archive | Implemented | Products management with validation and logical archiving. |
| Product image upload and display preparation | Implemented | Validated uploads are re-encoded to JPEG and resized within 1000 x 1000 pixels. |
| Customer CRUD | Implemented | Customer list, creation, editing and logical deletion. Deleted customers are hidden from active operations while past sales retain their relationships. |
| Staff CRUD and hashed passwords | Implemented | Staff management, password hashing, optional password changes on edit and logical deletion. The currently signed-in account cannot delete itself. |
| Avatar upload and display preparation | Implemented | Validated avatars are re-encoded and cropped to 256 x 256 pixels. |
| Authentication for all management pages | Implemented | Session login and authentication filter; inactive staff sessions are rejected. |
| Select product and optional customer for a sale | Implemented | Record Sale form supports registered customers and walk-ins. |
| Quantity validation and stock deduction | Implemented | Conditional stock decrement and sale insertion are committed in one database transaction. |
| Clear rejection of overselling | Implemented | Insufficient-stock requests display an error and preserve stock. |
| Sales history with related names, quantity, total and date | Implemented | Joined, paginated sales history with immutable stored sale quantity and total. |
| Input validation and safe uploads | Implemented | Server validation, MIME/size/dimension checks, random filenames and storage outside the public document root. |
| README and database migration/seeder | Included | Setup, usage, architecture, deployment instructions, migration and optional product catalog seeder. |

The recorded HTTP integration run passed 37 checks. These cover management operations, authentication, CSRF rejection, image and avatar processing, overselling, duplicate submissions, total overflow and rollback. See [verification details](verification.md) and [individual check results](test-results.json). SQLite was tested locally; MySQL and Docker execution were not tested in that run.

## Submission links

| Required deliverable | Status |
| --- | --- |
| GitHub repository containing raw source and migration/seeder | Available at [clayinthesky/subaan-pos](https://github.com/clayinthesky/subaan-pos). |
| Hosted working application matching the source | Still required. The local `http://localhost:8080/` address is a development server and does not satisfy the hosted-link requirement. |

## Content review

The application contains working forms and database operations rather than unfinished implementation markers or dummy answer sections. The optional seeder creates six stationery products for demonstration; it does not fabricate customer identities or transaction history. Test fixtures are confined to the integration suite and were removed from the prepared local database after verification.

CodeIgniter configuration comments refer to framework nonce and code-generator placeholders. Those are framework mechanisms, not unfinished project content. The project does not require invented repository links, hosting links, credentials or personal information.

The supplied midterm's functional requirements are covered. Its deployment requirement remains open until a real hosted app is deployed and verified. A separate finals assignment would need its own requirements review.

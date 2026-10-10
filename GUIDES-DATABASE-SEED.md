# Guide catalogue is database-driven

The static editorial catalogue has been removed from `app/Controllers/GuideController.php`.
The public guide index, article pages, sitemap guide URLs and `llms.txt` guide list use published records from the `guides` table.

## Seed content
- `database/seeds/004_seed_guides.sql` inserts four complete articles for Kenyan readers.
- `database/seeds/README.md` documents how to apply the seed.
- Apply migrations first with `php bin/migrate.php`, then run the seed SQL against the configured database.

The seed is idempotent and does not overwrite existing rows with matching slugs. Review articles in Admin > Guides & Articles. The article body uses allow-listed semantic HTML (headings, paragraphs, lists, emphasis and links) and includes search-focused titles and descriptions without guaranteeing rankings.

If the database is unavailable, the controller logs the failure and returns no guide records rather than silently serving an obsolete hardcoded catalogue. Confirm database availability before deploying.

# Seed the four AlbaTech Solutions guides

Run this **after** applying migrations, including `008_guides_cms.sql`.

From the project root, use your MySQL client and the database name configured in `.env`:

```bash
mysql -u alumasinde -p albatechsolutions_dev < database/seeds/004_seed_guides.sql
```

Enter the database password when prompted. Do not put database credentials in source control or paste them into chat.

The SQL inserts four complete, published articles:
1. Website design costs in Kenya
2. Domain names, hosting, HTTPS and business email
3. Preparing a business website
4. Local online visibility for Kenyan small businesses

The seed uses `INSERT IGNORE`, so it is safe to run again: existing rows with the same unique slug are not overwritten. If you have already created a guide with one of these slugs in Admin, its content remains unchanged. Review the four seed articles in Admin > Guides & Articles before making edits or publishing any future seed content.

Guides are now database-driven. `GuideController::articles()` reads only rows with `status = 'published'`; there is no static article catalogue fallback. Draft and archived records are not displayed publicly.

# Database migrations

This WordPress project does not use custom database tables. Schema requirements
are handled by WordPress core plus the custom post types and taxonomies
registered in [mu-plugins/visitsaudiarab-core.php](../mu-plugins/visitsaudiarab-core.php).

Use this folder for future, versioned, non-destructive migrations only — for
example if you later add custom tables, new WordPress options, or data
conversion routines.

## Rules

1. Name migrations sequentially: `YYYY-MM-DD-description.sql` or `.php`.
2. Make migrations idempotent (safe to run more than once).
3. Always back up the database before applying a migration.
4. Never drop or truncate production data automatically.
5. Include rollback instructions in each migration file.

# Upgrading the demo from Sulu 2.6 to 3.0

This is the path this repository actually took, in the order the four merge requests took it.
The authoritative reference is `vendor/sulu/sulu/UPGRADE-3.x.md`; this file records the decisions
and the traps that file does not cover.

The biggest change is that content no longer lives in a PHPCR repository. Pages, articles and
snippets are Doctrine entities, which means an existing installation needs a real data migration,
not just a dependency bump.

## 1. Prepare, while still on 2.6

Everything here is valid on Sulu 2.6 and can be deployed before the upgrade.

- Move to the latest 2.6 releases of `sulu/sulu` and every Sulu bundle. The content migration
  reads a PHPCR repository that all 2.6 migrations have been applied to.
- Run `bin/adminconsole phpcr:migrations:migrate`, confirm `phpcr:migrations:status` shows nothing
  pending, then run `bin/adminconsole sulu:document:phpcr-cleanup`.
- Check that webspace, template and navigation context keys match `[a-z0-9_-]+` with at most 31
  characters. Sulu 3.0 enforces this strictly.
- Replace every `type: rest` route entry. Sulu 3.0 removes the RestRoutingBundle, and 2.6 already
  ships a `.yaml` variant of each routing resource that works without it.

The `up-to-sulu-26` rector set only ships in `sulu/sulu-rector` 2.0, which needs rector 2, which
needs phpstan 2. The whole static analysis stack therefore has to move before those rules can run.
PHPUnit has to reach 10 or newer as well, because that is what Sulu 3.0 test cases require.

## 2. Upgrade to Sulu 3.0

Use `--no-scripts` on every composer step: recipes and builds otherwise run against half migrated
config. Run `git checkout config/templates/articles/` afterwards, flex tends to delete it.

- The article, snippet, page, route and custom url bundles are part of the core package now and
  live under `Sulu\<Domain>\Infrastructure`. Their routing moved from `Resources/config/` to
  `config/`.
- `sulu/article-bundle`, `elasticsearch/elasticsearch` and `handcraftedinthealps/zendsearch` go.
  Search runs on CMS-IG SEAL; the Loupe adapter only needs `pdo_sqlite`, so no search server.
- Media storage moves to Flysystem 3. Point the local adapter at the directory the media already
  lives in, otherwise every existing image 404s.
- The whole PHPCR stack has to go in this step, not later: `doctrine/phpcr-bundle` needs the
  phpcr-odm that 3.0 no longer pulls in and cannot boot. The content is untouched, it stays in the
  `phpcr_*` tables until step 3 reads it.
- `SimpleContentType` and `Sulu\Component\Content\Types` are gone. A content type is now a
  property resolver returning resolvable references plus a resource loader that fetches them.

### Database schema

Generate the delta with `doctrine:migrations:diff` against a real 2.6 database rather than copying
SQL by hand, but review the result. Three corrections were needed here:

- Table drops must run after the foreign keys that reference them, or MySQL fails with error 3730.
- `me_media.type` has to be filled from `me_media_types` before that table is dropped, otherwise
  every media row silently loses its type.
- The new tables need an explicit `COLLATE utf8mb4_unicode_ci`. Without it they take the MySQL 8
  default and every join against a 2.6 table fails with "Illegal mix of collations".

Rename `ro_routes` to `ro_routes_old` first, and add a doctrine `schema_filter` so the `phpcr_*`
tables and `ro_routes_old` stay out of the diff while they still hold the content.

`doctrine:schema:update` must not be used on Sulu 3.0. A fresh database (CI, tests) is still built
from the mappings with `doctrine:schema:create`; the migrations describe the upgrade of an
existing database.

## 3. Migrate the content

```bash
composer require sulu/phpcr-migration-bundle
bin/adminconsole sulu:phpcr-migration:migrate --dry-run
bin/adminconsole sulu:phpcr-migration:migrate
bin/adminconsole cmsig:seal:reindex
bin/adminconsole sulu:reference:refresh
```

The DSN is `dbal://default?workspace=...` when PHPCR was stored in the database
(`PHPCR_BACKEND=doctrinedbal`), which also means one mysqldump carries everything and the
migration is easy to rehearse. The command is re-runnable and overwrites rather than duplicates.

The dry run reports one `snippet_area` error, `Warning: Undefined array key "uuid"`. It is a false
positive: in dry-run mode the repository returns a stub row that is truthy but has no `uuid`, so
the persister takes the "already exists" branch. The real run is clean.

Afterwards grant the new permission contexts to the roles: `sulu.article.articles`, the per group
article context, `sulu.snippet.snippets` and `sulu.webspaces.<webspace>.snippet-areas`.

Drop the legacy tables only once the migrated content has been verified, so a botched run can
still be retried against the original data.

## 4. Rewrite the fixtures

Document fixtures have no equivalent. Content is written through the message bus:
`CreatePageMessage`, `ModifyPageMessage` and `ApplyWorkflowTransitionPageMessage`, each dispatched
in an `Envelope` carrying an `EnableFlushStamp`, and the same for articles and snippets.

Two things that are not obvious:

- A smart content that used to list the children of the current page needs an explicit
  `dataSource` in 3.0. `CreatePageMessage` accepts a supplied `uuid`, so generate one with
  `Uuid::v7()` and use it for both the page and its own data source.
- `sulu:page:initialize` leaves a second draft dimension content per locale behind that has no
  route attached. A later `ModifyPageMessage` carrying a `url` picks it up and inserts a colliding
  second `/` route. Call `EntityManager::clear()` before writing to the homepage to get a clean
  identity map.

## Twig and template renames

| 2.6 | 3.0 |
| --- | --- |
| `DefaultController::indexAction` | `Sulu\Content\UserInterface\Controller\Website\ContentController::indexAction` |
| `url` as `resource_locator` | `url` as `route` |
| `<tag name="sulu_article.type"/>` | `<group>` element in the template |
| `sulu_content_load` | `sulu_page_load` / `sulu_article_load`, properties now mandatory |
| `sulu_navigation_*`, `sulu_breadcrumb` | `sulu_page_navigation_*`, `sulu_page_breadcrumb` |
| `$loadExcerpt` boolean | explicit properties map |
| `nodeType` | `linkProvider` |
| article `routePath` | `url` |
| `hit.document.*` (massive-search) | flat `hit.*` (SEAL) |

`sulu_article_load_recent()` has no replacement; use a `smart_content` property with the
`articles` provider. `humanize` came from `symfony/form`, which 3.0 no longer pulls in.

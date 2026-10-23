# Silverstripe Playground Site

A small [Silverstripe CMS](https://www.silverstripe.org/) 6 site to learn from. It shows the two things most
Silverstripe sites are made of, and comes with sample content that explains them:

- **Pages built from content blocks** with [silverstripe-elemental](https://github.com/silverstripe/silverstripe-elemental),
  including a block of its own (`ContactsBlock`).
- **A ModelAdmin**: contacts in categories, with a list, filters, an edit form and a preview in the CMS.

It is an ordinary Silverstripe project: run it with ddev or any PHP/MySQL setup. It is also the site of the
[Silverstripe Playground](https://github.com/s2hub/silverstripe-cms-playground), which runs it in the browser.

## Running it

With [ddev](https://ddev.com/):

```sh
ddev start
ddev composer install
ddev exec vendor/bin/sake db:build --flush
ddev exec vendor/bin/sake tasks:seed-content
ddev launch /admin
```

Without ddev: copy `.env.example` to `.env`, fill in the database and an admin account
(`SS_DEFAULT_ADMIN_USERNAME`, `SS_DEFAULT_ADMIN_PASSWORD`), point the web server at `public/` and run the same
`composer install`, `db:build` and `seed-content` commands.

`tasks:seed-content` creates the sample pages, blocks and contacts once; it does nothing when there are contacts.

## What is where

| Path | What it is |
|---|---|
| `app/_config/elemental.yml` | Every `Page` is built from content blocks |
| `app/src/Contacts/` | `Contact` and `ContactCategory` (DataObjects), `ContactAdmin` (the CMS section), `ContactController` (a page per contact, also the CMS preview) |
| `app/src/Blocks/ContactsBlock.php` | A content block listing contacts, all or of one category |
| `app/templates/` | `ContactsBlock.ss` and `Layout/ContactPage.ss` |
| `app/client/css/contacts.css` | Their styles (exposed as `_resources/app/client/…`) |
| `app/src/Tasks/SeedContentTask.php` | The sample content |
| `app/_config/routes.yml` | The route `/contact/<ID>` |

The theme is Silverstripe's standard [startup-theme](https://github.com/silverstripe/startup-theme).

## Things to try

1. Add a `Birthday` field to `Contact` (`'Birthday' => 'Date'`), run `dev/build` and show it in the list in the CMS
   (`$summary_fields`).
2. Add a `Favourite` checkbox (`'Favourite' => 'Boolean'`) and make it a filter of the list (`$searchable_fields`).
3. Show the phone number in the Contacts block (`app/templates/ContactsBlock.ss`).
4. Give contacts tags: a `ContactTag` with a `many_many` relation, managed in a third tab of `ContactAdmin`.
5. Write a block of your own, e.g. a quote with an author.

## Notes

- Silverstripe 6.2 cannot scaffold a search filter for a `has_one` in `$searchable_fields` by itself; `Contact`
  names the field and fills its options in `scaffoldSearchFields()`.

## License

BSD-3-Clause, like Silverstripe CMS.

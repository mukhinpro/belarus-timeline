# Setup scripts

Two scripts that turn a fresh WordPress install into a finished site in one
command each. Between them they create 44 pages with the right slugs,
templates and parents, drop the themes' own copy into every page that has a
pattern, build both menus, set the front page and the journal, and write the
studio details into the Customizer.

## Running them

Over SSH, from the WordPress root:

```sh
wp theme activate thereyare
wp eval-file tools/setup-thereyare.php
```

```sh
wp theme activate marcdemure
wp eval-file tools/setup-marcdemure.php
```

Each prints a line per page — `+` created, `=` already there — and finishes
with the short list of things only you can do.

## Safe to run twice

The rule everywhere: **create what is missing, never overwrite what exists.**
A page you have edited is left exactly as it is. So after adding a page to the
list in a script, just run it again — it fills in the gap and touches nothing
else.

The same goes for menus (skipped if a menu of that name exists) and for the
studio details (only written into an empty setting).

## Where the copy comes from

Page bodies are pulled from the theme's registered block patterns, so a page
created by the script and one built by hand in the editor come out identical.
There is one source of truth for the words, and it is the pattern file.

Pages with no pattern are created empty:

- **ThereYare** — eight service pages. Their copy is in
  `docs/site-structure.md`, section 4.
- **Both** — About and the legal pages, which nobody can write for you.

## The journal

ThereYare's four articles are created as **drafts**, not published. An article
without its photograph should not go live the moment the site does. Add an
image to each and publish when you are ready.

## Before you run

- WordPress installed, the theme uploaded and activated.
- WP-CLI available. Most hosts with SSH have it; check with `wp --info`.
- Nothing else needed — the scripts set the permalink structure themselves,
  because every slug here assumes `/%postname%/`.

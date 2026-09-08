# Marc Demure — theme setup

A block theme for a boudoir photographer: portfolio split three ways, and a
subscriber-only area with a server-side gate and protected files.

## Install

1. Zip the `marcdemure` folder and upload it under **Appearance → Themes → Add
   New → Upload Theme**, then activate it.
2. Activating creates the three portfolio sections (Women, Men, Couples), the
   private uploads folder and its Apache deny rule.
3. **Settings → Permalinks → Save.** Nothing else re-registers the URLs, and
   without this the portfolio returns 404s.
4. Fill in **Appearance → Customize → Studio details**.

## The one step that is not optional, on nginx

Member files live in `wp-content/uploads/md-private/`. The theme writes an
`.htaccess` there, which Apache obeys and **nginx ignores completely**. On
nginx the rule has to go into the server block by hand:

```nginx
location ^~ /wp-content/uploads/md-private/ {
    deny all;
    return 403;
}
```

Without it, every member photograph is one URL away from being public and the
subscription is decoration. The dashboard checks whether the folder is still
reachable and shows a red notice if it is — do not dismiss that notice, fix it.

Reload nginx afterwards, then confirm the notice is gone.

## Stripe

Billing is a Payment Link plus a signed webhook. The site never holds an API
secret key and never sees a card number.

1. In Stripe, create a **recurring price** and a **Payment Link** for it, in
   subscription mode.
2. Enable the **customer portal** and copy its link.
3. Paste both into **Appearance → Customize → Membership billing**.
4. Add a webhook endpoint pointing at:

   ```
   https://your-domain.com/wp-json/md/v1/stripe
   ```

   Send these events: `checkout.session.completed`,
   `customer.subscription.created`, `customer.subscription.updated`,
   `customer.subscription.deleted`.
5. Copy the signing secret into `wp-config.php`, **not** into the database:

   ```php
   define( 'MD_STRIPE_WEBHOOK_SECRET', 'whsec_...' );
   ```

Until that constant exists the endpoint refuses every request, which is the
correct failure: no secret, no access granted.

Test with Stripe's CLI before launch — `stripe trigger checkout.session.completed`
should create a subscriber and show "Active until …" in **Users**.

### Before you build on this, read the processor question

Stripe's prohibited-business list covers pornography and, in places, adult
content generally. A boudoir photography *service* is ordinary business, but a
*paid subscription to nude photography* is much closer to the line, and the
usual outcome is not a warning — it is a frozen balance and a closed account.

Ask Stripe directly, in writing, describing the member area honestly, before
sending real traffic. If they decline, adult-friendly processors exist —
CCBill, Segpay, Verotel — and the only part of this theme that would change is
`inc/stripe.php`; the gate, the protected files and the templates stay as they
are.

## Publishing a session

**Portfolio → Add new shoot.** Title, gallery, section, location. It will not
publish until **Release** is ticked — a shoot without a signed release stays a
draft. That guard is deliberate and it is the reason the theme exists in this
shape.

## Publishing a member set

**Private area → Add new private set.** Images uploaded here go into the
protected folder automatically. Every URL in the member area sends
`noindex`, stays out of the sitemap and is never rendered to a visitor without
an active subscription.

## Pages to create

| Title | Slug | Template | Insert pattern |
|---|---|---|---|
| Home | `home` | default | — (front-page template) |
| Boudoir Photography in Los Angeles | `boudoir-photography-los-angeles` | Service page | Service page: women's boudoir |
| Men's Boudoir in Los Angeles | `mens-boudoir-los-angeles` | Service page | Service page: men's boudoir |
| Couples Boudoir in Los Angeles | `couples-boudoir-los-angeles` | Service page | Service page: couples boudoir |
| Bridal Boudoir in Los Angeles | `bridal-boudoir-los-angeles` | Service page | Service page: bridal boudoir |
| Pricing | `pricing` | Pricing page | — |
| Gift Certificates | `gift-certificates` | Gift certificate page | Gift certificate page |
| Albums and Prints | `albums` | default | Albums and prints |
| Before Your Session | `prepare-for-your-session` | default | Before your session |
| Private access | `private` | Private access page | Private access page |
| Discretion | `discretion` | default | Discretion + What clients say |
| About | `about` | default | — |
| Book a session | `book` | Booking page | — |
| Journal | `journal` | default, empty | — then Settings → Reading → Posts page: Journal |
| Model call | `model-call` | default | Model call |
| Studio: Downtown | `studios/downtown-los-angeles` | Studio page | Studio page (fill the brackets; street address in the excerpt) |
| Studio: Arts District | `studios/arts-district` | Studio page | Studio page |
| Studio: Hollywood | `studios/hollywood` | Studio page | Studio page |
| Privacy · Terms | `privacy` · `terms` | default | — |

For the three studio pages, create a parent page `studios` first so the URLs nest.

The slug `private` matters: the gate redirects there by name. The service
slugs matter too: the Service schema reads `men`/`couple` from the slug to
pick the right price, so keep those words in.

## The journal

Four articles ship as patterns under **Studio: whole pages** — what to wear,
the week before, boudoir as a gift, hair and makeup. For each: **Posts → Add
New**, type the title, insert the pattern, set a featured image, publish.
The URL comes from the title; the articles link to each other by these slugs,
so keep them: `what-to-wear-to-a-boudoir-shoot`,
`the-week-before-a-boudoir-session`, `boudoir-as-an-anniversary-gift`,
`do-you-need-hair-and-makeup-for-boudoir`.

Each post carries BlogPosting structured data authored by the photographer.

## Gift certificates

Same mechanics as the ThereYare theme: one Stripe Payment Link per item under
**Customize → Gift certificates**, automatic `MD-XXXX-XXXX` codes in
**Certificates**, a printable PDF from the row action. No expiry, by
California law — the copy says so and turns it into a selling point.

## Booking calendar

Paste a Calendly or Acuity link under **Customize → Studio details →
Scheduling link**. The booking page embeds it; until then it shows the phone
and email. Turn on deposit collection inside the scheduler (both take Stripe)
so the $200 is paid at booking. The albums page sends `?add=album` to the
booking page — map that to an intake question in the scheduler if you want to
see it.

## Testimonials and Google reviews

The pattern ships with example quotes marked as examples. Replace them with
real words — first name and neighbourhood only, which is what boudoir clients
agree to. Paste the Google "write a review" link under **Customize → Studio
details**. No review markup is emitted on purpose: self-serving review schema
is against Google's guidelines.

## What the theme does for indexing

- Meta description on every page, from the excerpt or the first paragraph —
  never the tagline repeated site-wide. Steps aside when Rank Math, Yoast or
  SEOPress is active.
- JSON-LD: LocalBusiness with priced offers, Person, WebSite, BreadcrumbList,
  a Service node on each service page, ImageGallery on each shoot, and FAQPage
  built from whatever Details blocks are on the page.
- Canonical and Open Graph fallbacks, with the site icon as the default image.
- Search results and the whole member area send `noindex`; `robots.txt` also
  disallows `/private/`, the file endpoint and the private uploads folder, and
  points at the sitemap.
- The three gallery archives get real descriptions on activation, so they are
  not thin pages. Rewrite them under **Portfolio → Portfolio sections**.
- Core's sitemap already lists pages, shoots and the three sections.

After launch: add the site in Google Search Console, submit `/wp-sitemap.xml`,
and check that nothing under `/private/` is listed.

Galleries at `/portfolio/` and `/gallery/women|men|couples/` create themselves
once the first shoot is published.

## Before launch

- Replace the placeholder prices in the Pricing pattern with real ones.
- Self-host the two fonts to drop the Google round trip from the LCP path.
- Confirm the private folder returns 403 from a logged-out browser.
- Check Search Console shows nothing under `/private/`.

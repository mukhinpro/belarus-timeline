# SaturdayLa — сайт студии

Сайт для SaturdayLa — креативной студии семейной и детской фотографии в Лос-Анджелесе.

В репозитории два артефакта:

- **`index.html`** — статичный превью-макет главной страницы (открывается прямо в браузере или через GitHub Pages), без бэкенда. Удобно, чтобы быстро посмотреть дизайн и показать клиенту/партнёрам.
- **`wp-theme/saturdayla/`** — полноценная многостраничная WordPress-тема с тем же дизайном: портфолио по сессиям, цены, форма бронирования, SEO-разметка (JSON-LD), блог. Фото и тексты правятся в админке WordPress, лезть в код не нужно.

## 1. Домен

`saturdayla.com` — можно купить прямо в WordPress.com (см. скриншот в задаче) или у любого другого регистратора и подключить к хостингу.

## 2. Установка темы (Hostinger + WordPress)

1. В hPanel Hostinger: **Websites → Add website → WordPress** (бесплатно). Подключи домен, включи SSL (Let's Encrypt).
2. В WordPress: **Appearance → Themes → Add New → Upload Theme** → загрузи архив темы (заархивируй папку `wp-theme/saturdayla`) → Activate.
3. **Settings → Permalinks** → выбери "Post name" → Save (обязательно, иначе `/portfolio/` не откроется).
4. **Settings → Reading**: Homepage = "A static page" → Homepage: `Home`, Posts page: `Journal`.

## 3. Плагины (все бесплатные)

| Плагин | Зачем |
|---|---|
| **Rank Math SEO** | Meta title/description, sitemap, Google Search Console. При установке выбери "Local Business → Photographer". |
| **LiteSpeed Cache** | Кэш + WebP + lazy-load, если хостинг на LiteSpeed (Hostinger). |
| **WPForms Lite** или **Contact Form 7** | Форма бронирования. Шорткод формы — в Customizer → Studio settings → "Booking form shortcode". |
| **UpdraftPlus** | Бэкапы. |
| **Wordfence** | Защита. |

Не ставь: Elementor, Jetpack, page-builder'ы — замедлят сайт, теме они не нужны.

## 4. Страницы — создай с ЭТИМИ slug'ами

| Заголовок | Slug (Permalink) | Template (Page Attributes) |
|---|---|---|
| Home | `home` | Default (главная берёт `front-page.php` автоматически) |
| Family Photography Los Angeles | `family-photography-los-angeles` | **Service page** |
| Kids & Teen Photographer Los Angeles | `kids-photographer-los-angeles` | **Service page** |
| Kids' Party Photography Los Angeles | `kids-party-photography-los-angeles` | **Service page** |
| Wedding Photography Los Angeles | `wedding-photography-los-angeles` | **Service page** |
| Editorial & Kids Model Portfolio (опционально) | `editorial-kids-photography-los-angeles` | **Service page** |
| Pricing | `pricing` | **Pricing (all sessions)** |
| About | `about` | Default |
| Book a session | `book` | **Book / Contact** |
| Journal | `journal` | Default (пустая, Reading → Posts page) |

Готовые тексты для Family и Kids' Party лежат в `wp-theme/saturdayla/content/` — вставь через блок **Custom HTML**. Excerpt (подзаголовок) указан в комментарии в начале файла.

Для Service-страниц: **Featured image** = большое горизонтальное фото (это hero). **Excerpt** = 1–2 предложения под заголовком. В тексте можно добавить блок **Gallery** — выведется сеткой с лайтбоксом. Блоки `<details><summary>…</summary><p>…</p></details>` в тексте автоматически уходят в Google как FAQ-разметка.

## 5. Меню

**Appearance → Menus** → создай меню, добавь страницы по порядку: Family, Kids, Parties, Weddings, Portfolio (Custom link `/portfolio/`), Pricing, About. Отметь "Primary menu". Кнопка "Check my date" добавляется сама.

## 6. Настройки студии (без кода)

**Appearance → Customize → Studio settings**: название студии, телефон, email, Instagram, города, текст hero, фото hero, командное фото, 4 фото плиток (Family / Kids / Parties / Weddings — вертикальные 3:4).

## 7. Портфолио — как добавлять съёмки

**Portfolio → Add new session**:
- Title: «The Nguyen family» (имя семьи или «Baby Leo, 9 days»)
- Location (справа): «El Matador Beach, Malibu»
- Session type (справа): Family / Kids & teens / Birthday parties / Weddings / Editorial
- **Featured image** = лучший кадр съёмки (обложка в сетке)
- В тексте — блок **Gallery**, перетащи 15–40 фото.

Подготовка фото перед загрузкой:
- Длинная сторона 2000 px, JPEG качество 80. Не грузи RAW-размеры.
- Имя файла: `family-photographer-malibu-el-matador-01.jpg`, а не `IMG_4821.jpg`.
- Alt text: «Family of four laughing on El Matador Beach at sunset, Malibu».

## 8. Цены

Пакеты и цены — в `wp-theme/saturdayla/template-parts/packages.php` (один файл, читается как таблица). Текущие цифры — ориентир по рынку LA 2026, поставь свои.

## 9. SEO-стратегия после запуска

**Неделя 1**
- Rank Math → Google Search Console: подключить, отправить sitemap (`/sitemap_index.xml`).
- Google Business Profile: категория «Photographer», радиус: LA, Santa Monica, Malibu, Pasadena, OC. Загрузить 20+ фото, ссылка на сайт.
- Для каждой service-страницы: Title вида «Kids & Family Photographer in Los Angeles | SaturdayLa», Description с ценой «Sessions from $450».
- **Birthday party photographer Los Angeles** и **kids model portfolio photographer Los Angeles** — частотные ключи с меньшей конкуренцией, чем «family photographer LA».

**Месяц 1–3 — контент (Journal)**
2 статьи в месяц: локации для съёмок в LA, styling-гайды, «как собрать портфолио для модельного агентства», backstage со съёмок.

**Постоянно**
- Каждую съёмку — как Session в портфолио с локацией.
- Просить отзывы в Google после каждой галереи.
- Instagram: в bio — ссылка на `/book/`.

**Дизайн**: тёплая слоновая кость / песок, коралловые кнопки, кирпично-красные подписи, Fraunces + Karla, наклонённый коллаж на главной.

**Технически уже сделано в теме**: JSON-LD LocalBusiness + Service + ImageGallery + FAQ + Breadcrumb, canonical, Open Graph, lazy-load всех фото кроме hero, `fetchpriority=high` на hero, чистый HTML без лишних скриптов, sticky-CTA в меню.

## 10. Скорость — чеклист
- LiteSpeed Cache включён, WebP включён
- Hero-фото ≤ 400 KB
- Проверь на pagespeed.web.dev — цель: 90+ mobile

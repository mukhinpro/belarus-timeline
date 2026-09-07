# Newbrand — блочная тема WordPress

Тема для студии детской, семейной и свадебной фотографии в Лос-Анджелесе. Построена как **block theme (Full Site Editing)** на нативных блоках WordPress.

`newbrand` — рабочая заглушка. Когда имя утвердится, переименование делается одной командой (см. раздел 7).

## 1. Почему нативные блоки, а не своя вёрстка

Требование «максимально хорошо индексироваться» решается именно так:

| Блок ядра | Что он даёт поиску |
|---|---|
| `core/heading` | Настоящая иерархия H1→H2→H3. Google по ней понимает, о чём страница. |
| `core/details` | Реальные `<details>/<summary>`. Тема **сама** превращает их в разметку FAQPage — пишешь вопрос в редакторе, получаешь rich result. Работает без JavaScript, то есть краулер видит текст ответа. |
| `core/image`, `core/gallery` | Автоматические `srcset`, `sizes`, `loading`, `decoding`, WebP. Плюс `<figure>/<figcaption>` — контекст для Google Images, а это заметный канал трафика для фотографа. |
| `core/query` (Query Loop) | Портфолио рендерится на сервере: настоящие ссылки и настоящая пагинация, без JS. |
| `core/list`, `core/table` | Семантические `<ul>` и `<table>` — из них Google собирает списки и сравнения в выдаче. |
| `core/quote` | `<blockquote>` с `<cite>` для отзывов. |
| `core/group` с `tagName` | Даёт настоящие `<section>`, `<article>`, `<header>`, `<main>` — корректный документный контур. |
| `core/navigation` | Семантический `<nav>` с реальными `<a>`. |

Плюс блочные **паттерны**: ты собираешь новую страницу локации за две минуты, и структура заголовков остаётся правильной без разработчика.

## 2. Что делает тема сверх блоков

- **JSON-LD граф**: LocalBusiness + ProfessionalService, WebSite, BreadcrumbList, Service на страницах услуг, FAQPage из блоков Details, ImageGallery на страницах съёмок.
- **Свой блок Breadcrumbs** — в ядре его нет, а видимая цепочка должна совпадать с разметкой BreadcrumbList.
- **Core Web Vitals**: hero грузится с `fetchpriority=high`, всё остальное лениво; порог ленивой загрузки снижен до одного изображения, чтобы ничто не конкурировало с LCP.
- **Тип записи Sessions** с обязательным полем локации — основа локального SEO.
- Meta description, Open Graph и canonical — **только если SEO-плагин не установлен**, чтобы не было двух конкурирующих наборов тегов.

**Сознательно не делаем:** AggregateRating из отзывов на сайте. Google считает разметку отзывов, которую бизнес пишет сам о себе, нарушением правил, и за это можно потерять все rich results домена. Отзывы собираются в Google Business Profile.

## 3. Установка

1. Заархивировать папку `newbrand` в zip.
2. **Внешний вид → Темы → Добавить новую → Загрузить тему** → активировать.
3. **Настройки → Постоянные ссылки** → «Название записи» → сохранить. Обязательно, иначе `/portfolio/` не откроется.
4. **Настройки → Чтение** → главная страница: статическая, выбрать `Home`; страница записей: `Journal`.
5. **Внешний вид → Настроить → Studio details** — телефон, email, Instagram, города. Эти поля уходят в разметку для Google.

## 4. Плагины

| Плагин | Зачем |
|---|---|
| **Rank Math SEO** | Meta title/description, sitemap, Search Console. Тема автоматически уступает ему мета-теги. При настройке выбрать Local Business → Photographer. |
| **LiteSpeed Cache** или **WP Rocket** | Кэш, WebP, отложенный CSS. |
| **WPForms Lite** / **Contact Form 7** | Форма записи. |
| **UpdraftPlus** | Бэкапы. |
| **Wordfence** | Защита. |

Не ставить Elementor и другие page builder'ы: они дублируют то, что уже делает блочный редактор, и утяжеляют страницы.

## 5. Страницы — создать с этими slug'ами

| Заголовок | Slug | Шаблон |
|---|---|---|
| Home | `home` | берётся `front-page` автоматически |
| Family Photography Los Angeles | `family-photography-los-angeles` | Service page |
| Kids & Teen Photographer Los Angeles | `kids-photographer-los-angeles` | Service page |
| Kids' Party Photography Los Angeles | `kids-party-photography-los-angeles` | Service page |
| Wedding Photography Los Angeles | `wedding-photography-los-angeles` | Service page |
| Motherhood & Parent Portraits Los Angeles | `motherhood-photography-los-angeles` | Service page |
| Pricing | `pricing` | Pricing page |
| About | `about` | по умолчанию |
| Book a session | `book` | Booking page |
| Journal | `journal` | пустая, назначить страницей записей |

На страницах услуг: **изображение записи** = горизонтальный hero, **отрывок** = одна-две фразы под заголовком. Внутри вставить паттерн **FAQ** — он сам уйдёт в Google как FAQ-разметка.

## 6. Портфолио

**Portfolio → Add new session**. Заголовок — имя семьи. Локация в сайдбаре — обязательно, она идёт и в подпись, и в разметку. Изображение записи — лучший кадр. В теле — блок **Gallery** на 15–40 фото.

Подготовка файлов: длинная сторона 2000 px, JPEG 80. Имя файла `family-photographer-malibu-el-matador-01.jpg`, а не `IMG_4821.jpg`. Alt обязателен и осмысленный.

## 7. Переименование под финальное имя

```bash
cd wp-theme
NEW=mantel   # финальный слаг
git mv newbrand "$NEW"
cd "$NEW"
find . -type f \( -name '*.php' -o -name '*.json' -o -name '*.html' -o -name '*.css' -o -name '*.js' -o -name '*.md' \) \
  -exec sed -i "s/newbrand/$NEW/g; s/NEWBRAND/$(echo "$NEW" | tr a-z A-Z)/g; s/Newbrand/$(echo "$NEW" | sed 's/./\U&/')/g" {} +
```

Затем поправить в `style.css` поля Theme Name, Theme URI и Description.

## 8. Что проверить перед запуском

- [ ] Rank Math подключён к Search Console, sitemap отправлен
- [ ] Google Business Profile создан, категория Photographer, 20+ фото
- [ ] Rich Results Test на главной, странице услуги и странице съёмки
- [ ] PageSpeed Insights, цель 90+ на мобильных
- [ ] Hero-фото ≤ 400 KB
- [ ] Шрифты self-hosted (сейчас грузятся с Google Fonts — это лишний внешний запрос в критическом пути)

## 9. Ограничение этой сборки

Тема написана и проверена статически: синтаксис PHP, валидность JSON, целостность 432 блочных делимитеров во всех шаблонах и паттернах. **Живой рендер в WordPress не проверялся** — в среде разработки не было доступа к wordpress.org. Первый запуск на хостинге нужно пройти по чеклисту выше.

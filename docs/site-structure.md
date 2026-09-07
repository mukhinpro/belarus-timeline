# ThereYare — структура сайта

Полная карта: какие страницы создать, по каким адресам, каким шаблоном и с каким текстом. Порядок в таблицах — порядок сборки.

Тексты ниже готовы к вставке. Там, где стоит `[ ]`, нужны твои данные.

---

## 1. Карта сайта

```
/                                          Главная
│
├── /portfolio/                            Галереи — витрина тем
│   ├── /sessions/family/                  Галерея: семьи
│   ├── /sessions/kids/                    Галерея: дети и подростки
│   ├── /sessions/parties/                 Галерея: дни рождения
│   ├── /sessions/weddings/                Галерея: свадьбы
│   ├── /sessions/maternity/               Галерея: беременность
│   ├── /sessions/motherhood/              Галерея: родители
│   ├── /sessions/model-portfolios/        Галерея: модельное портфолио
│   └── /portfolio/<съёмка>/               Одна съёмка целиком
│
├── /family-photography-los-angeles/       Услуга: семейная съёмка
├── /kids-photographer-los-angeles/        Услуга: дети и подростки
├── /kids-party-photography-los-angeles/   Услуга: дни рождения
├── /wedding-photography-los-angeles/      Услуга: свадьбы
├── /maternity-photography-los-angeles/    Услуга: беременность
├── /motherhood-photography-los-angeles/   Услуга: портреты родителей
├── /kids-model-portfolio-los-angeles/     Услуга: модельное портфолио
│
├── /pricing/                              Цены
├── /about/                                О студии
├── /book/                                 Запись
│
├── /journal/                              Журнал
│   └── /journal/<статья>/                 Статья
│
└── /locations/<место>/                    Страницы локаций (растут со временем)
```

**Почему галереи разнесены по адресам, а не сделаны фильтром.** Фильтр на JavaScript Google не выполняет: он увидит одну страницу вместо семи. Отдельные адреса дают семь страниц, каждая со своим заголовком и своим набором ключевых слов. Плюс на такую страницу можно дать ссылку в Instagram — «вот только свадьбы».

**Разница между галереей и страницей услуги.** Галерея отвечает на «покажи работы», услуга — на «сколько стоит и как проходит». Это разные намерения в поиске, поэтому и страницы разные, но они ссылаются друг на друга.

---

## 2. Меню

**Главное:** Galleries · Families · Kids · Parties · Weddings · Pricing · About
Кнопка «Check your date» добавляется темой сама.

**Подвал:** About · Pricing · Journal · Book · Instagram · Privacy

---

## 3. Страницы: что создать

| # | Заголовок | Slug | Шаблон |
|---|---|---|---|
| 1 | Home | `home` | по умолчанию (тема подставит front-page) |
| 2 | Family Photography in Los Angeles | `family-photography-los-angeles` | Service page |
| 3 | Kids & Teen Photographer in Los Angeles | `kids-photographer-los-angeles` | Service page |
| 4 | Kids' Party Photographer in Los Angeles | `kids-party-photography-los-angeles` | Service page |
| 5 | Wedding Photography in Los Angeles | `wedding-photography-los-angeles` | Service page |
| 6 | Maternity Photography in Los Angeles | `maternity-photography-los-angeles` | Service page |
| 7 | Motherhood & Parent Portraits in Los Angeles | `motherhood-photography-los-angeles` | Service page |
| 8 | Kids Model Portfolio Photographer in Los Angeles | `kids-model-portfolio-los-angeles` | Service page |
| 9 | Pricing | `pricing` | Pricing page |
| 10 | About | `about` | по умолчанию |
| 11 | Book a session | `book` | Booking page |
| 12 | Journal | `journal` | по умолчанию, пустая → Настройки → Чтение |

Галереи `/portfolio/` и `/sessions/...` создавать **не нужно** — они появляются сами, как только опубликуешь первую съёмку.

---

## 4. Тексты

### Главная

Собирается из паттернов: Hero → Service tiles → последние съёмки → How it goes → Testimonials → Booking. Меняются только фотографии и отзывы.

**Title:** ThereYare — Family, Kids & Wedding Photographer in Los Angeles
**Meta description:** Unposed family, children's and wedding photography across Los Angeles. Sessions from $360. Family sessions $800, weddings from $1,600.

---

### Галереи — витрина `/portfolio/`

H1 и вступление уже в шаблоне. При желании поменяй вступление на своё.

**Title:** Galleries — Family, Kids & Wedding Photography | ThereYare
**Meta description:** Real sessions photographed across Los Angeles: families, children, birthday parties, weddings and parent portraits.

---

### Описания галерей

Вставляются в **Portfolio → Session types → изменить → Описание**. Google показывает их под заголовком в выдаче.

**Families.** Sessions with the whole family — at home, in the studio, or somewhere in the city that means something to you. Grandparents welcome and often the best part.

**Kids & teens.** Portraits that look like the child rather than like a school photo. Studio or location, no forced smiles, and comp-card crops for agencies on request.

**Parties.** Birthday parties photographed end to end: the room before the guests, the games, the cake, and the meltdown that everyone laughs about later.

**Weddings.** Documentary coverage with an editorial eye. We shoot the day as it happens and step in only for the light.

**Maternity.** The last weeks before everything changes, photographed while they are still happening. Partner and older children welcome.

**Model portfolios.** Agency tests, digitals and comp-card crops for child and teen models, shot to the brief agencies actually give.

**Mothers & fathers.** Portraits of the parent, not just the parent-of. Usually the photographs people are most nervous about and most grateful for.

---

### Услуга: семейная съёмка

**Slug:** `family-photography-los-angeles` · **Шаблон:** Service page
**Title:** Family Photographer in Los Angeles | ThereYare
**Meta description:** Unposed family photography across Los Angeles — studio, at home or outdoors. Family sessions $800 for two hours, travel within 30 miles included.
**H1:** Family photography in Los Angeles
**Отрывок:** Studio, your living room or a park with good light — family sessions where nobody has to say cheese.

**Текст страницы** (паттерны: How it goes → FAQ → Pricing conditions):

> Most families book us because of the same sentence: *we have a thousand photos on our phones and I'm in four of them.*
>
> That's the problem we solve. You get an hour where nobody is holding a camera, and afterwards you're in the pictures with everyone else.
>
> We shoot in the studio when you want clean, framed portraits, and at home or outdoors when you want the feel of a real afternoon. Families with children under six almost always do better at home — familiar rooms, familiar toys, no performance.

---

### Услуга: дети и подростки

**Slug:** `kids-photographer-los-angeles`
**Title:** Kids & Teen Photographer in Los Angeles | ThereYare
**Meta description:** Character portraits of children and teenagers in Los Angeles. Studio or location, agency comp cards on request.
**H1:** Children's and teen portraits in Los Angeles
**Отрывок:** Portraits that look like your child, not like a school photo.

> A school photo asks a child to sit still and smile at a stranger. It gets you a picture of a child obeying.
>
> We do the opposite. We give them something to do and photograph what happens — the concentration, the showing off, the moment they forget we're there. Parents usually pick the frame where their child isn't smiling at all.
>
> If your child is with an agency, we shoot agency tests and comp-card crops, with usage rights written down in plain language.

---

### Услуга: дни рождения

**Slug:** `kids-party-photography-los-angeles`
**Title:** Kids' Party Photographer in Los Angeles | ThereYare
**Meta description:** Birthday party photography in Los Angeles — the whole party, start to cake. Gallery within a week.
**H1:** Children's party photography in Los Angeles
**Отрывок:** We photograph the whole party — start to cake to meltdown — so you can put your phone down and be there.

> You spent a week on the decorations and you'll spend the party refilling cups. Somebody should photograph it, and it shouldn't be you.
>
> We arrive before the guests and shoot the room while it's still tidy — the table, the cake, the thing you made at midnight. Then we stay at the edge with a long lens until the children stop noticing us, which takes about ten minutes.
>
> Everyone gets photographed: guests, siblings, cousins, the parents standing by the kitchen door. Nobody goes home unphotographed.

---

### Услуга: свадьбы

**Slug:** `wedding-photography-los-angeles`
**Title:** Wedding Photographer in Los Angeles | ThereYare
**Meta description:** Documentary wedding photography in Los Angeles with an editorial eye. Coverage from $1,600 for four hours, two photographers.
**H1:** Wedding photography in Los Angeles
**Отрывок:** Documentary coverage with an editorial eye — your day as it happened, at its best.

> We photograph weddings the way we photograph families: quietly, and mostly from a step back.
>
> That means we're not going to run your day. We'll help with the timeline so the light works, and we'll step in for the portraits you actually want framed. The rest we watch for.
>
> What you get is the day in order — the hour before, the faces during, the part of the night nobody remembers clearly.

---

### Услуга: съёмка беременности

**Slug:** `maternity-photography-los-angeles`
**Title:** Maternity Photographer in Los Angeles | ThereYare
**Meta description:** Maternity photography in Los Angeles — $700 for two hours. Partner and older children welcome, lighting included at your home.
**H1:** Maternity photography in Los Angeles
**Отрывок:** The last weeks before everything changes, photographed while they're still happening.

> There is a narrow window here, and most people miss it. Too early and it doesn't read; too late and you're too tired to want to. Around 30 to 34 weeks is usually right, though we've shot later and it worked.
>
> Bring your partner, bring the older children — the photographs where a toddler is talking to the bump are the ones people frame.
>
> We shoot at home more often than in the studio for this one. Your own light, your own rooms, and nobody has to go anywhere.

---

### Услуга: портреты родителей

**Slug:** `motherhood-photography-los-angeles`
**Title:** Motherhood & Parent Portraits in Los Angeles | ThereYare
**Meta description:** Portraits of mothers and fathers in Los Angeles — with their children or alone. Sessions $400 for ninety minutes.
**H1:** Portraits of mothers and fathers
**Отрывок:** The parent, not just the parent-of.

> Almost every parent who books this says a version of the same thing: *I don't like being photographed.*
>
> That's usually not about the camera. It's about the last few years, and about being the one who always takes the picture.
>
> These sessions are quiet and short. With your children if you want them, alone if you'd rather. In twenty years these are the photographs your children will look for, and right now they don't exist.

---

### Услуга: модельное портфолио детей

**Slug:** `kids-model-portfolio-los-angeles`
**Title:** Kids Model Portfolio Photographer in Los Angeles | ThereYare
**Meta description:** Agency tests and comp cards for child and teen models in Los Angeles. Digitals, clean studio, usage rights in writing.
**H1:** Child model portfolios and agency comp cards
**Отрывок:** Agency tests, digitals and comp-card crops — shot to the brief agencies actually give.

> Agencies ask for specific things and reject portfolios that don't have them: clean digitals with no makeup, a smiling shot, a serious one, full length, profile. Most family photographers don't shoot to that list, and the parent finds out after paying.
>
> We shoot to the list. Clean background, honest skin, no heavy retouching — agencies want to see the child, not an edit.
>
> Usage rights are written down in plain language before we start, so you know exactly what you can send where.

**Почему эта страница ценна.** Запрос «kids model portfolio photographer Los Angeles» гораздо реже, чем «family photographer LA», но и конкуренция там в разы ниже, а чек выше. Такие страницы приносят первые заявки быстрее, чем главные.

---

### Цены

**Slug:** `pricing` · **Шаблон:** Pricing page
**Title:** Pricing | ThereYare Photography, Los Angeles
**Meta description:** Clear prices for family, children's, party and wedding photography in Los Angeles. Sessions $400 for ninety minutes.
**H1:** Pricing
**Отрывок:** Clear prices, no hidden fees. Pick the kind of day, then the package that fits.

Вставить паттерн **Pricing — all seven sessions**. Он содержит все семь карточек с твоими реальными ценами и блок общих условий (ассистент, свет, аренда студии, выезд).

---

### О студии

**Slug:** `about`
**Title:** About ThereYare — Los Angeles Photography Studio
**Meta description:** Who we are, how we work, and why nobody says cheese.
**H1:** We're ThereYare

> The name is a thing people say out loud. *There y'are* — said when someone you've been looking for finally turns up, and said again when a person stops performing and their real face arrives. We say it twice in a working day: once on the shoot, once when the gallery goes out.
>
> [ Здесь — кто вы: один фотограф или команда, сколько лет снимаете, что снимали до этого. ]
>
> We photograph in Los Angeles and the neighbourhoods around it. Everything on this site was taken and edited by a person — no AI images, no stock, no faces that never existed.

---

### Запись

**Slug:** `book` · **Шаблон:** Booking page
**Title:** Book a session | ThereYare, Los Angeles
**Meta description:** Send your date and we'll come back the same day with availability and a quote.
**H1:** Tell us about your day
**Отрывок:** Send the date and we'll come back the same day with availability and a quote.

Форма ставится плагином. Поля: имя, email, дата, тип съёмки, место, «что нам стоит знать».

---

## 5. Порядок сборки

1. Создать двенадцать страниц выше с точными slug'ами.
2. Назначить шаблоны (справа в редакторе, «Шаблон»).
3. Вставить паттерны, заменить текст и фото.
4. Опубликовать 3–5 съёмок в Portfolio — галереи появятся сами.
5. Заполнить описания типов съёмок.
6. Собрать меню.
7. Проверить: каждая страница услуги ссылается на свою галерею и обратно.

---

## 6. Адрес офиса — важная ловушка

Ты арендуешь студии под съёмку, но у тебя есть адрес офиса. Здесь легко потерять карточку в Google, поэтому разведём две вещи, которые часто путают.

**Разметка на сайте** и **карточка в Google Business Profile** — разные системы.

*На сайте* адрес офиса указывать можно и полезно: он помогает Google понять, какому именно бизнесу принадлежит сайт. Поля уже добавлены в Настройки → Studio details.

*В Google Business Profile* — нельзя. Правило Google: адрес показывают только те, кто принимает клиентов по этому адресу. Если клиенты в офис не приезжают, карточку нужно настроить как **service-area business** и адрес **скрыть**. Google спросит адрес при регистрации для проверки, но дальше его надо убрать из показа и выставить зоны обслуживания — районы LA.

Указанный, но непосещаемый адрес — самая частая причина, по которой карточки блокируют. Восстановление занимает недели, и всё это время тебя нет в Картах.

**Что делать:** при создании карточки на вопрос «Do you want to add a location customers can visit?» ответить **нет**, дальше указать зоны обслуживания.

---

## 7. Данные студии — заполнено

| | |
|---|---|
| Бренд | ThereYare |
| Ведущий фотограф | Alexandr Mukhin — уходит в разметку Person |
| Телефон | +1 747 217 9834 |
| Email | mukhinpro@gmail.com |
| Instagram | [@thereyare_studio](https://www.instagram.com/thereyare_studio/) |
| Команда | Фотограф + ассистент на всех съёмках; на свадьбах второй фотограф |
| География | LA и округ, выезд до 30 миль включён |
| Адрес | Офис есть, но в Google Business Profile скрыт (см. раздел 6) |
| Языки | Только английский |

Всё это уже проставлено дефолтами в теме: **Внешний вид → Настроить → Studio details**. Проверь и поправь, если что-то изменится.

---

## 8. Что ещё нужно

**Блокирует запуск:**

- **Фотографии** — hero, 7 плиток галерей, 7 плиток услуг, 3–5 съёмок целиком. Загружаешь через админку.
- **Домен** — `thereyare.com` занят. Проверить `thereyarestudio.com` (совпадает с ником в Instagram) и купить.

**Не блокирует, добавим по ходу:**

- Отзывы клиентов — реальные, с указанием района.
- Список районов LA для Google Business Profile.
- Адрес офиса и индекс — для разметки на сайте.
- Год основания студии.

---

## 9. Три замечания по брифу

**Почта.** `mukhinpro@gmail.com` работает, но `hello@thereyarestudio.com` на своём домене читается солиднее и сам по себе служит сигналом доверия — и для клиента, и для Google. Настраивается на хостинге бесплатно, письма можно пересылать на тот же Gmail. Рекомендую сделать до запуска.

**Твои публикации.** Ты снимал для FLG Magazine, Kids Magazine и Elements. Для Google это сильный сигнал экспертности — из тех, что реально влияют на позиции в конкурентной нише. Стоит вернуть на страницу About и в разметку Person. Скажи, какие издания указывать точно, и я впишу.

**Свадебный минимум.** $1600 за 4 часа — это половина смены. Большинство свадеб длится 8–10 часов, и пара, увидев только цифру за полдня, может решить, что ты не берёшь полные дни. На странице цен я написал «longer days quoted with the date», но лучше назвать вторую цифру — за полный день. Скажи её, добавлю.

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
│   ├── /sessions/motherhood/              Галерея: родители
│   └── /portfolio/<съёмка>/               Одна съёмка целиком
│
├── /family-photography-los-angeles/       Услуга: семейная съёмка
├── /kids-photographer-los-angeles/        Услуга: дети и подростки
├── /kids-party-photography-los-angeles/   Услуга: дни рождения
├── /wedding-photography-los-angeles/      Услуга: свадьбы
├── /motherhood-photography-los-angeles/   Услуга: портреты родителей
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

**Почему галереи разнесены по адресам, а не сделаны фильтром.** Фильтр на JavaScript Google не выполняет: он увидит одну страницу вместо пяти. Отдельные адреса дают пять страниц, каждая со своим заголовком и своим набором ключевых слов. Плюс на такую страницу можно дать ссылку в Instagram — «вот только свадьбы».

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
| 6 | Motherhood & Parent Portraits in Los Angeles | `motherhood-photography-los-angeles` | Service page |
| 7 | Pricing | `pricing` | Pricing page |
| 8 | About | `about` | по умолчанию |
| 9 | Book a session | `book` | Booking page |
| 10 | Journal | `journal` | по умолчанию, пустая → Настройки → Чтение |

Галереи `/portfolio/` и `/sessions/...` создавать **не нужно** — они появляются сами, как только опубликуешь первую съёмку.

---

## 4. Тексты

### Главная

Собирается из паттернов: Hero → Service tiles → последние съёмки → How it goes → Testimonials → Booking. Меняются только фотографии и отзывы.

**Title:** ThereYare — Family, Kids & Wedding Photographer in Los Angeles
**Meta description:** Unposed family, children's and wedding photography across Los Angeles. Sessions from $[ ]. Private gallery in two weeks.

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

**Mothers & fathers.** Portraits of the parent, not just the parent-of. Usually the photographs people are most nervous about and most grateful for.

---

### Услуга: семейная съёмка

**Slug:** `family-photography-los-angeles` · **Шаблон:** Service page
**Title:** Family Photographer in Los Angeles | ThereYare
**Meta description:** Unposed family photography across Los Angeles — studio, at home or outdoors. Sessions from $[ ], private gallery in two weeks.
**H1:** Family photography in Los Angeles
**Отрывок:** Studio, your living room or a park with good light — family sessions where nobody has to say cheese.

**Текст страницы** (паттерны: How it goes → FAQ → Packages):

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
**Meta description:** Documentary wedding photography in Los Angeles with an editorial eye. Coverage from $[ ].
**H1:** Wedding photography in Los Angeles
**Отрывок:** Documentary coverage with an editorial eye — your day as it happened, at its best.

> We photograph weddings the way we photograph families: quietly, and mostly from a step back.
>
> That means we're not going to run your day. We'll help with the timeline so the light works, and we'll step in for the portraits you actually want framed. The rest we watch for.
>
> What you get is the day in order — the hour before, the faces during, the part of the night nobody remembers clearly.

---

### Услуга: портреты родителей

**Slug:** `motherhood-photography-los-angeles`
**Title:** Motherhood & Parent Portraits in Los Angeles | ThereYare
**Meta description:** Portraits of mothers and fathers in Los Angeles — with their children or alone. Sessions from $[ ].
**H1:** Portraits of mothers and fathers
**Отрывок:** The parent, not just the parent-of.

> Almost every parent who books this says a version of the same thing: *I don't like being photographed.*
>
> That's usually not about the camera. It's about the last few years, and about being the one who always takes the picture.
>
> These sessions are quiet and short. With your children if you want them, alone if you'd rather. In twenty years these are the photographs your children will look for, and right now they don't exist.

---

### Цены

**Slug:** `pricing` · **Шаблон:** Pricing page
**Title:** Pricing | ThereYare Photography, Los Angeles
**Meta description:** Clear prices for family, children's, party and wedding photography in Los Angeles. Sessions from $[ ].
**H1:** Pricing
**Отрывок:** Clear prices, no hidden fees. Pick the kind of day, then the package that fits.

Вставить паттерн **Packages** — пять раз, по одному на направление, каждый под своим H2. **Цены в паттерне сейчас взяты с потолка, замени на свои.**

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

1. Создать десять страниц выше с точными slug'ами.
2. Назначить шаблоны (справа в редакторе, «Шаблон»).
3. Вставить паттерны, заменить текст и фото.
4. Опубликовать 3–5 съёмок в Portfolio — галереи появятся сами.
5. Заполнить описания типов съёмок.
6. Собрать меню.
7. Проверить: каждая страница услуги ссылается на свою галерею и обратно.

---

## 6. Что нужно от тебя

- Цены по пяти направлениям
- Телефон, email, Instagram
- Районы обслуживания
- Кто за камерой: один человек или команда
- Фотографии: hero, 5 плиток галерей, 5 плиток услуг, 3–5 съёмок целиком
- Реальные отзывы

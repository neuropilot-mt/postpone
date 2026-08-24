# Как получить и прописать ключи соцсетей

Инструкция под проект **Posting**: переменные в `.env`, OAuth-колбэки вида `{APP_URL}/oauth/{platform}/callback`.

Сначала в `.env` задайте публичный URL приложения (без лишнего порта в конце):

```env
APP_URL=https://your-domain.com
```

Для локальной разработки Instagram и TikTok почти всегда требуют **HTTPS** и публичный URL. Удобно поднять туннель, например [ngrok](https://ngrok.com/) или [Cloudflare Tunnel](https://developers.cloudflare.com/cloudflare-one/connections/connect-networks/), и прописать его в `APP_URL`.

После правок `.env`:

```bash
php artisan config:clear
```

Когда все четыре пары ключей будут заполнены:

```env
SOCIAL_SANDBOX=false
```

Статус «ключи заданы / не заданы» смотрите в **Админка**. Подключение аккаунтов — на странице **Соцсети**.

---

## Общая схема для всех платформ

1. Создать приложение в кабинете разработчика.
2. Добавить **Redirect URI** ровно как в таблице ниже.
3. Скопировать Client ID / Secret (Key) в `.env`.
4. Открыть `/accounts` → **Подключить через OAuth**.
5. Опубликовать тестовый пост (для Instagram медиа должно быть доступно по публичному HTTPS).

| Платформа | Redirect URI |
|-----------|----------------|
| Instagram | `{APP_URL}/oauth/instagram/callback` |
| VK | `{APP_URL}/oauth/vk/callback` |
| TikTok | `{APP_URL}/oauth/tiktok/callback` |
| YouTube | `{APP_URL}/oauth/youtube/callback` |

Пример при `APP_URL=https://abc123.ngrok-free.app`:

```text
https://abc123.ngrok-free.app/oauth/instagram/callback
https://abc123.ngrok-free.app/oauth/vk/callback
https://abc123.ngrok-free.app/oauth/tiktok/callback
https://abc123.ngrok-free.app/oauth/youtube/callback
```

---

## 1. Instagram (Facebook Graph / Meta)

В этом проекте используется схема **Instagram API with Facebook Login** (`graph.facebook.com`): Professional-аккаунт Instagram, привязанный к Facebook Page.

### Документация и кабинеты

- Создать приложение Meta: [Create an App](https://developers.facebook.com/docs/development/create-an-app)
- Кабинет: [Meta for Developers](https://developers.facebook.com/apps/)
- Публикация контента: [Content Publishing](https://developers.facebook.com/docs/instagram-platform/content-publishing)
- Настройка Instagram-приложения: [Create an Instagram app](https://developers.facebook.com/docs/instagram-platform/create-an-instagram-app)
- Facebook Login: [Facebook Login](https://developers.facebook.com/docs/facebook-login/)
- Права и App Review: [App Review for Instagram API](https://developers.facebook.com/docs/instagram-platform/app-review/)

### Что нужно заранее

1. [Facebook Page](https://www.facebook.com/pages/create).
2. Instagram-аккаунт типа **Professional** (Business или Creator).
3. Привязка Instagram к Page: в приложении Instagram → Профессиональная панель → Связанные активы / Facebook Page.
4. Для боевого доступа — [Meta Business Suite](https://business.facebook.com/) и обычно Business Verification.

### Пошагово

1. Откройте [developers.facebook.com/apps](https://developers.facebook.com/apps/) → **Create App**.
2. Выберите сценарий вроде **Other** / бизнес или **Manage messaging & content on Instagram** (формулировки в кабинете меняются).
3. В приложении добавьте продукты:
   - **Facebook Login for Business** (или Facebook Login);
   - **Instagram** с вариантом **API setup with Facebook login** (не «Instagram Login» — наш код ходит в `graph.facebook.com`).
4. **App settings → Basic**:
   - скопируйте **App ID** → `INSTAGRAM_CLIENT_ID`;
   - **App Secret** → `INSTAGRAM_CLIENT_SECRET`.
5. **Facebook Login → Settings** (Valid OAuth Redirect URIs):

   ```text
   {APP_URL}/oauth/instagram/callback
   ```

6. В режиме разработки добавьте себя как **Admin/Developer/Tester** приложения и роли на Page / Instagram.
7. Права, которые запрашивает Posting (см. `config/social.php`):

   - `instagram_basic`
   - `instagram_content_publish`
   - `pages_show_list`
   - `pages_read_engagement`
   - `business_management`

   Для пользователей вне списка тестировщиков нужен **App Review** и Advanced Access.

### Прописать в `.env`

```env
INSTAGRAM_CLIENT_ID=ваш_app_id
INSTAGRAM_CLIENT_SECRET=ваш_app_secret
INSTAGRAM_REDIRECT_URI="${APP_URL}/oauth/instagram/callback"
INSTAGRAM_GRAPH_VERSION=v21.0
```

### Важно для публикации

Instagram тянет фото/видео по **публичному HTTPS URL**. `http://localhost` не подойдёт: нужен туннель или прод-домен, и файлы должны отдаваться с диска `public` (`php artisan storage:link`).

После OAuth в приложении выбирается первая найденная связка Page → Instagram Business Account.

---

## 2. ВКонтакте

### Документация и кабинеты

- Портал: [VK for developers](https://dev.vk.com/)
- Управление приложениями: [vk.com/apps?act=manage](https://vk.com/apps?act=manage)
- Права доступа: [Access rights](https://dev.vk.com/en/reference/access-rights)
- Авторизация: [Authorization](https://dev.vk.com/en/api/access-token/authcode-flow-user)
- Публикация на стену: [wall.post](https://dev.vk.com/method/wall.post)

### Пошагово

1. Откройте [vk.com/apps?act=manage](https://vk.com/apps?act=manage) → **Создать**.
2. Тип приложения: лучше **Веб-сайт** / standalone-совместимый вариант для серверного OAuth (если кабинет предлагает несколько типов — ориентируйтесь на «сайт» с redirect URI).
3. В настройках приложения укажите:
   - **Адрес сайта** = ваш `APP_URL`;
   - **Доверенный redirect URI** = `{APP_URL}/oauth/vk/callback`.
4. Скопируйте:
   - **ID приложения** → `VK_CLIENT_ID`;
   - **Защищённый ключ** → `VK_CLIENT_SECRET`.
5. Права, которые запрашивает Posting: `wall`, `photos`, `video`, `offline`.
6. Для постинга в сообщество: вы должны быть админом группы. После OAuth приложение пытается взять первую группу, где вы админ, и пишет от её имени (`owner_id` отрицательный).

### Прописать в `.env`

```env
VK_CLIENT_ID=ваш_id_приложения
VK_CLIENT_SECRET=ваш_защищённый_ключ
VK_REDIRECT_URI="${APP_URL}/oauth/vk/callback"
VK_API_VERSION=5.199
```

### Замечания

- Новые токены VK ID (`vk2.a.*`) иногда ограничены для `wall.post` / загрузки фото. Если OAuth прошёл, а публикация падает с ошибкой прав — проверьте тип приложения и scopes в [доке VK](https://dev.vk.com/en/reference/access-rights).
- Для отладки группы можно также смотреть ключ сообщества в настройках группы → **Работа с API** (отдельный сценарий; текущий OAuth заточен под пользовательский токен админа группы).

---

## 3. TikTok

### Документация и кабинеты

- Кабинет: [TikTok for Developers](https://developers.tiktok.com/)
- Создать приложение: [Create an app](https://developers.tiktok.com/doc/getting-started-create-an-app)
- Login Kit (Web): [Login Kit for Web](https://developers.tiktok.com/doc/login-kit-web)
- Content Posting API: [Get started](https://developers.tiktok.com/doc/content-posting-api-get-started)
- Scopes: [Scopes](https://developers.tiktok.com/doc/scopes-overview)

### Пошагово

1. Зарегистрируйтесь на [developers.tiktok.com](https://developers.tiktok.com/) и создайте приложение.
2. Добавьте продукты:
   - **Login Kit** (Web);
   - **Content Posting API**.
3. В Login Kit укажите Redirect URI:

   ```text
   {APP_URL}/oauth/tiktok/callback
   ```

   URI должен быть **HTTPS**, абсолютным, без query/hash.

4. В Content Posting API включите нужный режим (Direct Post / upload — по требованиям кабинета) и дождитесь апрува scopes.
5. Скопируйте:
   - **Client Key** → `TIKTOK_CLIENT_KEY`;
   - **Client Secret** → `TIKTOK_CLIENT_SECRET`.
6. Scopes в Posting: `user.info.basic`, `video.upload`, `video.publish`.
7. Заполните Terms of Service / Privacy Policy URL (часто обязательны для review).
8. Отправьте приложение на проверку TikTok, если нужен доступ не только в Sandbox.

### Прописать в `.env`

```env
TIKTOK_CLIENT_KEY=ваш_client_key
TIKTOK_CLIENT_SECRET=ваш_client_secret
TIKTOK_REDIRECT_URI="${APP_URL}/oauth/tiktok/callback"
```

### Замечания

- `localhost` без HTTPS TikTok обычно не принимает — используйте туннель.
- До апрува `video.publish` публикация может работать только в sandbox TikTok или падать с ошибкой scope.
- В коде по умолчанию privacy может быть `SELF_ONLY` (только себе) — удобно для тестов.

---

## 4. YouTube Shorts (Google / YouTube Data API)

### Документация и кабинеты

- Google Cloud Console: [console.cloud.google.com](https://console.cloud.google.com/)
- Учётные данные YouTube: [Registering an application](https://developers.google.com/youtube/registering_an_application)
- YouTube Data API v3: [Overview](https://developers.google.com/youtube/v3)
- Загрузка видео: [Videos: insert](https://developers.google.com/youtube/v3/docs/videos/insert)
- Настройка OAuth: [Setting up OAuth 2.0](https://support.google.com/googleapi/answer/6158849)

### Пошагово

1. Создайте проект в [Google Cloud Console](https://console.cloud.google.com/).
2. **APIs & Services → Library** → найдите **YouTube Data API v3** → **Enable**.
3. **APIs & Services → OAuth consent screen**:
   - User type: **External** (если нет Google Workspace);
   - укажите название приложения и email;
   - добавьте scopes:
     - `https://www.googleapis.com/auth/youtube.upload`
     - `https://www.googleapis.com/auth/youtube.readonly`
   - в статусе Testing добавьте свой Google-аккаунт в **Test users** (иначе будет «Access blocked»).
4. **APIs & Services → Credentials → Create credentials → OAuth client ID**:
   - Application type: **Web application**;
   - Authorized redirect URIs:

     ```text
     {APP_URL}/oauth/youtube/callback
     ```

5. Скопируйте:
   - **Client ID** → `YOUTUBE_CLIENT_ID`;
   - **Client Secret** → `YOUTUBE_CLIENT_SECRET`.

### Прописать в `.env`

```env
YOUTUBE_CLIENT_ID=xxxxx.apps.googleusercontent.com
YOUTUBE_CLIENT_SECRET=ваш_secret
YOUTUBE_REDIRECT_URI="${APP_URL}/oauth/youtube/callback"
```

### Замечания

- OAuth должен делать тот Google-аккаунт, у которого есть YouTube-канал.
- Пока приложение в Testing, загружать могут только test users.
- Для Shorts нужен вертикальный ролик; в заголовок Posting добавляет `#Shorts`, если его ещё нет.
- Квоты YouTube ограничены — смотрите [quota](https://developers.google.com/youtube/v3/determine_quota_cost) в Cloud Console.

---

## Готовый блок для `.env`

Подставьте свои значения и свой `APP_URL`:

```env
APP_URL=https://your-domain-or-tunnel.example

SOCIAL_SANDBOX=false

INSTAGRAM_CLIENT_ID=
INSTAGRAM_CLIENT_SECRET=
INSTAGRAM_REDIRECT_URI="${APP_URL}/oauth/instagram/callback"
INSTAGRAM_GRAPH_VERSION=v21.0

VK_CLIENT_ID=
VK_CLIENT_SECRET=
VK_REDIRECT_URI="${APP_URL}/oauth/vk/callback"
VK_API_VERSION=5.199

TIKTOK_CLIENT_KEY=
TIKTOK_CLIENT_SECRET=
TIKTOK_REDIRECT_URI="${APP_URL}/oauth/tiktok/callback"

YOUTUBE_CLIENT_ID=
YOUTUBE_CLIENT_SECRET=
YOUTUBE_REDIRECT_URI="${APP_URL}/oauth/youtube/callback"
```

Затем:

```bash
php artisan config:clear
composer run dev
```

Откройте **Соцсети** → для каждой площадки **Подключить через OAuth**. В админке напротив платформы должно быть «ключи заданы».

---

## Чеклист после настройки

| Проверка | Ожидание |
|----------|----------|
| Redirect URI в кабинете = URI в `.env` | Совпадают символ в символ |
| `APP_URL` открывается в браузере | Без редиректа на другой хост |
| Instagram | Professional + Page, публичный HTTPS для медиа |
| VK | Админ группы, scopes `wall,photos,video,offline` |
| TikTok | Login Kit + Content Posting, HTTPS callback |
| YouTube | API v3 enabled, test user, Web OAuth client |
| `SOCIAL_SANDBOX=false` | После успешного OAuth |

---

## Если OAuth не открывается / кнопка серая

1. В админке смотрите, для каких платформ «ключи не заданы» — значит пустые `CLIENT_ID`/`SECRET` в `.env`.
2. `php artisan config:clear` после правок env.
3. Сверьте redirect URI: лишний слэш, `http` vs `https`, другой порт — частая причина `redirect_uri_mismatch`.
4. Для локалки Instagram/TikTok почти всегда нужен туннель, не `http://127.0.0.1:8000`.

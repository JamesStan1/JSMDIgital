# JSM Digital — Website

A professional full-stack website for **JSM Digital**, a custom web and mobile application development agency.

- **Frontend:** Vue 3 + Vite (SPA, Vue Router, scoped styles)
- **Backend:** PHP 8+ REST API (no framework, PDO, rate limiting, email notifications)

---

## Project Structure

```
JSMDigital/
├── frontend/                  # Vue.js application
│   ├── public/
│   │   ├── JSM Digital Logo.png
│   │   └── favicon.svg
│   ├── src/
│   │   ├── assets/main.css    # Global design tokens & utilities
│   │   ├── components/
│   │   │   ├── NavBar.vue
│   │   │   └── FooterSection.vue
│   │   ├── router/index.js
│   │   ├── views/
│   │   │   ├── HomeView.vue
│   │   │   ├── AboutView.vue
│   │   │   ├── ServicesView.vue
│   │   │   ├── PortfolioView.vue
│   │   │   └── ContactView.vue
│   │   ├── App.vue
│   │   └── main.js
│   ├── index.html
│   ├── package.json
│   └── vite.config.js
│
└── backend/                   # PHP API
    ├── api/
    │   ├── contact/index.php  # POST /api/contact
    │   ├── inquiry/index.php  # POST /api/inquiry
    │   └── health/index.php   # GET  /api/health
    ├── config/
    │   ├── config.php         # App constants (reads env vars)
    │   └── database.php       # PDO connection + schema bootstrap
    ├── helpers/
    │   └── functions.php      # Shared utilities (CORS, validation, rate limit, mail)
    ├── schema.sql             # MySQL schema
    ├── .env.example           # Environment variable template
    └── .htaccess              # Apache URL rewriting + security headers
```

---

## Prerequisites

| Tool | Version |
|------|---------|
| Node.js | 18+ |
| npm / pnpm | latest |
| PHP | 8.1+ |
| MySQL / MariaDB | 8.0+ / 10.6+ |
| Apache (with `mod_rewrite`) | 2.4+ |

---

## Frontend Setup

```bash
cd frontend
npm install
npm run dev      # Development server on http://localhost:5173
npm run build    # Production build → frontend/dist/
```

### Environment / Proxy

The Vite dev server proxies `/api/*` requests to `http://localhost:8080` (configurable in `vite.config.js`). In production, point your web server to serve `frontend/dist/` and proxy `/api/` to the PHP backend.

---

## Backend Setup

### 1. Configure environment variables

```bash
cp backend/.env.example backend/.env
```

Edit `backend/.env` with your real values:

```env
APP_ENV=development
APP_URL=http://localhost

DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=jsm_digital
DB_USER=your_db_user
DB_PASSWORD=your_db_password

MAIL_FROM=no-reply@jsmdigital.com
MAIL_TO=hello@jsmdigital.com
```

> **Note:** The PHP config reads these via `getenv()`. Load `.env` in your web server config, via `putenv()` in a bootstrap file, or with a library like `vlucas/phpdotenv`.

### 2. Create the database

```sql
-- Option A: run the included schema file
mysql -u root -p < backend/schema.sql

-- Option B: let the API auto-create tables on first request
-- (ensureSchema() in database.php handles this)
```

### 3. Configure Apache virtual host

```apache
<VirtualHost *:8080>
    DocumentRoot "/path/to/JSMDigital/backend"
    ServerName localhost

    <Directory "/path/to/JSMDigital/backend">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Ensure `mod_rewrite` is enabled:

```bash
a2enmod rewrite
systemctl restart apache2
```

### 4. Verify the API

```bash
curl http://localhost:8080/api/health
# → {"status":"ok","service":"JSM Digital API","timestamp":"..."}
```

---

## API Reference

### `POST /api/contact`

General contact message.

**Request body (JSON):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `name` | string | ✅ | Max 255 chars |
| `email` | string | ✅ | Valid email |
| `subject` | string | ✅ | Max 500 chars |
| `message` | string | ✅ | Min 10 chars |

**Success response:**
```json
{ "success": true, "message": "Message sent successfully..." }
```

---

### `POST /api/inquiry`

Full project inquiry.

**Request body (JSON):**

| Field | Type | Required | Notes |
|-------|------|----------|-------|
| `name` | string | ✅ | |
| `email` | string | ✅ | Valid email |
| `company` | string | ❌ | |
| `phone` | string | ❌ | Digits/spaces/+-(). only |
| `project_type` | string | ✅ | `web-app`, `mobile-app`, `custom-software`, `e-commerce`, `api`, `ui-ux`, `other` |
| `budget` | string | ❌ | `under-5k`, `5k-15k`, `15k-50k`, `50k-100k`, `over-100k`, `not-sure` |
| `timeline` | string | ❌ | `asap`, `1-2-months`, `3-6-months`, `6-plus-months`, `flexible` |
| `description` | string | ✅ | Min 20 chars |

**Success response:**
```json
{ "success": true, "message": "Inquiry submitted successfully..." }
```

---

### `GET /api/health`

```json
{ "status": "ok", "service": "JSM Digital API", "timestamp": "2026-03-08T..." }
```

---

## Rate Limiting

Both form endpoints are limited to **5 requests per IP per 5 minutes**. Exceeding this returns HTTP `429`.

---

## Production Deployment

1. **Build the frontend:**
   ```bash
   cd frontend && npm run build
   ```

2. **Deploy `frontend/dist/`** to your web root (or CDN).

3. **Configure your web server** to:
   - Serve `frontend/dist/index.html` for all non-API routes (SPA fallback).
   - Forward `/api/*` requests to the PHP backend.

4. **Set environment variables** on the server (do not commit `.env`).

5. **Enable HTTPS** — update `APP_URL` and `ALLOWED_ORIGINS` in `backend/config/config.php` accordingly.

---

## Pages

| Page | Route | Description |
|------|-------|-------------|
| Home | `/` | Hero, services overview, process, testimonials, CTA |
| About | `/about` | Company story, mission, values, team |
| Services | `/services` | Detailed service cards, process timeline, engagement models, FAQ |
| Portfolio | `/portfolio` | Filterable project grid, industry list |
| Contact | `/contact` | Contact form + Project inquiry form |

---

## Security Notes

- All user input is sanitised with `strip_tags()` + `trim()` before storage.
- Parameterised PDO statements prevent SQL injection.
- CORS is validated against a strict allowlist.
- Email subjects and headers are safely encoded to prevent header injection.
- Rate limiting prevents form spam and brute-force attempts.
- `.htaccess` blocks direct access to `config/` and `helpers/` directories.
- Security headers (`X-Frame-Options`, `X-Content-Type-Options`, etc.) are set on every response.

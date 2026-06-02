# Deploy entirely inside `htdocs`

Upload **everything** from this **`htdocs`** folder directly into your hosting **`htdocs`** folder (domain web root).

---

## Target layout on the server

```
htdocs/                          ← Your web root (pickleball.synergize.co)
│
├── index.php                    ← Main entry (required)
├── .htaccess                    ← URL rewriting (required)
├── .env                         ← Database config (required)
│
├── config/
├── includes/
├── models/
├── actions/
├── views/
├── assets/
│   ├── css/
│   │   └── app.css
│   └── js/
│       └── app.js
│
└── database.sql                 ← Import in phpMyAdmin only (do not need on server after import)
```

**Do not** put the app inside a subfolder like `htdocs/htdocs/` on the server. Upload the **contents** of this project folder into the hosting `htdocs` root. For `yoursite.com/login`, files must sit at the web root, not in a nested folder.

---

## Step 1: Upload files

In your file manager or FTP:

1. Open **`htdocs`**
2. Upload all folders: `config`, `includes`, `models`, `actions`, `views`, `assets`
3. Upload root files: `index.php`, `.htaccess`, `.env`

> **Hidden files:** Enable “show hidden files” so `.htaccess` and `.env` upload.

---

## Step 2: Import database

1. Hosting panel → **phpMyAdmin**
2. Select database: `if0_39954650_ccp`
3. **Import** → choose `database.sql` → **Go**

---

## Step 3: Edit `.env` in htdocs

```env
APP_URL=https://pickleball.synergize.co
DB_HOST=sql302.infinityfree.com
DB_NAME=if0_39954650_ccp
DB_USER=if0_39954650
DB_PASS=Za3Fu8TbiO
```

Save the file in `htdocs/.env`.

---

## Step 4: Test URLs

| Page | URL |
|------|-----|
| Home / Login | https://pickleball.synergize.co/ |
| Login | https://pickleball.synergize.co/login |
| Dashboard | https://pickleball.synergize.co/dashboard |
| Public display | https://pickleball.synergize.co/display |

**Login:** `admin@courtqueue.com` / `password`

---

## If you see 404 on /login

`.htaccess` may not be active. Try:

- https://pickleball.synergize.co/index.php (should redirect to login)
- Confirm `.htaccess` exists in `htdocs`
- Ask host to enable **mod_rewrite**

---

## If you see blank page or 500 error

1. Check `.env` exists in `htdocs`
2. Confirm database was imported
3. Check PHP version is **7.4+** in hosting panel
4. Temporarily add to `.env` (for debugging only): create `config/debug.php` or enable errors in hosting panel — then remove after fixing

---

## What NOT to upload

| Skip | Reason |
|------|--------|
| Laravel `court-queue-system` folder | Old version, not needed |
| `vendor/` | Plain PHP has no Composer |
| `README.md`, `DEPLOY_*.md` | Optional docs only |
| `database.sql` after import | Optional on server |

---

## Quick checklist

- [ ] All files inside **`htdocs`** (not a subfolder)
- [ ] `index.php` and `.htaccess` at htdocs root
- [ ] `.env` with correct DB credentials
- [ ] `database.sql` imported in phpMyAdmin
- [ ] Visit https://pickleball.synergize.co/login

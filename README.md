# Court Queue System (Plain PHP)

**Folder name: `htdocs`** — upload this entire folder to your hosting `htdocs` (web root).

**No Laravel. No Composer. No vendor folder.**  
Works on InfinityFree, synergize.co, and any PHP + MySQL hosting.

---

## Requirements

- PHP 7.4+ (8.x recommended)
- MySQL
- mod_rewrite (optional but recommended)

---

## Deploy on pickleball.synergize.co (all inside `htdocs`)

Upload **every file and folder** from this **`htdocs`** project folder **into your hosting `htdocs`** (web root) — not into a subfolder.

```
htdocs/
├── index.php
├── .htaccess
├── .env
├── config/
├── includes/
├── models/
├── actions/
├── views/
└── assets/
```

See **`DEPLOY_HTDOCS.md`** for the full step-by-step guide.

### 1. Upload files

Open your hosting **`htdocs`** folder and upload all project files there (domain root).

### 2. Import database

1. Open **phpMyAdmin**
2. Select database `if0_39954650_ccp`
3. Import `database.sql`

### 3. Configure `.env`

Edit `.env` (already included with your DB credentials):

```env
APP_URL=https://pickleball.synergize.co
DB_HOST=sql302.infinityfree.com
DB_NAME=if0_39954650_ccp
DB_USER=if0_39954650
DB_PASS=your_password
```

### 4. Enable URL rewriting

Ensure `.htaccess` is uploaded. If links break, access via `index.php?` paths won't work — contact host to enable mod_rewrite.

### 5. Login

| Email | Password |
|-------|----------|
| admin@courtqueue.com | password |

**Change the admin password after first login** (edit in phpMyAdmin users table using `password_hash`).

---

## Features

- Dashboard with court/queue stats
- Court CRUD
- Queue management (call, serve, complete, skip, recall, cancel)
- Player management
- Public display screen (`/display`)
- Reports with CSV export
- User management (admin)
- System settings (admin)
- Role-based access (admin, staff, operator)

---

## Folder structure

```
court-queue-plain/
├── index.php          # Front controller
├── .htaccess          # URL rewriting
├── .env               # Configuration
├── database.sql       # Database schema + seed data
├── config/
├── includes/          # Auth, helpers, CSRF
├── models/            # PDO data layer
├── actions/           # Route handlers
├── views/             # HTML templates
└── assets/            # CSS & JS
```

---

## vs Laravel version

| Laravel version | Plain PHP version |
|-----------------|-------------------|
| Needs `vendor/` (~50MB) | No dependencies |
| Needs Composer | Upload and run |
| Needs `artisan` | Import SQL only |
| PHP 8.2+ | PHP 7.4+ |

Use **court-queue-plain** for free shared hosting.

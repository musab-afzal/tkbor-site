# Production Deployment Guide

This guide provides exact, production-safe deployment instructions for your Laravel application on Linux live servers (Hostinger, cPanel, VPS, or dedicated hosting).

---

## 1. Production Architecture Overview

This project has a flexible structure:
- **Core Laravel Application**: Located in `system/` (contains `app`, `config`, `storage`, `vendor`, `bootstrap`, etc.).
- **Web Document Root**:
  - **Case A (Standard Hosting / VPS / Subdomain)**: Web root points directly to `system/public/` or the repository root.
  - **Case B (Hostinger / Shared Hosting `public_html`)**: Web root is `public_html/`.

---

## 2. Environment Configuration (`.env`)

In your production `.env` file (located at `system/.env`):

```env
APP_NAME="TKBOR"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_secure_password

# Filesystem
FILESYSTEM_DISK=local

# If deploying to Hostinger/cPanel where public_html is separate from system/:
# PUBLIC_PATH=/home/username/public_html
```

> [!IMPORTANT]
> - Ensure `APP_URL` uses `https://` and your exact live domain without a trailing slash.
> - Never set `APP_DEBUG=true` on a live production server.

---

## 3. Directory Permissions

Set safe Linux permissions. Do **not** use `777`.

Run these commands from your Laravel root directory on the server:

```bash
# Set base directory permissions
find . -type d -exec chmod 755 {} \;

# Set base file permissions
find . -type f -exec chmod 644 {} \;

# Ensure storage and bootstrap/cache are writable by web server (www-data / nobody / cPanel user)
chmod -R 775 system/storage
chmod -R 775 system/bootstrap/cache

# If on VPS (Apache/Nginx user ownership, e.g. www-data):
# chown -R www-data:www-data system/storage system/bootstrap/cache
```

---

## 4. Production Storage Symlink Setup

Public images (logos, sliders, services, attachments) are stored physically in:
`system/storage/app/public/`

For the web server to serve them directly at `/storage/...`, a symbolic link named `storage` must exist in the web document root pointing to `system/storage/app/public`.

### Case A: Web Document Root is `public/` or Repository Root

1. Navigate to the `system/` directory:
   ```bash
   cd system
   ```
2. Run the storage link command:
   ```bash
   php artisan storage:link
   ```
   *Note: Our updated `config/filesystems.php` automatically links both `system/public/storage` and the repository root `storage`.*

### Case B: Shared Hosting / Hostinger `public_html`

If your Laravel application files are in `/home/username/misiom/system` and your web root is `/home/username/public_html`:

1. Set `PUBLIC_PATH` in `system/.env`:
   ```env
   PUBLIC_PATH=/home/username/public_html
   ```
2. Run `php artisan storage:link`:
   ```bash
   cd system
   php artisan storage:link
   ```
3. Or manually create the symlink via SSH if needed:
   ```bash
   ln -s /home/username/misiom/system/storage/app/public /home/username/public_html/storage
   ```

---

## 5. How to Fix Broken Symlinks

If `php artisan storage:link` outputs:
`The [...] link already exists.`
or if the image displays broken/404:

### Check the existing link
From the web root (or `system/`):
```bash
ls -la storage
# or
ls -la public/storage
```

A healthy symlink will show:
```text
lrwxrwxrwx 1 user group ... storage -> /path/to/system/storage/app/public
```

If it shows a Windows path (e.g. `C:\xampp2\...`) or points to a non-existent path:

### SAFE Link Removal Procedure
> [!CAUTION]
> **NEVER** run `rm -rf system/storage/app/public`! That directory contains your actual uploaded images!
> Only remove the symlink file itself using `rm`:

```bash
# Safe removal of the broken symlink only:
rm public/storage
# or from root:
rm storage

# Then re-create the link:
cd system
php artisan storage:link
```

---

## 6. Run the Built-In Storage Diagnostic Command

We created an artisan command specifically to verify your production setup:

```bash
cd system
php artisan app:check-storage
```

This command automatically tests:
1. `APP_ENV` and `APP_URL`.
2. Whether the `'local'` disk `serve` route is disabled (preventing 403 errors).
3. Physical existence and write permissions of `system/storage/app/public`.
4. Symlink validity and target paths.
5. Storage and cache directory permissions.
6. Sample image URL resolution (checking for no double `/storage/` prefixes).

If any check fails, it provides the exact fix command.

---

## 7. Production Optimization & Cache Commands

Always run these commands after deploying updates or editing `.env`:

```bash
cd system

# 1. Clear all old caches
php artisan optimize:clear

# 2. Re-create storage symlink (if not already linked)
php artisan storage:link

# 3. Cache configuration and routes for maximum performance
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 8. Live Server Verification Checklist

After deployment, perform these verification steps:

1. **Run Diagnostic Command**:
   ```bash
   php system/artisan app:check-storage
   ```
   Ensure output ends with: `✓ All core storage checks PASSED! Project is ready.`

2. **Verify File on Disk**:
   Upload a logo or image from the Admin Panel (`Admin -> Settings -> Website Settings` or `General Settings`).
   Verify the file was created:
   ```bash
   ls -la system/storage/app/public/settings/
   ```

3. **Verify Direct URL in Incognito Browser**:
   Open a private/incognito browser window and visit the image URL directly:
   ```text
   https://your-domain.com/storage/settings/<uploaded-filename>.png
   ```
   **Expected Result**:
   - HTTP 200 OK.
   - The image is displayed directly.
   - **NOT** HTTP 403 Forbidden.
   - **NOT** "You do not have permission to view this page".

4. **Verify Frontend Display**:
   Visit the public homepage `https://your-domain.com/`.
   Verify the logo renders clearly in both the header and footer without broken-image icons.

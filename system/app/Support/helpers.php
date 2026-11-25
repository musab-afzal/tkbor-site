<?php

use App\Services\Settings;

if (! function_exists('settings')) {
    /**
     * Agency settings accessor. Call with no arguments for the service itself.
     */
    function settings(?string $key = null, mixed $default = null): mixed
    {
        $service = app(Settings::class);

        return $key === null ? $service : $service->get($key, $default);
    }
}

if (! function_exists('money')) {
    /** Format an amount using the agency currency (or an explicit one). */
    function money(float|int|string|null $amount, ?string $currency = null): string
    {
        return app(Settings::class)->money((float) $amount, $currency);
    }
}

if (! function_exists('uploaded_url')) {
    /**
     * Public URL for a file stored on the public disk.
     * Safely normalizes paths to prevent double prefixes, handles full external URLs,
     * strips legacy/local prefixes, and returns a clean root-relative /storage/... URL.
     */
    function uploaded_url(?string $path, ?string $fallback = null): ?string
    {
        if (! $path || trim($path) === '') {
            return $fallback;
        }

        $trimmed = trim($path);

        // If it's already an absolute web URL
        if (preg_match('#^https?://#i', $trimmed)) {
            // If it's a localhost or old development URL pointing to /storage/..., strip domain to make it root-relative
            if (preg_match('#^https?://(?:localhost|127\.0\.0\.1|0\.0\.0\.0|[^/]+\.test|[^/]+\.local)(?::\d+)?(/.*)$#i', $trimmed, $matches)) {
                $trimmed = $matches[1];
            } else {
                return $trimmed;
            }
        }

        // Normalize Windows backslashes
        $clean = str_replace(['\\', '/'], '/', $trimmed);

        // If it's an absolute Windows path (e.g. C:/xampp/htdocs/.../storage/app/public/...)
        if (preg_match('#^[a-zA-Z]:/#', $clean)) {
            if (preg_match('#/storage/app/public/(.+)$#i', $clean, $matches)) {
                $clean = $matches[1];
            } elseif (preg_match('#/public/storage/(.+)$#i', $clean, $matches)) {
                $clean = $matches[1];
            } else {
                return $fallback;
            }
        }

        // Strip leading slashes
        $clean = ltrim($clean, '/');

        // Strip redundant storage/public prefixes to prevent /storage/storage/...
        if (str_starts_with($clean, 'storage/app/public/')) {
            $clean = substr($clean, strlen('storage/app/public/'));
        } elseif (str_starts_with($clean, 'app/public/')) {
            $clean = substr($clean, strlen('app/public/'));
        } elseif (str_starts_with($clean, 'public/storage/')) {
            $clean = substr($clean, strlen('public/storage/'));
        } elseif (str_starts_with($clean, 'storage/')) {
            $clean = substr($clean, strlen('storage/'));
        }

        $clean = ltrim($clean, '/');

        if ($clean === '') {
            return $fallback;
        }

        return asset('storage/'.$clean);
    }
}

if (! function_exists('placeholder_image')) {
    /**
     * URL for one of the theme's placeholder images, cycling by index. The
     * bundled files mix .jpg and .png, so the extension is resolved against
     * what is actually on disk rather than assumed.
     */
    function placeholder_image(string $pattern, int $index, int $count = 3, string $default = 'assets/images/logo-light.png'): string
    {
        $number = ($index % max($count, 1)) + 1;
        $base = str_replace('%d', (string) $number, $pattern);

        foreach (['jpg', 'jpeg', 'png', 'webp'] as $extension) {
            $relative = $base.'.'.$extension;

            foreach ([public_path($relative), base_path('../'.$relative)] as $candidate) {
                if (is_file($candidate)) {
                    return asset($relative);
                }
            }
        }

        return asset($default);
    }
}

if (! function_exists('versioned_asset')) {
    /**
     * Asset URL stamped with the file's modification time, so a browser that
     * cached an older copy of our CSS or JS fetches the current one.
     */
    function versioned_asset(string $path): string
    {
        $relative = ltrim($path, '/');

        foreach ([public_path($relative), base_path('../'.$relative)] as $candidate) {
            if (is_file($candidate)) {
                return asset($relative).'?v='.filemtime($candidate);
            }
        }

        return asset($relative);
    }
}
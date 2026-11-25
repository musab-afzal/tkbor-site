<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CheckStorageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-storage {--sample=settings/tkbor-site-logo.png : Sample image path to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect and verify Laravel storage configuration, symlinks, permissions, and image URLs for production';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->info('======================================================');
        $this->info('       Laravel Production Storage Health Check        ');
        $this->info('======================================================');
        $this->newLine();

        $allOk = true;

        // 1. Check Environment & URLs
        $this->comment('--- 1. Environment & Configuration ---');
        $appEnv = config('app.env');
        $appUrl = config('app.url');
        $this->line("• APP_ENV: <fg=cyan>{$appEnv}</>");
        $this->line("• APP_URL: <fg=cyan>{$appUrl}</>");

        if ($appEnv === 'production' && (str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1'))) {
            $this->warn("  [WARNING] APP_URL is set to localhost in production. Please update .env to your live domain.");
            $allOk = false;
        }

        $defaultDisk = config('filesystems.default');
        $this->line("• Default Filesystem Disk: <fg=cyan>{$defaultDisk}</>");

        $localServe = config('filesystems.disks.local.serve', false);
        if ($localServe) {
            $this->error("  [ERROR] 'local' disk has 'serve' => true without custom URL. This will hijack /storage/* and cause 403 errors!");
            $allOk = false;
        } else {
            $this->line("  [OK] 'local' disk 'serve' is disabled (will not hijack /storage/*).");
        }

        $this->newLine();

        // 2. Check Physical Storage Target Directory
        $this->comment('--- 2. Physical Storage Directory ---');
        $publicStoragePath = storage_path('app/public');
        $this->line("• Target path: <fg=cyan>{$publicStoragePath}</>");

        if (! is_dir($publicStoragePath)) {
            $this->error("  [FAIL] Directory does not exist: {$publicStoragePath}");
            $this->line("  Fix: mkdir -p {$publicStoragePath}");
            $allOk = false;
        } else {
            $this->line("  [OK] Directory exists.");
            if (is_writable($publicStoragePath)) {
                $this->line("  [OK] Directory is writable.");
            } else {
                $this->error("  [FAIL] Directory is NOT writable by PHP/web-server user.");
                $this->line("  Fix: chmod -R 775 storage");
                $allOk = false;
            }
        }

        $this->newLine();

        // 3. Check Symlinks
        $this->comment('--- 3. Public Storage Symlinks ---');
        $links = config('filesystems.links', []);
        
        clearstatcache();
        foreach ($links as $link => $target) {
            $this->line("• Checking link: <fg=cyan>{$link}</>");
            $this->line("  Expected target: <fg=cyan>{$target}</>");

            clearstatcache(true, $link);
            $readlinkResult = @readlink($link);
            $isSymlink = is_link($link) || ($readlinkResult !== false && $readlinkResult !== $link);
            $exists = file_exists($link);
            $isDir = is_dir($link);

            if ($isSymlink) {
                $actualTarget = $readlinkResult ?: readlink($link);
                $this->line("  Link type: " . (PHP_OS_FAMILY === 'Windows' ? 'Symlink / NTFS Junction' : 'Symbolic link'));
                $this->line("  Actual link points to: <fg=cyan>{$actualTarget}</>");

                $targetReal = realpath($target);
                $actualReal = realpath($actualTarget) ?: realpath($link);

                if ($targetReal && $actualReal && $targetReal === $actualReal) {
                    $this->line("  [OK] Link is valid and points to the correct target.");
                } elseif (! $exists) {
                    $this->error("  [FAIL] Broken symlink! Target does not exist on this filesystem.");
                    $this->line("  Fix: rm \"{$link}\" && php artisan storage:link");
                    $allOk = false;
                } else {
                    $this->warn("  [WARNING] Link points to: {$actualTarget}, expected: {$target}");
                }
            } elseif ($isDir) {
                $realLink = realpath($link);
                $realTarget = realpath($target);
                if ($realLink && $realTarget && $realLink === $realTarget) {
                    $this->line("  [OK] Directory matches target.");
                } else {
                    $this->warn("  [NOTE] '{$link}' is a real directory, not a symlink. If images are not shared with storage/app/public, recreate it with: php artisan storage:link");
                }
            } else {
                $this->error("  [FAIL] Symlink does not exist at {$link}");
                $this->line("  Fix: php artisan storage:link");
                $allOk = false;
            }
        }

        $this->newLine();

        // 4. Writable Cache & Storage Directories
        $this->comment('--- 4. Directory Permissions ---');
        $dirsToCheck = [
            'storage' => storage_path(),
            'storage/framework' => storage_path('framework'),
            'storage/logs' => storage_path('logs'),
            'bootstrap/cache' => app()->bootstrapPath('cache'),
        ];

        foreach ($dirsToCheck as $label => $dir) {
            if (! is_dir($dir)) {
                $this->warn("• {$label}: does not exist");
                $allOk = false;
            } elseif (is_writable($dir)) {
                $this->line("• {$label}: <fg=green>Writable [OK]</>");
            } else {
                $this->error("• {$label}: <fg=red>NOT writable [FAIL]</>");
                $this->line("  Fix: chmod -R 775 {$dir}");
                $allOk = false;
            }
        }

        $this->newLine();

        // 5. Test Sample Image URL Generation
        $this->comment('--- 5. Sample Image URL Resolution ---');
        $samplePath = $this->option('sample');
        $sampleUrl = uploaded_url($samplePath);
        $this->line("• Sample path: <fg=cyan>{$samplePath}</>");
        $this->line("• Generated URL: <fg=cyan>{$sampleUrl}</>");

        // Verify URL format
        if (str_contains($sampleUrl, '/storage/storage/')) {
            $this->error("  [FAIL] Double /storage/ prefix detected in generated URL!");
            $allOk = false;
        } else {
            $this->line("  [OK] URL format is clean (no double prefix).");
        }

        // Check if physical file exists for sample
        $samplePhysical = storage_path('app/public/' . ltrim($samplePath, '/'));
        if (file_exists($samplePhysical)) {
            $this->line("• Physical file status: <fg=green>Found on disk ({$samplePhysical})</>");
        } else {
            $this->line("• Physical file status: <fg=yellow>Not found on disk ({$samplePhysical})</> (Will be created when uploaded from Admin)");
        }

        $this->newLine();
        $this->info('======================================================');
        if ($allOk) {
            $this->info('  ✓ All core storage checks PASSED! Project is ready. ');
        } else {
            $this->warn('  ! Some issues were detected. Please review recommendations above.');
        }
        $this->info('======================================================');
        $this->newLine();

        return $allOk ? Command::SUCCESS : Command::FAILURE;
    }
}

<?php

namespace Amplify\System\Cms\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\File;

class ThemeCheckUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds before retrying.
     */
    public int $backoff = 60;

    /**
     * Prevent overlapping theme updates.
     */
    public function middleware(): array
    {
        return [
            new \Illuminate\Queue\Middleware\WithoutOverlapping('theme-update'),
        ];
    }

    public function handle(): void
    {
        $themePath = base_path('theme');
        $temporaryPath = base_path('theme_new');

        $repository = config('theme.github.repository');
        $branch = config('theme.github.branch', 'main');

        if (!$repository) {
            throw new RuntimeException(
                'Theme GitHub repository is not configured.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 1. Get latest GitHub release
         * ---------------------------------------------------------
         */

        $release = $this->getLatestRelease($repository);

        $version = $release['tag_name'] ?? null;

        if (!$version) {
            throw new RuntimeException(
                'GitHub release does not contain a tag name.'
            );
        }

        /*
         * ---------------------------------------------------------
         * 2. Check currently installed version
         * ---------------------------------------------------------
         */

        $currentVersion = $this->getCurrentVersion($themePath);

        if ($currentVersion === $version) {
            Log::info('Theme is already up to date.', [
                'version' => $version,
            ]);

            return;
        }

        /*
         * ---------------------------------------------------------
         * 3. Find release asset
         * ---------------------------------------------------------
         */

        $asset = $this->findReleaseAsset($release);

        if (!$asset) {
            throw new RuntimeException(
                "No theme asset found in GitHub release {$version}."
            );
        }

        /*
         * ---------------------------------------------------------
         * 4. Clean previous temporary directory
         * ---------------------------------------------------------
         */

        if (File::exists($temporaryPath)) {
            File::deleteDirectory($temporaryPath);
        }

        File::makeDirectory($temporaryPath, 0755, true);

        /*
         * ---------------------------------------------------------
         * 5. Download release asset
         * ---------------------------------------------------------
         */

        $archivePath = $temporaryPath . '/theme.zip';

        $this->downloadAsset(
            $asset['browser_download_url'],
            $archivePath
        );

        /*
         * ---------------------------------------------------------
         * 6. Extract release
         * ---------------------------------------------------------
         */

        $extractedPath = $temporaryPath . '/extracted';

        File::makeDirectory($extractedPath, 0755, true);

        $this->extractZip(
            $archivePath,
            $extractedPath
        );

        /*
         * ---------------------------------------------------------
         * 7. Locate actual theme directory
         * ---------------------------------------------------------
         *
         * Depending on how the GitHub ZIP is created, the files
         * might be:
         *
         * extracted/
         *     theme/
         *         ...
         *
         * or:
         *
         * extracted/
         *     ...
         */

        $newThemePath = $this->resolveThemeDirectory(
            $extractedPath
        );

        /*
         * ---------------------------------------------------------
         * 8. Validate theme
         * ---------------------------------------------------------
         */

        $this->validateTheme($newThemePath);

        /*
         * ---------------------------------------------------------
         * 9. Rename current theme to archive
         * ---------------------------------------------------------
         */

        $archiveThemePath = null;

        if (File::exists($themePath)) {
            $archiveThemePath = base_path(
                'theme_archive_' .
                ($currentVersion ?: now()->format('Ymd_His'))
            );

            /*
             * Avoid collision.
             */
            $archiveThemePath = $this->uniquePath(
                $archiveThemePath
            );

            File::move(
                $themePath,
                $archiveThemePath
            );
        }

        /*
         * ---------------------------------------------------------
         * 10. Move new theme into /theme
         * ---------------------------------------------------------
         */

        try {
            File::move(
                $newThemePath,
                $themePath
            );
        } catch (Throwable $e) {

            /*
             * New theme could not be activated.
             *
             * Restore previous theme.
             */
            if (
                $archiveThemePath &&
                File::exists($archiveThemePath) &&
                !File::exists($themePath)
            ) {
                File::move(
                    $archiveThemePath,
                    $themePath
                );
            }

            throw $e;
        }

        /*
         * ---------------------------------------------------------
         * 11. Store installed version
         * ---------------------------------------------------------
         */

        $this->storeVersion(
            $themePath,
            $version
        );

        /*
         * ---------------------------------------------------------
         * 12. Remove temporary files
         * ---------------------------------------------------------
         */

        if (File::exists($temporaryPath)) {
            File::deleteDirectory($temporaryPath);
        }

        /*
         * ---------------------------------------------------------
         * 13. Remove old archive
         * ---------------------------------------------------------
         *
         * Keep only the archive created by this update.
         */

        $this->removeOldArchives(
            $archiveThemePath
        );

        Log::info('Theme updated successfully.', [
            'old_version' => $currentVersion,
            'new_version' => $version,
            'archive' => $archiveThemePath,
        ]);
    }

    /**
     * Get latest GitHub release.
     */
    protected function getLatestRelease(string $repository): array
    {
        $url = "https://api.github.com/repos/{$repository}/releases/latest";

        $request = Http::acceptJson()
            ->withHeaders([
                'User-Agent' => config(
                    'app.name',
                    'Laravel'
                ),
            ])
            ->timeout(30);

        /*
         * For private repositories:
         */
        if ($token = config('theme.github.token')) {
            $request->withToken($token);
        }

        $response = $request->get($url);

        $response->throw();

        return $response->json();
    }

    /**
     * Find the theme asset from the release.
     */
    protected function findReleaseAsset(array $release): ?array
    {
        $assetName = config(
            'theme.github.asset',
            'theme.zip'
        );

        foreach ($release['assets'] ?? [] as $asset) {
            if (($asset['name'] ?? null) === $assetName) {
                return $asset;
            }
        }

        return null;
    }

    /**
     * Download GitHub release asset.
     */
    protected function downloadAsset(
        string $url,
        string $destination
    ): void {
        $request = Http::timeout(300)
            ->connectTimeout(30)
            ->withOptions([
                'sink' => $destination,
            ]);

        if ($token = config('theme.github.token')) {
            $request = $request->withToken($token);
        }

        $response = $request->get($url);

        $response->throw();

        if (
            !File::exists($destination) ||
            File::size($destination) === 0
        ) {
            throw new RuntimeException(
                'Downloaded theme asset is empty.'
            );
        }
    }

    /**
     * Extract ZIP archive.
     */
    protected function extractZip(
        string $archivePath,
        string $destination
    ): void {
        $zip = new ZipArchive();

        $result = $zip->open($archivePath);

        if ($result !== true) {
            throw new RuntimeException(
                "Unable to open theme ZIP. Error code: {$result}"
            );
        }

        if (!$zip->extractTo($destination)) {
            $zip->close();

            throw new RuntimeException(
                'Unable to extract theme ZIP.'
            );
        }

        $zip->close();
    }

    /**
     * Find the actual theme directory.
     */
    protected function resolveThemeDirectory(
        string $extractedPath
    ): string {
        /*
         * If ZIP contains:
         *
         * extracted/theme/...
         */
        $themeDirectory = $extractedPath . '/theme';

        if (File::isDirectory($themeDirectory)) {
            return $themeDirectory;
        }

        /*
         * If ZIP itself contains the theme files.
         */
        if (
            File::exists($extractedPath . '/theme.json') ||
            File::exists($extractedPath . '/manifest.json')
        ) {
            return $extractedPath;
        }

        /*
         * GitHub automatically generated archives can have:
         *
         * repository-name-tag/
         */
        $directories = File::directories(
            $extractedPath
        );

        if (count($directories) === 1) {
            return $directories[0];
        }

        throw new RuntimeException(
            'Unable to determine theme directory from release asset.'
        );
    }

    /**
     * Validate the downloaded theme.
     */
    protected function validateTheme(
        string $themePath
    ): void {
        if (!File::isDirectory($themePath)) {
            throw new \RuntimeException(
                'Downloaded theme directory does not exist.'
            );
        }

        /*
         * Adjust these according to your theme.
         */
        $requiredFiles = [
            'theme.json',
        ];

        foreach ($requiredFiles as $file) {
            if (!File::exists($themePath . '/' . $file)) {
                throw new RuntimeException(
                    "Theme validation failed. Missing: {$file}"
                );
            }
        }

        /*
         * Validate theme.json.
         */
        $themeConfig = json_decode(
            File::get($themePath . '/theme.json'),
            true
        );

        if (!is_array($themeConfig)) {
            throw new RuntimeException(
                'Invalid theme.json.'
            );
        }
    }

    /**
     * Read currently installed theme version.
     */
    protected function getCurrentVersion(
        string $themePath
    ): ?string {
        $versionFile = $themePath . '/theme.json';

        if (!File::exists($versionFile)) {
            return null;
        }

        $config = json_decode(
            File::get($versionFile),
            true
        );

        return $config['version'] ?? null;
    }

    /**
     * Store version inside theme.
     */
    protected function storeVersion(
        string $themePath,
        string $version
    ): void {
        $versionFile = $themePath . '/theme.json';

        if (!File::exists($versionFile)) {
            return;
        }

        $config = json_decode(
            File::get($versionFile),
            true
        );

        if (!is_array($config)) {
            return;
        }

        $config['version'] = $version;

        File::put(
            $versionFile,
            json_encode(
                $config,
                JSON_PRETTY_PRINT |
                JSON_UNESCAPED_SLASHES
            ) . PHP_EOL
        );
    }

    /**
     * Generate unique path if the archive already exists.
     */
    protected function uniquePath(string $path): string
    {
        if (!File::exists($path)) {
            return $path;
        }

        $counter = 1;

        while (
        File::exists($path . '_' . $counter)
        ) {
            $counter++;
        }

        return $path . '_' . $counter;
    }

    /**
     * Keep only the latest archive.
     */
    protected function removeOldArchives(
        ?string $currentArchive
    ): void {
        $basePath = base_path();

        foreach (
            File::glob(
                $basePath . '/theme_archive_*'
            ) as $archive
        ) {
            if ($archive === $currentArchive) {
                continue;
            }

            if (File::isDirectory($archive)) {
                File::deleteDirectory($archive);
            } else {
                File::delete($archive);
            }
        }
    }

    /**
     * Handle permanently failed job.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error(
            'Theme update failed.',
            [
                'message' => $exception->getMessage(),
                'exception' => $exception,
            ]
        );
    }
}

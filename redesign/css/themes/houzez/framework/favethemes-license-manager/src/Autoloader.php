<?php

namespace FavethemesLicenseManager;

/**
 * PSR-4 Autoloader for Favethemes License Manager
 */
class Autoloader
{
    private const NAMESPACE_PREFIX = 'FavethemesLicenseManager\\';

    private const BASE_DIR = __DIR__.'/';

    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        // Check if class uses our namespace
        $len = strlen(self::NAMESPACE_PREFIX);
        if (strncmp(self::NAMESPACE_PREFIX, $class, $len) !== 0) {
            return;
        }

        // Get relative class name
        $relativeClass = substr($class, $len);

        // Replace namespace separators with directory separators
        $file = self::BASE_DIR.str_replace('\\', '/', $relativeClass).'.php';

        // Load file if exists
        if (file_exists($file)) {
            require $file;
        }
    }
}

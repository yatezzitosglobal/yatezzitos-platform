<?php

namespace FavethemesLicenseManager\Core;

/**
 * Detects the active Favethemes product.
 *
 * The theme must define FLM_PRODUCT_SLUG constant in its functions.php.
 * This is the source of truth - no complex detection needed.
 *
 * Example in Houzez theme's functions.php:
 *   define('FLM_PRODUCT_SLUG', 'houzez');
 *
 * Example in Homey theme's functions.php:
 *   define('FLM_PRODUCT_SLUG', 'homey');
 */
class ThemeDetector
{
    /**
     * Get the product slug.
     *
     * @return string The product slug (e.g., 'houzez', 'homey')
     */
    public static function getSlug(): string
    {
        // Theme defines this constant in functions.php
        if (defined('FLM_PRODUCT_SLUG')) {
            return FLM_PRODUCT_SLUG;
        }

        // Fallback for legacy installs (before theme update adds the constant)
        return 'houzez';
    }

    /**
     * Get the theme's filesystem slug.
     *
     * Returns the actual theme folder name (handles child themes).
     *
     * @return string The theme folder name
     */
    public static function getThemeSlug(): string
    {
        $theme = wp_get_theme();

        // If child theme, get parent's slug
        if ($theme->parent()) {
            return $theme->parent()->get_stylesheet();
        }

        return $theme->get_stylesheet();
    }

    /**
     * Check if the current product matches a specific slug.
     *
     * @param  string  $productSlug  Product slug to check
     * @return bool True if current product matches
     */
    public static function isProduct(string $productSlug): bool
    {
        return self::getSlug() === $productSlug;
    }
}

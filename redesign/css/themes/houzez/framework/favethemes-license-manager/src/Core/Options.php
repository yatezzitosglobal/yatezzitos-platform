<?php

namespace FavethemesLicenseManager\Core;

/**
 * Encrypted options storage for license data
 */
class Options
{
    private const LICENSE_OPTION = 'flm_license_data';

    private const ACTIVATION_OPTION = 'flm_activation_data';

    private const USER_OPTION = 'flm_user_data';

    private const CACHE_OPTION = 'flm_update_cache';

    /**
     * Get encryption key derived from WordPress salts
     */
    private function getEncryptionKey(): string
    {
        $key = '';
        if (defined('AUTH_KEY')) {
            $key .= AUTH_KEY;
        }
        if (defined('SECURE_AUTH_KEY')) {
            $key .= SECURE_AUTH_KEY;
        }
        if (defined('LOGGED_IN_KEY')) {
            $key .= LOGGED_IN_KEY;
        }

        return hash('sha256', $key, true);
    }

    /**
     * Encrypt data before storing
     */
    private function encrypt(array $data): string
    {
        $json = wp_json_encode($data);
        $key = $this->getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(16);

        $encrypted = openssl_encrypt(
            $json,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return base64_encode($iv.$encrypted);
    }

    /**
     * Decrypt stored data
     */
    private function decrypt(string $data): ?array
    {
        if (empty($data)) {
            return null;
        }

        $decoded = base64_decode($data);
        if ($decoded === false || strlen($decoded) < 17) {
            return null;
        }

        $key = $this->getEncryptionKey();
        $iv = substr($decoded, 0, 16);
        $encrypted = substr($decoded, 16);

        $decrypted = openssl_decrypt(
            $encrypted,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($decrypted === false) {
            return null;
        }

        return json_decode($decrypted, true);
    }

    /**
     * Get license data
     */
    public function getLicense(): array
    {
        $data = get_option(self::LICENSE_OPTION, '');
        $decrypted = $this->decrypt($data);

        return $decrypted ?? [
            'source' => '',
            'license_id' => '',
            'purchase_code' => '',
            'envato_username' => '',
            'plan_code' => '',
            'status' => '',
            'expires_at' => '',
            'purchased_at' => '',
        ];
    }

    /**
     * Save license data
     */
    public function saveLicense(array $data): bool
    {
        $encrypted = $this->encrypt($data);

        return update_option(self::LICENSE_OPTION, $encrypted);
    }

    /**
     * Get activation data
     */
    public function getActivation(): array
    {
        $data = get_option(self::ACTIVATION_OPTION, '');
        $decrypted = $this->decrypt($data);

        return $decrypted ?? [
            'activation_id' => '',
            'token' => '',
            'domain' => '',
            'environment' => '',
            'activated_at' => '',
            'fingerprint' => '',
        ];
    }

    /**
     * Save activation data
     */
    public function saveActivation(array $data): bool
    {
        $encrypted = $this->encrypt($data);

        return update_option(self::ACTIVATION_OPTION, $encrypted);
    }

    /**
     * Get user data
     */
    public function getUser(): array
    {
        $data = get_option(self::USER_OPTION, '');
        $decrypted = $this->decrypt($data);

        return $decrypted ?? [
            'email' => '',
        ];
    }

    /**
     * Save user data
     */
    public function saveUser(array $data): bool
    {
        $encrypted = $this->encrypt($data);

        return update_option(self::USER_OPTION, $encrypted);
    }

    /**
     * Clear all license and activation data
     */
    public function clearAll(): void
    {
        delete_option(self::LICENSE_OPTION);
        delete_option(self::ACTIVATION_OPTION);
        delete_option(self::USER_OPTION);
        delete_option(self::CACHE_OPTION);
    }

    /**
     * Check if site is activated
     */
    public function isActivated(): bool
    {
        $activation = $this->getActivation();

        return ! empty($activation['token']) && ! empty($activation['activation_id']);
    }

    /**
     * Get update cache
     */
    public function getUpdateCache(): ?array
    {
        $cache = get_transient(self::CACHE_OPTION);

        return $cache ?: null;
    }

    /**
     * Set update cache (7 days for fail-open)
     */
    public function setUpdateCache(array $data, int $expiration = DAY_IN_SECONDS): bool
    {
        return set_transient(self::CACHE_OPTION, $data, $expiration);
    }

    /**
     * Generate site fingerprint
     */
    public function generateFingerprint(): string
    {
        $components = [
            defined('WP_SITEURL') ? WP_SITEURL : get_site_url(),
            defined('DB_NAME') ? DB_NAME : '',
            defined('AUTH_KEY') ? AUTH_KEY : '',
        ];

        return hash('sha256', implode('|', $components));
    }

    /**
     * Get current domain (normalized)
     */
    public function getCurrentDomain(): string
    {
        $url = get_site_url();
        $parsed = parse_url($url);
        $host = $parsed['host'] ?? '';

        // Remove www prefix
        $host = preg_replace('/^www\./', '', $host);

        return strtolower($host);
    }

    /**
     * Detect if this is likely a staging environment
     */
    public function detectStagingEnvironment(): bool
    {
        $domain = $this->getCurrentDomain();

        $stagingPatterns = [
            '/^staging\./i',
            '/^dev\./i',
            '/^test\./i',
            '/^local\./i',
            '/\.staging\./i',
            '/\.dev\./i',
            '/\.test$/i',
            '/\.local$/i',
            '/\.localhost$/i',
            '/localhost/i',
            '/127\.0\.0\.1/',
            '/\.ngrok\.io$/i',
            '/\.lndo\.site$/i',
            '/\.ddev\.site$/i',
        ];

        foreach ($stagingPatterns as $pattern) {
            if (preg_match($pattern, $domain)) {
                return true;
            }
        }

        return false;
    }
}

<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

namespace App\Framework;

class FrameworkInitializer
{
    /**
     * Initialize framework configuration based on environment variables
     *
     * @return string Output log of initialization process
     */
    public static function initializeFrameworkConfig(): string
    {
        ob_start();
        $framework = getenv('PHP_FRAMEWORK') ?: 'none';
        $frameworksPath = dirname(__DIR__, 2) . '/frameworks/';
        
        echo "[Init Framework] Menginisialisasi konfigurasi untuk framework: $framework\n";
        
        switch (strtolower($framework)) {
            case 'laravel':
                self::initializeLaravelConfig($frameworksPath);
                break;
                
            case 'symfony':
                self::initializeSymfonyConfig($frameworksPath);
                break;
                
            case 'codeigniter':
                self::initializeCodeIgniterConfig($frameworksPath);
                break;
                
            default:
                echo "[Init Framework] Tidak ada konfigurasi framework yang perlu diperbarui\n";
                break;
        }
        
        echo "[Init Framework] Konfigurasi lingkungan framework selesai\n";
        
        $output = ob_get_clean();
        $GLOBALS['framework_init_output'] = $output;
        
        return $output;
    }
    
    /**
     * Initialize Laravel configuration
     *
     * @param string $frameworksPath
     * @return void
     */
    private static function initializeLaravelConfig(string $frameworksPath): void
    {
        $envPath = $frameworksPath . 'laravel/.env';
        
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $envContent = preg_replace('/DB_HOST=.*/', 'DB_HOST=mydb', $envContent);
            $envContent = preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=' . (getenv('MYSQL_DATABASE') ?: 'lemp_db'), $envContent);
            $envContent = preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=' . (getenv('MYSQL_USER') ?: 'lemp_user'), $envContent);
            $envContent = preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=' . (getenv('MYSQL_PASSWORD') ?: 'userpassword'), $envContent);
            $envContent = preg_replace('/REDIS_HOST=.*/', 'REDIS_HOST=redis', $envContent);
            $envContent = preg_replace('/REDIS_PASSWORD=.*/', 'REDIS_PASSWORD=' . (getenv('REDIS_PASSWORD') ?: 'redispassword'), $envContent);
            
            file_put_contents($envPath, $envContent);
            echo "[Init Framework] Laravel .env berhasil diperbarui\n";
        } else {
            echo "[Init Framework] File Laravel .env tidak ditemukan\n";
        }
    }
    
    /**
     * Initialize Symfony configuration
     *
     * @param string $frameworksPath
     * @return void
     */
    private static function initializeSymfonyConfig(string $frameworksPath): void
    {
        $envLocalPath = $frameworksPath . 'symfony/.env.local';
        $envContent = "DATABASE_URL=mysql://" . (getenv('MYSQL_USER') ?: 'lemp_user') . ":" . (getenv('MYSQL_PASSWORD') ?: 'userpassword') . "@mydb:3306/" . (getenv('MYSQL_DATABASE') ?: 'lemp_db') . "?serverVersion=8.0\n";
        $envContent .= "REDIS_URL=redis://redis:6379?password=" . (getenv('REDIS_PASSWORD') ?: 'redispassword') . "\n";
        
        file_put_contents($envLocalPath, $envContent);
        echo "[Init Framework] Symfony .env.local berhasil dibuat/diperbarui\n";
    }
    
    /**
     * Initialize CodeIgniter configuration
     *
     * @param string $frameworksPath
     * @return void
     */
    private static function initializeCodeIgniterConfig(string $frameworksPath): void
    {
        $envPath = $frameworksPath . 'codeigniter/.env';
        
        if (file_exists($envPath)) {
            $envContent = file_get_contents($envPath);
            $envContent = preg_replace('/database\.default\.hostname = .*/', 'database.default.hostname = mydb', $envContent);
            $envContent = preg_replace('/database\.default\.database = .*/', 'database.default.database = ' . (getenv('MYSQL_DATABASE') ?: 'lemp_db'), $envContent);
            $envContent = preg_replace('/database\.default\.username = .*/', 'database.default.username = ' . (getenv('MYSQL_USER') ?: 'lemp_user'), $envContent);
            $envContent = preg_replace('/database\.default\.password = .*/', 'database.default.password = ' . (getenv('MYSQL_PASSWORD') ?: 'userpassword'), $envContent);
            
            file_put_contents($envPath, $envContent);
            echo "[Init Framework] CodeIgniter .env berhasil diperbarui\n";
        } else {
            echo "[Init Framework] File CodeIgniter .env tidak ditemukan\n";
        }
    }
    
    /**
     * Detect if framework configuration has changed
     *
     * @return bool
     */
    public static function detectFrameworkChange(): bool
    {
        $envFile = dirname(__DIR__, 2) . '/.env';
        
        if (!file_exists($envFile)) {
            return false;
        }
        
        $currentTimestamp = filemtime($envFile);
        $cacheKey = 'framework_change_timestamp';
        
        // Coba gunakan Redis cache terlebih dahulu
        if (class_exists('\App\Cache\RedisCache')) {
            $lastTimestamp = \App\Cache\RedisCache::get($cacheKey, 0);
            
            // Jika timestamp berbeda, ada perubahan
            if ($currentTimestamp !== $lastTimestamp) {
                // Simpan timestamp baru ke Redis dengan TTL 24 jam
                \App\Cache\RedisCache::set($cacheKey, $currentTimestamp, 86400);
                return true;
            }
            
            return false;
        }
        
        // Fallback ke file-based cache jika Redis tidak tersedia
        $cacheDir = dirname(__DIR__, 2) . '/cache';
        $cacheFile = $cacheDir . '/framework_change.cache';
        
        // Buat direktori cache jika belum ada
        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        
        // Baca timestamp terakhir dari cache
        $lastTimestamp = 0;
        if (file_exists($cacheFile)) {
            $lastTimestamp = (int) file_get_contents($cacheFile);
        }
        
        // Jika timestamp berbeda, ada perubahan
        if ($currentTimestamp !== $lastTimestamp) {
            // Simpan timestamp baru
            file_put_contents($cacheFile, $currentTimestamp);
            return true;
        }
        
        return false;
    }
    
    /**
     * Reload framework configuration if changed
     *
     * @return array|null Data framework baru jika ada perubahan
     */
    public static function reloadIfChanged(): ?array
    {
        if (self::detectFrameworkChange()) {
            // Reset opcache untuk memastikan konfigurasi terbaru dimuat
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // Clear framework info cache jika menggunakan Redis
            if (class_exists('\App\Cache\RedisCache')) {
                \App\Cache\RedisCache::delete('framework_info_cache');
            }
            
            return self::getFrameworkInfo();
        }
        
        return null;
    }
    
    /**
     * Get framework initialization info for API
     *
     * @return array
     */
    public static function getFrameworkInfo(): array
    {
        $framework = getenv('PHP_FRAMEWORK') ?: 'none';
        $frameworksPath = dirname(__DIR__, 2) . '/frameworks/';
        
        $initOutput = [];
        $initOutput[] = "[Init Framework] Menginisialisasi konfigurasi untuk framework: $framework";
        
        switch (strtolower($framework)) {
            case 'laravel':
                if (file_exists($frameworksPath . 'laravel/.env')) {
                    $initOutput[] = "[Init Framework] Laravel .env berhasil diperbarui";
                } else {
                    $initOutput[] = "[Init Framework] File Laravel .env tidak ditemukan";
                }
                break;
                
            case 'symfony':
                $initOutput[] = "[Init Framework] Symfony .env.local berhasil dibuat/diperbarui";
                break;
                
            case 'codeigniter':
                if (file_exists($frameworksPath . 'codeigniter/.env')) {
                    $initOutput[] = "[Init Framework] CodeIgniter .env berhasil diperbarui";
                } else {
                    $initOutput[] = "[Init Framework] File CodeIgniter .env tidak ditemukan";
                }
                break;
                
            default:
                $initOutput[] = "[Init Framework] Tidak ada konfigurasi framework yang perlu diperbarui";
                break;
        }
        
        $initOutput[] = "[Init Framework] Konfigurasi lingkungan framework selesai";
        
        return ['framework' => $framework, 'output' => $initOutput];
    }
}
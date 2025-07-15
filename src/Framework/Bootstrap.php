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

use App\Framework\FrameworkInitializer;
use Exception;

class Bootstrap
{
    /**
     * Initialize the selected framework
     *
     * @return array
     */
    public static function initialize(): array
    {
        $framework = getenv('PHP_FRAMEWORK') ?: 'none';
        
        $frameworksPath = dirname(__DIR__, 2) . '/frameworks/';
        
        FrameworkInitializer::initializeFrameworkConfig();
        
        require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
        switch (strtolower($framework)) {
            case 'laravel':
                return self::initializeLaravel($frameworksPath);
                
            case 'symfony':
                return self::initializeSymfony($frameworksPath);
                
            case 'codeigniter':
                return self::initializeCodeIgniter($frameworksPath);
                
            case 'none':
            default:
                return ['framework' => 'none'];
        }
    }
    
    /**
     * Initialize Laravel framework
     *
     * @param string $frameworksPath
     * @return array
     */
    private static function initializeLaravel(string $frameworksPath): array
    {
        if (file_exists($frameworksPath . 'laravel/bootstrap/app.php')) {
            $app = require_once $frameworksPath . 'laravel/bootstrap/app.php';
            
            /** @phpstan-ignore-next-line - Illuminate\Contracts\Http\Kernel hanya tersedia ketika Laravel terinstall */
            if (class_exists('\Illuminate\Contracts\Http\Kernel')) {
                /** @var \Illuminate\Contracts\Http\Kernel $kernel */
                $kernel = $app->make('\Illuminate\Contracts\Http\Kernel');
                return ['framework' => 'laravel', 'app' => $app, 'kernel' => $kernel];
            }
            
            return ['framework' => 'laravel', 'app' => $app];
        }
        
        return ['framework' => 'none'];
    }
    
    /**
     * Initialize Symfony framework
     *
     * @param string $frameworksPath
     * @return array
     */
    private static function initializeSymfony(string $frameworksPath): array
    {
        if (file_exists($frameworksPath . 'symfony/public/index.php') && 
            file_exists($frameworksPath . 'symfony/vendor/autoload_runtime.php')) {
            try {
                require_once $frameworksPath . 'symfony/vendor/autoload_runtime.php';
                return ['framework' => 'symfony'];
            } catch (\Exception $e) {
                error_log("Symfony initialization failed: " . $e->getMessage());
            }
        }
        
        return ['framework' => 'none'];
    }
    
    /**
     * Initialize CodeIgniter framework
     *
     * @param string $frameworksPath
     * @return array
     */
    private static function initializeCodeIgniter(string $frameworksPath): array
    {
        if (file_exists($frameworksPath . 'codeigniter/app/Config/Paths.php')) {
            try {
                require_once $frameworksPath . 'codeigniter/app/Config/Paths.php';
                return ['framework' => 'codeigniter'];
            } catch (\Exception $e) {
                error_log("CodeIgniter initialization failed: " . $e->getMessage());
            }
        }
        
        return ['framework' => 'none'];
    }
}
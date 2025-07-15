<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

namespace App\Api;

use App\System\SystemInfo;
use App\Database\MySQLMonitor;
use App\Cache\RedisMonitor;
use App\Cache\Opcache;
use App\Framework\FrameworkInitializer;

class ApiController
{
    /**
     * Handle API requests
     *
     * @param string|null $action
     * @return void
     */
    public static function handleRequest(?string $action = null): void
    {
        header('Content-Type: application/json');
        
        $action = $action ?? $_GET['action'] ?? null;
        
        switch ($action) {
            case 'warmup':
                self::handleWarmup();
                break;
                
            case 'opcache_status':
                self::handleOpcacheStatus();
                break;
                
            case 'redis_status':
                self::handleRedisStatus();
                break;
                
            case 'mysql_status':
                self::handleMysqlStatus();
                break;
                
            case 'system_info':
                self::handleSystemInfo();
                break;
                
            case 'framework_info':
                self::handleFrameworkInfo();
                break;
                
            case 'check_framework_changes':
                self::handleFrameworkChanges();
                break;
                
            default:
                self::handleNotFound();
                break;
        }
    }
    
    /**
     * Handle warmup request
     *
     * @return void
     */
    private static function handleWarmup(): void
    {
        echo json_encode(Opcache::warmup());
    }
    
    /**
     * Handle opcache status request
     *
     * @return void
     */
    private static function handleOpcacheStatus(): void
    {
        echo json_encode(Opcache::getStatus());
    }
    
    /**
     * Handle redis status request
     *
     * @return void
     */
    private static function handleRedisStatus(): void
    {
        echo json_encode(RedisMonitor::checkConnection());
    }
    
    /**
     * Handle mysql status request
     *
     * @return void
     */
    private static function handleMysqlStatus(): void
    {
        echo json_encode(MySQLMonitor::checkConnection());
    }
    
    /**
     * Handle system info request
     *
     * @return void
     */
    private static function handleSystemInfo(): void
    {
        echo json_encode(SystemInfo::getSystemInfo());
    }
    
    /**
     * Handle framework info request
     *
     * @return void
     */
    private static function handleFrameworkInfo(): void
    {
        echo json_encode(FrameworkInitializer::getFrameworkInfo());
    }
    
    /**
     * Handle framework changes check request
     *
     * @return void
     */
    private static function handleFrameworkChanges(): void
    {
        $changed = FrameworkInitializer::detectFrameworkChange();
        $response = ['changed' => $changed];
        
        if ($changed) {
            $response['framework_info'] = FrameworkInitializer::getFrameworkInfo();
        }
        
        echo json_encode($response);
    }
    
    /**
     * Handle not found request
     *
     * @return void
     */
    private static function handleNotFound(): void
    {
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
    }
    
    /**
     * Get response data for specific action
     *
     * @param string $action
     * @return array
     */
    public static function getResponseData(string $action): array
    {
        switch ($action) {
            case 'warmup':
                return Opcache::warmup();
                
            case 'opcache_status':
                return Opcache::getStatus();
                
            case 'redis_status':
                return RedisMonitor::checkConnection();
                
            case 'mysql_status':
                return MySQLMonitor::checkConnection();
                
            case 'system_info':
                return SystemInfo::getSystemInfo();
                
            case 'framework_info':
                return FrameworkInitializer::getFrameworkInfo();
                
            case 'check_framework_changes':
                $changed = FrameworkInitializer::detectFrameworkChange();
                $response = ['changed' => $changed];
                if ($changed) {
                    $response['framework_info'] = FrameworkInitializer::getFrameworkInfo();
                }
                return $response;
                
            default:
                return ['error' => 'Action not found'];
        }
    }
}
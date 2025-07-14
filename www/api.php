<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\System\SystemInfo;
use App\Database\MySQLMonitor;
use App\Cache\RedisMonitor;
use App\Cache\Opcache;

header('Content-Type: application/json');

$action = $_GET['action'] ?? null;

switch ($action) {
    case 'warmup':
        echo json_encode(Opcache::warmup());
        break;

    case 'opcache_status':
        echo json_encode(Opcache::getStatus());
        break;

    case 'redis_status':
        echo json_encode(RedisMonitor::checkConnection());
        break;

    case 'mysql_status':
        echo json_encode(MySQLMonitor::checkConnection());
        break;

    case 'system_info':
        echo json_encode(SystemInfo::getSystemInfo());
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Action not found']);
        break;
}
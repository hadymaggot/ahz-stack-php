<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\System\SystemInfo;
use App\Database\MySQLMonitor;
use App\Cache\RedisMonitor;
use App\Cache\Opcache;



// Jika tidak ada permintaan khusus, tampilkan dashboard
$system_info = SystemInfo::getSystemInfo();
$opcache_status = Opcache::getStatus();
$redis_status = RedisMonitor::checkConnection();
$mysql_status = MySQLMonitor::checkConnection();

require_once __DIR__ . '/templates/dashboard.php';
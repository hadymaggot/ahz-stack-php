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

use App\Framework\Bootstrap;

$bootstrap = Bootstrap::initialize();

if ($bootstrap['framework'] !== 'none') {
    switch ($bootstrap['framework']) {
        case 'laravel':
            if (isset($bootstrap['kernel']) && class_exists('Illuminate\Http\Request')) {
                /** @phpstan-ignore-next-line - Illuminate\Http\Request hanya tersedia jika Laravel terinstall */
                $request = \Illuminate\Http\Request::capture();
                $response = $bootstrap['kernel']->handle($request);
                $response->send();
                $bootstrap['kernel']->terminate($request, $response);
                exit;
            }
            break;
            
        case 'symfony':
            exit;
            
        case 'codeigniter':
            require_once __DIR__ . '/../frameworks/codeigniter/public/index.php';
            exit;
    }
}

use App\System\SystemInfo;
use App\Database\MySQLMonitor;
use App\Cache\RedisMonitor;
use App\Cache\Opcache;

$system_info = SystemInfo::getSystemInfo();
$opcache_status = Opcache::getStatus();
$redis_status = RedisMonitor::checkConnection();
$mysql_status = MySQLMonitor::checkConnection();

require_once __DIR__ . '/templates/dashboard.php';
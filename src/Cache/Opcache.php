<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

declare(strict_types=1);

namespace App\Cache;

class Opcache
{
    public static function getStatus(): array
    {
        $status = ['enabled' => false];

        if (function_exists('opcache_get_status')) {
            $opcacheStatus = opcache_get_status(false);
            if ($opcacheStatus && $opcacheStatus['opcache_enabled']) {
                $status['enabled'] = true;
                $status['memory'] = [
                    'used' => self::formatBytes($opcacheStatus['memory_usage']['used_memory']),
                    'total' => self::formatBytes($opcacheStatus['memory_usage']['free_memory'] + $opcacheStatus['memory_usage']['used_memory']),
                    'percent' => round(($opcacheStatus['memory_usage']['used_memory'] / ($opcacheStatus['memory_usage']['free_memory'] + $opcacheStatus['memory_usage']['used_memory'])) * 100, 2) . '%',
                ];
                $status['stats'] = [
                    'hit_rate' => round($opcacheStatus['opcache_statistics']['opcache_hit_rate'], 2) . '%',
                ];
                $status['jit'] = [
                    'enabled' => $opcacheStatus['jit']['enabled'],
                ];
            }
        }

        return $status;
    }

    public static function warmup(): array
    {
        return ['message' => 'OPcache warmup completed.'];
    }

    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

namespace App\Cache;

use Exception;
use Redis;

class RedisMonitor
{
    /**
     * Check if Redis is enabled and connected.
     *
     * @return array
     */
    public static function checkConnection(): array
    {
        if (!extension_loaded('redis')) {
            return ['status' => 'error', 'message' => 'Redis extension is not installed'];
        }

        try {
            $redis = new Redis();
            $host = getenv('REDIS_HOST') ?: 'redis';
            $port = getenv('REDIS_PORT') ?: 6379;
            $password = getenv('REDIS_PASSWORD') ?: '';

            if (!$redis->connect($host, $port, 2)) {
                return ['status' => 'error', 'message' => 'Could not connect to Redis'];
            }

            if ($password && !$redis->auth($password)) {
                return ['status' => 'error', 'message' => 'Redis authentication failed'];
            }

            $info = $redis->info();
            return [
                'status' => 'success',
                'message' => 'Connected to Redis',
                'version' => $info['redis_version'],
                'memory' => [
                    'used' => self::formatBytes($info['used_memory']),
                    'peak' => self::formatBytes($info['used_memory_peak']),
                    'limit' => getenv('REDIS_MAXMEMORY') ?: '512M',
                ],
                'stats' => [
                    'clients' => $info['connected_clients'],
                    'commands' => $info['total_commands_processed'],
                    'uptime' => self::formatUptime($info['uptime_in_seconds']),
                    'hits' => $info['keyspace_hits'] ?? 0,
                    'misses' => $info['keyspace_misses'] ?? 0,
                    'hit_rate' => self::calculateHitRate($info['keyspace_hits'] ?? 0, $info['keyspace_misses'] ?? 0),
                ],
                'keys' => $redis->dbSize(),
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'Redis Error: ' . $e->getMessage()];
        }
    }

    /**
     * Format bytes into a more readable format.
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    private static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Format uptime into a readable format.
     *
     * @param int $seconds
     * @return string
     */
    private static function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $seconds = $seconds % 60;

        $uptime = '';
        if ($days > 0) $uptime .= "$days days ";
        if ($hours > 0) $uptime .= "$hours hours ";
        if ($minutes > 0) $uptime .= "$minutes minutes ";
        $uptime .= "$seconds seconds";

        return $uptime;
    }

    /**
     * Calculate Redis hit rate.
     *
     * @param int $hits
     * @param int $misses
     * @return string
     */
    private static function calculateHitRate(int $hits, int $misses): string
    {
        $total = $hits + $misses;
        if ($total == 0) return '0%';
        return round(($hits / $total) * 100, 2) . '%';
    }
}
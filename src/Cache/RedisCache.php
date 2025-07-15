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

class RedisCache
{
    private static $redis = null;
    private static $connected = false;

    /**
     * Get Redis connection instance
     *
     * @return Redis|null
     */
    private static function getConnection(): ?Redis
    {
        if (self::$redis === null || !self::$connected) {
            try {
                self::$redis = new Redis();
                $host = getenv('REDIS_HOST') ?: 'redis';
                $port = getenv('REDIS_PORT') ?: 6379;
                $password = getenv('REDIS_PASSWORD') ?: '';

                if (!self::$redis->connect($host, $port, 2)) {
                    return null;
                }

                if ($password && !self::$redis->auth($password)) {
                    return null;
                }

                self::$connected = true;
            } catch (Exception $e) {
                return null;
            }
        }

        return self::$redis;
    }

    /**
     * Set cache value with optional expiration
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl Time to live in seconds (0 = no expiration)
     * @return bool
     */
    public static function set(string $key, $value, int $ttl = 0): bool
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            $serializedValue = serialize($value);
            if ($ttl > 0) {
                return $redis->setex($key, $ttl, $serializedValue);
            } else {
                return $redis->set($key, $serializedValue);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get cache value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $redis = self::getConnection();
        if (!$redis) {
            return $default;
        }

        try {
            $value = $redis->get($key);
            if ($value === false) {
                return $default;
            }
            return unserialize($value);
        } catch (Exception $e) {
            return $default;
        }
    }

    /**
     * Delete cache key
     *
     * @param string $key
     * @return bool
     */
    public static function delete(string $key): bool
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            return $redis->del($key) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Check if cache key exists
     *
     * @param string $key
     * @return bool
     */
    public static function exists(string $key): bool
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            return $redis->exists($key) > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Set cache value only if key doesn't exist
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public static function setNx(string $key, $value, int $ttl = 0): bool
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            $serializedValue = serialize($value);
            if ($ttl > 0) {
                return $redis->set($key, $serializedValue, ['nx', 'ex' => $ttl]);
            } else {
                return $redis->setnx($key, $serializedValue);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Increment numeric value
     *
     * @param string $key
     * @param int $increment
     * @return int|false
     */
    public static function increment(string $key, int $increment = 1)
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            return $redis->incrBy($key, $increment);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get multiple cache values
     *
     * @param array $keys
     * @return array
     */
    public static function getMultiple(array $keys): array
    {
        $redis = self::getConnection();
        if (!$redis) {
            return [];
        }

        try {
            $values = $redis->mget($keys);
            $result = [];
            foreach ($keys as $index => $key) {
                $result[$key] = $values[$index] !== false ? unserialize($values[$index]) : null;
            }
            return $result;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Clear all cache (use with caution)
     *
     * @return bool
     */
    public static function flush(): bool
    {
        $redis = self::getConnection();
        if (!$redis) {
            return false;
        }

        try {
            return $redis->flushDB();
        } catch (Exception $e) {
            return false;
        }
    }
}
<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

namespace App\Database;

use Exception;
use mysqli;

class MySQLMonitor
{
    /**
     * Check MySQL connection and get status information.
     *
     * @return array
     */
    public static function checkConnection(): array
    {
        if (!extension_loaded('mysqli')) {
            return ['status' => 'error', 'message' => 'MySQLi extension is not installed'];
        }

        try {
            $host = getenv('MYSQL_HOST') ?: '172.19.0.3';
            $port = getenv('MYSQL_PORT') ?: getenv('DOCKER_MYSQL_PORT') ?: 3306;
            $user = getenv('MYSQL_USER') ?: 'root';
            $password = getenv('MYSQL_PASSWORD') ?: getenv('MYSQL_ROOT_PASSWORD') ?: '';
            $database = getenv('MYSQL_DATABASE') ?: '';

            $mysqli = @new mysqli($host, $user, $password, $database, $port);

            if ($mysqli->connect_error) {
                return ['status' => 'error', 'message' => 'Could not connect to MySQL: ' . $mysqli->connect_error];
            }

            $server_info = $mysqli->server_info;

            $variables = [];
            $result = $mysqli->query("SHOW GLOBAL VARIABLES");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $variables[$row['Variable_name']] = $row['Value'];
                }
                $result->free();
            }

            $status = [];
            $result = $mysqli->query("SHOW GLOBAL STATUS");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $status[$row['Variable_name']] = $row['Value'];
                }
                $result->free();
            }

            $databases = [];
            $total_tables = 0;
            $result = $mysqli->query("SHOW DATABASES");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $db_name = $row['Database'];
                    if (!in_array($db_name, ['information_schema', 'performance_schema', 'mysql', 'sys'])) {
                        $table_count = 0;
                        $table_result = $mysqli->query("SELECT COUNT(*) AS count FROM information_schema.tables WHERE table_schema = '{$db_name}'");
                        if ($table_result && $table_row = $table_result->fetch_assoc()) {
                            $table_count = $table_row['count'];
                            $total_tables += $table_count;
                        }
                        $databases[$db_name] = $table_count;
                        if ($table_result) $table_result->free();
                    }
                }
                $result->free();
            }

            $max_connections = isset($variables['max_connections']) ? (int)$variables['max_connections'] : 0;
            $current_connections = isset($status['Threads_connected']) ? (int)$status['Threads_connected'] : 0;
            $connection_usage = $max_connections > 0 ? round(($current_connections / $max_connections) * 100, 2) : 0;

            $uptime = isset($status['Uptime']) ? (int)$status['Uptime'] : 0;

            $queries = isset($status['Questions']) ? (int)$status['Questions'] : 0;
            $queries_per_second = $uptime > 0 ? round($queries / $uptime, 2) : 0;

            $mysqli->close();

            return [
                'status' => 'success',
                'message' => 'Connected to MySQL',
                'version' => $server_info,
                'uptime' => self::formatUptime($uptime),
                'databases' => count($databases),
                'tables' => $total_tables,
                'connection' => [
                    'max' => $max_connections,
                    'current' => $current_connections,
                    'usage' => $connection_usage . '%',
                ],
                'queries' => [
                    'total' => $queries,
                    'per_second' => $queries_per_second,
                ],
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => 'An exception occurred: ' . $e->getMessage()];
        }
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
}
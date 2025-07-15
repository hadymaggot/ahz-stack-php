<?php
/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */

namespace App\System;

class SystemInfo
{
    /**
     * Get basic system information.
     *
     * @return array
     */
    public static function getSystemInfo(): array
    {
        $os_info = php_uname();
        $os_parts = explode(' ', $os_info);
        
        $formatted_os = '';
        if (count($os_parts) >= 3) {
            $formatted_os = $os_parts[0] . ' ' . $os_parts[2];
        } else {
            $formatted_os = $os_info;
        }
        
        return [
            'php_version' => phpversion(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'hostname' => gethostname(),
            'os' => $formatted_os,
            'os_full' => $os_info,
            'timezone' => date_default_timezone_get(),
        ];
    }
}
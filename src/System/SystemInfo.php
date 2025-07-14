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
        // Format OS info to be more readable
        $os_info = php_uname();
        // Parse OS info to extract relevant parts
        $os_parts = explode(' ', $os_info);
        
        // Create a more readable OS format
        $formatted_os = '';
        if (count($os_parts) >= 3) {
            // Format: OS Name + Version
            $formatted_os = $os_parts[0] . ' ' . $os_parts[2];
        } else {
            $formatted_os = $os_info; // Fallback to original if parsing fails
        }
        
        return [
            'php_version' => phpversion(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'hostname' => gethostname(),
            'os' => $formatted_os,
            'os_full' => $os_info, // Keep the full OS info if needed
            'timezone' => date_default_timezone_get(), // Get current timezone
        ];
    }
}
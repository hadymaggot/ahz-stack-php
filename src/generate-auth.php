<?php
/**
 * Script untuk menggenerate user dan password hash dari .env ke Caddyfile
 * Menggunakan bcrypt hash untuk basic auth Caddy
 * 
 * Usage: php src/generate-auth.php
 */

// Fungsi untuk membaca file .env
function loadEnv($filePath) {
    if (!file_exists($filePath)) {
        throw new Exception("File .env tidak ditemukan: {$filePath}");
    }
    
    $env = [];
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Skip komentar
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Parse key=value
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value);
        }
    }
    
    return $env;
}

// Fungsi untuk menggenerate bcrypt hash
function generateBcryptHash($password) {
    // Menggunakan cost 10 untuk bcrypt (default yang aman)
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}

// Fungsi untuk update Caddyfile dengan pendekatan manual
function updateCaddyfile($caddyfilePath, $username, $passwordHash) {
    if (!file_exists($caddyfilePath)) {
        throw new Exception("File Caddyfile tidak ditemukan: {$caddyfilePath}");
    }
    
    $lines = file($caddyfilePath, FILE_IGNORE_NEW_LINES);
    $newLines = [];
    $inBasicAuth = false;
    
    foreach ($lines as $line) {
        if (strpos(trim($line), 'basic_auth {') !== false) {
            $inBasicAuth = true;
            $newLines[] = $line;
            $newLines[] = "        {$username} {$passwordHash}";
            continue;
        }
        
        if ($inBasicAuth && strpos(trim($line), '}') !== false && strpos(trim($line), 'basic_auth') === false) {
            $inBasicAuth = false;
            $newLines[] = $line;
            continue;
        }
        
        if (!$inBasicAuth) {
            $newLines[] = $line;
        }
    }
    
    return implode("\n", $newLines);
}

// Fungsi untuk backup file
function backupFile($filePath) {
    $backupPath = $filePath . '.backup.' . date('Y-m-d_H-i-s');
    if (!copy($filePath, $backupPath)) {
        throw new Exception("Gagal membuat backup: {$backupPath}");
    }
    return $backupPath;
}

try {
    echo "=== AHZ-Stack-PHP Auth Generator ===\n";
    echo "Menggenerate kredensial untuk Caddyfile...\n\n";
    
    // Path file
    $envPath = __DIR__ . '/.env';
    $caddyfilePath = __DIR__ . '/Caddyfile';
    
    // Load environment variables
    echo "📖 Membaca file .env...\n";
    $env = loadEnv($envPath);
    
    // Ambil kredensial dari .env
    $username = $env['REDIS_COMMANDER_USER'] ?? 'admin';
    $password = $env['REDIS_COMMANDER_PASSWORD'] ?? 'admin';
    
    echo "👤 Username: {$username}\n";
    echo "🔐 Password: {$password}\n\n";
    
    // Generate bcrypt hash
    echo "🔨 Menggenerate bcrypt hash...\n";
    $passwordHash = generateBcryptHash($password);
    echo "✅ Hash generated: {$passwordHash}\n\n";
    
    // Backup Caddyfile
    echo "💾 Membuat backup Caddyfile...\n";
    $backupPath = backupFile($caddyfilePath);
    echo "✅ Backup dibuat: {$backupPath}\n\n";
    
    // Update Caddyfile
    echo "📝 Memperbarui Caddyfile...\n";
    $newContent = updateCaddyfile($caddyfilePath, $username, $passwordHash);
    
    // Tulis file baru
    if (file_put_contents($caddyfilePath, $newContent) === false) {
        throw new Exception("Gagal menulis Caddyfile");
    }
    
    echo "✅ Caddyfile berhasil diperbarui!\n\n";
    
    echo "📋 Ringkasan:\n";
    echo "   - Username: {$username}\n";
    echo "   - Password: {$password}\n";
    echo "   - Hash: {$passwordHash}\n";
    echo "   - Backup: {$backupPath}\n\n";
    
    echo "🔄 Untuk menerapkan perubahan, restart container Caddy:\n";
    echo "   docker-compose restart caddy\n\n";
    
    echo "🌐 Akses admin panel:\n";
    echo "   - phpMyAdmin: http://pma.localhost\n";
    echo "   - Redis Commander: http://redis.localhost\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
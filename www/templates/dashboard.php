<?php

/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AHZ-Stack-PHP</title>
    <link rel="icon" href="favicon.svg" type="image/svg+xml">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="d-flex flex-column min-vh-100">


    <header class="text-center py-5">
        <img src="favicon.svg" alt="FrankenPHP Logo" width="120" class="mb-4">
        <h1 class="display-4 fw-bold">AHZ-Stack-PHP</h1>
        <p class="lead text-muted">A monitoring and diagnostics dashboard for your PHP development environment.</p>
    </header>

    <main class="container flex-grow-1">

        <div class="row g-4">
            <!-- System Info -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-hdd-stack-fill me-2"></i>System Information</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-filetype-php me-2"></i>PHP Version:</span> <strong class="text-end" id="php-version"><?php echo $system_info['php_version']; ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-server me-2"></i>Server:</span> <strong class="text-end" id="server-info"><?php echo $system_info['server']; ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-pc-display me-2"></i>Hostname:</span> <strong class="text-end" id="hostname"><?php echo $system_info['hostname']; ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-cpu me-2"></i></span> <strong class="text-end" id="os-info"><?php echo $system_info['os']; ?></strong></li>
                            <li class="list-group-item d-flex justify-content-between"><span><i class="bi bi-clock me-2"></i>Timezone:</span> <strong class="text-end" id="timezone"><?php echo $system_info['timezone']; ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- OpCache -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-speedometer2 me-2"></i>OpCache</h5>
                        <span id="opcache-badge" class="badge bg-<?php echo !empty($opcache_status['enabled']) ? 'success' : 'danger'; ?>"><?php echo !empty($opcache_status['enabled']) ? 'Active' : 'Inactive'; ?></span>
                    </div>
                    <?php if (!empty($opcache_status['enabled'])) : ?>
                        <div class="card-body">
                            <p>Memory: <span id="opcache-memory"><?php echo $opcache_status['memory']['used']; ?> / <?php echo $opcache_status['memory']['total']; ?></span></p>
                            <div class="progress mb-3">
                                <div id="opcache-memory-bar" class="progress-bar" role="progressbar" style="width: <?php echo $opcache_status['memory']['percent']; ?>;" aria-valuenow="<?php echo floatval($opcache_status['memory']['percent']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $opcache_status['memory']['percent']; ?></div>
                            </div>
                            <p>Hit Rate: <strong id="opcache-hit-rate"><?php echo $opcache_status['stats']['hit_rate']; ?></strong></p>
                            <p>JIT: <span id="opcache-jit-badge" class="badge bg-<?php echo $opcache_status['jit']['enabled'] ? 'success' : 'secondary'; ?>"><?php echo $opcache_status['jit']['enabled'] ? 'Active' : 'Inactive'; ?></span></p>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#opcacheModal">Details</button>
                        </div>
                    <?php else : ?>
                        <div class="card-body">
                            <p class="text-danger">OpCache is not enabled.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- MySQL -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-database me-2"></i>MySQL</h5>
                        <span id="mysql-badge" class="badge bg-<?php echo $mysql_status['status'] === 'success' ? 'success' : 'danger'; ?>"><?php echo $mysql_status['status'] === 'success' ? 'Connected' : 'Failed'; ?></span>
                    </div>
                    <?php if ($mysql_status['status'] === 'success') : ?>
                        <div class="card-body">
                            <p>Version: <strong id="mysql-version"><?php echo $mysql_status['version']; ?></strong></p>
                            <p>Uptime: <strong id="mysql-uptime"><?php echo $mysql_status['uptime']; ?></strong></p>
                            <p>Connections: <span id="mysql-connections"><?php echo $mysql_status['connection']['current']; ?> / <?php echo $mysql_status['connection']['max']; ?></span></p>
                            <div class="progress mb-3">
                                <div id="mysql-connections-bar" class="progress-bar" role="progressbar" style="width: <?php echo $mysql_status['connection']['usage']; ?>;" aria-valuenow="<?php echo floatval($mysql_status['connection']['usage']); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $mysql_status['connection']['usage']; ?></div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="http://pma.localhost" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>phpMyAdmin</a>
                        </div>
                    <?php else : ?>
                        <div class="card-body">
                            <p class="text-danger"><?php echo $mysql_status['message']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Redis -->
            <div class="col-md-6 col-lg-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="bi bi-lightning-charge me-2"></i>Redis</h5>
                        <span id="redis-badge" class="badge bg-<?php echo $redis_status['status'] === 'success' ? 'success' : 'danger'; ?>"><?php echo $redis_status['status'] === 'success' ? 'Connected' : 'Failed'; ?></span>
                    </div>
                    <?php if ($redis_status['status'] === 'success') : ?>
                        <div class="card-body">
                            <p>Version: <strong id="redis-version"><?php echo $redis_status['version']; ?></strong></p>
                            <p>Memory: <strong id="redis-memory"><?php echo $redis_status['memory']['used']; ?> / <?php echo $redis_status['memory']['limit']; ?></strong></p>
                            <p>Hit Rate: <strong id="redis-hit-rate"><?php echo $redis_status['stats']['hit_rate']; ?></strong></p>
                            <p>Keys: <strong id="redis-keys"><?php echo $redis_status['keys']; ?></strong></p>
                        </div>
                        <div class="card-footer">
                            <a href="http://redis.localhost" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>RedisCommander</a>
                        </div>
                    <?php else : ?>
                        <div class="card-body">
                            <p class="text-danger"><?php echo $redis_status['message']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- OpCache Modal -->
    <div class="modal fade" id="opcacheModal" tabindex="-1" aria-labelledby="opcacheModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="opcacheModalLabel">OpCache Diagnostic Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="bi bi-info-circle me-2"></i>General Information</h5>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <td>Status</td>
                                    <td><span class="badge bg-<?php echo !empty($opcache_status['enabled']) ? 'success' : 'danger'; ?>"><?php echo !empty($opcache_status['enabled']) ? 'Active' : 'Inactive'; ?></span></td>
                                </tr>
                                <tr>
                                    <td>Memory Usage</td>
                                    <td><?php echo $opcache_status['memory']['used']; ?> / <?php echo $opcache_status['memory']['total']; ?> (<?php echo $opcache_status['memory']['percent']; ?>)</td>
                                </tr>
                                <tr>
                                    <td>Hit Rate</td>
                                    <td><?php echo $opcache_status['stats']['hit_rate']; ?></td>
                                </tr>
                                <tr>
                                    <td>JIT Status</td>
                                    <td><span class="badge bg-<?php echo $opcache_status['jit']['enabled'] ? 'success' : 'secondary'; ?>"><?php echo $opcache_status['jit']['enabled'] ? 'Active' : 'Inactive'; ?></span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="bi bi-graph-up me-2"></i>Performance Metrics</h5>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <td>Cached Scripts</td>
                                    <td><?php echo $opcache_status['stats']['cached_scripts'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td>Hits</td>
                                    <td><?php echo $opcache_status['stats']['hits'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td>Misses</td>
                                    <td><?php echo $opcache_status['stats']['misses'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td>Blacklist Misses</td>
                                    <td><?php echo $opcache_status['stats']['blacklist_misses'] ?? 'N/A'; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5><i class="bi bi-gear me-2"></i>Configuration</h5>
                            <table class="table table-sm table-striped">
                                <tr>
                                    <td>Memory Consumption</td>
                                    <td><?php echo $opcache_status['memory']['used']; ?> / <?php echo $opcache_status['memory']['total']; ?></td>
                                </tr>
                                <tr>
                                    <td>Interned Strings</td>
                                    <td><?php echo $opcache_status['interned_strings_usage']['used_memory'] ?? 'N/A'; ?> / <?php echo $opcache_status['interned_strings_usage']['buffer_size'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td>OPCache Restarts</td>
                                    <td><?php echo $opcache_status['stats']['oom_restarts'] ?? 'N/A'; ?></td>
                                </tr>
                                <tr>
                                    <td>Keys</td>
                                    <td><?php echo $opcache_status['stats']['num_cached_keys'] ?? 'N/A'; ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer mt-auto py-3 bg-dark-subtle">
        <div class="container text-center">
            <div class="d-flex justify-content-between align-items-center">
                <span class="text-muted">Made with ❤️ for PHP Community</span>
                <span>AHZ-Stack-PHP v1.0.0 &copy; 2025 <a href="https://hadymaggot.github.io" target="_blank">ahadizapto</a></span>
                <small>Distributed under <a href="../LICENSE" target="_blank">MIT License</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function warmupOpCache() {
            fetch('api.php?action=warmup')
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
        }

        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = value;
            }
        }

        // Fungsi untuk memperbarui status Redis secara otomatis
        function updateRedisStatus() {
            fetch('api.php?action=redis_status')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateElement('redis-version', data.version);
                        updateElement('redis-memory', data.memory.used + ' / ' + data.memory.limit);
                        updateElement('redis-hit-rate', data.stats.hit_rate);
                        updateElement('redis-keys', data.keys);
                    }
                });
        }

        // Fungsi untuk memperbarui status OpCache secara otomatis
        function updateOpcacheStatus() {
            fetch('api.php?action=opcache_status')
                .then(response => response.json())
                .then(data => {
                    if (data.enabled) {
                        updateElement('opcache-memory', data.memory.used + ' / ' + data.memory.total);
                        updateElement('opcache-hit-rate', data.stats.hit_rate);
                        const memoryBar = document.getElementById('opcache-memory-bar');
                        if (memoryBar) {
                            memoryBar.style.width = data.memory.percent;
                            memoryBar.innerHTML = data.memory.percent;
                        }
                    }
                });
        }

        // Fungsi untuk memperbarui informasi sistem secara otomatis
        function updateSystemInfo() {
            fetch('api.php?action=system_info')
                .then(response => response.json())
                .then(data => {
                    updateElement('php-version', data.php_version);
                    updateElement('server-info', data.server);
                    updateElement('hostname', data.hostname);
                    updateElement('os-info', data.os);
                    updateElement('timezone', data.timezone);
                });
        }

        // Fungsi untuk memperbarui status MySQL secara otomatis
        function updateMySQLStatus() {
            fetch('api.php?action=mysql_status')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        updateElement('mysql-version', data.version);
                        updateElement('mysql-uptime', data.uptime);
                        updateElement('mysql-connections', data.connection.current + ' / ' + data.connection.max);
                        const connectionsBar = document.getElementById('mysql-connections-bar');
                        if (connectionsBar) {
                            connectionsBar.style.width = data.connection.usage;
                            connectionsBar.innerHTML = data.connection.usage;
                        }
                    }
                });
        }

        // Memperbarui status setiap 5 detik
        setInterval(function() {
            updateSystemInfo();
            updateRedisStatus();
            updateOpcacheStatus();
            updateMySQLStatus();
        }, 5000);
    </script>
</body>

</html>
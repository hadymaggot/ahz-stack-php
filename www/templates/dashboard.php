<?php

/**
 * AHZ-Stack-PHP: PHP Development Environment with FrankenPHP, MySQL, Redis, and Admin Tools
 * 
 * @version    1.0.0
 * @author     ahadizapto <9hs@tuta.io>
 * @link       https://hadymaggot.github.io
 * @license    MIT License
 */


use App\Framework\FrameworkInitializer;
// Cek perubahan framework terlebih dahulu, jika ada reload data baru
$framework_init_output = FrameworkInitializer::reloadIfChanged() 
    ?? FrameworkInitializer::getFrameworkInfo();
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
    
    <script>

        var frameworkInitOutput = <?php echo json_encode($framework_init_output); ?>;
    </script>
    <style>
        /* Terminal styling */
        .terminal-card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            z-index: 1050;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
            border-radius: 8px;
            overflow: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        }
        
        .terminal-card.show {
            opacity: 1;
        }
        
        #terminal-overlay {
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
        }
        
        .terminal-card .card-body {
            max-height: 60vh;
            overflow-y: auto;
            background-color: #1e1e1e;
            font-size: 14px;
            line-height: 1.5;
        }
        
        .terminal-line {
            display: block;
            margin-bottom: 5px;
            color: #f8f8f8;
        }
        
        /* Cursor animation */
        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }
        
        .terminal-cursor {
            display: inline-block;
            width: 8px;
            height: 16px;
            background-color: #00ff00;
            margin-left: 2px;
            animation: blink 1s infinite;
        }
        
        /* Toast Framework Init Styles */
        .framework-init-toast {
            min-width: 400px;
            max-width: 600px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .framework-init-toast .toast-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .terminal-output-toast {
            scrollbar-width: thin;
            scrollbar-color: #666 #1a1a1a;
        }
        
        .terminal-output-toast::-webkit-scrollbar {
            width: 8px;
        }
        
        .terminal-output-toast::-webkit-scrollbar-track {
            background: #1a1a1a;
        }
        
        .terminal-output-toast::-webkit-scrollbar-thumb {
            background: #666;
            border-radius: 4px;
        }
        
        .terminal-output-toast::-webkit-scrollbar-thumb:hover {
            background: #888;
        }
        
        /* Toast container positioning */
        #framework-toast-container {
            z-index: 1055 !important;
        }
        
        /* Toast animation improvements */
        .toast.showing {
            opacity: 1;
            transform: translateX(0);
        }
        
        .toast.hide {
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease-out;
        }
    </style>
</head>

<body class="d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">AHZ-Stack-PHP</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:void(0);" onclick="showFrameworkInitInfo();"><i class="bi bi-terminal-fill"></i> Framework Init</a>
                    </li>
                    <li class="nav-item d-flex align-items-center me-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="auto-show-toggle">
                            <label class="form-check-label text-white small" for="auto-show-toggle"><i class="bi bi-magic me-1"></i>Auto-show</label>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/hadymaggot/ahz-stack-php" target="_blank"><i class="bi bi-github"></i> GitHub</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="text-center py-5">
        <img src="favicon.svg" alt="FrankenPHP Logo" width="120" class="mb-4">
        <h1 class="display-4 fw-bold">AHZ-Stack-PHP</h1>
        <p class="lead text-muted">A monitoring and diagnostics dashboard for your PHP development environment.</p>
    </header>

    <main class="container flex-grow-1">

        <!-- Framework Initialization Terminal -->        
        <div id="terminal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.7); z-index: 1040;"></div>
        <div class="card mb-4 terminal-card" id="framework-terminal" style="display: none;">
            <div class="card-header bg-dark d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0 text-light"><i class="bi bi-terminal-fill me-2"></i>Framework Initialization</h5>
                <div>
                    <div class="d-flex align-items-center">
                          <div class="form-check form-switch me-2 d-inline-block">
                              <input class="form-check-input" type="checkbox" id="sound-toggle" checked>
                              <label class="form-check-label text-white small" for="sound-toggle"><i class="bi bi-volume-up-fill me-1"></i>Sound</label>
                          </div>
                          <div class="d-inline-block me-2">
                               <div class="d-flex align-items-center">
                                   <label for="typing-speed" class="text-white small me-1"><i class="bi bi-speedometer2 me-1"></i>Speed:</label>
                                   <select id="typing-speed" class="form-select form-select-sm bg-dark text-light border-secondary" style="width: 90px;">
                                       <option value="10">Fast</option>
                                       <option value="20" selected>Normal</option>
                                       <option value="50">Slow</option>
                                   </select>
                               </div>
                           </div>
                      </div>
                    <div>
                         <button class="btn btn-sm btn-outline-light me-1" onclick="resetTerminalPreferences();" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Reset semua preferensi terminal"><i class="bi bi-arrow-counterclockwise"></i></button>
                         <button class="btn btn-sm btn-outline-light" onclick="closeTerminal();" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tutup terminal"><i class="bi bi-x-lg"></i></button>
                     </div>
                </div>
            </div>
            <div class="card-body bg-dark text-light p-3" style="font-family: 'Courier New', monospace;">
                <div id="terminal-output" style="white-space: pre-line;"></div>
            </div>
        </div>
        
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
                            <div class="progress mb-3">
                                <div id="opcache-hit-rate-bar" class="progress-bar bg-success" role="progressbar" style="width: <?php echo floatval(str_replace('%', '', $opcache_status['stats']['hit_rate'])); ?>%;" aria-valuenow="<?php echo floatval(str_replace('%', '', $opcache_status['stats']['hit_rate'])); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $opcache_status['stats']['hit_rate']; ?></div>
                            </div>
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
                            <div class="progress mb-3">
                                <div id="redis-hit-rate-bar" class="progress-bar bg-info" role="progressbar" style="width: <?php echo floatval(str_replace('%', '', $redis_status['stats']['hit_rate'])); ?>%;" aria-valuenow="<?php echo floatval(str_replace('%', '', $redis_status['stats']['hit_rate'])); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $redis_status['stats']['hit_rate']; ?></div>
                            </div>
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

    <!-- Toast untuk konfirmasi reset preferensi -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="reset-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Sukses</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                Semua preferensi terminal telah direset ke pengaturan default.
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
    <script src="/assets/typing-sound.js"></script>
    <script>
        function warmupOpCache() {
            fetch('api.php?action=warmup')
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    location.reload();
                });
        }
        
        function closeTerminal() {
            const terminal = document.getElementById('framework-terminal');
            const overlay = document.getElementById('terminal-overlay');
            
            terminal.classList.remove('show');
            overlay.style.opacity = '0';
            
            setTimeout(() => {
                terminal.style.display = 'none';
                overlay.style.display = 'none';
            }, 300);
        }
        
        function saveSoundPreference() {
            const soundToggle = document.getElementById('sound-toggle');
            const soundEnabled = soundToggle.checked;
            localStorage.setItem('terminal-sound-enabled', soundEnabled);
            
            updateSoundIcon(soundEnabled);
        }
        
        function updateSoundIcon(enabled) {
            const iconElement = document.querySelector('label[for="sound-toggle"] i');
            if (iconElement) {
                if (enabled) {
                    iconElement.className = 'bi bi-volume-up-fill me-1';
                } else {
                    iconElement.className = 'bi bi-volume-mute-fill me-1';
                }
            }
        }
        
        function loadSoundPreference() {
            const savedPreference = localStorage.getItem('terminal-sound-enabled');
            const soundEnabled = savedPreference === null ? true : savedPreference === 'true';
            
            document.getElementById('sound-toggle').checked = soundEnabled;
            
            updateSoundIcon(soundEnabled);
        }
        
        function saveTypingSpeedPreference() {
            const typingSpeed = document.getElementById('typing-speed').value;
            localStorage.setItem('terminal-typing-speed', typingSpeed);
        }
        
        function loadTypingSpeedPreference() {
            const savedSpeed = localStorage.getItem('terminal-typing-speed');
            if (savedSpeed !== null) {
                document.getElementById('typing-speed').value = savedSpeed;
            }
        }
        
        function saveAutoShowPreference() {
            const autoShow = document.getElementById('auto-show-toggle').checked;
            localStorage.setItem('terminal-auto-show', autoShow);
        }
        
        function loadAutoShowPreference() {
            const savedAutoShow = localStorage.getItem('terminal-auto-show');
            return savedAutoShow === null ? false : savedAutoShow === 'true';
        }
        
        function resetTerminalPreferences() {
            localStorage.removeItem('terminal-sound-enabled');
            localStorage.removeItem('terminal-typing-speed');
            localStorage.removeItem('terminal-auto-show');
            
            document.getElementById('sound-toggle').checked = true;
            document.getElementById('typing-speed').value = '20';
            document.getElementById('auto-show-toggle').checked = false;
            
            updateSoundIcon(true);
            
            const toast = new bootstrap.Toast(document.getElementById('reset-toast'));
            toast.show();
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            loadSoundPreference();
            loadTypingSpeedPreference();
            
            document.getElementById('sound-toggle').addEventListener('change', saveSoundPreference);
            document.getElementById('typing-speed').addEventListener('change', saveTypingSpeedPreference);
            document.getElementById('auto-show-toggle').addEventListener('change', saveAutoShowPreference);
            
            document.getElementById('auto-show-toggle').checked = loadAutoShowPreference();
            
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            if (loadAutoShowPreference()) {
                setTimeout(function() {
                    showFrameworkInitInfo();
                }, 500);
            }
        });
        
        function showFrameworkInitInfo() {
            if (typeof frameworkInitOutput !== 'undefined' && frameworkInitOutput) {
                // frameworkInitOutput adalah objek dengan struktur {framework: 'laravel', output: [array]}
                if (frameworkInitOutput.output && Array.isArray(frameworkInitOutput.output)) {
                    displayFrameworkInfoToast(frameworkInitOutput.output, frameworkInitOutput.framework);
                } else {
                    // Fallback jika format tidak sesuai
                    console.error('Format frameworkInitOutput tidak valid:', frameworkInitOutput);
                    fetch('api.php?action=framework_info')
                        .then(response => response.json())
                        .then(data => {
                            if (data.output) {
                                displayFrameworkInfoToast(data.output, data.framework);
                            }
                        })
                        .catch(error => {
                            console.error('Error fetching framework info:', error);
                        });
                }
            } else {
                fetch('api.php?action=framework_info')
                    .then(response => response.json())
                    .then(data => {
                        if (data.output) {
                            displayFrameworkInfoToast(data.output, data.framework);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching framework info:', error);
                    });
            }
        }
        
        function displayFrameworkInfoToast(outputLines, framework) {
            // Buat toast container jika belum ada
            let toastContainer = document.getElementById('framework-toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'framework-toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '1055';
                document.body.appendChild(toastContainer);
            }
            
            // Buat toast element
            const toastId = 'framework-toast-' + Date.now();
            const toastHtml = `
                <div id="${toastId}" class="toast framework-init-toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
                    <div class="toast-header bg-primary text-white">
                        <i class="bi bi-gear-fill me-2"></i>
                        <strong class="me-auto">Framework Initialization</strong>
                        <small class="text-white-50">${framework ? framework.toUpperCase() : 'Unknown'}</small>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body p-0">
                        <div class="terminal-output-toast" id="terminal-output-${toastId}" style="
                            background: #1a1a1a;
                            color: #00ff00;
                            font-family: 'Courier New', monospace;
                            font-size: 12px;
                            padding: 15px;
                            max-height: 300px;
                            overflow-y: auto;
                            white-space: pre-wrap;
                            border-radius: 0 0 0.375rem 0.375rem;
                        "></div>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = document.getElementById(toastId);
            const terminalOutput = document.getElementById(`terminal-output-${toastId}`);
            
            // Tampilkan toast
            const toast = new bootstrap.Toast(toastElement, {
                autohide: false
            });
            toast.show();
            
            // Auto-hide setelah 10 detik
            setTimeout(() => {
                toast.hide();
                setTimeout(() => {
                    if (toastElement && toastElement.parentNode) {
                        toastElement.parentNode.removeChild(toastElement);
                    }
                }, 500);
            }, 10000);
            
            // Tampilkan output dengan efek typing
            if (outputLines && outputLines.length > 0) {
                const typingSpeedValue = parseInt(document.getElementById('typing-speed')?.value || '20');
                const soundEnabled = document.getElementById('sound-toggle')?.checked || false;
                const typingSound = createTypingSound();
                
                let lineIndex = 0;
                let charIndex = 0;
                let currentLine = '';
                
                const typeCharacter = () => {
                    if (lineIndex >= outputLines.length) {
                        const cursor = document.createElement('span');
                        cursor.className = 'terminal-cursor';
                        cursor.style.animation = 'blink 1s infinite';
                        terminalOutput.appendChild(cursor);
                        return;
                    }
                    
                    if (charIndex === 0) {
                        currentLine = outputLines[lineIndex];
                        
                        if (lineIndex > 0) {
                            terminalOutput.appendChild(document.createElement('br'));
                        }
                        
                        const lineSpan = document.createElement('span');
                        lineSpan.className = 'terminal-line';
                        
                        // Warna berdasarkan konten
                        if (currentLine.includes('berhasil')) {
                            lineSpan.style.color = '#00ff00';
                        } else if (currentLine.includes('tidak ditemukan')) {
                            lineSpan.style.color = '#ff9900';
                        } else if (currentLine.includes('selesai')) {
                            lineSpan.style.color = '#00ffff';
                        } else {
                            lineSpan.style.color = '#ffffff';
                        }
                        
                        terminalOutput.appendChild(lineSpan);
                    }
                    
                    const lineSpan = terminalOutput.lastElementChild;
                    
                    if (charIndex < currentLine.length) {
                        if (soundEnabled && currentLine.charAt(charIndex) !== ' ') {
                            typingSound.playKeySound();
                        }
                        
                        lineSpan.textContent += currentLine.charAt(charIndex);
                        charIndex++;
                        setTimeout(typeCharacter, typingSpeedValue);
                    } else {
                        lineIndex++;
                        charIndex = 0;
                        setTimeout(typeCharacter, typingSpeedValue * 3);
                    }
                };
                
                typeCharacter();
            }
        }
        
        // Fungsi legacy untuk backward compatibility
        function displayFrameworkInfo(outputLines) {
            displayFrameworkInfoToast(outputLines, 'legacy');
        }

        function updateElement(id, value) {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = value;
            }
        }

        function updateRedisStatus() {
            fetch('api.php?action=redis_status')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('redis-badge');
                    const redisCard = badge ? badge.closest('.card') : null;
                    
                    if (data.status === 'success') {
                        // Update badge status
                        if (badge) {
                            badge.className = 'badge bg-success';
                            badge.textContent = 'Connected';
                        }
                        
                        // Update Redis data
                        updateElement('redis-version', data.version);
                        updateElement('redis-memory', data.memory.used + ' / ' + data.memory.limit);
                        updateElement('redis-hit-rate', data.stats.hit_rate);
                        updateElement('redis-keys', data.keys);
                        
                        // Update card content untuk menampilkan data Redis
                        if (redisCard) {
                            const cardBody = redisCard.querySelector('.card-body');
                            if (cardBody) {
                                cardBody.innerHTML = `
                                    <p>Version: <strong id="redis-version">${data.version}</strong></p>
                                    <p>Memory: <strong id="redis-memory">${data.memory.used} / ${data.memory.limit}</strong></p>
                                    <p>Hit Rate: <strong id="redis-hit-rate">${data.stats.hit_rate}</strong></p>
                                    <p>Keys: <strong id="redis-keys">${data.keys}</strong></p>
                                `;
                            }
                            
                            // Tambahkan footer jika belum ada
                            let cardFooter = redisCard.querySelector('.card-footer');
                            if (!cardFooter) {
                                cardFooter = document.createElement('div');
                                cardFooter.className = 'card-footer';
                                cardFooter.innerHTML = '<a href="http://redis.localhost" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>RedisCommander</a>';
                                redisCard.appendChild(cardFooter);
                            }
                        }
                    } else {
                        // Update badge status untuk error
                        if (badge) {
                            badge.className = 'badge bg-danger';
                            badge.textContent = 'Failed';
                        }
                        
                        // Update card content untuk menampilkan pesan error
                        if (redisCard) {
                            const cardBody = redisCard.querySelector('.card-body');
                            if (cardBody) {
                                cardBody.innerHTML = `<p class="text-danger">${data.message}</p>`;
                            }
                            
                            // Hapus footer jika ada
                            const cardFooter = redisCard.querySelector('.card-footer');
                            if (cardFooter) {
                                cardFooter.remove();
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating Redis status:', error);
                    const badge = document.getElementById('redis-badge');
                    if (badge) {
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Failed';
                    }
                });
        }

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
                        const hitRateBar = document.getElementById('opcache-hit-rate-bar');
                        if (hitRateBar) {
                            const hitRateValue = parseFloat(data.stats.hit_rate.replace('%', ''));
                            hitRateBar.style.width = hitRateValue + '%';
                            hitRateBar.innerHTML = data.stats.hit_rate;
                            hitRateBar.setAttribute('aria-valuenow', hitRateValue);
                        }
                    }
                });
        }

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

        function updateMySQLStatus() {
            fetch('api.php?action=mysql_status')
                .then(response => response.json())
                .then(data => {
                    const badge = document.getElementById('mysql-badge');
                    const mysqlCard = badge ? badge.closest('.card') : null;
                    
                    if (data.status === 'success') {
                        // Update badge status
                        if (badge) {
                            badge.className = 'badge bg-success';
                            badge.textContent = 'Connected';
                        }
                        
                        // Update MySQL data
                        updateElement('mysql-version', data.version);
                        updateElement('mysql-uptime', data.uptime);
                        updateElement('mysql-connections', data.connection.current + ' / ' + data.connection.max);
                        const connectionsBar = document.getElementById('mysql-connections-bar');
                        if (connectionsBar) {
                            connectionsBar.style.width = data.connection.usage;
                            connectionsBar.innerHTML = data.connection.usage;
                        }
                        
                        // Update card content untuk menampilkan data MySQL
                        if (mysqlCard) {
                            const cardBody = mysqlCard.querySelector('.card-body');
                            if (cardBody) {
                                cardBody.innerHTML = `
                                    <p>Version: <strong id="mysql-version">${data.version}</strong></p>
                                    <p>Uptime: <strong id="mysql-uptime">${data.uptime}</strong></p>
                                    <p>Connections: <span id="mysql-connections">${data.connection.current} / ${data.connection.max}</span></p>
                                    <div class="progress mb-3">
                                        <div id="mysql-connections-bar" class="progress-bar" role="progressbar" style="width: ${data.connection.usage};" aria-valuenow="${parseFloat(data.connection.usage)}" aria-valuemin="0" aria-valuemax="100">${data.connection.usage}</div>
                                    </div>
                                `;
                            }
                            
                            // Tambahkan footer jika belum ada
                            let cardFooter = mysqlCard.querySelector('.card-footer');
                            if (!cardFooter) {
                                cardFooter = document.createElement('div');
                                cardFooter.className = 'card-footer';
                                cardFooter.innerHTML = '<a href="http://pma.localhost" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i>phpMyAdmin</a>';
                                mysqlCard.appendChild(cardFooter);
                            }
                        }
                    } else {
                        // Update badge status untuk error
                        if (badge) {
                            badge.className = 'badge bg-danger';
                            badge.textContent = 'Failed';
                        }
                        
                        // Update card content untuk menampilkan pesan error
                        if (mysqlCard) {
                            const cardBody = mysqlCard.querySelector('.card-body');
                            if (cardBody) {
                                cardBody.innerHTML = `<p class="text-danger">${data.message}</p>`;
                            }
                            
                            // Hapus footer jika ada
                            const cardFooter = mysqlCard.querySelector('.card-footer');
                            if (cardFooter) {
                                cardFooter.remove();
                            }
                        }
                    }
                })
                .catch(error => {
                    console.error('Error updating MySQL status:', error);
                    const badge = document.getElementById('mysql-badge');
                    if (badge) {
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Failed';
                    }
                });
        }

        // Fungsi untuk mengecek perubahan framework
        function checkFrameworkChanges() {
            fetch('api.php?action=check_framework_changes')
                .then(response => response.json())
                .then(data => {
                    if (data.changed) {
                        // Update framework info
                        frameworkInitOutput = data.framework_info;
                        
                        // Show notification
                        const toast = new bootstrap.Toast(document.getElementById('framework-change-toast'));
                        toast.show();
                        
                        console.log('Framework configuration changed and reloaded');
                    }
                })
                .catch(error => console.error('Error checking framework changes:', error));
        }
        
        setInterval(function() {
            updateOpcacheStatus();
            updateRedisStatus();
            updateSystemInfo();
            updateMySQLStatus();
            checkFrameworkChanges(); // Cek perubahan framework setiap 5 detik
        }, 5000);
        
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTerminal();
            }
        });
        
        document.getElementById('terminal-overlay').addEventListener('click', function() {
            closeTerminal();
        });
    </script>
    
    <!-- Toast Notification untuk Framework Changes -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="framework-change-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="bi bi-arrow-repeat text-primary me-2"></i>
                <strong class="me-auto">Framework Configuration</strong>
                <small class="text-muted">Just now</small>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <i class="bi bi-check-circle-fill text-success me-2"></i>
                Konfigurasi framework telah berubah dan dimuat ulang otomatis.
            </div>
        </div>
    </div>
</body>

</html>
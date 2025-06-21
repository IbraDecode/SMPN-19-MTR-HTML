<?php
require_once '../config.php';
require_login();

$current_user = get_current_admin_user();
if (!$current_user || !check_permission('super_admin')) {
    redirect(ADMIN_URL . '/dashboard');
}

// Get system information
function getSystemInfo() {
    $info = [];
    
    // PHP Information
    $info['php_version'] = phpversion();
    $info['memory_limit'] = ini_get('memory_limit');
    $info['max_execution_time'] = ini_get('max_execution_time');
    $info['upload_max_filesize'] = ini_get('upload_max_filesize');
    $info['post_max_size'] = ini_get('post_max_size');
    
    // Server Information
    $info['server_software'] = $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown';
    $info['document_root'] = $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown';
    $info['server_name'] = $_SERVER['SERVER_NAME'] ?? 'Unknown';
    $info['server_port'] = $_SERVER['SERVER_PORT'] ?? 'Unknown';
    
    // System Load (if available)
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $info['system_load'] = round($load[0], 2);
    } else {
        $info['system_load'] = 'N/A';
    }
    
    // Memory Usage
    $info['memory_usage'] = round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB';
    $info['memory_peak'] = round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';
    
    // Disk Space (if available)
    if (function_exists('disk_free_space')) {
        $bytes = disk_free_space('.');
        $info['disk_free'] = round($bytes / 1024 / 1024 / 1024, 2) . ' GB';
        
        $total_bytes = disk_total_space('.');
        $info['disk_total'] = round($total_bytes / 1024 / 1024 / 1024, 2) . ' GB';
        $info['disk_used_percent'] = round((($total_bytes - $bytes) / $total_bytes) * 100, 1);
    } else {
        $info['disk_free'] = 'N/A';
        $info['disk_total'] = 'N/A';
        $info['disk_used_percent'] = 0;
    }
    
    return $info;
}

// Get database statistics
function getDatabaseStats() {
    global $db;
    
    $stats = [];
    
    try {
        // Get database size
        $query = "SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS db_size_mb
                  FROM information_schema.tables 
                  WHERE table_schema = DATABASE()";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stats['db_size'] = $result['db_size_mb'] ?? 0;
        
        // Get table count
        $query = "SELECT COUNT(*) as table_count FROM information_schema.tables WHERE table_schema = DATABASE()";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stats['table_count'] = $result['table_count'] ?? 0;
        
        // Get recent activity
        $query = "SELECT COUNT(*) as recent_logins FROM login_attempts WHERE attempted_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        $stats['recent_logins'] = $result['recent_logins'] ?? 0;
        
    } catch (Exception $e) {
        $stats['error'] = $e->getMessage();
    }
    
    return $stats;
}

// Get security status
function getSecurityStatus() {
    $status = [];
    
    // Check if HTTPS is enabled
    $status['https_enabled'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    
    // Check if important files are protected
    $protected_files = ['.htaccess', 'config.php', 'database.sql'];
    $status['files_protected'] = true;
    
    foreach ($protected_files as $file) {
        if (is_readable($file)) {
            $status['files_protected'] = false;
            break;
        }
    }
    
    // Check PHP security settings
    $status['display_errors'] = ini_get('display_errors') == '0';
    $status['expose_php'] = ini_get('expose_php') == '0';
    $status['allow_url_fopen'] = ini_get('allow_url_fopen') == '0';
    
    return $status;
}

$system_info = getSystemInfo();
$db_stats = getDatabaseStats();
$security_status = getSecurityStatus();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem - Admin SMPN 19 Mataram</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link href="../assets/css/fontawesome.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../assets/css/style.protected.css" rel="stylesheet">
    <!-- Protection Script -->
    <script src="../assets/js/protection.js"></script>
    
    <style>
        :root {
            --primary-color: #3A86FF;
            --secondary-color: #06FFA5;
            --sidebar-width: 280px;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        
        .system-container {
            padding: 30px;
        }
        
        .system-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .system-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 30px;
            border-bottom: none;
        }
        
        .system-body {
            padding: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .info-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-good {
            background-color: #28a745;
        }
        
        .status-warning {
            background-color: #ffc107;
        }
        
        .status-danger {
            background-color: #dc3545;
        }
        
        .progress-custom {
            height: 8px;
            border-radius: 4px;
            background-color: #e9ecef;
            overflow: hidden;
        }
        
        .progress-bar-custom {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
            color: white;
        }
        
        .metric-value {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        
        .metric-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .log-viewer {
            background: #1e1e1e;
            color: #f8f8f2;
            border-radius: 10px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            box-shadow: 0 5px 20px rgba(58, 134, 255, 0.3);
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .refresh-btn:hover {
            background: #2563eb;
            transform: scale(1.1);
        }
        
        .refresh-btn.spinning {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="system-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Monitoring Sistem</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sistem</li>
                    </ol>
                </nav>
            </div>
            
            <!-- System Metrics -->
            <div class="row mb-4">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: var(--primary-color);">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="metric-value"><?php echo $system_info['system_load']; ?></div>
                        <div class="metric-label">System Load</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #28a745;">
                            <i class="fas fa-memory"></i>
                        </div>
                        <div class="metric-value"><?php echo $system_info['memory_usage']; ?></div>
                        <div class="metric-label">Memory Usage</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #ffc107;">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="metric-value"><?php echo $system_info['disk_used_percent']; ?>%</div>
                        <div class="metric-label">Disk Usage</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="metric-card">
                        <div class="metric-icon" style="background: #17a2b8;">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="metric-value"><?php echo $db_stats['db_size']; ?> MB</div>
                        <div class="metric-label">Database Size</div>
                    </div>
                </div>
            </div>
            
            <!-- System Information -->
            <div class="system-card">
                <div class="system-header">
                    <h4 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Sistem</h4>
                </div>
                <div class="system-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">PHP Version</div>
                            <div class="info-value"><?php echo $system_info['php_version']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Server Software</div>
                            <div class="info-value"><?php echo $system_info['server_software']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Memory Limit</div>
                            <div class="info-value"><?php echo $system_info['memory_limit']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Max Execution Time</div>
                            <div class="info-value"><?php echo $system_info['max_execution_time']; ?>s</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Upload Max Size</div>
                            <div class="info-value"><?php echo $system_info['upload_max_filesize']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Post Max Size</div>
                            <div class="info-value"><?php echo $system_info['post_max_size']; ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Security Status -->
            <div class="system-card">
                <div class="system-header">
                    <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Status Keamanan</h4>
                </div>
                <div class="system-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="status-indicator <?php echo $security_status['https_enabled'] ? 'status-good' : 'status-danger'; ?>"></span>
                                HTTPS Enabled: <?php echo $security_status['https_enabled'] ? 'Yes' : 'No'; ?>
                            </div>
                            <div class="mb-3">
                                <span class="status-indicator <?php echo $security_status['files_protected'] ? 'status-good' : 'status-warning'; ?>"></span>
                                Protected Files: <?php echo $security_status['files_protected'] ? 'Protected' : 'Check Required'; ?>
                            </div>
                            <div class="mb-3">
                                <span class="status-indicator <?php echo $security_status['display_errors'] ? 'status-good' : 'status-warning'; ?>"></span>
                                Display Errors: <?php echo $security_status['display_errors'] ? 'Disabled' : 'Enabled'; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <span class="status-indicator <?php echo $security_status['expose_php'] ? 'status-good' : 'status-warning'; ?>"></span>
                                Expose PHP: <?php echo $security_status['expose_php'] ? 'Disabled' : 'Enabled'; ?>
                            </div>
                            <div class="mb-3">
                                <span class="status-indicator <?php echo $security_status['allow_url_fopen'] ? 'status-good' : 'status-warning'; ?>"></span>
                                Allow URL Fopen: <?php echo $security_status['allow_url_fopen'] ? 'Disabled' : 'Enabled'; ?>
                            </div>
                            <div class="mb-3">
                                <span class="status-indicator status-good"></span>
                                Recent Logins (24h): <?php echo $db_stats['recent_logins']; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Disk Usage -->
            <div class="system-card">
                <div class="system-header">
                    <h4 class="mb-0"><i class="fas fa-hdd me-2"></i>Penggunaan Disk</h4>
                </div>
                <div class="system-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="progress-custom">
                                <div class="progress-bar-custom" style="width: <?php echo $system_info['disk_used_percent']; ?>%; background: <?php echo $system_info['disk_used_percent'] > 80 ? '#dc3545' : ($system_info['disk_used_percent'] > 60 ? '#ffc107' : '#28a745'); ?>;"></div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <strong><?php echo $system_info['disk_free']; ?> free of <?php echo $system_info['disk_total']; ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Refresh Button -->
    <button class="refresh-btn" onclick="refreshData()" title="Refresh Data">
        <i class="fas fa-sync-alt"></i>
    </button>

    <!-- Bootstrap 5 JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <!-- Protected JS -->
    <script src="../assets/js/main.protected.js"></script>
    
    <script>
        function refreshData() {
            const btn = document.querySelector('.refresh-btn');
            btn.classList.add('spinning');
            
            // Simulate refresh (in real implementation, this would be an AJAX call)
            setTimeout(() => {
                location.reload();
            }, 1000);
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
        
        // Real-time clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID');
            document.title = `Sistem - ${timeString} - Admin SMPN 19 Mataram`;
        }
        
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>


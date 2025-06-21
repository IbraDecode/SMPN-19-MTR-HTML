<?php
require_once '../config.php';
require_login();

$current_user = get_current_admin_user();
if (!$current_user || !check_permission('admin')) {
    redirect(ADMIN_URL . '/dashboard');
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $error = 'Token keamanan tidak valid.';
    } else {
        switch ($_POST['action']) {
            case 'update_general':
                $site_name = sanitize_input($_POST['site_name']);
                $site_description = sanitize_input($_POST['site_description']);
                $site_keywords = sanitize_input($_POST['site_keywords']);
                $contact_email = sanitize_input($_POST['contact_email']);
                $contact_phone = sanitize_input($_POST['contact_phone']);
                $contact_address = sanitize_input($_POST['contact_address']);
                
                update_setting('site_name', $site_name, $current_user['id']);
                update_setting('site_description', $site_description, $current_user['id']);
                update_setting('site_keywords', $site_keywords, $current_user['id']);
                update_setting('contact_email', $contact_email, $current_user['id']);
                update_setting('contact_phone', $contact_phone, $current_user['id']);
                update_setting('contact_address', $contact_address, $current_user['id']);
                
                $message = 'Pengaturan umum berhasil diperbarui.';
                break;
                
            case 'update_social':
                $facebook_url = sanitize_input($_POST['facebook_url']);
                $instagram_url = sanitize_input($_POST['instagram_url']);
                $youtube_url = sanitize_input($_POST['youtube_url']);
                $twitter_url = sanitize_input($_POST['twitter_url']);
                
                update_setting('facebook_url', $facebook_url, $current_user['id']);
                update_setting('instagram_url', $instagram_url, $current_user['id']);
                update_setting('youtube_url', $youtube_url, $current_user['id']);
                update_setting('twitter_url', $twitter_url, $current_user['id']);
                
                $message = 'Pengaturan media sosial berhasil diperbarui.';
                break;
                
            case 'update_security':
                $enable_maintenance = isset($_POST['enable_maintenance']) ? 1 : 0;
                $max_login_attempts = intval($_POST['max_login_attempts']);
                $session_timeout = intval($_POST['session_timeout']);
                $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
                
                update_setting('enable_maintenance', $enable_maintenance, $current_user['id']);
                update_setting('max_login_attempts', $max_login_attempts, $current_user['id']);
                update_setting('session_timeout', $session_timeout, $current_user['id']);
                update_setting('enable_2fa', $enable_2fa, $current_user['id']);
                
                $message = 'Pengaturan keamanan berhasil diperbarui.';
                break;
                
            case 'update_appearance':
                $primary_color = sanitize_input($_POST['primary_color']);
                $secondary_color = sanitize_input($_POST['secondary_color']);
                $enable_dark_mode = isset($_POST['enable_dark_mode']) ? 1 : 0;
                $custom_css = $_POST['custom_css']; // Don't sanitize CSS
                
                update_setting('primary_color', $primary_color, $current_user['id']);
                update_setting('secondary_color', $secondary_color, $current_user['id']);
                update_setting('enable_dark_mode', $enable_dark_mode, $current_user['id']);
                update_setting('custom_css', $custom_css, $current_user['id']);
                
                $message = 'Pengaturan tampilan berhasil diperbarui.';
                break;
        }
    }
}

// Get current settings
$settings = [
    'site_name' => get_setting('site_name', 'SMPN 19 Mataram'),
    'site_description' => get_setting('site_description', 'Website resmi SMPN 19 Mataram'),
    'site_keywords' => get_setting('site_keywords', 'SMPN 19, Mataram, sekolah, pendidikan'),
    'contact_email' => get_setting('contact_email', 'info@smpn19mataram.sch.id'),
    'contact_phone' => get_setting('contact_phone', '(0370) 123456'),
    'contact_address' => get_setting('contact_address', 'Jl. Pendidikan No. 19, Mataram, NTB'),
    'facebook_url' => get_setting('facebook_url', ''),
    'instagram_url' => get_setting('instagram_url', ''),
    'youtube_url' => get_setting('youtube_url', ''),
    'twitter_url' => get_setting('twitter_url', ''),
    'enable_maintenance' => get_setting('enable_maintenance', 0),
    'max_login_attempts' => get_setting('max_login_attempts', 5),
    'session_timeout' => get_setting('session_timeout', 3600),
    'enable_2fa' => get_setting('enable_2fa', 0),
    'primary_color' => get_setting('primary_color', '#3A86FF'),
    'secondary_color' => get_setting('secondary_color', '#06FFA5'),
    'enable_dark_mode' => get_setting('enable_dark_mode', 0),
    'custom_css' => get_setting('custom_css', ''),
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Admin SMPN 19 Mataram</title>
    
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
            --primary-color: <?php echo $settings['primary_color']; ?>;
            --secondary-color: <?php echo $settings['secondary_color']; ?>;
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
        
        .settings-container {
            padding: 30px;
        }
        
        .settings-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        
        .settings-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px 30px;
            border-bottom: none;
        }
        
        .settings-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(58, 134, 255, 0.25);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        
        .nav-tabs {
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 30px;
        }
        
        .nav-tabs .nav-link {
            border: none;
            border-radius: 10px 10px 0 0;
            color: #6c757d;
            font-weight: 500;
            padding: 15px 25px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: white;
            border: none;
        }
        
        .color-picker {
            width: 60px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background-color: var(--primary-color);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
        }
        
        .code-editor {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            background: #f8f9fa;
            border-radius: 10px;
            min-height: 200px;
        }
    </style>
</head>
<body>
    <!-- Include sidebar from dashboard -->
    <?php include 'sidebar.php'; ?>
    
    <div class="main-content">
        <div class="settings-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Pengaturan Website</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/admin/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengaturan</li>
                    </ol>
                </nav>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Settings Tabs -->
            <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                        <i class="fas fa-cog me-2"></i>Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="social-tab" data-bs-toggle="tab" data-bs-target="#social" type="button" role="tab">
                        <i class="fas fa-share-alt me-2"></i>Media Sosial
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-shield-alt me-2"></i>Keamanan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="appearance-tab" data-bs-toggle="tab" data-bs-target="#appearance" type="button" role="tab">
                        <i class="fas fa-palette me-2"></i>Tampilan
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="settingsTabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    <div class="settings-card">
                        <div class="settings-header">
                            <h4 class="mb-0"><i class="fas fa-cog me-2"></i>Pengaturan Umum</h4>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="update_general">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nama Website</label>
                                            <input type="text" class="form-control" name="site_name" value="<?php echo htmlspecialchars($settings['site_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Email Kontak</label>
                                            <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Deskripsi Website</label>
                                    <textarea class="form-control" name="site_description" rows="3" required><?php echo htmlspecialchars($settings['site_description']); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Kata Kunci (Keywords)</label>
                                    <input type="text" class="form-control" name="site_keywords" value="<?php echo htmlspecialchars($settings['site_keywords']); ?>" placeholder="Pisahkan dengan koma">
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Nomor Telepon</label>
                                            <input type="text" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Alamat</label>
                                            <input type="text" class="form-control" name="contact_address" value="<?php echo htmlspecialchars($settings['contact_address']); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Social Media Settings -->
                <div class="tab-pane fade" id="social" role="tabpanel">
                    <div class="settings-card">
                        <div class="settings-header">
                            <h4 class="mb-0"><i class="fas fa-share-alt me-2"></i>Media Sosial</h4>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="update_social">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fab fa-facebook me-2"></i>Facebook URL</label>
                                            <input type="url" class="form-control" name="facebook_url" value="<?php echo htmlspecialchars($settings['facebook_url']); ?>" placeholder="https://facebook.com/smpn19mataram">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fab fa-instagram me-2"></i>Instagram URL</label>
                                            <input type="url" class="form-control" name="instagram_url" value="<?php echo htmlspecialchars($settings['instagram_url']); ?>" placeholder="https://instagram.com/smpn19mataram">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fab fa-youtube me-2"></i>YouTube URL</label>
                                            <input type="url" class="form-control" name="youtube_url" value="<?php echo htmlspecialchars($settings['youtube_url']); ?>" placeholder="https://youtube.com/smpn19mataram">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label"><i class="fab fa-twitter me-2"></i>Twitter URL</label>
                                            <input type="url" class="form-control" name="twitter_url" value="<?php echo htmlspecialchars($settings['twitter_url']); ?>" placeholder="https://twitter.com/smpn19mataram">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Security Settings -->
                <div class="tab-pane fade" id="security" role="tabpanel">
                    <div class="settings-card">
                        <div class="settings-header">
                            <h4 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Pengaturan Keamanan</h4>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="update_security">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Mode Maintenance</label>
                                            <div class="d-flex align-items-center">
                                                <label class="switch me-3">
                                                    <input type="checkbox" name="enable_maintenance" <?php echo $settings['enable_maintenance'] ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                                <span class="text-muted">Aktifkan mode maintenance</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Two-Factor Authentication</label>
                                            <div class="d-flex align-items-center">
                                                <label class="switch me-3">
                                                    <input type="checkbox" name="enable_2fa" <?php echo $settings['enable_2fa'] ? 'checked' : ''; ?>>
                                                    <span class="slider"></span>
                                                </label>
                                                <span class="text-muted">Aktifkan 2FA untuk admin</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Maksimal Percobaan Login</label>
                                            <input type="number" class="form-control" name="max_login_attempts" value="<?php echo $settings['max_login_attempts']; ?>" min="3" max="10">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Timeout Session (detik)</label>
                                            <input type="number" class="form-control" name="session_timeout" value="<?php echo $settings['session_timeout']; ?>" min="1800" max="86400">
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Appearance Settings -->
                <div class="tab-pane fade" id="appearance" role="tabpanel">
                    <div class="settings-card">
                        <div class="settings-header">
                            <h4 class="mb-0"><i class="fas fa-palette me-2"></i>Pengaturan Tampilan</h4>
                        </div>
                        <div class="settings-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="action" value="update_appearance">
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Warna Primer</label>
                                            <div class="d-flex align-items-center">
                                                <input type="color" class="color-picker me-3" name="primary_color" value="<?php echo $settings['primary_color']; ?>">
                                                <input type="text" class="form-control" value="<?php echo $settings['primary_color']; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Warna Sekunder</label>
                                            <div class="d-flex align-items-center">
                                                <input type="color" class="color-picker me-3" name="secondary_color" value="<?php echo $settings['secondary_color']; ?>">
                                                <input type="text" class="form-control" value="<?php echo $settings['secondary_color']; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Mode Gelap</label>
                                    <div class="d-flex align-items-center">
                                        <label class="switch me-3">
                                            <input type="checkbox" name="enable_dark_mode" <?php echo $settings['enable_dark_mode'] ? 'checked' : ''; ?>>
                                            <span class="slider"></span>
                                        </label>
                                        <span class="text-muted">Aktifkan mode gelap untuk admin panel</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">Custom CSS</label>
                                    <textarea class="form-control code-editor" name="custom_css" rows="10" placeholder="/* Masukkan CSS kustom di sini */"><?php echo htmlspecialchars($settings['custom_css']); ?></textarea>
                                    <small class="text-muted">CSS kustom akan diterapkan ke seluruh website.</small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <!-- Protected JS -->
    <script src="../assets/js/main.protected.js"></script>
    
    <script>
        // Color picker sync
        document.querySelectorAll('.color-picker').forEach(picker => {
            picker.addEventListener('change', function() {
                this.nextElementSibling.value = this.value;
            });
        });
        
        // Auto-save draft (optional)
        let autoSaveTimer;
        document.querySelectorAll('input, textarea, select').forEach(input => {
            input.addEventListener('input', function() {
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(() => {
                    // Auto-save logic here if needed
                    console.log('Auto-saving...');
                }, 2000);
            });
        });
    </script>
</body>
</html>


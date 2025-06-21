<?php
// Get current page for active navigation
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="/admin/dashboard" class="sidebar-brand">
            <img src="../assets/images/smpn19-logo.jpg" alt="Logo">
            <div>
                <div class="fw-bold">SMPN 19 Mataram</div>
                <small class="opacity-75">Admin Panel</small>
            </div>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin/dashboard" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/news" class="nav-link <?php echo $current_page === 'news' ? 'active' : ''; ?>">
                    <i class="fas fa-newspaper"></i>
                    Berita
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/events" class="nav-link <?php echo $current_page === 'events' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i>
                    Event & Kalender
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/gallery" class="nav-link <?php echo $current_page === 'gallery' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i>
                    Galeri
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/messages" class="nav-link <?php echo $current_page === 'messages' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope"></i>
                    Pesan Kontak
                    <?php if (isset($stats) && $stats['unread_messages'] > 0): ?>
                        <span class="badge bg-danger ms-auto"><?php echo $stats['unread_messages']; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/settings" class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i>
                    Pengaturan
                </a>
            </li>
            <?php if (check_permission('super_admin')): ?>
            <li class="nav-item">
                <a href="/admin/users" class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i>
                    Pengguna Admin
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/system" class="nav-link <?php echo $current_page === 'system' ? 'active' : ''; ?>">
                    <i class="fas fa-server"></i>
                    Sistem
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/backup" class="nav-link <?php echo $current_page === 'backup' ? 'active' : ''; ?>">
                    <i class="fas fa-database"></i>
                    Backup & Restore
                </a>
            </li>
            <?php endif; ?>
            
            <hr class="sidebar-divider">
            
            <li class="nav-item">
                <a href="/admin/profile" class="nav-link <?php echo $current_page === 'profile' ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    Profil Saya
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/help" class="nav-link <?php echo $current_page === 'help' ? 'active' : ''; ?>">
                    <i class="fas fa-question-circle"></i>
                    Bantuan
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/logout" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </a>
            </li>
        </ul>
    </nav>
</div>

<style>
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width, 280px);
    background: linear-gradient(135deg, #073B4C 0%, #0a4a5c 100%);
    color: white;
    z-index: 1000;
    transition: all 0.3s ease;
    overflow-y: auto;
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: white;
}

.sidebar-brand img {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 12px;
}

.sidebar-nav {
    padding: 20px 0;
}

.nav-item {
    margin-bottom: 5px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-left: 3px solid transparent;
}

.nav-link:hover,
.nav-link.active {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-left-color: var(--primary-color, #3A86FF);
}

.nav-link i {
    width: 20px;
    margin-right: 12px;
}

.sidebar-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 20px 0;
}

.badge {
    font-size: 0.7rem;
    padding: 4px 8px;
}

@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
}
</style>


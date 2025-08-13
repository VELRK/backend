<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ferrigor Management System' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --primary-color: #3498db;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        
        .sidebar.collapsed {
            width: 60px;
        }
        
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-header h4 {
            color: white;
            margin: 0;
            font-size: 1.2rem;
        }
        
        .sidebar-header.collapsed h4 {
            display: none;
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: var(--sidebar-hover);
            color: white;
            text-decoration: none;
        }
        
        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 10px;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
        }
        
        .main-content.expanded {
            margin-left: 60px;
        }
        
        /* Top Navigation */
        .top-nav {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .top-nav-left {
            display: flex;
            align-items: center;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #333;
            cursor: pointer;
            margin-right: 20px;
            padding: 8px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background-color: #f8f9fa;
        }
        
        .page-title {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
            font-weight: 600;
        }
        
        .top-nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: auto;
        }
        
        /* Single User Profile Display in Main Nav */
        .user-profile-main {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 30px;
            border: 2px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .user-profile-main:hover {
            border-color: #3498db;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.25);
            transform: translateY(-2px);
        }
        
        .user-avatar-large {
            position: relative;
        }
        
        .user-avatar-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        
        .user-name-display {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 2px;
        }
        
        .user-first-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1.2;
        }
        
        .user-role-small {
            font-size: 0.8rem;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Enhanced dropdown styling */
        .dropdown-menu {
            border: none;
            padding: 0;
            overflow: hidden;
        }
        
        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }
        
        .dropdown-item:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            width: 20px;
            text-align: center;
        }
        
        .dropdown-divider {
            margin: 0;
            border-color: #e9ecef;
        }
        
        /* Content Area */
        .content-area {
            padding: 30px;
        }
        
        /* Cards */
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .top-nav {
                padding: 10px 15px;
            }
            
            .user-profile-main {
                padding: 8px 15px;
                gap: 10px;
            }
            
            .user-avatar-img {
                width: 45px;
                height: 45px;
            }
            
            .user-first-name {
                font-size: 1.1rem;
            }
            
            .user-role-small {
                font-size: 0.75rem;
            }
            
            .page-title {
                font-size: 1.2rem;
            }
        }
        
        @media (max-width: 576px) {
            .user-profile-main {
                padding: 6px 12px;
                gap: 8px;
            }
            
            .user-avatar-img {
                width: 40px;
                height: 40px;
            }
            
            .user-first-name {
                font-size: 1rem;
            }
            
            .user-role-small {
                font-size: 0.7rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header" id="sidebarHeader">
            <h4><i class="fas fa-cube"></i> <span>Ferrigor</span></h4>
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="<?= base_url('dashboard') ?>" class="nav-link" data-module="dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= base_url('users') ?>" class="nav-link" data-module="users">
                    <i class="fas fa-users"></i>
                    <span>User Management</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= base_url('blogs') ?>" class="nav-link" data-module="blogs">
                    <i class="fas fa-blog"></i>
                    <span>Blog Management</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= base_url('projects') ?>" class="nav-link" data-module="projects">
                    <i class="fas fa-project-diagram"></i>
                    <span>Project Management</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?= base_url('careers') ?>" class="nav-link" data-module="careers">
                    <i class="fas fa-briefcase"></i>
                    <span>Career Management</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navigation -->
        <div class="top-nav">
            <div class="top-nav-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title"><?= $title ?? 'Dashboard' ?></h1>
            </div>
            
            <div class="top-nav-right">
                <!-- Single User Profile Display -->
                <div class="user-profile-main" id="userProfile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar-large">
                        <img src="<?=  base_url('uploads/users/' . $this->session->userdata('profile_pic'))  ?>" 
                             alt="Profile" class="user-avatar-img">
                    </div>
                    <div class="user-name-display">
                        <span class="user-first-name"><?= $this->session->userdata('username') ?></span>
                    </div>
                    <i class="fas fa-chevron-down" style="color: #6c757d; font-size: 0.8rem; margin-left: auto;"></i>
                </div>
                
                <ul class="dropdown-menu dropdown-menu-end" style="margin-top: 10px; min-width: 200px; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
                   
                    <li><a class="dropdown-item text-danger" href="<?= base_url('auth/logout') ?>"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                </ul>
            </div>
        </div>
        
        <!-- Content Area -->
        <div class="content-area">
            <?php if (isset($content)): ?>
                <?= $content ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Ensure jQuery is loaded
        window.jQuery = window.$ = jQuery;
        
        // Initialize global array for page-specific functions
        window.pageLoadFunctions = window.pageLoadFunctions || [];
        
        // Sidebar toggle functionality
        $(document).ready(function() {
            const sidebar = $('#sidebar');
            const mainContent = $('#mainContent');
            const sidebarHeader = $('#sidebarHeader');
            
            $('#sidebarToggle').click(function() {
                sidebar.toggleClass('collapsed');
                mainContent.toggleClass('expanded');
                sidebarHeader.toggleClass('collapsed');
            });
            
            // Set active nav item based on current page
            const currentModule = '<?= $this->uri->segment(1) ?>';
            $(`.nav-link[data-module="${currentModule}"]`).addClass('active');
            
            // Mobile sidebar toggle
            if ($(window).width() <= 768) {
                sidebar.addClass('collapsed');
                mainContent.addClass('expanded');
            }
            
            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() <= 768) {
                    sidebar.addClass('collapsed');
                    mainContent.addClass('expanded');
                }
            });
            
            // Execute all functions registered for page load
            if (window.pageLoadFunctions && Array.isArray(window.pageLoadFunctions)) {
                window.pageLoadFunctions.forEach(function(func) {
                    if (typeof func === 'function') {
                        try {
                            func();
                        } catch (error) {
                            console.error('Error executing page load function:', error);
                        }
                    }
                });
                // Clear the array after execution to prevent re-running on subsequent loads
                window.pageLoadFunctions = [];
            }
        });
    </script>
</body>
</html> 
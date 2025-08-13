<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number" id="totalUsers"><?= isset($stats['total_users']) ? $stats['total_users'] : 0 ?></div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-number" id="activeUsers"><?= isset($stats['active_users']) ? $stats['active_users'] : 0 ?></div>
            <div class="stat-label">Active Users</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="fas fa-blog"></i>
            </div>
            <div class="stat-number" id="totalBlogs"><?= isset($stats['total_blogs']) ? $stats['total_blogs'] : 0 ?></div>
            <div class="stat-label">Total Blogs</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number" id="totalProjects"><?= isset($stats['total_projects']) ? $stats['total_projects'] : 0 ?></div>
            <div class="stat-label">Total Projects</div>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="row">
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-envelope"></i> Recent Email Activities
                </h5>
                <div class="float-end">
                    <button class="btn btn-sm btn-outline-secondary" id="testLoadBtn">
                        <i class="fas fa-sync-alt"></i> Test Load
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="recentActivitiesTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Message</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="activitiesTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading recent activities...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user"></i> Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= base_url('users') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> Manage Users
                    </a>
                    <a href="<?= base_url('blogs') ?>" class="btn btn-outline-success">
                        <i class="fas fa-blog"></i> Manage Blogs
                    </a>
                    <a href="<?= base_url('projects') ?>" class="btn btn-outline-warning">
                        <i class="fas fa-project-diagram"></i> Manage Projects
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Email Activity Modal -->
<div class="modal fade" id="viewActivityModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewActivityBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Wait for jQuery to be available
function initDashboard() {
    if (typeof window.jQuery === 'undefined') {
        console.log('jQuery not ready, waiting...');
        setTimeout(initDashboard, 100);
        return;
    }
    
    var $ = window.jQuery;
    console.log('jQuery loaded, initializing dashboard...');
    
    // Load stats immediately
    loadStats();
    
    // Load recent activities immediately
    loadRecentActivities();
    
    // Refresh stats every 30 seconds
    setInterval(function() {
        loadStats();
        loadRecentActivities();
    }, 30000);
    
    // Also refresh when page becomes visible (user returns to tab)
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            loadStats();
            loadRecentActivities();
        }
    });
    
    // Refresh counts when navigating back to dashboard
    $(window).on('focus', function() {
        loadStats();
        loadRecentActivities();
    });
    
    // Load recent activities function
    function loadRecentActivities() {
        console.log('Loading recent activities...');
        $.ajax({
            url: '<?= base_url('dashboard/get_recent_activities') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('Activities response:', response);
                if (response.status === 'success') {
                    displayActivities(response.data);
                } else {
                    console.error('Failed to load activities:', response.message);
                    $('#activitiesTableBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load activities: ' + response.message + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading activities:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $('#activitiesTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading activities: ' + error + '</td></tr>');
            }
        });
    }
    
    // Display activities in table
    function displayActivities(activities) {
        console.log('Displaying activities:', activities);
        if (activities && activities.length > 0) {
            var html = '';
            activities.forEach(function(activity) {
                html += '<tr>';
                html += '<td>' + (activity.message && activity.message.length > 50 ? activity.message.substring(0, 50) + '...' : (activity.message || 'N/A')) + '</td>';
                html += '<td>' + (activity.fullname || 'N/A') + '</td>';
                html += '<td>' + (activity.email || 'N/A') + '</td>';
                html += '<td>' + (activity.date ? new Date(activity.date).toLocaleDateString() + ' ' + new Date(activity.date).toLocaleTimeString() : 'N/A') + '</td>';
                html += '<td><button class="btn btn-sm btn-outline-info view-activity" data-id="' + (activity.id || 0) + '"><i class="fas fa-eye"></i> View</button></td>';
                html += '</tr>';
            });
            $('#activitiesTableBody').html(html);
        } else {
            $('#activitiesTableBody').html('<tr><td colspan="5" class="text-center text-muted">No recent email activities found</td></tr>');
        }
    }
    
    // Test load button
    $('#testLoadBtn').click(function() {
        console.log('Test load button clicked');
        loadRecentActivities();
    });
    
    // View Activity Click Handler
    $(document).on('click', '.view-activity', function() {
        var activityId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('dashboard/get_activity/') ?>' + activityId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var activity = response.data;
                    
                    var modalBody = `
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <strong><i class="fas fa-envelope me-2"></i>Message:</strong>
                                    <p class="mb-0">${activity.message || 'N/A'}</p>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="fas fa-user me-2"></i>Full Name:</strong>
                                    <p class="mb-0">${activity.fullname || 'N/A'}</p>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="fas fa-at me-2"></i>Email:</strong>
                                    <p class="mb-0">${activity.email || 'N/A'}</p>
                                </div>
                                <div class="mb-3">
                                    <strong><i class="fas fa-calendar me-2"></i>Date:</strong>
                                    <p class="mb-0">${activity.date ? new Date(activity.date).toLocaleString() : 'N/A'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#viewActivityBody').html(modalBody);
                    $('#viewActivityModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                alert('Error loading activity details: ' + error);
            }
        });
    });
    
    function loadStats() {
        $.ajax({
            url: '<?= base_url('dashboard/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(response) {
                if (response.status === 'success') {
                    // Update all stat numbers with animation
                    updateStatWithAnimation('#totalUsers', response.data.total_users);
                    updateStatWithAnimation('#activeUsers', response.data.active_users);
                    updateStatWithAnimation('#totalBlogs', response.data.total_blogs);
                    updateStatWithAnimation('#totalProjects', response.data.total_projects);
                } else {
                    console.error('Failed to load stats:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading stats:', error);
            }
        });
    }
    
    function updateStatWithAnimation(selector, newValue) {
        const element = $(selector);
        const currentValue = parseInt(element.text()) || 0;
        
        if (currentValue !== newValue) {
            element.addClass('text-primary');
            element.text(newValue);
            
            setTimeout(function() {
                element.removeClass('text-primary');
            }, 1000);
        }
    }
    
    // Add click handlers for quick action buttons to refresh data
    $('.btn-outline-primary, .btn-outline-success, .btn-outline-warning').click(function() {
        loadStats();
        loadRecentActivities();
    });
    
    // Make loadStats function globally available for other modules to call
    window.refreshDashboardStats = function() {
        loadStats();
    };
    window.refreshDashboardActivities = function() {
        loadRecentActivities();
    };
}

// Start initialization when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initDashboard();
});

// Also try to initialize if jQuery becomes available later
if (typeof window.jQuery !== 'undefined') {
    initDashboard();
} else {
    // Wait for jQuery to load
    var checkJQuery = setInterval(function() {
        if (typeof window.jQuery !== 'undefined') {
            clearInterval(checkJQuery);
            initDashboard();
        }
    }, 100);
}
</script> 
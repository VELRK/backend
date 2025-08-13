<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-number" id="totalUsers"><?= isset($data['active_users']) ? $data['active_users'] + $data['inactive_users'] : 0 ?></div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-number" id="activeUsers"><?= isset($data['active_users']) ? $data['active_users'] : 0 ?></div>
            <div class="stat-label">Active Users</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-number" id="inactiveUsers"><?= isset($data['inactive_users']) ? $data['inactive_users'] : 0 ?></div>
            <div class="stat-label">Inactive Users</div>
        </div>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-users"></i> User Management
        </h5>
        <div>
            <button class="btn btn-outline-secondary me-2" id="refreshStatsBtn" title="Refresh Statistics">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-primary" id="addUserBtn">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="usersTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data will be loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="userId" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password <span id="passwordNote">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted" id="passwordHelp" style="display: none;">Leave blank to keep current password</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="profile_pic" class="form-label">Profile Picture</label>
                        <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                        <div id="currentImage" class="mt-2" style="display: none;">
                            <img src="" alt="Current Profile" class="img-thumbnail" style="max-width: 100px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveUserBtn">
                        <span class="spinner-border spinner-border-sm me-2" style="display: none;"></span>
                        Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Define the initialization function
function initUsersDataTable() {
    // Check if jQuery and DataTables are available
    if (typeof window.jQuery === 'undefined') {
        console.error('jQuery is not loaded');
        return;
    }
    
    var $ = window.jQuery;
    
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables is not loaded');
        return;
    }
    
    // Initialize DataTable
    var usersTable = $('#usersTable').DataTable({
        ajax: {
            url: '<?= base_url('users/get_users') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'profile_pic',
                render: function(data) {
                    if (data) {
                        return '<img src="<?= base_url('uploads/users/') ?>' + data + '" alt="Profile" class="rounded-circle" width="40" height="40">';
                    }
                    return '<div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;"><i class="fas fa-user text-white"></i></div>';
                }
            },
            { data: 'name' },
            { data: 'username' },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'status',
                render: function(data) {
                    var badgeClass = data === 'active' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge ' + badgeClass + '">' + data + '</span>';
                }
            },
            { 
                data: 'created_at',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            {
                data: null,
                render: function(data) {
                    return '<div class="btn-group" role="group">' +
                           '<button class="btn btn-sm btn-outline-primary edit-user" data-id="' + data.id + '"><i class="fas fa-edit"></i></button>' +
                           '<button class="btn btn-sm btn-outline-danger delete-user" data-id="' + data.id + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
    
    // Add User Button
    $('#addUserBtn').click(function() {
        $('#modalTitle').text('Add New User');
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#passwordNote').text('*');
        $('#passwordHelp').hide();
        $('#currentImage').hide();
        $('#userModal').modal('show');
    });
    
    // Edit User
    $(document).on('click', '.edit-user', function() {
        var userId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('users/get_user/') ?>' + userId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var user = response.data;
                    $('#modalTitle').text('Edit User');
                    $('#userId').val(user.id);
                    $('#name').val(user.name);
                    $('#username').val(user.username);
                    $('#email').val(user.email);
                    $('#phone').val(user.phone);
                    $('#status').val(user.status);
                    $('#passwordNote').text('');
                    $('#passwordHelp').show();
                    
                    if (user.profile_pic) {
                        $('#currentImage').show().find('img').attr('src', '<?= base_url('uploads/users/') ?>' + user.profile_pic);
                    } else {
                        $('#currentImage').hide();
                    }
                    
                    $('#userModal').modal('show');
                }
            }
        });
    });
    
    // Delete User
    $(document).on('click', '.delete-user', function() {
        var userId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('users/delete/') ?>' + userId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        usersTable.ajax.reload();
                        
                        // Update counts if stats are provided
                        if (response.stats) {
                            updateUserCounts(response.stats);
                        }
                        
                        // Refresh all counts across the system
                        refreshAllCounts();
                        
                        alert('User deleted successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
    
    // Save User Form
    $('#userForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var userId = $('#userId').val();
        var url = userId ? 
            '<?= base_url('users/update/') ?>' + userId : 
            '<?= base_url('users/create') ?>';
        
        $('#saveUserBtn').prop('disabled', true).find('.spinner-border').show();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#userModal').modal('hide');
                    usersTable.ajax.reload();
                    
                    // Update counts if stats are provided
                    if (response.stats) {
                        updateUserCounts(response.stats);
                    }
                    
                    // Refresh all counts across the system
                    refreshAllCounts();
                    
                    alert('User saved successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            complete: function() {
                $('#saveUserBtn').prop('disabled', false).find('.spinner-border').hide();
            }
        });
    });
    
    // Function to update user counts
    function updateUserCounts(stats) {
        $('#totalUsers').text(stats.total_users);
        $('#activeUsers').text(stats.active_users);
        $('#inactiveUsers').text(stats.inactive_users);
    }
    
    // Function to refresh module statistics
    function refreshModuleStats() {
        $.ajax({
            url: '<?= base_url('users/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateUserCounts(response.data);
                }
            }
        });
    }
    
    // Function to refresh all counts across the system
    function refreshAllCounts() {
        // Update user counts
        refreshModuleStats();
        
        // Update dashboard stats if available
        if (typeof window.refreshDashboardStats === 'function') {
            window.refreshDashboardStats();
        }
        
        // Update recent activities if available
        if (typeof window.refreshDashboardActivities === 'function') {
            window.refreshDashboardActivities();
        }
    }
    
    // Refresh button functionality
    $('#refreshStatsBtn').click(function() {
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
        
        // Refresh all data
        refreshModuleStats();
        usersTable.ajax.reload();
        
        // Re-enable button after a short delay
        setTimeout(function() {
            $('#refreshStatsBtn').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh');
        }, 1000);
    });
    
    // Load initial stats when page loads
    refreshModuleStats();
}

// Ensure window.pageLoadFunctions exists and push the function
window.pageLoadFunctions = window.pageLoadFunctions || [];
window.pageLoadFunctions.push(initUsersDataTable);
</script> 
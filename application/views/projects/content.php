<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <i class="fas fa-project-diagram"></i>
            </div>
            <div class="stat-number" id="totalProjects"><?= isset($data['total_projects']) ? $data['total_projects'] : 0 ?></div>
            <div class="stat-label">Total Projects</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-number" id="activeProjects"><?= isset($data['active_projects']) ? $data['active_projects'] : 0 ?></div>
            <div class="stat-label">Active Projects</div>
        </div>
    </div>
    
    <div class="col-md-4 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number" id="completedProjects"><?= isset($data['completed_projects']) ? $data['completed_projects'] : 0 ?></div>
            <div class="stat-label">Completed Projects</div>
        </div>
    </div>
</div>

<!-- Projects Management -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-project-diagram"></i> Project Management
        </h5>
        <div>
            <button class="btn btn-outline-secondary me-2" id="refreshStatsBtn" title="Refresh Statistics">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-primary" id="addProjectBtn">
                <i class="fas fa-plus"></i> Add New Project
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="projectsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Date</th>
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

<!-- Add/Edit Project Modal -->
<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Project</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="projectForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="projectId" name="id">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="project_date" class="form-label">Project Date *</label>
                            <input type="date" class="form-control" id="project_date" name="project_date" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Project Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="form-text">Supported formats: JPG, PNG, GIF, WEBP (Max: 2MB)</div>
                    </div>
                    
                    <div id="currentImage" class="mb-3" style="display: none;">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img id="currentImagePreview" src="" alt="Current Image" class="img-thumbnail" style="max-height: 150px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveProjectBtn">
                        <span class="spinner-border spinner-border-sm me-2" style="display: none;"></span>
                        Save Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Project Modal -->
<div class="modal fade" id="viewProjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewProjectBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Define the initialization function
function initProjectsDataTable() {
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
    var projectsTable = $('#projectsTable').DataTable({
        ajax: {
            url: '<?= base_url('projects/get_projects') ?>',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id' },
            { 
                data: 'image',
                render: function(data) {
                    if (data) {
                        return '<img src="<?= base_url('uploads/projects/') ?>' + data + '" alt="Project" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">';
                    }
                    return '<div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><i class="fas fa-image text-white"></i></div>';
                }
            },
            { data: 'title' },
            { data: 'location' },
            { 
                data: 'project_date',
                render: function(data) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'status',
                render: function(data) {
                    var badgeClass = 'bg-secondary';
                    if (data === 'active') badgeClass = 'bg-success';
                    else if (data === 'completed') badgeClass = 'bg-primary';
                    else if (data === 'inactive') badgeClass = 'bg-warning';
                    
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
                           '<button class="btn btn-sm btn-outline-info view-project" data-id="' + data.id + '"><i class="fas fa-eye"></i></button>' +
                           '<button class="btn btn-sm btn-outline-primary edit-project" data-id="' + data.id + '"><i class="fas fa-edit"></i></button>' +
                           '<button class="btn btn-sm btn-outline-danger delete-project" data-id="' + data.id + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
    
    // Add Project Button
    $('#addProjectBtn').click(function() {
        $('#modalTitle').text('Add New Project');
        $('#projectForm')[0].reset();
        $('#projectId').val('');
        $('#currentImage').hide();
        $('#projectModal').modal('show');
    });
    
    // Edit Project
    $(document).on('click', '.edit-project', function() {
        var projectId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('projects/get_project/') ?>' + projectId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var project = response.data;
                    $('#modalTitle').text('Edit Project');
                    $('#projectId').val(project.id);
                    $('#title').val(project.title);
                    $('#description').val(project.description);
                    $('#location').val(project.location);
                    $('#project_date').val(project.project_date);
                    $('#status').val(project.status || 'active');
                    
                    if (project.image) {
                        $('#currentImage').show().find('#currentImagePreview').attr('src', '<?= base_url('uploads/projects/') ?>' + project.image);
                    } else {
                        $('#currentImage').hide();
                    }
                    
                    $('#projectModal').modal('show');
                }
            }
        });
    });
    
    // View Project
    $(document).on('click', '.view-project', function() {
        var projectId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('projects/get_project/') ?>' + projectId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var project = response.data;
                    
                    var modalBody = '<div class="row">' +
                        '<div class="col-md-6">';
                    
                    if (project.image) {
                        modalBody += '<img src="<?= base_url('uploads/projects/') ?>' + project.image + '" class="img-fluid rounded" alt="' + project.title + '">';
                    } else {
                        modalBody += '<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 300px;"><i class="fas fa-image text-white" style="font-size: 4rem;"></i></div>';
                    }
                    
                    modalBody += '</div><div class="col-md-6">' +
                        '<h4 class="text-primary">' + project.title + '</h4>' +
                        '<p class="text-muted mb-3">' + project.description + '</p>' +
                        '<div class="mb-3"><strong><i class="fas fa-map-marker-alt me-2"></i>Location:</strong><p class="mb-0">' + project.location + '</p></div>' +
                        '<div class="mb-3"><strong><i class="fas fa-calendar me-2"></i>Project Date:</strong><p class="mb-0">' + new Date(project.project_date).toLocaleDateString() + '</p></div>' +
                        '<div class="mb-3"><strong><i class="fas fa-info-circle me-2"></i>Status:</strong><p class="mb-0"><span class="badge bg-' + (project.status === 'active' ? 'success' : project.status === 'completed' ? 'primary' : 'warning') + '">' + (project.status || 'active') + '</span></p></div>' +
                        '<div class="mb-3"><strong><i class="fas fa-clock me-2"></i>Created:</strong><p class="mb-0">' + new Date(project.created_at).toLocaleString() + '</p></div>' +
                        '</div></div>';
                    
                    $('#viewProjectBody').html(modalBody);
                    $('#viewProjectModal').modal('show');
                }
            }
        });
    });
    
    // Delete Project
    $(document).on('click', '.delete-project', function() {
        var projectId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this project? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('projects/delete/') ?>' + projectId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        projectsTable.ajax.reload();
                        
                        // Update counts if stats are provided
                        if (response.stats) {
                            updateProjectCounts(response.stats);
                        }
                        
                        // Refresh all counts across the system
                        refreshAllCounts();
                        
                        alert('Project deleted successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
    
    // Save Project Form
    $('#projectForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var projectId = $('#projectId').val();
        var url = projectId ? 
            '<?= base_url('projects/update/') ?>' + projectId : 
            '<?= base_url('projects/create') ?>';
        
        $('#saveProjectBtn').prop('disabled', true).find('.spinner-border').show();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#projectModal').modal('hide');
                    projectsTable.ajax.reload();
                    
                    // Update counts if stats are provided
                    if (response.stats) {
                        updateProjectCounts(response.stats);
                    }
                    
                    // Refresh all counts across the system
                    refreshAllCounts();
                    
                    alert('Project saved successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            complete: function() {
                $('#saveProjectBtn').prop('disabled', false).find('.spinner-border').hide();
            }
        });
    });
    
    // Function to update project counts
    function updateProjectCounts(stats) {
        $('#totalProjects').text(stats.total_projects);
        $('#activeProjects').text(stats.active_projects);
        $('#completedProjects').text(stats.completed_projects);
    }
    
    // Function to refresh module statistics
    function refreshModuleStats() {
        $.ajax({
            url: '<?= base_url('projects/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    updateProjectCounts(response.data);
                }
            }
        });
    }
    
    // Function to refresh all counts across the system
    function refreshAllCounts() {
        // Update project counts
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
        projectsTable.ajax.reload();
        
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
window.pageLoadFunctions.push(initProjectsDataTable);
</script> 
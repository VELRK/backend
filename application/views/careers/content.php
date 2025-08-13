<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-number" id="totalCareers"><?= isset($data['total_careers']) ? $data['total_careers'] : 0 ?></div>
            <div class="stat-label">Total Careers</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number" id="activeCareers"><?= isset($data['active_careers']) ? $data['active_careers'] : 0 ?></div>
            <div class="stat-label">Active Careers</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-number" id="inactiveCareers"><?= isset($data['inactive_careers']) ? $data['inactive_careers'] : 0 ?></div>
            <div class="stat-label">Inactive Careers</div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-number" id="closedCareers"><?= isset($data['closed_careers']) ? $data['closed_careers'] : 0 ?></div>
            <div class="stat-label">Closed Careers</div>
        </div>
    </div>
</div>

<!-- Careers Management -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-briefcase"></i> Career Management
        </h5>
        <div>
            <button class="btn btn-outline-secondary me-2" id="refreshStatsBtn" title="Refresh Statistics">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-primary" id="addCareerBtn">
                <i class="fas fa-plus"></i> Add New Career
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="careersTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Location</th>
                        <th>Type</th>
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

<!-- Add/Edit Career Modal -->
<div class="modal fade" id="careerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Career</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="careerForm">
                <div class="modal-body">
                    <input type="hidden" id="careerId" name="id">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="full-time">Full Time</option>
                                <option value="part-time">Part Time</option>
                                <option value="contract">Contract</option>
                                <option value="internship">Internship</option>
                                <option value="freelance">Freelance</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Location *</label>
                            <input type="text" class="form-control" id="location" name="location" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Career</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this career? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="statusForm">
                <div class="modal-body">
                    <input type="hidden" id="statusCareerId" name="id">
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-control" id="newStatus" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Global variables
let currentCareerId = null;

// Wait for jQuery to be available
function waitForJQuery(callback) {
    if (typeof jQuery !== 'undefined') {
        callback();
    } else {
        setTimeout(function() {
            waitForJQuery(callback);
        }, 100);
    }
}

// Initialize careers functionality when jQuery is ready
waitForJQuery(function() {
    $(document).ready(function() {
        console.log('Careers page loaded, initializing...');
        
        // Load careers on page load
        loadCareers();
        
        // Add new career button
        $('#addCareerBtn').click(function() {
            console.log('Add career button clicked');
            resetForm();
            $('#modalTitle').text('Add New Career');
            $('#careerModal').modal('show');
        });
        
        // Form submission
        $('#careerForm').submit(function(e) {
            e.preventDefault();
            console.log('Career form submitted');
            
            const formData = new FormData(this);
            const careerId = $('#careerId').val();
            
            if (careerId) {
                // Update existing career
                updateCareer(careerId, formData);
            } else {
                // Create new career
                createCareer(formData);
            }
        });
        
        // Status form submission
        $('#statusForm').submit(function(e) {
            e.preventDefault();
            console.log('Status form submitted');
            
            const status = $('#newStatus').val();
            updateCareerStatus(currentCareerId, status);
        });
        
        // Confirm delete
        $('#confirmDeleteBtn').click(function() {
            console.log('Confirm delete clicked for career ID:', currentCareerId);
            if (currentCareerId) {
                deleteCareer(currentCareerId);
            }
        });
        
        // Refresh stats
        $('#refreshStatsBtn').click(function() {
            console.log('Refresh stats clicked');
            loadCareers();
        });
        
        // Load careers
        function loadCareers() {
            console.log('Loading careers...');
            $.ajax({
                url: '<?= base_url("careers/get_careers") ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('Careers loaded successfully:', response);
                    if (response.status === 'success') {
                        displayCareers(response.data);
                        updateStats(response.data);
                    } else {
                        console.error('Error in response:', response);
                        showAlert('Error loading careers: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', xhr, status, error);
                    showAlert('Error loading careers: ' + error, 'error');
                }
            });
        }
        
        // Display careers in table
        function displayCareers(careers) {
            console.log('Displaying careers:', careers);
            const tbody = $('#careersTable tbody');
            tbody.empty();
            
            if (careers.length === 0) {
                tbody.append('<tr><td colspan="7" class="text-center">No careers found</td></tr>');
                return;
            }
            
            careers.forEach(function(career) {
                const row = `
                    <tr>
                        <td>${career.id}</td>
                        <td>${career.title}</td>
                        <td>${career.location}</td>
                        <td><span class="badge bg-info">${career.type}</span></td>
                        <td>${getStatusBadge(career.status)}</td>
                        <td>${formatDate(career.created_at)}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="editCareer(${career.id})" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-warning me-1" onclick="updateStatus(${career.id})" title="Update Status">
                                <i class="fas fa-toggle-on"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(${career.id})" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(row);
            });
        }
        
        // Update statistics
        function updateStats(careers) {
            const total = careers.length;
            const active = careers.filter(c => c.status === 'active').length;
            const inactive = careers.filter(c => c.status === 'inactive').length;
            const closed = careers.filter(c => c.status === 'closed').length;
            
            console.log('Updating stats:', { total, active, inactive, closed });
            
            $('#totalCareers').text(total);
            $('#activeCareers').text(active);
            $('#inactiveCareers').text(inactive);
            $('#closedCareers').text(closed);
        }
        
        // Create new career
        function createCareer(formData) {
            console.log('Creating career with data:', formData);
            $.ajax({
                url: '<?= base_url("careers/create") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Create career response:', response);
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        $('#careerModal').modal('hide');
                        loadCareers();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Create career error:', xhr, status, error);
                    showAlert('Error creating career: ' + error, 'error');
                }
            });
        }
        
        // Update existing career
        function updateCareer(id, formData) {
            console.log('Updating career ID:', id, 'with data:', formData);
            $.ajax({
                url: '<?= base_url("careers/update/") ?>' + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Update career response:', response);
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        $('#careerModal').modal('hide');
                        loadCareers();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update career error:', xhr, status, error);
                    showAlert('Error updating career: ' + error, 'error');
                }
            });
        }
        
        // Delete career
        function deleteCareer(id) {
            console.log('Deleting career ID:', id);
            $.ajax({
                url: '<?= base_url("careers/delete/") ?>' + id,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    console.log('Delete career response:', response);
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        $('#deleteModal').modal('hide');
                        loadCareers();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete career error:', xhr, status, error);
                    showAlert('Error deleting career: ' + error, 'error');
                }
            });
        }
        
        // Update career status
        function updateCareerStatus(id, status) {
            console.log('Updating career status ID:', id, 'to:', status);
            $.ajax({
                url: '<?= base_url("careers/update_status/") ?>' + id,
                type: 'POST',
                data: { status: status },
                dataType: 'json',
                success: function(response) {
                    console.log('Update status response:', response);
                    if (response.status === 'success') {
                        showAlert(response.message, 'success');
                        $('#statusModal').modal('hide');
                        loadCareers();
                    } else {
                        showAlert(response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Update status error:', xhr, status, error);
                    showAlert('Error updating status: ' + error, 'error');
                }
            });
        }
        
        // Reset form
        function resetForm() {
            $('#careerForm')[0].reset();
            $('#careerId').val('');
            $('#status').val('active');
        }
        
        // Helper functions
        function getStatusBadge(status) {
            const badges = {
                'active': '<span class="badge bg-success">Active</span>',
                'inactive': '<span class="badge bg-warning">Inactive</span>',
                'closed': '<span class="badge bg-danger">Closed</span>'
            };
            return badges[status] || status;
        }
        
        function formatDate(dateString) {
            return new Date(dateString).toLocaleDateString();
        }
        
        function showAlert(message, type) {
            console.log('Alert:', type, message);
            // You can implement your own alert system here
            alert(message);
        }
    });
});

// Global functions for onclick handlers
function editCareer(id) {
    console.log('Edit career clicked for ID:', id);
    $.ajax({
        url: '<?= base_url("careers/get_career/") ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Get career response:', response);
            if (response.status === 'success') {
                const career = response.data;
                $('#careerId').val(career.id);
                $('#title').val(career.title);
                $('#location').val(career.location);
                $('#type').val(career.type);
                $('#description').val(career.description);
                $('#status').val(career.status);
                
                $('#modalTitle').text('Edit Career');
                $('#careerModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            console.error('Get career error:', xhr, status, error);
            alert('Error loading career details');
        }
    });
}

function updateStatus(id) {
    console.log('Update status clicked for ID:', id);
    currentCareerId = id;
    $('#statusCareerId').val(id);
    $('#statusModal').modal('show');
}

function confirmDelete(id) {
    console.log('Confirm delete clicked for ID:', id);
    currentCareerId = id;
    $('#deleteModal').modal('show');
}
</script> 
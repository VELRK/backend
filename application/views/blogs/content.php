<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <i class="fas fa-blog"></i>
            </div>
            <div class="stat-number" id="totalBlogs"><?= isset($data['total_blogs']) ? $data['total_blogs'] : 0 ?></div>
            <div class="stat-label">Total Blogs</div>
        </div>
    </div>
    
    <div class="col-md-6 mb-3">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                <i class="fas fa-user-edit"></i>
            </div>
            <div class="stat-number" id="userBlogs"><?= isset($data['user_blogs']) ? $data['user_blogs'] : 0 ?></div>
            <div class="stat-label">Your Blogs</div>
        </div>
    </div>
</div>

<!-- Blogs Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="fas fa-blog"></i> Blog Management
        </h5>
        <div>
            <button class="btn btn-outline-secondary me-2" id="refreshStatsBtn" title="Refresh Statistics">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
            <button class="btn btn-primary" id="addBlogBtn">
                <i class="fas fa-plus"></i> Add New Blog
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="blogsTable" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Data loaded via AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add/Edit Blog Modal -->
<div class="modal fade" id="blogModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Blog</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blogForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="blogId" name="id">
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="blog_image" class="form-label">Blog Image</label>
                            <input type="file" class="form-control" id="blog_image" name="blog_image" accept="image/*">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="10" required></textarea>
                        <div class="form-text">Use the rich text editor above to format your content with HTML, images, and styling.</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="content_image1" class="form-label">Content Image 1</label>
                            <input type="file" class="form-control" id="content_image1" name="content_image1" accept="image/*">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="content_image2" class="form-label">Content Image 2</label>
                            <input type="file" class="form-control" id="content_image2" name="content_image2" accept="image/*">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="content_image3" class="form-label">Content Image 3</label>
                            <input type="file" class="form-control" id="content_image3" name="content_image3" accept="image/*">
                        </div>
                    </div>
                    
                    <div id="currentImages" class="row" style="display: none;">
                        <div class="col-12">
                            <h6>Current Images:</h6>
                            <div class="row" id="currentImagesContainer">
                                <!-- Current images will be displayed here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBlogBtn">
                        <span class="spinner-border spinner-border-sm me-2" style="display: none;"></span>
                        Save Blog
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Blog Modal -->
<div class="modal fade" id="viewBlogModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Blog Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewBlogBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<script>
// Global variable for CKEditor instance
let editor = null;

// Define the initialization function
function initBlogsDataTable() {
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
    var blogsTable = $('#blogsTable').DataTable({
        ajax: '<?= base_url('blogs/get_blogs') ?>',
        columns: [
            { data: 'id' },
            { 
                data: 'blog_image',
                render: function(data) {
                    if (data) {
                        return '<img src="<?= base_url('uploads/blogs/') ?>' + data + '" alt="Blog" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">';
                    }
                    return '<div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><i class="fas fa-image text-white"></i></div>';
                }
            },
            { data: 'title' },
            { data: 'author_name' },
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
                           '<button class="btn btn-sm btn-outline-info view-blog" data-id="' + data.id + '"><i class="fas fa-eye"></i></button>' +
                           '<button class="btn btn-sm btn-outline-primary edit-blog" data-id="' + data.id + '"><i class="fas fa-edit"></i></button>' +
                           '<button class="btn btn-sm btn-outline-danger delete-blog" data-id="' + data.id + '"><i class="fas fa-trash"></i></button>' +
                           '</div>';
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
    
    // Refresh button functionality
    $('#refreshStatsBtn').click(function() {
        // Show loading state
        $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
        
        // Refresh all data
        refreshModuleStats();
        blogsTable.ajax.reload();
        
        // Re-enable button after a short delay
        setTimeout(function() {
            $('#refreshStatsBtn').prop('disabled', false).html('<i class="fas fa-sync-alt"></i> Refresh');
        }, 1000);
    });
    
    // Load initial stats when page loads
    refreshModuleStats();
    
    // Initialize CKEditor with better error handling
    function initCKEditor() {
        return new Promise((resolve, reject) => {
            try {
                // Destroy existing editor if it exists
                if (editor) {
                    editor.destroy();
                    editor = null;
                }
                
                // Check if CKEditor is available
                if (typeof ClassicEditor === 'undefined') {
                    reject(new Error('CKEditor not loaded'));
                    return;
                }
                
                const editorElement = document.querySelector('#description');
                if (!editorElement) {
                    reject(new Error('Editor element not found'));
                    return;
                }
                
                ClassicEditor
                    .create(editorElement, {
                        toolbar: {
                            items: [
                                'heading',
                                '|',
                                'bold',
                                'italic',
                                'link',
                                'bulletedList',
                                'numberedList',
                                '|',
                                'outdent',
                                'indent',
                                '|',
                                'imageUpload',
                                'blockQuote',
                                'insertTable',
                                'mediaEmbed',
                                'undo',
                                'redo',
                                '|',
                                'alignment',
                                'fontSize',
                                'fontColor',
                                'fontBackgroundColor',
                                '|',
                                'horizontalLine',
                                'pageBreak',
                                '|',
                                'code',
                                'codeBlock'
                            ]
                        },
                        language: 'en',
                        image: {
                            toolbar: [
                                'imageTextAlternative',
                                'imageStyle:inline',
                                'imageStyle:block',
                                'imageStyle:side'
                            ]
                        },
                        table: {
                            contentToolbar: [
                                'tableColumn',
                                'tableRow',
                                'mergeTableCells'
                            ]
                        },
                        licenseKey: '',
                    })
                    .then(newEditor => {
                        editor = newEditor;
                        
                        // Add Bootstrap classes to editor
                        editor.editing.view.change(writer => {
                            const root = editor.editing.view.document.getRoot();
                            if (root) {
                                writer.addClass('form-control', root);
                            }
                        });
                        
                        resolve(editor);
                    })
                    .catch(error => {
                        console.error('CKEditor initialization error:', error);
                        reject(error);
                    });
            } catch (error) {
                console.error('Error in initCKEditor:', error);
                reject(error);
            }
        });
    }
    
    // Add Blog Button
    $('#addBlogBtn').click(function() {
        $('#modalTitle').text('Add New Blog');
        $('#blogForm')[0].reset();
        $('#blogId').val('');
        $('#currentImages').hide();
        $('#blogModal').modal('show');
        
        // Initialize CKEditor after modal is shown with proper timing
        $('#blogModal').on('shown.bs.modal', function() {
            setTimeout(function() {
                initCKEditor().catch(error => {
                    console.error('Failed to initialize CKEditor:', error);
                    // Fallback to regular textarea if CKEditor fails
                    $('#description').removeClass('ck-editor__editable').addClass('form-control');
                });
            }, 100);
        });
    });
    
    // Edit Blog
    $(document).on('click', '.edit-blog', function() {
        var blogId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('blogs/get_blog/') ?>' + blogId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var blog = response.data;
                    $('#modalTitle').text('Edit Blog');
                    $('#blogId').val(blog.id);
                    $('#title').val(blog.title);
                    
                    // Show current images
                    var images = [blog.blog_image, blog.content_image1, blog.content_image2, blog.content_image3].filter(function(img) { return img; });
                    if (images.length > 0) {
                        var imagesHtml = '';
                        images.forEach(function(image) {
                            imagesHtml += '<div class="col-md-3 mb-2"><img src="<?= base_url('uploads/blogs/') ?>' + image + '" alt="Current Image" class="img-thumbnail" style="max-width: 100px;"></div>';
                        });
                        $('#currentImagesContainer').html(imagesHtml);
                        $('#currentImages').show();
                    } else {
                        $('#currentImages').hide();
                    }
                    
                    $('#blogModal').modal('show');
                    
                    // Initialize CKEditor and set content after modal is shown
                    $('#blogModal').on('shown.bs.modal', function() {
                        setTimeout(function() {
                            initCKEditor().then(function(editorInstance) {
                                // Set content after editor is ready
                                setTimeout(function() {
                                    if (editorInstance) {
                                        editorInstance.setData(blog.description);
                                    }
                                }, 200);
                            }).catch(error => {
                                console.error('Failed to initialize CKEditor:', error);
                                // Fallback to regular textarea
                                $('#description').removeClass('ck-editor__editable').addClass('form-control').val(blog.description);
                            });
                        }, 100);
                    });
                }
            }
        });
    });
    
    // View Blog
    $(document).on('click', '.view-blog', function() {
        var blogId = $(this).data('id');
        
        $.ajax({
            url: '<?= base_url('blogs/get_blog/') ?>' + blogId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var blog = response.data;
                    
                    var modalBody = '<div class="row">' +
                        '<div class="col-md-6">';
                    
                    if (blog.blog_image) {
                        modalBody += '<img src="<?= base_url('uploads/blogs/') ?>' + blog.blog_image + '" class="img-fluid rounded" alt="' + blog.title + '">';
                    } else {
                        modalBody += '<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 300px;"><i class="fas fa-image text-white" style="font-size: 4rem;"></i></div>';
                    }
                    
                    modalBody += '</div><div class="col-md-6">' +
                        '<h4 class="text-primary">' + blog.title + '</h4>' +
                        '<p class="text-muted mb-3">By: ' + (blog.author_name || 'Unknown') + '</p>' +
                        '<div class="mb-3"><strong><i class="fas fa-calendar me-2"></i>Created:</strong><p class="mb-0">' + new Date(blog.created_at).toLocaleString() + '</p></div>' +
                        '</div></div>' +
                        '<div class="mt-4">' + blog.description + '</div>';
                    
                    $('#viewBlogBody').html(modalBody);
                    $('#viewBlogModal').modal('show');
                }
            }
        });
    });
    
    // Delete Blog
    $(document).on('click', '.delete-blog', function() {
        var blogId = $(this).data('id');
        
        if (confirm('Are you sure you want to delete this blog? This action cannot be undone.')) {
            $.ajax({
                url: '<?= base_url('blogs/delete/') ?>' + blogId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Refresh the DataTable
                        blogsTable.ajax.reload();
                        
                        // Refresh all counts across the system
                        refreshAllCounts();
                        
                        // Refresh module stats specifically
                        refreshModuleStats();
                        
                        alert('Blog deleted successfully!');
                    } else {
                        alert('Error: ' + response.message);
                    }
                }
            });
        }
    });
    
    // Save Blog Form
    $('#blogForm').submit(function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var blogId = $('#blogId').val();
        var url = blogId ? 
            '<?= base_url('blogs/update/') ?>' + blogId : 
            '<?= base_url('blogs/create') ?>';
        
        // Get content from CKEditor
        if (editor) {
            formData.set('description', editor.getData());
        }
        
        $('#saveBlogBtn').prop('disabled', true).find('.spinner-border').show();
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#blogModal').modal('hide');
                    
                    // Refresh the DataTable
                    blogsTable.ajax.reload();
                    
                    // Refresh all counts across the system
                    refreshAllCounts();
                    
                    // Refresh module stats specifically
                    refreshModuleStats();
                    
                    alert('Blog saved successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            complete: function() {
                $('#saveBlogBtn').prop('disabled', false).find('.spinner-border').hide();
            }
        });
    });
    
    // Function to update dashboard stats
    function updateDashboardStats() {
        $.ajax({
            url: '<?= base_url('blogs/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update the statistics cards on the blog page
                    $('#totalBlogs').text(response.data.total_blogs);
                    $('#userBlogs').text(response.data.user_blogs);
                    
                    // Also refresh dashboard stats if available
                    if (typeof window.refreshDashboardStats === 'function') {
                        window.refreshDashboardStats();
                    }
                }
            }
        });
    }
    
    // Function to refresh all counts across the system
    function refreshAllCounts() {
        // Update blog counts
        updateDashboardStats();
        
        // Update dashboard stats if on dashboard
        if (typeof window.refreshDashboardStats === 'function') {
            window.refreshDashboardStats();
        }
        
        // Update recent activities if available
        if (typeof window.refreshDashboardActivities === 'function') {
            window.refreshDashboardActivities();
        }
    }

    // Function to refresh module statistics cards
    function refreshModuleStats() {
        $.ajax({
            url: '<?= base_url('blogs/get_stats') ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update the statistics cards on the current page
                    $('#totalBlogs').text(response.data.total_blogs);
                    $('#userBlogs').text(response.data.user_blogs);
                }
            }
        });
    }

    // Clean up CKEditor when modal is closed
    $('#blogModal').on('hidden.bs.modal', function() {
        if (editor) {
            editor.destroy();
            editor = null;
        }
        // Remove the event listener to prevent multiple bindings
        $('#blogModal').off('shown.bs.modal');
    });
}

// Ensure window.pageLoadFunctions exists and push the function
window.pageLoadFunctions = window.pageLoadFunctions || [];
window.pageLoadFunctions.push(initBlogsDataTable);
</script>

<style>
/* CKEditor Bootstrap Styling */
.ck-editor__editable {
    min-height: 300px !important;
    max-height: 500px !important;
    overflow-y: auto !important;
}

.ck.ck-editor__main > .ck-editor__editable {
    background-color: #fff !important;
    border: 1px solid #ced4da !important;
    border-radius: 0.375rem !important;
}

.ck.ck-toolbar {
    border: 1px solid #ced4da !important;
    border-bottom: none !important;
    border-radius: 0.375rem 0.375rem 0 0 !important;
    background-color: #f8f9fa !important;
}

.ck.ck-editor__main > .ck-editor__editable:not(.ck-focused) {
    border-color: #ced4da !important;
}

.ck.ck-editor__main > .ck-editor__editable.ck-focused {
    border-color: #86b7fe !important;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
}

/* Bootstrap button styling for CKEditor */
.ck.ck-button {
    border-radius: 0.375rem !important;
    font-size: 0.875rem !important;
}

.ck.ck-button:hover {
    background-color: #e9ecef !important;
}

.ck.ck-button.ck-on {
    background-color: #0d6efd !important;
    color: white !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .ck-editor__editable {
        min-height: 200px !important;
    }
}
</style> 
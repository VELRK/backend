<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blogs extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->helper(['url', 'file']);
        $this->load->model('Blog_model');
    }

    // Main blogs page
    public function index() {
        $data['title'] = 'Blog Management';
        $data['total_blogs'] = $this->Blog_model->get_total_blogs_count();
        $data['user_blogs'] = $this->Blog_model->get_blogs_count_by_user($this->session->userdata('user_id'));
        
        $this->load_view('blogs/index', $data);
    }

    // AJAX: Get all blogs with pagination and search
    public function get_blogs() {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $search = $this->input->get('search') ? $this->input->get('search') : null;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $blogs = $this->Blog_model->get_all_blogs($limit, $offset, $search);
        $total = $this->Blog_model->get_blogs_count($search);
        
        echo json_encode([
            'status' => 'success',
            'data' => $blogs,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ]);
    }

    // AJAX: Get single blog
    public function get_blog($id) {
        $blog = $this->Blog_model->get_blog($id);
        if ($blog) {
            echo json_encode(['status' => 'success', 'data' => $blog]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Blog not found']);
        }
    }

    // AJAX: Create blog
    public function create() {
        if ($this->input->is_ajax_request()) {
            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Handle blog image upload
            $blog_image = '';
            if (!empty($_FILES['blog_image']['name'])) {
                $config['upload_path'] = './uploads/blogs/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                // Create directory if it doesn't exist
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('blog_image')) {
                    $upload_data = $this->upload->data();
                    $blog_image = $upload_data['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Handle content images upload (maximum 3)
            $content_images = ['', '', ''];
            $uploaded_images = 0;
            
            for ($i = 1; $i <= 3; $i++) {
                if (!empty($_FILES['content_image' . $i]['name'])) {
                    $config['upload_path'] = './uploads/blogs/';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                    $config['max_size'] = 2048; // 2MB
                    $config['encrypt_name'] = TRUE;

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('content_image' . $i)) {
                        $upload_data = $this->upload->data();
                        $content_images[$i-1] = $upload_data['file_name'];
                        $uploaded_images++;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error uploading content image ' . $i . ': ' . $this->upload->display_errors()]);
                        return;
                    }
                }
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'blog_image' => $blog_image,
                'content_image1' => $content_images[0],
                'content_image2' => $content_images[1],
                'content_image3' => $content_images[2],
                'user_id' => $this->session->userdata('user_id')
            ];

            // Insert blog
            $blog_id = $this->Blog_model->create_blog($data);
            if ($blog_id) {
                $blog = $this->Blog_model->get_blog($blog_id);
                echo json_encode(['status' => 'success', 'message' => 'Blog created successfully', 'data' => $blog]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create blog']);
            }
        }
    }

    // AJAX: Update blog
    public function update($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->Blog_model->blog_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Blog not found']);
                return;
            }

            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Get current blog data
            $current_blog = $this->Blog_model->get_blog($id);

            // Handle blog image upload
            $blog_image = $current_blog->blog_image;
            if (!empty($_FILES['blog_image']['name'])) {
                $config['upload_path'] = './uploads/blogs/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('blog_image')) {
                    $upload_data = $this->upload->data();
                    $blog_image = $upload_data['file_name'];
                    
                    // Delete old blog image
                    if ($current_blog->blog_image) {
                        $old_file = './uploads/blogs/' . $current_blog->blog_image;
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Handle content images upload
            $content_images = [
                $current_blog->content_image1,
                $current_blog->content_image2,
                $current_blog->content_image3
            ];
            
            for ($i = 1; $i <= 3; $i++) {
                if (!empty($_FILES['content_image' . $i]['name'])) {
                    $config['upload_path'] = './uploads/blogs/';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                    $config['max_size'] = 2048; // 2MB
                    $config['encrypt_name'] = TRUE;

                    $this->load->library('upload', $config);

                    if ($this->upload->do_upload('content_image' . $i)) {
                        $upload_data = $this->upload->data();
                        
                        // Delete old content image
                        if ($content_images[$i-1]) {
                            $old_file = './uploads/blogs/' . $content_images[$i-1];
                            if (file_exists($old_file)) {
                                unlink($old_file);
                            }
                        }
                        
                        $content_images[$i-1] = $upload_data['file_name'];
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'Error uploading content image ' . $i . ': ' . $this->upload->display_errors()]);
                        return;
                    }
                }
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'blog_image' => $blog_image,
                'content_image1' => $content_images[0],
                'content_image2' => $content_images[1],
                'content_image3' => $content_images[2]
            ];

            // Update blog
            if ($this->Blog_model->update_blog($id, $data)) {
                $blog = $this->Blog_model->get_blog($id);
                echo json_encode(['status' => 'success', 'message' => 'Blog updated successfully', 'data' => $blog]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update blog']);
            }
        }
    }

    // AJAX: Delete blog
    public function delete($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->Blog_model->blog_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Blog not found']);
                return;
            }

            if ($this->Blog_model->delete_blog($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Blog deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete blog']);
            }
        }
    }

    // Get blog statistics
    public function get_stats() {
        $data = [
            'total_blogs' => $this->Blog_model->get_total_blogs_count(),
            'user_blogs' => $this->Blog_model->get_blogs_count_by_user($this->session->userdata('user_id')),
            'recent_blogs' => $this->Blog_model->get_recent_blogs(5)
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // Refresh dashboard stats after blog operations
    private function refresh_dashboard_stats() {
        // Get updated stats
        $stats = [
            'total_blogs' => $this->Blog_model->get_total_blogs_count(),
            'user_blogs' => $this->Blog_model->get_blogs_count_by_user($this->session->userdata('user_id'))
        ];
        
        // Return stats for AJAX response
        return $stats;
    }
} 
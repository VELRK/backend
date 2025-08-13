<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->helper(['url', 'file']);
    }

    // Main users page
    public function index() {
        $data['title'] = 'User Management';
        $data['active_users'] = $this->User_model->get_active_users_count();
        $data['inactive_users'] = $this->User_model->get_inactive_users_count();
        
        $this->load_view('users/index', $data);
    }

    // AJAX: Get all users with pagination and search
    public function get_users() {
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $search = $this->input->get('search') ? $this->input->get('search') : null;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $users = $this->User_model->get_all_users($limit, $offset, $search);
        $total = $this->User_model->get_users_count($search);
        
        echo json_encode([
            'status' => 'success',
            'data' => $users,
            'total' => $total,
            'pages' => ceil($total / $limit),
            'current_page' => $page
        ]);
    }

    // AJAX: Get single user
    public function get_user($id) {
        $user = $this->User_model->get_user($id);
        if ($user) {
            // Remove password from response
            unset($user->password);
            echo json_encode(['status' => 'success', 'data' => $user]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found']);
        }
    }

    // AJAX: Create user
    public function create() {
        if ($this->input->is_ajax_request()) {
            // Set validation rules
            $this->form_validation->set_rules('name', 'Name', 'required|trim');
            $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
            $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Handle profile picture upload
            $profile_pic = '';
            if (!empty($_FILES['profile_pic']['name'])) {
                $config['upload_path'] = './uploads/users/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                // Create directory if it doesn't exist
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_pic')) {
                    $upload_data = $this->upload->data();
                    $profile_pic = $upload_data['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Prepare data
            $data = [
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'password' => $this->input->post('password'),
                'profile_pic' => $profile_pic,
                'status' => 'active'
            ];

            // Insert user
            $user_id = $this->User_model->create_user($data);
            if ($user_id) {
                $user = $this->User_model->get_user($user_id);
                unset($user->password);
                
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'User created successfully', 
                    'data' => $user,
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create user']);
            }
        }
    }

    // AJAX: Update user
    public function update($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->User_model->user_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                return;
            }

            // Set validation rules
            $this->form_validation->set_rules('name', 'Name', 'required|trim');
            $this->form_validation->set_rules('username', 'Username', 'required|trim');
            $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email');
            $this->form_validation->set_rules('phone', 'Phone', 'required|trim');

            // Check unique constraints
            if ($this->User_model->username_exists($this->input->post('username'), $id)) {
                echo json_encode(['status' => 'error', 'message' => 'Username already exists']);
                return;
            }

            if ($this->User_model->email_exists($this->input->post('email'), $id)) {
                echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
                return;
            }

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Handle profile picture upload
            $profile_pic = '';
            if (!empty($_FILES['profile_pic']['name'])) {
                $config['upload_path'] = './uploads/users/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('profile_pic')) {
                    $upload_data = $this->upload->data();
                    $profile_pic = $upload_data['file_name'];
                    
                    // Delete old profile picture
                    $old_user = $this->User_model->get_user($id);
                    if ($old_user && $old_user->profile_pic) {
                        $old_file = './uploads/users/' . $old_user->profile_pic;
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Prepare data
            $data = [
                'name' => $this->input->post('name'),
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'phone' => $this->input->post('phone'),
                'status' => $this->input->post('status')
            ];

            // Add password if provided
            if ($this->input->post('password')) {
                $data['password'] = $this->input->post('password');
            }

            // Add profile picture if uploaded
            if ($profile_pic) {
                $data['profile_pic'] = $profile_pic;
            }

            // Update user
            if ($this->User_model->update_user($id, $data)) {
                $user = $this->User_model->get_user($id);
                unset($user->password);
                
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'User updated successfully', 
                    'data' => $user,
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
            }
        }
    }

    // AJAX: Delete user
    public function delete($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->User_model->user_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
                return;
            }

            if ($this->User_model->delete_user($id)) {
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'User deleted successfully',
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete user']);
            }
        }
    }

    // AJAX: Update user status
    public function update_status($id) {
        if ($this->input->is_ajax_request()) {
            $status = $this->input->post('status');
            
            if ($this->User_model->update_status($id, $status)) {
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'User status updated successfully',
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update user status']);
            }
        }
    }

    // Get user statistics
    public function get_stats() {
        $data = [
            'active_users' => $this->User_model->get_active_users_count(),
            'inactive_users' => $this->User_model->get_inactive_users_count(),
            'total_users' => $this->User_model->get_active_users_count() + $this->User_model->get_inactive_users_count()
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // Refresh dashboard stats after user operations
    private function refresh_dashboard_stats() {
        // Get updated stats
        $stats = [
            'active_users' => $this->User_model->get_active_users_count(),
            'inactive_users' => $this->User_model->get_inactive_users_count(),
            'total_users' => $this->User_model->get_active_users_count() + $this->User_model->get_inactive_users_count()
        ];
        
        // Return stats for AJAX response
        return $stats;
    }
} 
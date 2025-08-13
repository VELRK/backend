<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library(['form_validation', 'session']);
        $this->load->helper(['url', 'file']);
    }

    // Login page
    public function login() {
        // Check if already logged in
        if ($this->session->userdata('user_id')) {
            redirect('dashboard');
        }
        
        $data['title'] = 'Login';
        $this->load->view('auth/login', $data);
    }

    // AJAX: Process login
    public function process_login() {
        if ($this->input->is_ajax_request()) {
            // Set validation rules
            $this->form_validation->set_rules('username', 'Username/Email', 'required|trim');
            $this->form_validation->set_rules('password', 'Password', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            $username_or_email = $this->input->post('username');
            $password = $this->input->post('password');

            // Get user by username or email
            $user = $this->User_model->get_user_by_credentials($username_or_email);
            
            if (!$user) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid username or email']);
                return;
            }

            // Check if user is active
            if ($user->status !== 'active') {
                echo json_encode(['status' => 'error', 'message' => 'Your account is inactive. Please contact administrator.']);
                return;
            }

            // Verify password
            if (!password_verify($password, $user->password)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid password']);
                return;
            }

            // Set session data
            $session_data = [
                'user_id' => $user->id,
                'username' => $user->username,
                'name' => $user->name,
                'email' => $user->email,
                'profile_pic' => $user->profile_pic,
                'logged_in' => TRUE
            ];
            
            $this->session->set_userdata($session_data);

            echo json_encode(['status' => 'success', 'message' => 'Login successful', 'redirect' => base_url('dashboard')]);
        }
    }

    // Logout
    public function logout() {
        $this->session->sess_destroy();
        redirect('auth/login');
    }

    // Check if user is logged in (helper method)
    public function is_logged_in() {
        return $this->session->userdata('logged_in') === TRUE;
    }

    // Get current user info
    public function get_current_user() {
        if ($this->session->userdata('user_id')) {
            $user = $this->User_model->get_user($this->session->userdata('user_id'));
            if ($user) {
                unset($user->password);
                echo json_encode(['status' => 'success', 'data' => $user]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User not found']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        }
    }
} 
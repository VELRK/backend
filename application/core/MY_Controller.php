<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    
    protected $user_data = [];
    
    public function __construct() {
        parent::__construct();
        
        // Load common models
        $this->load->model('User_model');
        
        // Check if user is logged in (except for auth pages)
        if ($this->router->fetch_class() !== 'auth') {
            if (!$this->session->userdata('user_id')) {
                redirect('auth/login');
            }
            
            // Get current user data
            $this->user_data['user'] = $this->User_model->get_user($this->session->userdata('user_id'));
            
            // Make user data available to all views
            $this->load->vars($this->user_data);
        }
    }
    
    /**
     * Load view with common data
     */
    protected function load_view($view, $data = []) {
        // Merge common data with view-specific data
        $view_data = array_merge($this->user_data, $data);
        $this->load->view($view, $view_data);
    }
} 
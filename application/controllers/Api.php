<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Blog_model', 'Project_model', 'Career_model', 'Email_activity_model']);
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'file']);
        
        // Set JSON response header
        header('Content-Type: application/json');
        
        // Check if it's an API request
        if (!$this->is_api_request()) {
            $this->send_response(['status' => 'error', 'message' => 'Invalid API request'], 400);
            exit;
        }
        
        // Check authentication for protected endpoints
        // if (!$this->is_authenticated()) {
        //     $this->send_response(['status' => 'error', 'message' => 'Authentication required'], 401);
        //     exit;
        // }
    }

    /**
     * Check if request is an API request
     */
    private function is_api_request() {
        return $this->input->get_request_header('Accept') === 'application/json' || 
               $this->input->get_request_header('Content-Type') === 'application/json' ||
               $this->uri->segment(1) === 'api';
    }

    /**
     * Check if user is authenticated
     */
    private function is_authenticated() {
        // Skip authentication for login endpoint
        if ($this->uri->segment(2) === 'login') {
            return true;
        }
        
        return $this->session->userdata('user_id') ? true : false;
    }

    /**
     * Send JSON response
     */
    private function send_response($data, $status_code = 200) {
        http_response_code($status_code);
        echo json_encode($data);
    }

    /**
     * Get request data (POST, PUT, PATCH)
     */
    private function get_request_data() {
        $input = file_get_contents('php://input');
        return json_decode($input, true) ?: $_POST;
    }

    // ==================== AUTHENTICATION ENDPOINTS ====================

    /**
     * POST /api/login
     * User login
     */
    public function login() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('username', 'Username/Email', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        $user = $this->User_model->get_user_by_credentials($data['username']);
        
        if ($user && password_verify($data['password'], $user->password)) {
            // Set session
            $this->session->set_userdata([
                'user_id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'name' => $user->name
            ]);

            $this->send_response([
                'status' => 'success',
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'name' => $user->name,
                    'profile_pic' => $user->profile_pic
                ]
            ]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Invalid credentials'], 401);
        }
    }

    /**
     * POST /api/logout
     * User logout
     */
    public function logout() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $this->session->sess_destroy();
        $this->send_response(['status' => 'success', 'message' => 'Logout successful']);
    }

    // ==================== DASHBOARD ENDPOINTS ====================

    /**
     * GET /api/dashboard/stats
     * Get dashboard statistics
     */
    public function dashboard_stats() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $stats = [
            'total_users' => $this->User_model->get_active_users_count() + $this->User_model->get_inactive_users_count(),
            'active_users' => $this->User_model->get_active_users_count(),
            'inactive_users' => $this->User_model->get_inactive_users_count(),
            'total_blogs' => $this->Blog_model->get_total_blogs_count(),
            'user_blogs' => $this->Blog_model->get_blogs_count_by_user($this->session->userdata('user_id')),
            'total_projects' => $this->Project_model->get_total_projects_count()
        ];

        $this->send_response(['status' => 'success', 'data' => $stats]);
    }

    /**
     * GET /api/dashboard/recent-activities
     * Get recent activities
     */
    public function recent_activities() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $recent_blogs = $this->Blog_model->get_recent_blogs(5);
        $recent_projects = $this->Project_model->get_recent_projects(5);
        
        $activities = [];
        
        foreach ($recent_blogs as $blog) {
            $activities[] = [
                'type' => 'blog',
                'id' => $blog->id,
                'title' => $blog->title,
                'author' => $blog->author_name,
                'date' => $blog->created_at,
                'icon' => 'fas fa-blog'
            ];
        }
        
        foreach ($recent_projects as $project) {
            $activities[] = [
                'type' => 'project',
                'id' => $project->id,
                'title' => $project->title,
                'date' => $project->created_at,
                'icon' => 'fas fa-project-diagram'
            ];
        }
        
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        $this->send_response(['status' => 'success', 'data' => array_slice($activities, 0, 10)]);
    }

    // ==================== USER ENDPOINTS ====================

    /**
     * GET /api/users
     * Get all users with pagination and search
     */
    public function users() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $page = $this->input->get('page') ?: 1;
        $search = $this->input->get('search') ?: null;
        $limit = $this->input->get('limit') ?: 10;
        $offset = ($page - 1) * $limit;

        $users = $this->User_model->get_all_users($limit, $offset, $search);
        $total = $this->User_model->get_users_count($search);

        $this->send_response([
            'status' => 'success',
            'data' => $users,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/users/{id}
     * Get single user
     */
    public function user($id = null) {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'User ID required'], 400);
            return;
        }

        $user = $this->User_model->get_user($id);
        if ($user) {
            unset($user->password); // Don't send password
            $this->send_response(['status' => 'success', 'data' => $user]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'User not found'], 404);
        }
    }

    /**
     * POST /api/users
     * Create new user
     */
    public function create_user() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[6]');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        $user_id = $this->User_model->create_user($data);
        if ($user_id) {
            $user = $this->User_model->get_user($user_id);
            unset($user->password);
            $this->send_response(['status' => 'success', 'message' => 'User created successfully', 'data' => $user]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to create user'], 500);
        }
    }

    /**
     * PUT /api/users/{id}
     * Update user
     */
    public function update_user($id = null) {
        if ($this->input->method() !== 'put' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'User ID required'], 400);
            return;
        }

        if (!$this->User_model->user_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'User not found'], 404);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        // Check username uniqueness
        if ($this->User_model->username_exists($data['username'], $id)) {
            $this->send_response(['status' => 'error', 'message' => 'Username already exists'], 400);
            return;
        }

        // Check email uniqueness
        if ($this->User_model->email_exists($data['email'], $id)) {
            $this->send_response(['status' => 'error', 'message' => 'Email already exists'], 400);
            return;
        }

        if ($this->User_model->update_user($id, $data)) {
            $user = $this->User_model->get_user($id);
            unset($user->password);
            $this->send_response(['status' => 'success', 'message' => 'User updated successfully', 'data' => $user]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to update user'], 500);
        }
    }

    /**
     * DELETE /api/users/{id}
     * Delete user
     */
    public function delete_user($id = null) {
        if ($this->input->method() !== 'delete' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'User ID required'], 400);
            return;
        }

        if (!$this->User_model->user_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'User not found'], 404);
            return;
        }

        if ($this->User_model->delete_user($id)) {
            $this->send_response(['status' => 'success', 'message' => 'User deleted successfully']);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to delete user'], 500);
        }
    }

    // ==================== BLOG ENDPOINTS ====================

    /**
     * GET /api/blogs
     * Get all blogs with pagination and search
     */
    public function blogs() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $page = $this->input->get('page') ?: 1;
        $search = $this->input->get('search') ?: null;
        $limit = $this->input->get('limit') ?: 10;
        $offset = ($page - 1) * $limit;

        $blogs = $this->Blog_model->get_all_blogs($limit, $offset, $search);
        $total = $this->Blog_model->get_blogs_count($search);

        $this->send_response([
            'status' => 'success',
            'data' => $blogs,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/blogs/{id}
     * Get single blog
     */
    public function blog($id = null) {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Blog ID required'], 400);
            return;
        }

        $blog = $this->Blog_model->get_blog($id);
        if ($blog) {
            $this->send_response(['status' => 'success', 'data' => $blog]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Blog not found'], 404);
        }
    }

    /**
     * POST /api/blogs
     * Create new blog
     */
    public function create_blog() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        $data['user_id'] = $this->session->userdata('user_id');
        $blog_id = $this->Blog_model->create_blog($data);
        
        if ($blog_id) {
            $blog = $this->Blog_model->get_blog($blog_id);
            $this->send_response(['status' => 'success', 'message' => 'Blog created successfully', 'data' => $blog]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to create blog'], 500);
        }
    }

    /**
     * PUT /api/blogs/{id}
     * Update blog
     */
    public function update_blog($id = null) {
        if ($this->input->method() !== 'put' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Blog ID required'], 400);
            return;
        }

        if (!$this->Blog_model->blog_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'Blog not found'], 404);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        if ($this->Blog_model->update_blog($id, $data)) {
            $blog = $this->Blog_model->get_blog($id);
            $this->send_response(['status' => 'success', 'message' => 'Blog updated successfully', 'data' => $blog]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to update blog'], 500);
        }
    }

    /**
     * DELETE /api/blogs/{id}
     * Delete blog
     */
    public function delete_blog($id = null) {
        if ($this->input->method() !== 'delete' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Blog ID required'], 400);
            return;
        }

        if (!$this->Blog_model->blog_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'Blog not found'], 404);
            return;
        }

        if ($this->Blog_model->delete_blog($id)) {
            $this->send_response(['status' => 'success', 'message' => 'Blog deleted successfully']);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to delete blog'], 500);
        }
    }

    // ==================== PROJECT ENDPOINTS ====================

    /**
     * GET /api/projects
     * Get all projects with pagination and search
     */
    public function projects() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $page = $this->input->get('page') ?: 1;
        $search = $this->input->get('search') ?: null;
        $limit = $this->input->get('limit') ?: 1000;
        $offset = ($page - 1) * $limit;

        $projects = $this->Project_model->get_all_projects_paginated($limit, $offset, $search);
        $total = $this->Project_model->get_projects_count($search);

        $this->send_response([
            'status' => 'success',
            'data' => $projects,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    /**
     * GET /api/projects/{id}
     * Get single project
     */
    public function project($id = null) {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Project ID required'], 400);
            return;
        }

        $project = $this->Project_model->get_project($id);
        if ($project) {
            $this->send_response(['status' => 'success', 'data' => $project]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Project not found'], 404);
        }
    }

    /**
     * POST /api/projects
     * Create new project
     */
    public function create_project() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,completed]');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        $project_id = $this->Project_model->create_project($data);
        
        if ($project_id) {
            $project = $this->Project_model->get_project($project_id);
            $this->send_response(['status' => 'success', 'message' => 'Project created successfully', 'data' => $project]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to create project'], 500);
        }
    }

    /**
     * PUT /api/projects/{id}
     * Update project
     */
    public function update_project($id = null) {
        if ($this->input->method() !== 'put' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Project ID required'], 400);
            return;
        }

        if (!$this->Project_model->project_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'Project not found'], 404);
            return;
        }

        $data = $this->get_request_data();
        
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('title', 'Title', 'required|trim');
        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('status', 'Status', 'required|in_list[active,inactive,completed]');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        if ($this->Project_model->update_project($id, $data)) {
            $project = $this->Project_model->get_project($id);
            $this->send_response(['status' => 'success', 'message' => 'Project updated successfully', 'data' => $project]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to update project'], 500);
        }
    }

    /**
     * DELETE /api/projects/{id}
     * Delete project
     */
    public function delete_project($id = null) {
        if ($this->input->method() !== 'delete' && $this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Project ID required'], 400);
            return;
        }

        if (!$this->Project_model->project_exists($id)) {
            $this->send_response(['status' => 'error', 'message' => 'Project not found'], 404);
            return;
        }

        if ($this->Project_model->delete_project($id)) {
            $this->send_response(['status' => 'success', 'message' => 'Project deleted successfully']);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to delete project'], 500);
        }
    }

    // ==================== CAREER ENDPOINTS ====================

    /**
     * GET /api/career
     * Get career information and opportunities from database
     */

     public function careerData()
     {
        $active_careers = $this->Career_model->get_active_careers();
        if ($active_careers) {
            $this->send_response(['status' => 'success','data'=>$active_careers,'message' => 'successfully fetched']);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Failed to fetch'], 500);
        }
     }


    public function career() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        try {
            // Get active careers from database
            $active_careers = $this->Career_model->get_active_careers();
            
            // Get total careers count
            $total_careers = $this->Career_model->get_total_careers_count();
            
            // Get careers by type for statistics
            $full_time_count = $this->Career_model->get_careers_count_by_type('full-time');
            $part_time_count = $this->Career_model->get_careers_count_by_type('part-time');
            $contract_count = $this->Career_model->get_careers_count_by_type('contract');
            $internship_count = $this->Career_model->get_careers_count_by_type('internship');
            $freelance_count = $this->Career_model->get_careers_count_by_type('freelance');
            
            // Get recent careers
            $recent_careers = $this->Career_model->get_recent_careers(5);
            
            // Get unique locations
            $locations = $this->Career_model->get_unique_locations();
            
            $career_data = [
                'statistics' => [
                    'total_positions' => $total_careers,
                    'active_positions' => count($active_careers),
                    'by_type' => [
                        'full_time' => $full_time_count,
                        'part_time' => $part_time_count,
                        'contract' => $contract_count,
                        'internship' => $internship_count,
                        'freelance' => $freelance_count
                    ]
                ],
                'open_positions' => $active_careers,
                'recent_positions' => $recent_careers,
                'available_locations' => $locations,
                'last_updated' => date('Y-m-d H:i:s')
            ];

            $this->send_response([
                'status' => 'success',
                'data' => $career_data
            ]);
            
        } catch (Exception $e) {
            $this->send_response([
                'status' => 'error', 
                'message' => 'Failed to fetch career data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/career/{id}
     * Get single career position details
     */
    public function career_detail($id = null) {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        if (!$id) {
            $this->send_response(['status' => 'error', 'message' => 'Career ID required'], 400);
            return;
        }

        $career = $this->Career_model->get_career($id);
        if ($career) {
            $this->send_response(['status' => 'success', 'data' => $career]);
        } else {
            $this->send_response(['status' => 'error', 'message' => 'Career position not found'], 404);
        }
    }

    /**
     * GET /api/career/search
     * Search careers with filters
     */
    public function career_search() {
        if ($this->input->method() !== 'get') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $search = $this->input->get('search') ?: null;
        $type = $this->input->get('type') ?: null;
        $location = $this->input->get('location') ?: null;
        $page = $this->input->get('page') ?: 1;
        $limit = $this->input->get('limit') ?: 10;
        $offset = ($page - 1) * $limit;

        try {
            $careers = $this->Career_model->search_careers($search, $type, $location, $limit, $offset);
            $total = $this->Career_model->get_search_count($search, $type, $location);

            $this->send_response([
                'status' => 'success',
                'data' => $careers,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ],
                'filters' => [
                    'search' => $search,
                    'type' => $type,
                    'location' => $location
                ]
            ]);
            
        } catch (Exception $e) {
            $this->send_response([
                'status' => 'error', 
                'message' => 'Failed to search careers: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== EMAIL ACTIVITY ENDPOINTS ====================

    /**
     * POST /api/email-activity
     * Store recent activity data from frontend
     */
    public function store_email_activity() {
        if ($this->input->method() !== 'post') {
            $this->send_response(['status' => 'error', 'message' => 'Method not allowed'], 405);
            return;
        }

        $data = $this->get_request_data();
        
        // Validate required fields
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('message', 'message', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('name', 'name', 'required|trim|max_length[255]');
        $this->form_validation->set_rules('email', 'email', 'required|valid_email|max_length[255]');

        if ($this->form_validation->run() == FALSE) {
            $this->send_response(['status' => 'error', 'message' => validation_errors()], 400);
            return;
        }

        try {
            // Prepare data for database (map frontend fields to database fields)
            $activity_data = [
                'message' => $data['message'],
                'fullname' => $data['name'], // Map 'name' to 'fullname' field
                'email' => $data['email'],
                'date' => date('Y-m-d H:i:s') // Current timestamp
            ];

            // Store in database
            $activity_id = $this->Email_activity_model->add_activity($activity_data);
            
            if ($activity_id) {
                $this->send_response([
                    'status' => 'success',
                    'message' => 'Mail Sent',
                    'data' => [
                        'id' => $activity_id,
                        'message' => $data['message'],
                        'name' => $data['name'],
                        'email' => $data['email'],
                        'date' => $activity_data['date']
                    ]
                ]);
            } else {
                $this->send_response(['status' => 'error', 'message' => 'Mail not send'], 500);
            }
            
        } catch (Exception $e) {
            $this->send_response([
                'status' => 'error', 
                'message' => 'Failed to store activity: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== UTILITY ENDPOINTS ====================

    /**
     * GET /api/health
     * Health check endpoint
     */
    public function health() {
        $this->send_response([
            'status' => 'success',
            'message' => 'API is running',
            'timestamp' => date('Y-m-d H:i:s'),
            'version' => '1.0.0'
        ]);
    }

    /**
     * GET /api/options
     * Get common options for forms
     */
    public function options() {
        $options = [
            'user_statuses' => ['active', 'inactive'],
            'project_statuses' => ['active', 'inactive', 'completed'],
            'blog_categories' => ['Technology', 'Business', 'Lifestyle', 'Education', 'Other']
        ];

        $this->send_response(['status' => 'success', 'data' => $options]);
    }
} 
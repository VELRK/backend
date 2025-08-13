<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['User_model', 'Blog_model', 'Project_model']);
        $this->load->library(['session']);
    }

    public function index() {
        echo "<h2>Database Connection Test</h2>";
        
        // Test database connection
        if ($this->db->simple_query('SELECT 1')) {
            echo "<p style='color: green;'>✓ Database connection successful</p>";
        } else {
            echo "<p style='color: red;'>✗ Database connection failed</p>";
            return;
        }
        
        // Test User model
        try {
            $user_count = $this->User_model->get_active_users_count();
            echo "<p style='color: green;'>✓ User model working - Active users: $user_count</p>";
            
            // Get all users
            $users = $this->User_model->get_all_users();
            echo "<p style='color: green;'>✓ Total users in database: " . count($users) . "</p>";
            
            if (count($users) > 0) {
                echo "<h3>Users in Database:</h3>";
                echo "<ul>";
                foreach ($users as $user) {
                    echo "<li>ID: {$user->id}, Name: {$user->name}, Username: {$user->username}, Email: {$user->email}, Status: {$user->status}</li>";
                }
                echo "</ul>";
            } else {
                echo "<p style='color: orange;'>⚠ No users found in database</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ User model error: " . $e->getMessage() . "</p>";
        }
        
        // Test Blog model
        try {
            $blog_count = $this->Blog_model->get_total_blogs_count();
            echo "<p style='color: green;'>✓ Blog model working - Total blogs: $blog_count</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Blog model error: " . $e->getMessage() . "</p>";
        }
        
        // Test Project model
        try {
            $projects = $this->Project_model->get_all_projects();
            $project_count = count($projects);
            echo "<p style='color: green;'>✓ Project model working - Total projects: $project_count</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Project model error: " . $e->getMessage() . "</p>";
        }
        
        // Test session
        if ($this->session->userdata('user_id')) {
            echo "<p style='color: green;'>✓ Session working - User ID: " . $this->session->userdata('user_id') . "</p>";
        } else {
            echo "<p style='color: orange;'>⚠ No active session</p>";
        }
        
        echo "<hr>";
        echo "<h3>Test AJAX Endpoints:</h3>";
        echo "<p><a href='" . base_url('test/users_ajax') . "' target='_blank'>Test Users AJAX</a></p>";
        echo "<p><a href='" . base_url('test/blogs_ajax') . "' target='_blank'>Test Blogs AJAX</a></p>";
        echo "<hr>";
        echo "<p><a href='" . base_url('auth/login') . "'>Go to Login</a></p>";
        echo "<p><a href='" . base_url('users') . "'>Go to Users</a></p>";
    }
    
    public function users_ajax() {
        echo "<h2>Users AJAX Test</h2>";
        echo "<p>Testing: " . base_url('users/get_users') . "</p>";
        
        $users = $this->User_model->get_all_users();
        echo "<pre>";
        print_r($users);
        echo "</pre>";
    }
    
    public function blogs_ajax() {
        echo "<h2>Blogs AJAX Test</h2>";
        echo "<p>Testing: " . base_url('blogs/get_blogs') . "</p>";
        
        $blogs = $this->Blog_model->get_all_blogs();
        echo "<pre>";
        print_r($blogs);
        echo "</pre>";
    }

    public function jquery_test() {
        echo "<h2>jQuery Test</h2>";
        echo "<p>Testing jQuery loading and DataTables...</p>";
        echo "<div id='testDiv'>jQuery Test</div>";
        echo "<script src='https://code.jquery.com/jquery-3.7.0.min.js'></script>";
        echo "<script>";
        echo "$(document).ready(function() {";
        echo "  $('#testDiv').html('jQuery is working!');";
        echo "  console.log('jQuery version:', $.fn.jquery);";
        echo "});";
        echo "</script>";
    }
} 
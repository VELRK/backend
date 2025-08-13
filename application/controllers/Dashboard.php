<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(['Blog_model', 'Project_model', 'Email_activity_model']);
    }

    // Main dashboard page
    public function index() {
        $data['title'] = 'Dashboard';
        
        // Get statistics with error handling
        try {
            $data['stats'] = $this->get_dashboard_stats();
        } catch (Exception $e) {
            log_message('error', 'Dashboard stats error: ' . $e->getMessage());
            $data['stats'] = [
                'total_users' => 0,
                'active_users' => 0,
                'total_blogs' => 0,
                'user_blogs' => 0,
                'total_projects' => 0
            ];
        }
        
        // Get recent data
        $data['recent_blogs'] = $this->Blog_model->get_recent_blogs(5);
        $data['recent_projects'] = $this->Project_model->get_recent_projects(5);
        
        $this->load_view('dashboard/index', $data);
    }

    // Get dashboard statistics via AJAX
    public function get_stats() {
        try {
            $stats = $this->get_dashboard_stats();
            echo json_encode(['status' => 'success', 'data' => $stats]);
        } catch (Exception $e) {
            log_message('error', 'Dashboard stats AJAX error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Failed to load statistics']);
        }
    }

    // AJAX: Get recent activities
    public function get_recent_activities() {
        try {
            $recent_activities = $this->Email_activity_model->get_recent_activities(10);
            
            echo json_encode(['status' => 'success', 'data' => $recent_activities]);
        } catch (Exception $e) {
            log_message('error', 'Recent activities error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Failed to load recent activities']);
        }
    }

    // AJAX: Get single activity details
    public function get_activity($id) {
        try {
            $activity = $this->Email_activity_model->get_activity($id);
            if ($activity) {
                echo json_encode(['status' => 'success', 'data' => $activity]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Activity not found']);
            }
        } catch (Exception $e) {
            log_message('error', 'Get activity error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Failed to load activity details']);
        }
    }

    // Get real-time updates for dashboard
    public function get_updates() {
        try {
            $updates = [
                'stats' => $this->get_dashboard_stats(),
                'recent_activities' => $this->get_recent_activities_data()
            ];
            
            echo json_encode(['status' => 'success', 'data' => $updates]);
        } catch (Exception $e) {
            log_message('error', 'Dashboard updates error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Failed to load updates']);
        }
    }

    // Helper method to get dashboard stats with proper error handling
    private function get_dashboard_stats() {
        $total_users = $this->User_model->get_active_users_count() + $this->User_model->get_inactive_users_count();
        $active_users = $this->User_model->get_active_users_count();
        $total_blogs = $this->Blog_model->get_total_blogs_count();
        $user_blogs = $this->Blog_model->get_blogs_count_by_user($this->session->userdata('user_id'));
        $total_projects = $this->Project_model->get_total_projects_count();
        
        return [
            'total_users' => (int)$total_users,
            'active_users' => (int)$active_users,
            'inactive_users' => (int)($total_users - $active_users),
            'total_blogs' => (int)$total_blogs,
            'user_blogs' => (int)$user_blogs,
            'total_projects' => (int)$total_projects
        ];
    }

    // Helper method to get recent activities data
    private function get_recent_activities_data() {
        return $this->Email_activity_model->get_recent_activities(10);
    }
} 
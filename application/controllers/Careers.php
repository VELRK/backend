<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Careers extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->helper(['url', 'file']);
        $this->load->model('Career_model');
    }

    // Main careers page
    public function index() {
        $data['title'] = 'Career Management';
        $data['careers'] = $this->Career_model->get_all_careers();
        $data['total_careers'] = $this->Career_model->get_total_careers_count();
        $data['active_careers'] = $this->Career_model->get_careers_count_by_status('active');
        $data['inactive_careers'] = $this->Career_model->get_careers_count_by_status('inactive');
        $data['closed_careers'] = $this->Career_model->get_careers_count_by_status('closed');
        
        $this->load_view('careers/index', $data);
    }

    // AJAX: Get all careers
    public function get_careers() {
        $careers = $this->Career_model->get_all_careers();
        echo json_encode(['status' => 'success', 'data' => $careers]);
    }

    // AJAX: Get single career
    public function get_career($id) {
        $career = $this->Career_model->get_career($id);
        if ($career) {
            echo json_encode(['status' => 'success', 'data' => $career]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Career not found']);
        }
    }

    // AJAX: Create career
    public function create() {
        if ($this->input->is_ajax_request()) {
            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('location', 'Location', 'required|trim');
            $this->form_validation->set_rules('type', 'Type', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'location' => $this->input->post('location'),
                'type' => $this->input->post('type'),
                'description' => $this->input->post('description'),
                'status' => 'active' // Default status for new careers
            ];

            // Insert career
            $career_id = $this->Career_model->create_career($data);
            if ($career_id) {
                $career = $this->Career_model->get_career($career_id);
                
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Career created successfully', 
                    'data' => $career,
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create career']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    // AJAX: Update career
    public function update($id) {
        if ($this->input->is_ajax_request()) {
            // Check if career exists
            if (!$this->Career_model->career_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Career not found']);
                return;
            }

            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('location', 'Location', 'required|trim');
            $this->form_validation->set_rules('type', 'Type', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required|trim');
            $this->form_validation->set_rules('status', 'Status', 'required|trim');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'location' => $this->input->post('location'),
                'type' => $this->input->post('type'),
                'description' => $this->input->post('description'),
                'status' => $this->input->post('status')
            ];

            // Update career
            if ($this->Career_model->update_career($id, $data)) {
                $career = $this->Career_model->get_career($id);
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Career updated successfully', 
                    'data' => $career
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update career']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    // AJAX: Delete career
    public function delete($id) {
        if ($this->input->is_ajax_request()) {
            // Check if career exists
            if (!$this->Career_model->career_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Career not found']);
                return;
            }

            // Delete career
            if ($this->Career_model->delete_career($id)) {
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Career deleted successfully',
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete career']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    // AJAX: Update career status
    public function update_status($id) {
        if ($this->input->is_ajax_request()) {
            $status = $this->input->post('status');
            
            if (!in_array($status, ['active', 'inactive', 'closed'])) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                return;
            }

            if ($this->Career_model->update_career($id, ['status' => $status])) {
                echo json_encode(['status' => 'success', 'message' => 'Status updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    // AJAX: Search careers
    public function search() {
        if ($this->input->is_ajax_request()) {
            $search = $this->input->get('search');
            $careers = $this->Career_model->get_all_careers_paginated(null, null, $search);
            echo json_encode(['status' => 'success', 'data' => $careers]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        }
    }

    // Get careers by type
    public function by_type($type) {
        $data['title'] = 'Careers - ' . ucfirst(str_replace('-', ' ', $type));
        $data['careers'] = $this->Career_model->get_careers_by_type($type);
        $data['type'] = $type;
        
        $this->load_view('careers/by_type', $data);
    }

    // Get careers by location
    public function by_location($location) {
        $data['title'] = 'Careers in ' . $location;
        $data['careers'] = $this->Career_model->get_careers_by_location($location);
        $data['location'] = $location;
        
        $this->load_view('careers/by_location', $data);
    }

    // Public careers page (for job seekers)
    public function public_careers() {
        $data['title'] = 'Available Positions';
        $data['careers'] = $this->Career_model->get_active_careers();
        
        $this->load_view('careers/public', $data);
    }

    // Refresh dashboard stats
    private function refresh_dashboard_stats() {
        return [
            'total_careers' => $this->Career_model->get_total_careers_count(),
            'active_careers' => $this->Career_model->get_careers_count_by_status('active'),
            'inactive_careers' => $this->Career_model->get_careers_count_by_status('inactive'),
            'closed_careers' => $this->Career_model->get_careers_count_by_status('closed')
        ];
    }
} 
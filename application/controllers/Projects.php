<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library(['form_validation']);
        $this->load->helper(['url', 'file']);
        $this->load->model('Project_model');
    }

    // Main projects page
    public function index() {
        $data['title'] = 'Project Management';
        $data['projects'] = $this->Project_model->get_all_projects();
        $data['total_projects'] = $this->Project_model->get_total_projects_count();
        $data['active_projects'] = $this->Project_model->get_projects_count_by_status('active');
        $data['completed_projects'] = $this->Project_model->get_projects_count_by_status('completed');
        $data['inactive_projects'] = $this->Project_model->get_projects_count_by_status('inactive');
        
        $this->load_view('projects/index', $data);
    }

    // AJAX: Get all projects
    public function get_projects() {
        $projects = $this->Project_model->get_all_projects();
        echo json_encode(['status' => 'success', 'data' => $projects]);
    }

    // AJAX: Get single project
    public function get_project($id) {
        $project = $this->Project_model->get_project($id);
        if ($project) {
            echo json_encode(['status' => 'success', 'data' => $project]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Project not found']);
        }
    }

    // AJAX: Create project
    public function create() {
        if ($this->input->is_ajax_request()) {
            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required|trim');
            $this->form_validation->set_rules('location', 'Location', 'required|trim');
            $this->form_validation->set_rules('project_date', 'Project Date', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Handle file upload
            $image_name = '';
            if (!empty($_FILES['image']['name'])) {
                $config['upload_path'] = './uploads/projects/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                // Create directory if it doesn't exist
                if (!is_dir($config['upload_path'])) {
                    mkdir($config['upload_path'], 0777, TRUE);
                }

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $upload_data = $this->upload->data();
                    $image_name = $upload_data['file_name'];
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'location' => $this->input->post('location'),
                'project_date' => $this->input->post('project_date'),
                'image' => $image_name,
                'status' => 'active' // Default status for new projects
            ];

            // Insert project
            $project_id = $this->Project_model->create_project($data);
            if ($project_id) {
                $project = $this->Project_model->get_project($project_id);
                
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Project created successfully', 
                    'data' => $project,
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create project']);
            }
        }
    }

    // AJAX: Update project
    public function update($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->Project_model->project_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Project not found']);
                return;
            }

            // Set validation rules
            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('description', 'Description', 'required|trim');
            $this->form_validation->set_rules('location', 'Location', 'required|trim');
            $this->form_validation->set_rules('project_date', 'Project Date', 'required');

            if ($this->form_validation->run() == FALSE) {
                echo json_encode(['status' => 'error', 'message' => validation_errors()]);
                return;
            }

            // Handle file upload
            $image_name = '';
            if (!empty($_FILES['image']['name'])) {
                $config['upload_path'] = './uploads/projects/';
                $config['allowed_types'] = 'gif|jpg|jpeg|png|webp';
                $config['max_size'] = 2048; // 2MB
                $config['encrypt_name'] = TRUE;

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('image')) {
                    $upload_data = $this->upload->data();
                    $image_name = $upload_data['file_name'];
                    
                    // Delete old image
                    $old_project = $this->Project_model->get_project($id);
                    if ($old_project && $old_project->image) {
                        $old_image_path = './uploads/projects/' . $old_project->image;
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                } else {
                    echo json_encode(['status' => 'error', 'message' => $this->upload->display_errors()]);
                    return;
                }
            }

            // Prepare data
            $data = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'location' => $this->input->post('location'),
                'project_date' => $this->input->post('project_date'),
                'status' => $this->input->post('status') ?: 'active'
            ];

            // Add image to data if new image was uploaded
            if ($image_name) {
                $data['image'] = $image_name;
            }

            // Update project
            if ($this->Project_model->update_project($id, $data)) {
                $project = $this->Project_model->get_project($id);
                
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Project updated successfully', 
                    'data' => $project,
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update project']);
            }
        }
    }

    // AJAX: Delete project
    public function delete($id) {
        if ($this->input->is_ajax_request()) {
            if (!$this->Project_model->project_exists($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Project not found']);
                return;
            }

            if ($this->Project_model->delete_project($id)) {
                // Include updated stats in response
                $stats = $this->refresh_dashboard_stats();
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Project deleted successfully',
                    'stats' => $stats
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete project']);
            }
        }
    }

    // Get project statistics
    public function get_stats() {
        $data = [
            'total_projects' => $this->Project_model->get_total_projects_count(),
            'active_projects' => $this->Project_model->get_projects_count_by_status('active'),
            'completed_projects' => $this->Project_model->get_projects_count_by_status('completed'),
            'inactive_projects' => $this->Project_model->get_projects_count_by_status('inactive')
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data]);
    }

    // Refresh dashboard stats after project operations
    private function refresh_dashboard_stats() {
        // Get updated stats
        $stats = [
            'total_projects' => $this->Project_model->get_total_projects_count(),
            'active_projects' => $this->Project_model->get_projects_count_by_status('active'),
            'completed_projects' => $this->Project_model->get_projects_count_by_status('completed'),
            'inactive_projects' => $this->Project_model->get_projects_count_by_status('inactive')
        ];
        
        // Return stats for AJAX response
        return $stats;
    }
} 
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'projects';
    }

    // Get all projects
    public function get_all_projects() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get all projects with pagination and search
    public function get_all_projects_paginated($limit = null, $offset = null, $search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get total count for pagination
    public function get_projects_count($search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    // Get single project by ID
    public function get_project($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Create new project
    public function create_project($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update project
    public function update_project($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete project
    public function delete_project($id) {
        // Get project image before deletion
        $project = $this->get_project($id);
        if ($project && $project->image) {
            $image_path = './uploads/projects/' . $project->image;
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Check if project exists
    public function project_exists($id) {
        return $this->db->get_where($this->table, ['id' => $id])->num_rows() > 0;
    }

    // Get recent projects
    public function get_recent_projects($limit = 5) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    // Get total projects count
    public function get_total_projects_count() {
        return $this->db->count_all_results($this->table);
    }

    // Get projects by status
    public function get_projects_by_status($status) {
        $this->db->where('status', $status);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get projects count by status
    public function get_projects_count_by_status($status) {
        return $this->db->where('status', $status)->count_all_results($this->table);
    }
} 
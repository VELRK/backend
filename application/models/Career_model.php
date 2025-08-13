<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Career_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'careers';
    }

    // Get all careers
    public function get_all_careers() {
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get all careers with pagination and search
    public function get_all_careers_paginated($limit = null, $offset = null, $search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('location', $search);
            $this->db->or_like('type', $search);
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
    public function get_careers_count($search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('location', $search);
            $this->db->or_like('type', $search);
            $this->db->or_like('status', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    // Get single career by ID
    public function get_career($id) {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    // Create new career
    public function create_career($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update career
    public function update_career($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete career
    public function delete_career($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Check if career exists
    public function career_exists($id) {
        return $this->db->get_where($this->table, ['id' => $id])->num_rows() > 0;
    }

    // Get recent careers
    public function get_recent_careers($limit = 5) {
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get($this->table)->result();
    }

    // Get total careers count
    public function get_total_careers_count() {
        return $this->db->count_all_results($this->table);
    }

    // Get careers by status
    public function get_careers_by_status($status) {
        $this->db->where('status', $status);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get careers count by status
    public function get_careers_count_by_status($status) {
        return $this->db->where('status', $status)->count_all_results($this->table);
    }

    // Get careers by type
    public function get_careers_by_type($type) {
        $this->db->where('type', $type);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get careers by location
    public function get_careers_by_location($location) {
        $this->db->like('location', $location);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get careers count by type
    public function get_careers_count_by_type($type) {
        $this->db->where('type', $type);
        $this->db->where('status', 'active');
        return $this->db->count_all_results($this->table);
    }

    // Get active careers for public display
    public function get_active_careers() {
        $this->db->where('status', 'active');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get unique locations from careers
    public function get_unique_locations() {
        $this->db->select('DISTINCT(location) as location');
        $this->db->where('status', 'active');
        $this->db->order_by('location', 'ASC');
        $result = $this->db->get($this->table)->result();
        
        $locations = [];
        foreach ($result as $row) {
            $locations[] = $row->location;
        }
        return $locations;
    }

    // Search careers with multiple filters
    public function search_careers($search = null, $type = null, $location = null, $limit = null, $offset = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('location', $search);
            $this->db->group_end();
        }
        
        if ($type) {
            $this->db->where('type', $type);
        }
        
        if ($location) {
            $this->db->like('location', $location);
        }
        
        // Only show active careers
        $this->db->where('status', 'active');
        
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get($this->table)->result();
    }

    // Get count for search results
    public function get_search_count($search = null, $type = null, $location = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('title', $search);
            $this->db->or_like('description', $search);
            $this->db->or_like('location', $search);
            $this->db->group_end();
        }
        
        if ($type) {
            $this->db->where('type', $type);
        }
        
        if ($location) {
            $this->db->like('location', $location);
        }
        
        // Only count active careers
        $this->db->where('status', 'active');
        
        return $this->db->count_all_results($this->table);
    }
} 
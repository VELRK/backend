<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_activity_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'email_activities';
    }

    // Get all email activities with pagination
    public function get_all_activities($limit = null, $offset = null) {
        $this->db->order_by('date', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get($this->table);
        return $query->result();
    }

    // Get total count of email activities
    public function get_total_count() {
        return $this->db->count_all($this->table);
    }

    // Get single email activity by ID
    public function get_activity($id) {
        $query = $this->db->get_where($this->table, ['id' => $id]);
        return $query->row();
    }

    // Add new email activity
    public function add_activity($data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    // Update email activity
    public function update_activity($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    // Delete email activity
    public function delete_activity($id) {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Get recent email activities (last 10)
    public function get_recent_activities($limit = 10) {
        $this->db->order_by('date', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table);
        return $query->result();
    }
} 
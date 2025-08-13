<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get all users with pagination and search
    public function get_all_users($limit = null, $offset = null, $search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('username', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }
        
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('users');
        return $query->result();
    }

    // Get total count for pagination
    public function get_users_count($search = null) {
        if ($search) {
            $this->db->group_start();
            $this->db->like('name', $search);
            $this->db->or_like('username', $search);
            $this->db->or_like('email', $search);
            $this->db->or_like('phone', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results('users');
    }

    // Get single user by ID
    public function get_user($id) {
        $query = $this->db->get_where('users', ['id' => $id]);
        return $query->row();
    }

    // Get user by username or email for login
    public function get_user_by_credentials($username_or_email) {
        $this->db->where('username', $username_or_email);
        $this->db->or_where('email', $username_or_email);
        $query = $this->db->get('users');
        return $query->row();
    }

    // Check if user exists
    public function user_exists($id) {
        return $this->db->where('id', $id)->count_all_results('users') > 0;
    }

    // Check if username exists
    public function username_exists($username, $exclude_id = null) {
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->where('username', $username)->count_all_results('users') > 0;
    }

    // Check if email exists
    public function email_exists($email, $exclude_id = null) {
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        return $this->db->where('email', $email)->count_all_results('users') > 0;
    }

    // Create new user
    public function create_user($data) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    // Update user
    public function update_user($id, $data) {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update('users', $data);
    }

    // Delete user
    public function delete_user($id) {
        // Get user info to delete profile picture
        $user = $this->get_user($id);
        if ($user && $user->profile_pic) {
            $file_path = './uploads/users/' . $user->profile_pic;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        
        $this->db->where('id', $id);
        return $this->db->delete('users');
    }

    // Update user status
    public function update_status($id, $status) {
        $this->db->where('id', $id);
        return $this->db->update('users', ['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // Verify password
    public function verify_password($user_id, $password) {
        $user = $this->get_user($user_id);
        if ($user) {
            return password_verify($password, $user->password);
        }
        return false;
    }

    // Get active users count
    public function get_active_users_count() {
        return $this->db->where('status', 'active')->count_all_results('users');
    }

    // Get inactive users count
    public function get_inactive_users_count() {
        return $this->db->where('status', 'inactive')->count_all_results('users');
    }
} 
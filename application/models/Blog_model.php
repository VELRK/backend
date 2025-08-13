<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Get all blogs with pagination and search
    public function get_all_blogs($limit = null, $offset = null, $search = null) {
        $this->db->select('blogs.*, users.name as author_name');
        $this->db->from('blogs');
        $this->db->join('users', 'users.id = blogs.user_id', 'left');
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('blogs.title', $search);
            $this->db->or_like('blogs.description', $search);
            $this->db->or_like('users.name', $search);
            $this->db->group_end();
        }
        
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('blogs.created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    // Get total count for pagination
    public function get_blogs_count($search = null) {
        $this->db->select('blogs.id');
        $this->db->from('blogs');
        $this->db->join('users', 'users.id = blogs.user_id', 'left');
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('blogs.title', $search);
            $this->db->or_like('blogs.description', $search);
            $this->db->or_like('users.name', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    // Get single blog by ID
    public function get_blog($id) {
        $this->db->select('blogs.*, users.name as author_name');
        $this->db->from('blogs');
        $this->db->join('users', 'users.id = blogs.user_id', 'left');
        $this->db->where('blogs.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    // Check if blog exists
    public function blog_exists($id) {
        return $this->db->where('id', $id)->count_all_results('blogs') > 0;
    }

    // Create new blog
    public function create_blog($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert('blogs', $data);
        return $this->db->insert_id();
    }

    // Update blog
    public function update_blog($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        return $this->db->update('blogs', $data);
    }

    // Delete blog
    public function delete_blog($id) {
        // Get blog info to delete images
        $blog = $this->get_blog($id);
        if ($blog) {
            $images = [
                $blog->blog_image,
                $blog->content_image1,
                $blog->content_image2,
                $blog->content_image3
            ];
            
            foreach ($images as $image) {
                if ($image) {
                    $file_path = './uploads/blogs/' . $image;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }
        
        $this->db->where('id', $id);
        return $this->db->delete('blogs');
    }

    // Get blogs by user
    public function get_blogs_by_user($user_id, $limit = null, $offset = null) {
        $this->db->where('user_id', $user_id);
        
        if ($limit && $offset) {
            $this->db->limit($limit, $offset);
        }
        
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get('blogs');
        return $query->result();
    }

    // Get recent blogs
    public function get_recent_blogs($limit = 5) {
        $this->db->select('blogs.*, users.name as author_name');
        $this->db->from('blogs');
        $this->db->join('users', 'users.id = blogs.user_id', 'left');
        $this->db->order_by('blogs.created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    // Get total blogs count
    public function get_total_blogs_count() {
        return $this->db->count_all_results('blogs');
    }

    // Get blogs count by user
    public function get_blogs_count_by_user($user_id) {
        return $this->db->where('user_id', $user_id)->count_all_results('blogs');
    }
} 
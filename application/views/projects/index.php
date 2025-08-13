<?php 
// Ensure $data is defined
if (!isset($data)) {
    $data = [];
}

// Check if user data exists and is valid
if (isset($data['user']) && $data['user']) {
    $user_data = [
        'name' => $data['user']->name ?? 'User',
        'username' => $data['user']->username ?? 'user',
        'email' => $data['user']->email ?? 'user@example.com',
        'profile_pic' => $data['user']->profile_pic ?? null
    ];
} else {
    // Fallback user data
    $user_data = [
        'name' => 'User',
        'username' => 'user',
        'email' => 'user@example.com',
        'profile_pic' => null
    ];
}

$content = $this->load->view('projects/content', $data, TRUE);
$this->load->view('layouts/main', [
    'content' => $content, 
    'user' => $user_data, 
    'title' => 'Project Management'
]);
?> 
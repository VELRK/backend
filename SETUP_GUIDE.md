# Ferrigor Management System - Setup Guide

## Quick Setup

### 1. Database Setup
- Create a new MySQL database
- Import `database.sql` file
- Update database credentials in `application/config/database.php`

### 2. Configuration
- Update base URL in `application/config/config.php`
- Ensure upload directories exist: `uploads/users/`, `uploads/blogs/`, `uploads/projects/`

### 3. Default Login
- Username: `admin`
- Password: `admin123`
- Email: `admin@ferrigor.com`

## Features Implemented

### ✅ User Management
- Name, username, email, phone, password, profile picture
- Status management (active/inactive)
- AJAX CRUD operations with DataTables
- Search and pagination

### ✅ Blog Management
- Title, description (HTML editor), blog image
- Content images (up to 3)
- CKEditor 5 integration
- AJAX operations

### ✅ Project Management
- Title, description, location, project date, image
- Existing functionality enhanced

### ✅ Authentication System
- Login/logout functionality
- Session management
- Status-based access control

### ✅ Modern UI
- Bootstrap 5 responsive design
- Sidebar navigation
- DataTables with search/pagination
- SweetAlert2 notifications
- AJAX operations (no page reloads)

## Access URLs
- Login: `http://localhost/ferrigor/`
- Dashboard: `http://localhost/ferrigor/dashboard`
- Users: `http://localhost/ferrigor/users`
- Blogs: `http://localhost/ferrigor/blogs`
- Projects: `http://localhost/ferrigor/projects`

## Database Tables
- `users` - User management
- `blogs` - Blog management  
- `projects` - Project management

All tables include proper relationships and timestamps. 
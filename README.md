# Ferrigor Management System

A comprehensive web-based management system built with CodeIgniter 3, featuring user management, blog management, and project management with modern UI and AJAX functionality.

## Features

### 🔐 User Management
- **User Registration & Authentication**
  - Secure login with username/email
  - Password hashing with bcrypt
  - Session management
  - Status-based access control (active/inactive users)

- **User Profile Management**
  - Name, username, email, phone
  - Profile picture upload
  - Password change functionality
  - User status management

### 📝 Blog Management
- **Rich Content Editor**
  - CKEditor 5 integration
  - HTML content support
  - Multiple image uploads (up to 3 content images)
  - Blog cover image

- **Blog Features**
  - Title and description
  - Author tracking
  - Creation date tracking
  - Search and pagination

### 🏗️ Project Management
- **Project CRUD Operations**
  - Project title and description
  - Location and project date
  - Project image upload
  - Full CRUD functionality

### 🎨 Modern UI/UX
- **Responsive Design**
  - Bootstrap 5 framework
  - Mobile-friendly interface
  - Modern card-based layout

- **Interactive Components**
  - DataTables with search and pagination
  - AJAX-powered operations (no page reloads)
  - SweetAlert2 for notifications
  - Modal forms for CRUD operations

- **Sidebar Navigation**
  - Collapsible sidebar
  - Module-based navigation
  - User profile display

## Database Structure

### Users Table
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR(255), NOT NULL)
- username (VARCHAR(100), NOT NULL, UNIQUE)
- email (VARCHAR(255), NOT NULL, UNIQUE)
- phone (VARCHAR(20), NOT NULL)
- password (VARCHAR(255), NOT NULL)
- profile_pic (VARCHAR(255))
- status (ENUM('active','inactive'), DEFAULT 'active')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Blogs Table
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR(255), NOT NULL)
- description (LONGTEXT, NOT NULL)
- blog_image (VARCHAR(255))
- content_image1 (VARCHAR(255))
- content_image2 (VARCHAR(255))
- content_image3 (VARCHAR(255))
- user_id (INT, FOREIGN KEY)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### Projects Table
```sql
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR(255), NOT NULL)
- description (TEXT)
- location (VARCHAR(255))
- project_date (DATE)
- image (VARCHAR(255))
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

### Setup Instructions

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd ferrigor
   ```

2. **Database Setup**
   - Create a new MySQL database
   - Import the `database.sql` file
   - Update database credentials in `application/config/database.php`

3. **Configure Database**
   ```php
   // application/config/database.php
   $db['default'] = array(
       'hostname' => 'localhost',
       'username' => 'your_username',
       'password' => 'your_password',
       'database' => 'your_database_name',
       'dbdriver' => 'mysqli',
       // ... other settings
   );
   ```

4. **Set Base URL**
   ```php
   // application/config/config.php
   $config['base_url'] = 'http://localhost/ferrigor/';
   ```

5. **Create Upload Directories**
   ```bash
   mkdir uploads/users
   mkdir uploads/blogs
   mkdir uploads/projects
   chmod 755 uploads/users uploads/blogs uploads/projects
   ```

6. **Access the Application**
   - Open your browser and navigate to `http://localhost/ferrigor/`
   - You'll be redirected to the login page

### Default Login Credentials
- **Username:** admin
- **Password:** admin123
- **Email:** admin@ferrigor.com

## Usage

### Authentication
1. **Login**: Use username/email and password to log in
2. **Dashboard**: View system statistics and recent activities
3. **Logout**: Click the logout button in the top navigation

### User Management
1. **View Users**: Navigate to "User Management" in the sidebar
2. **Add User**: Click "Add New User" button
3. **Edit User**: Click the edit button on any user row
4. **Delete User**: Click the delete button (with confirmation)
5. **Status Management**: Toggle user active/inactive status

### Blog Management
1. **View Blogs**: Navigate to "Blog Management" in the sidebar
2. **Add Blog**: Click "Add New Blog" button
3. **Rich Editor**: Use CKEditor for content creation
4. **Image Upload**: Upload blog cover and content images
5. **Edit/Delete**: Manage existing blogs

### Project Management
1. **View Projects**: Navigate to "Project Management" in the sidebar
2. **Add Project**: Click "Add New Project" button
3. **Project Details**: Fill in title, description, location, and date
4. **Image Upload**: Upload project images
5. **Manage Projects**: Edit or delete existing projects

## Technical Features

### AJAX Integration
- All CRUD operations use AJAX
- No page reloads for better UX
- Real-time form validation
- Dynamic content updates

### Security Features
- Password hashing with bcrypt
- SQL injection protection
- XSS protection
- File upload validation
- Session management

### File Management
- Secure file uploads
- Image type validation
- File size limits (2MB)
- Encrypted filenames
- Automatic cleanup on delete

### Responsive Design
- Bootstrap 5 framework
- Mobile-first approach
- Collapsible sidebar
- Touch-friendly interface

## File Structure

```
ferrigor/
├── application/
│   ├── controllers/
│   │   ├── Auth.php          # Authentication controller
│   │   ├── Dashboard.php     # Dashboard controller
│   │   ├── Users.php         # User management controller
│   │   ├── Blogs.php         # Blog management controller
│   │   └── Projects.php      # Project management controller
│   ├── models/
│   │   ├── User_model.php    # User data operations
│   │   ├── Blog_model.php    # Blog data operations
│   │   └── Project_model.php # Project data operations
│   ├── views/
│   │   ├── layouts/
│   │   │   └── main.php      # Main layout template
│   │   ├── auth/
│   │   │   └── login.php     # Login page
│   │   ├── dashboard/
│   │   │   ├── index.php     # Dashboard wrapper
│   │   │   └── content.php   # Dashboard content
│   │   ├── users/
│   │   │   ├── index.php     # Users wrapper
│   │   │   └── content.php   # Users content
│   │   └── blogs/
│   │       ├── index.php     # Blogs wrapper
│   │       └── content.php   # Blogs content
│   └── config/
│       ├── autoload.php      # Auto-loading configuration
│       ├── database.php      # Database configuration
│       └── config.php        # Application configuration
├── uploads/
│   ├── users/                # User profile pictures
│   ├── blogs/                # Blog images
│   └── projects/             # Project images
├── database.sql              # Database structure
├── index.php                 # Application entry point
└── README.md                 # This file
```

## API Endpoints

### Authentication
- `GET /auth/login` - Login page
- `POST /auth/process_login` - Process login
- `GET /auth/logout` - Logout

### User Management
- `GET /users` - Users page
- `GET /users/get_users` - Get users with pagination
- `GET /users/get_user/{id}` - Get single user
- `POST /users/create` - Create user
- `POST /users/update/{id}` - Update user
- `POST /users/delete/{id}` - Delete user
- `POST /users/update_status/{id}` - Update user status

### Blog Management
- `GET /blogs` - Blogs page
- `GET /blogs/get_blogs` - Get blogs with pagination
- `GET /blogs/get_blog/{id}` - Get single blog
- `POST /blogs/create` - Create blog
- `POST /blogs/update/{id}` - Update blog
- `POST /blogs/delete/{id}` - Delete blog

### Dashboard
- `GET /dashboard` - Dashboard page
- `GET /dashboard/get_stats` - Get statistics
- `GET /dashboard/get_recent_activities` - Get recent activities

## Browser Support
- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Dependencies
- **Frontend**: Bootstrap 5.3.0, jQuery 3.7.0, DataTables 1.13.6, SweetAlert2 11.0, CKEditor 5.27.1
- **Backend**: CodeIgniter 3.1.13, PHP 7.4+
- **Database**: MySQL 5.7+

## License
This project is licensed under the MIT License.

## Support
For support and questions, please contact the development team.

---

**Note**: This is a development version. For production deployment, ensure proper security configurations and environment settings. #   b a c k e n d  
 
# Project Management Module Setup

## Database Setup

1. **Create Database**: Create a new MySQL database in phpMyAdmin
2. **Import Table**: Run the SQL from `database.sql` file in your database
3. **Configure Database**: Update `application/config/database.php` with your database credentials:

```php
$db['default'] = array(
    'hostname' => 'localhost',
    'username' => 'your_username',
    'password' => 'your_password',
    'database' => 'your_database_name',
    'dbdriver' => 'mysqli',
    // ... other settings
);
```

## File Structure Created

```
application/
├── controllers/
│   └── Projects.php          # Main controller with CRUD operations
├── models/
│   └── Project_model.php     # Database operations
├── views/
│   └── projects/
│       └── index.php         # Main view with Bootstrap UI
└── config/
    ├── autoload.php          # Updated to include database library
    └── config.php            # Updated base_url

uploads/
└── projects/                 # Directory for project images

.htaccess                     # URL rewriting rules
database.sql                  # Database table structure
```

## Features Implemented

### ✅ CRUD Operations
- **Create**: Add new projects with image upload
- **Read**: View all projects and individual project details
- **Update**: Edit existing projects
- **Delete**: Remove projects with image cleanup

### ✅ Modern UI/UX
- **Bootstrap 5**: Modern, responsive design
- **Card Layout**: Beautiful project cards with hover effects
- **Modal Forms**: Clean create/edit forms
- **Date Picker**: Flatpickr for date selection
- **Image Preview**: Current image display in edit mode

### ✅ AJAX Integration
- **No Page Reloads**: All operations handled via AJAX
- **Real-time Updates**: Dynamic content updates
- **Form Validation**: Client and server-side validation
- **File Upload**: AJAX image upload with progress

### ✅ Security Features
- **File Type Validation**: Only image files allowed
- **File Size Limits**: 2MB maximum file size
- **SQL Injection Protection**: CodeIgniter's built-in protection
- **XSS Protection**: Input sanitization

## Usage

### Access the Module
- **Main URL**: `http://localhost/ferrigor/projects`
- **Alternative**: `http://localhost/ferrigor/welcome/projects`

### Available Operations
1. **View Projects**: See all projects in a responsive grid
2. **Add Project**: Click "Add New Project" button
3. **Edit Project**: Click "Edit" button on any project card
4. **View Details**: Click "View" button for detailed information
5. **Delete Project**: Click "Delete" button (with confirmation)

### Form Fields
- **Title**: Project title (required)
- **Description**: Detailed project description (required)
- **Location**: Project location (required)
- **Project Date**: Date picker for project date (required)
- **Image**: Optional image upload (JPG, PNG, GIF, WEBP, max 2MB)

## Technical Details

### Database Table: `projects`
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

### AJAX Endpoints
- `GET /projects/get_projects` - Get all projects
- `GET /projects/get_project/{id}` - Get single project
- `POST /projects/create` - Create new project
- `POST /projects/update/{id}` - Update project
- `POST /projects/delete/{id}` - Delete project

### File Upload
- **Directory**: `uploads/projects/`
- **Naming**: Encrypted filenames for security
- **Cleanup**: Old images deleted when updating/deleting projects

## Troubleshooting

### Common Issues
1. **404 Errors**: Ensure `.htaccess` is in root directory and mod_rewrite is enabled
2. **Database Connection**: Check database credentials in `database.php`
3. **Image Upload**: Ensure `uploads/projects/` directory is writable
4. **AJAX Errors**: Check browser console for JavaScript errors

### Required Apache Modules
- `mod_rewrite` (for URL rewriting)
- `mod_php` (for PHP processing)

### Browser Compatibility
- Modern browsers with ES6+ support
- jQuery 3.6.0+
- Bootstrap 5.3.0+ 
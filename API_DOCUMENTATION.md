# Ferrigor Management System - REST API Documentation

## Overview
This document describes the REST API endpoints for the Ferrigor Management System. The API provides access to all system modules including users, blogs, projects, and dashboard data.

## Base URL
```
http://your-domain.com/api/
```

## Authentication
Most endpoints require authentication via session cookies. The login endpoint is the only public endpoint.

## Response Format
All API responses follow this standard format:
```json
{
    "status": "success|error",
    "message": "Response message",
    "data": {...}
}
```

## HTTP Status Codes
- `200` - Success
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `405` - Method Not Allowed
- `500` - Internal Server Error

---

## Authentication Endpoints

### Login
**POST** `/api/login`

Authenticate a user and create a session.

**Request Body:**
```json
{
    "username": "admin",
    "password": "password123"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user_id": 1,
        "username": "admin",
        "email": "admin@example.com",
        "name": "Administrator",
        "profile_pic": "profile.jpg"
    }
}
```

### Logout
**POST** `/api/logout`

Destroy the current user session.

**Response:**
```json
{
    "status": "success",
    "message": "Logout successful"
}
```

---

## Dashboard Endpoints

### Get Dashboard Statistics
**GET** `/api/dashboard/stats`

Retrieve dashboard statistics including user counts, blog counts, and project counts.

**Response:**
```json
{
    "status": "success",
    "data": {
        "total_users": 25,
        "active_users": 20,
        "inactive_users": 5,
        "total_blogs": 15,
        "user_blogs": 8,
        "total_projects": 12
    }
}
```

### Get Recent Activities
**GET** `/api/dashboard/recent-activities`

Retrieve recent activities from blogs and projects.

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "type": "blog",
            "id": 1,
            "title": "New Blog Post",
            "author": "John Doe",
            "date": "2024-01-15 10:30:00",
            "icon": "fas fa-blog"
        },
        {
            "type": "project",
            "id": 3,
            "title": "Website Redesign",
            "date": "2024-01-14 15:45:00",
            "icon": "fas fa-project-diagram"
        }
    ]
}
```

---

## User Management Endpoints

### Get All Users
**GET** `/api/users?page=1&limit=10&search=john`

Retrieve all users with pagination and search capabilities.

**Query Parameters:**
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10)
- `search` - Search term for name, username, email, or phone

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "username": "johndoe",
            "email": "john@example.com",
            "phone": "+1234567890",
            "status": "active",
            "created_at": "2024-01-01 00:00:00",
            "updated_at": "2024-01-15 10:30:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 25,
        "pages": 3
    }
}
```

### Get Single User
**GET** `/api/users/{id}`

Retrieve a specific user by ID.

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "name": "John Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "phone": "+1234567890",
        "status": "active",
        "profile_pic": "profile.jpg",
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

### Create User
**POST** `/api/users`

Create a new user.

**Request Body:**
```json
{
    "name": "Jane Smith",
    "username": "janesmith",
    "email": "jane@example.com",
    "phone": "+1234567891",
    "password": "password123",
    "status": "active"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User created successfully",
    "data": {
        "id": 2,
        "name": "Jane Smith",
        "username": "janesmith",
        "email": "jane@example.com",
        "phone": "+1234567891",
        "status": "active",
        "created_at": "2024-01-15 11:00:00",
        "updated_at": "2024-01-15 11:00:00"
    }
}
```

### Update User
**PUT** `/api/users/{id}`

Update an existing user.

**Request Body:**
```json
{
    "name": "Jane Smith Updated",
    "email": "jane.updated@example.com",
    "status": "inactive"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User updated successfully",
    "data": {
        "id": 2,
        "name": "Jane Smith Updated",
        "username": "janesmith",
        "email": "jane.updated@example.com",
        "phone": "+1234567891",
        "status": "inactive",
        "updated_at": "2024-01-15 12:00:00"
    }
}
```

### Delete User
**DELETE** `/api/users/{id}`

Delete a user.

**Response:**
```json
{
    "status": "success",
    "message": "User deleted successfully"
}
```

---

## Blog Management Endpoints

### Get All Blogs
**GET** `/api/blogs?page=1&limit=10&search=technology`

Retrieve all blogs with pagination and search capabilities.

**Query Parameters:**
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10)
- `search` - Search term for title, description, or author

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Technology Trends 2024",
            "description": "<p>Latest technology trends...</p>",
            "blog_image": "tech-trends.jpg",
            "content_image1": "trend1.jpg",
            "content_image2": "trend2.jpg",
            "content_image3": "trend3.jpg",
            "user_id": 1,
            "author_name": "John Doe",
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-15 10:30:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 15,
        "pages": 2
    }
}
```

### Get Single Blog
**GET** `/api/blogs/{id}`

Retrieve a specific blog by ID.

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Technology Trends 2024",
        "description": "<p>Latest technology trends...</p>",
        "blog_image": "tech-trends.jpg",
        "content_image1": "trend1.jpg",
        "content_image2": "trend2.jpg",
        "content_image3": "trend3.jpg",
        "user_id": 1,
        "author_name": "John Doe",
        "created_at": "2024-01-15 10:30:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

### Create Blog
**POST** `/api/blogs`

Create a new blog post.

**Request Body:**
```json
{
    "title": "New Blog Post",
    "description": "<p>This is the content of the blog post...</p>",
    "blog_image": "blog-image.jpg",
    "content_image1": "content1.jpg",
    "content_image2": "content2.jpg",
    "content_image3": "content3.jpg"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Blog created successfully",
    "data": {
        "id": 2,
        "title": "New Blog Post",
        "description": "<p>This is the content of the blog post...</p>",
        "blog_image": "blog-image.jpg",
        "content_image1": "content1.jpg",
        "content_image2": "content2.jpg",
        "content_image3": "content3.jpg",
        "user_id": 1,
        "author_name": "John Doe",
        "created_at": "2024-01-15 11:00:00",
        "updated_at": "2024-01-15 11:00:00"
    }
}
```

### Update Blog
**PUT** `/api/blogs/{id}`

Update an existing blog post.

**Request Body:**
```json
{
    "title": "Updated Blog Title",
    "description": "<p>Updated blog content...</p>"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Blog updated successfully",
    "data": {
        "id": 2,
        "title": "Updated Blog Title",
        "description": "<p>Updated blog content...</p>",
        "blog_image": "blog-image.jpg",
        "content_image1": "content1.jpg",
        "content_image2": "content2.jpg",
        "content_image3": "content3.jpg",
        "user_id": 1,
        "author_name": "John Doe",
        "updated_at": "2024-01-15 12:00:00"
    }
}
```

### Delete Blog
**DELETE** `/api/blogs/{id}`

Delete a blog post.

**Response:**
```json
{
    "status": "success",
    "message": "Blog deleted successfully"
}
```

---

## Project Management Endpoints

### Get All Projects
**GET** `/api/projects?page=1&limit=10&search=website`

Retrieve all projects with pagination and search capabilities.

**Query Parameters:**
- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10)
- `search` - Search term for title, description, or status

**Response:**
```json
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "title": "Website Redesign",
            "description": "Complete redesign of company website",
            "status": "active",
            "image": "website-project.jpg",
            "created_at": "2024-01-01 00:00:00",
            "updated_at": "2024-01-15 10:30:00"
        }
    ],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 12,
        "pages": 2
    }
}
```

### Get Single Project
**GET** `/api/projects/{id}`

Retrieve a specific project by ID.

**Response:**
```json
{
    "status": "success",
    "data": {
        "id": 1,
        "title": "Website Redesign",
        "description": "Complete redesign of company website",
        "status": "active",
        "image": "website-project.jpg",
        "created_at": "2024-01-01 00:00:00",
        "updated_at": "2024-01-15 10:30:00"
    }
}
```

### Create Project
**POST** `/api/projects`

Create a new project.

**Request Body:**
```json
{
    "title": "Mobile App Development",
    "description": "Development of iOS and Android mobile applications",
    "status": "active",
    "image": "mobile-app.jpg"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Project created successfully",
    "data": {
        "id": 2,
        "title": "Mobile App Development",
        "description": "Development of iOS and Android mobile applications",
        "status": "active",
        "image": "mobile-app.jpg",
        "created_at": "2024-01-15 11:00:00",
        "updated_at": "2024-01-15 11:00:00"
    }
}
```

### Update Project
**PUT** `/api/projects/{id}`

Update an existing project.

**Request Body:**
```json
{
    "title": "Updated Project Title",
    "description": "Updated project description",
    "status": "completed"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "Project updated successfully",
    "data": {
        "id": 2,
        "title": "Updated Project Title",
        "description": "Updated project description",
        "status": "completed",
        "image": "mobile-app.jpg",
        "updated_at": "2024-01-15 12:00:00"
    }
}
```

### Delete Project
**DELETE** `/api/projects/{id}`

Delete a project.

**Response:**
```json
{
    "status": "success",
    "message": "Project deleted successfully"
}
```

---

## Utility Endpoints

### Health Check
**GET** `/api/health`

Check if the API is running.

**Response:**
```json
{
    "status": "success",
    "message": "API is running",
    "timestamp": "2024-01-15 12:00:00",
    "version": "1.0.0"
}
```

### Get Options
**GET** `/api/options`

Get common options for forms and dropdowns.

**Response:**
```json
{
    "status": "success",
    "data": {
        "user_statuses": ["active", "inactive"],
        "project_statuses": ["active", "inactive", "completed"],
        "blog_categories": ["Technology", "Business", "Lifestyle", "Education", "Other"]
    }
}
```

---

## Error Handling

### Validation Errors
When form validation fails, the API returns a 400 status with validation error messages:

```json
{
    "status": "error",
    "message": "The Title field is required.\nThe Description field is required."
}
```

### Authentication Errors
When authentication is required but not provided:

```json
{
    "status": "error",
    "message": "Authentication required"
}
```

### Not Found Errors
When a resource is not found:

```json
{
    "status": "error",
    "message": "Blog not found"
}
```

---

## Usage Examples

### JavaScript (Fetch API)
```javascript
// Login
const response = await fetch('/api/login', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
    },
    body: JSON.stringify({
        username: 'admin',
        password: 'password123'
    })
});

const data = await response.json();
console.log(data);

// Get blogs
const blogsResponse = await fetch('/api/blogs?page=1&limit=5');
const blogsData = await blogsResponse.json();
console.log(blogsData);
```

### cURL
```bash
# Login
curl -X POST http://your-domain.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password123"}'

# Get blogs
curl -X GET "http://your-domain.com/api/blogs?page=1&limit=5"

# Create blog
curl -X POST http://your-domain.com/api/blogs \
  -H "Content-Type: application/json" \
  -d '{"title":"New Blog","description":"Blog content"}'
```

### Python (requests)
```python
import requests

# Login
response = requests.post('http://your-domain.com/api/login', json={
    'username': 'admin',
    'password': 'password123'
})

# Get blogs
blogs = requests.get('http://your-domain.com/api/blogs?page=1&limit=5')

# Create blog
new_blog = requests.post('http://your-domain.com/api/blogs', json={
    'title': 'New Blog',
    'description': 'Blog content'
})
```

---

## Notes

1. **Session-based Authentication**: The API uses session-based authentication. After login, cookies are automatically handled by the browser.

2. **File Uploads**: For file uploads (images), use the regular form endpoints in the main controllers, not the API endpoints.

3. **Rate Limiting**: Consider implementing rate limiting for production use.

4. **CORS**: If accessing from different domains, ensure CORS is properly configured.

5. **HTTPS**: Use HTTPS in production for secure data transmission.

---

## Support

For API support or questions, please contact the development team or refer to the system documentation. 
-- Create recent_activities table
CREATE TABLE `recent_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('user','blog','project') NOT NULL,
  `action` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reference_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_date` (`date`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data
INSERT INTO `recent_activities` (`type`, `action`, `title`, `message`, `fullname`, `email`, `date`, `reference_id`) VALUES
('user', 'created', 'New User Registration', 'A new user has been registered in the system', 'John Doe', 'john.doe@example.com', NOW() - INTERVAL 1 HOUR, 1),
('user', 'updated', 'User Profile Updated', 'User profile information has been modified', 'Jane Smith', 'jane.smith@example.com', NOW() - INTERVAL 2 HOUR, 2),
('blog', 'created', 'New Blog Post Published', 'A new blog post has been published on the website', 'Admin User', 'admin@ferrigor.com', NOW() - INTERVAL 3 HOUR, 1),
('blog', 'updated', 'Blog Post Modified', 'An existing blog post has been updated', 'Content Manager', 'content@ferrigor.com', NOW() - INTERVAL 4 HOUR, 2),
('project', 'created', 'New Project Added', 'A new project has been added to the portfolio', 'Project Manager', 'projects@ferrigor.com', NOW() - INTERVAL 5 HOUR, 1),
('project', 'completed', 'Project Completed', 'A project has been marked as completed', 'Team Lead', 'team@ferrigor.com', NOW() - INTERVAL 6 HOUR, 3),
('user', 'logged_in', 'User Login', 'User has successfully logged into the system', 'Regular User', 'user@example.com', NOW() - INTERVAL 30 MINUTE, 5),
('blog', 'viewed', 'Blog Post Viewed', 'A blog post has received significant views', 'Guest User', 'guest@example.com', NOW() - INTERVAL 1 DAY, 1),
('project', 'started', 'Project Started', 'A new project has been initiated', 'Project Coordinator', 'coordinator@ferrigor.com', NOW() - INTERVAL 2 DAY, 4),
('user', 'status_changed', 'User Status Changed', 'User account status has been modified', 'System Admin', 'admin@ferrigor.com', NOW() - INTERVAL 3 DAY, 6); 
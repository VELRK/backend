-- Create projects table
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `location` varchar(255) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table for user management
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL UNIQUE,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blogs table
CREATE TABLE `blogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `blog_image` varchar(255) DEFAULT NULL,
  `content_image1` varchar(255) DEFAULT NULL,
  `content_image2` varchar(255) DEFAULT NULL,
  `content_image3` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create careers table
CREATE TABLE `careers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `type` enum('full-time','part-time','contract','internship','freelance') NOT NULL,
  `description` text NOT NULL,
  `status` enum('active','inactive','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample data for projects (optional)
INSERT INTO `projects` (`title`, `description`, `location`, `project_date`, `image`) VALUES
('Modern Office Building', 'A state-of-the-art office complex with sustainable design features and modern amenities.', 'Downtown Business District', '2024-06-15', NULL),
('Residential Complex', 'Luxury residential development with 200 units, featuring rooftop gardens and community facilities.', 'Suburban Area', '2024-07-20', NULL),
('Shopping Mall Renovation', 'Complete renovation of existing shopping mall with new retail spaces and entertainment zones.', 'City Center', '2024-08-10', NULL);

-- Sample data for careers
INSERT INTO `careers` (`title`, `location`, `type`, `description`, `status`) VALUES
('Senior Software Engineer', 'New York, NY', 'full-time', 'We are looking for an experienced software engineer to join our development team. The ideal candidate should have 5+ years of experience in web development, strong knowledge of PHP, JavaScript, and modern frameworks.', 'active'),
('Marketing Specialist', 'Remote', 'full-time', 'Join our marketing team to help promote our services and grow our brand presence. Experience in digital marketing, social media, and content creation is required.', 'active'),
('Project Manager', 'Los Angeles, CA', 'full-time', 'We need a skilled project manager to oversee construction projects from conception to completion. Must have experience in the construction industry and strong leadership skills.', 'active'),
('Graphic Designer', 'Chicago, IL', 'part-time', 'Creative graphic designer needed for part-time work on various design projects including logos, brochures, and digital assets.', 'active'),
('Sales Representative', 'Miami, FL', 'full-time', 'Dynamic sales professional needed to drive business growth and maintain client relationships. Experience in B2B sales preferred.', 'active');

-- Sample admin user (password: admin123)
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`, `status`) VALUES
('Admin User', 'admin', 'admin@ferrigor.com', '+1234567890', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'active'); 
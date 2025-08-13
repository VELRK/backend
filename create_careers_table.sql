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

-- Sample data for careers
INSERT INTO `careers` (`title`, `location`, `type`, `description`, `status`) VALUES
('Senior Software Engineer', 'New York, NY', 'full-time', 'We are looking for an experienced software engineer to join our development team. The ideal candidate should have 5+ years of experience in web development, strong knowledge of PHP, JavaScript, and modern frameworks.', 'active'),
('Marketing Specialist', 'Remote', 'full-time', 'Join our marketing team to help promote our services and grow our brand presence. Experience in digital marketing, social media, and content creation is required.', 'active'),
('Project Manager', 'Los Angeles, CA', 'full-time', 'We need a skilled project manager to oversee construction projects from conception to completion. Must have experience in the construction industry and strong leadership skills.', 'active'),
('Graphic Designer', 'Chicago, IL', 'part-time', 'Creative graphic designer needed for part-time work on various design projects including logos, brochures, and digital assets.', 'active'),
('Sales Representative', 'Miami, FL', 'full-time', 'Dynamic sales professional needed to drive business growth and maintain client relationships. Experience in B2B sales preferred.', 'active'); 
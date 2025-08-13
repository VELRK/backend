-- Create email_activities table for recent activities
CREATE TABLE `email_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample email activity data
INSERT INTO `email_activities` (`message`, `fullname`, `email`, `date`) VALUES
('Welcome email sent to new user', 'John Doe', 'john.doe@example.com', NOW() - INTERVAL 1 HOUR),
('Password reset email sent', 'Jane Smith', 'jane.smith@example.com', NOW() - INTERVAL 2 HOUR),
('Newsletter sent to subscribers', 'Marketing Team', 'newsletter@company.com', NOW() - INTERVAL 3 HOUR),
('Order confirmation email', 'Mike Johnson', 'mike.j@example.com', NOW() - INTERVAL 4 HOUR),
('System notification sent', 'Admin User', 'admin@ferrigor.com', NOW() - INTERVAL 5 HOUR),
('Welcome email sent to new user', 'Sarah Wilson', 'sarah.w@example.com', NOW() - INTERVAL 6 HOUR),
('Password reset email sent', 'David Brown', 'david.b@example.com', NOW() - INTERVAL 7 HOUR),
('Newsletter sent to subscribers', 'Marketing Team', 'newsletter@company.com', NOW() - INTERVAL 1 DAY),
('Order confirmation email', 'Lisa Davis', 'lisa.d@example.com', NOW() - INTERVAL 2 DAY),
('System notification sent', 'Admin User', 'admin@ferrigor.com', NOW() - INTERVAL 3 DAY); 
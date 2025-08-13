-- Add sample users
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`, `status`) VALUES
('John Doe', 'john', 'john@example.com', '+1234567891', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'active'),
('Jane Smith', 'jane', 'jane@example.com', '+1234567892', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'active'),
('Mike Johnson', 'mike', 'mike@example.com', '+1234567893', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'active'),
('Sarah Wilson', 'sarah', 'sarah@example.com', '+1234567894', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'inactive');

-- Add sample blogs
INSERT INTO `blogs` (`title`, `description`, `user_id`, `created_at`, `updated_at`) VALUES
('Getting Started with Web Development', '<h2>Introduction</h2><p>Web development is an exciting journey that combines creativity with technical skills. In this blog post, we will explore the fundamentals of web development and how to get started.</p><h3>Key Topics:</h3><ul><li>HTML Basics</li><li>CSS Styling</li><li>JavaScript Fundamentals</li><li>Responsive Design</li></ul><p>Start your web development journey today!</p>', 1, NOW(), NOW()),
('Modern JavaScript Frameworks', '<h2>Popular Frameworks</h2><p>JavaScript frameworks have revolutionized web development. Let us explore the most popular ones:</p><h3>Top Frameworks:</h3><ul><li>React.js</li><li>Vue.js</li><li>Angular</li><li>Svelte</li></ul><p>Choose the right framework for your project!</p>', 2, NOW(), NOW()),
('Database Design Best Practices', '<h2>Database Fundamentals</h2><p>Good database design is crucial for application performance and scalability. Here are some best practices:</p><h3>Design Principles:</h3><ul><li>Normalization</li><li>Indexing</li><li>Relationships</li><li>Performance Optimization</li></ul><p>Build robust and efficient databases!</p>', 3, NOW(), NOW()),
('UI/UX Design Trends 2024', '<h2>Design Trends</h2><p>Stay ahead of the curve with the latest UI/UX design trends for 2024:</p><h3>Trending Elements:</h3><ul><li>Minimalist Design</li><li>Dark Mode</li><li>Micro-interactions</li><li>Accessibility Focus</li></ul><p>Create engaging user experiences!</p>', 1, NOW(), NOW()),
('Cloud Computing Solutions', '<h2>Cloud Platforms</h2><p>Cloud computing has transformed how we build and deploy applications. Explore the major cloud platforms:</p><h3>Popular Platforms:</h3><ul><li>AWS</li><li>Azure</li><li>Google Cloud</li><li>DigitalOcean</li></ul><p>Scale your applications with cloud solutions!</p>', 2, NOW(), NOW());

-- Note: All users have the same password: admin123
-- You can change passwords later through the user management interface 
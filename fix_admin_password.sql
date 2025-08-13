-- Fix admin password (password: admin123)
UPDATE `users` SET `password` = '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG' WHERE `username` = 'admin';

-- If admin user doesn't exist, create it
INSERT INTO `users` (`name`, `username`, `email`, `phone`, `password`, `status`) 
SELECT 'Admin User', 'admin', 'admin@ferrigor.com', '+1234567890', '$2y$10$/r/gasn0bXNYjeiPzAXLROGMPszyrxYV41mqnyT7SeY0yyI33.zeG', 'active'
WHERE NOT EXISTS (SELECT 1 FROM `users` WHERE `username` = 'admin'); 
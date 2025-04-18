-- Update admin password to 'admin'
UPDATE admins 
SET password = '$2y$10$YourNewHashHere' 
WHERE username = 'admin';

-- If no admin exists, create one
INSERT INTO admins (username, password) 
SELECT 'admin', '$2y$10$YourNewHashHere'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'admin'); 
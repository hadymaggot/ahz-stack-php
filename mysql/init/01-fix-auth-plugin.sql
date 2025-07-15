-- AHZ Stack PHP - MySQL Authentication Plugin Fix
-- This script fixes the 'mysql_native_password' plugin not loaded error
-- by configuring users with compatible authentication methods

-- MySQL 8.4+ Authentication Fix
-- Create application user with mysql_native_password for compatibility
-- This ensures compatibility with older PHP MySQL drivers

-- Create or alter the application user with mysql_native_password
CREATE USER IF NOT EXISTS 'lemp_user'@'%' IDENTIFIED WITH mysql_native_password BY 'userpassword';
ALTER USER 'lemp_user'@'%' IDENTIFIED WITH mysql_native_password BY 'userpassword';

-- Grant necessary privileges
GRANT ALL PRIVILEGES ON lemp_db.* TO 'lemp_user'@'%';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON *.* TO 'lemp_user'@'%';

-- Also fix root user for compatibility
ALTER USER 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'rootpassword';
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'rootpassword';

-- Flush privileges to apply changes
FLUSH PRIVILEGES;

-- Display current authentication plugins for verification
SELECT user, host, plugin FROM mysql.user WHERE user IN ('root', 'lemp_user');

-- Log successful initialization
SELECT 'MySQL Authentication Plugin Fix Applied Successfully' AS status;
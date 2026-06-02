-- Run in phpMyAdmin if setup-admin.php cannot be used
-- Sets admin password to: password

UPDATE users
SET
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    is_active = 1,
    deleted_at = NULL,
    role = 'admin'
WHERE email = 'admin@courtqueue.com';

-- If no rows updated, insert admin:
INSERT INTO users (name, email, email_verified_at, password, role, phone, is_active, created_at, updated_at)
SELECT 'Admin User', 'admin@courtqueue.com', NOW(),
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
       'admin', '123-456-7890', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@courtqueue.com');

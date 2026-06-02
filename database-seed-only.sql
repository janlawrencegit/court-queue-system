-- Run ONLY if tables already exist (do not run full database.sql again)
-- Fixes admin login password: password

UPDATE users SET
    name = 'Admin User',
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role = 'admin',
    is_active = 1,
    deleted_at = NULL,
    updated_at = NOW()
WHERE email = 'admin@courtqueue.com';

-- If admin does not exist, run this instead:
-- INSERT INTO users (name, email, email_verified_at, password, role, phone, is_active, created_at, updated_at)
-- VALUES ('Admin User', 'admin@courtqueue.com', NOW(), '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '123-456-7890', 1, NOW(), NOW());

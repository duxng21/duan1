<?php
class User
{
    protected $conn;
    public function __construct()
    {
        $this->conn = connectDB();
    }

    public function findByUsername($username)
    {
        $sql = 'SELECT u.*, r.role_code, r.role_name FROM users u INNER JOIN roles r ON u.role_id = r.role_id WHERE u.username = ? LIMIT 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function usernameExists($username)
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE username = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }
    public function emailExists($email)
    {
        if ($email === null || $email === '')
            return false;
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }

    public function create($data)
    {
        // Pre-check duplicate email to avoid PDOException
        if (isset($data['email']) && $this->emailExists($data['email'])) {
            throw new Exception('Email đã tồn tại.');
        }
        $sql = 'INSERT INTO users (username, password, full_name, email, phone, avatar, role_id, staff_id, status, login_attempts, last_login, created_at) VALUES (?,?,?,?,?,?,?,?,?,0,NULL,NOW())';
        $stmt = $this->conn->prepare($sql);
        try {
            $stmt->execute([
                $data['username'],
                $data['password'],
                $data['full_name'],
                $data['email'] ?? null,
                $data['phone'] ?? null,
                $data['avatar'] ?? null,
                $data['role_id'],
                $data['staff_id'],
                $data['status'] ?? 'Active'
            ]);
        } catch (PDOException $e) {
            // Graceful handling for duplicate keys (email/username)
            if ($e->getCode() === '23000') {
                $msg = $e->getMessage();
                if (stripos($msg, 'email') !== false) {
                    throw new Exception('Email đã tồn tại.');
                }
                if (stripos($msg, 'username') !== false) {
                    throw new Exception('Username đã tồn tại.');
                }
            }
            throw $e; // rethrow if different error
        }
        return $this->conn->lastInsertId();
    }

    public function updateLastLogin($user_id)
    {
        $sql = 'UPDATE users SET last_login = NOW(), login_attempts = 0, updated_at = NOW() WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
    }

    public function incrementLoginAttempts($user_id)
    {
        $sql = 'UPDATE users SET login_attempts = login_attempts + 1, updated_at = NOW() WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
    }

    public function lockAccountIfExceeded($user_id, $max, $statusLock = 'Locked')
    {
        $sql = 'SELECT login_attempts FROM users WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        $attempts = (int) $stmt->fetchColumn();
        if ($attempts >= $max) {
            $up = $this->conn->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE user_id = ?');
            $up->execute([$statusLock, $user_id]);
            return true;
        }
        return false;
    }

    public function findById($user_id)
    {
        $sql = 'SELECT u.*, r.role_code FROM users u INNER JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ? LIMIT 1';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function updatePassword($user_id, $hash)
    {
        $sql = 'UPDATE users SET password = ?, login_attempts = 0, updated_at = NOW() WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$hash, $user_id]);
    }

    public function getAllUsers()
    {
        $sql = 'SELECT u.user_id, u.username, u.full_name, u.email, u.status, u.role_id, r.role_code, u.staff_id, u.last_login, u.created_at FROM users u INNER JOIN roles r ON u.role_id = r.role_id ORDER BY u.user_id DESC';
        return $this->conn->query($sql)->fetchAll();
    }

    public function setStatus($user_id, $status)
    {
        $sql = 'UPDATE users SET status = ?, updated_at = NOW() WHERE user_id = ?';
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $user_id]);
    }
}

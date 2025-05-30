<?php

namespace Model;

class AccountModel extends BaseModel {
    protected $table = 'tbl_login_account';

    public function createNewAccount(string $username, string $password, string $role): int {
        $data = [
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
            'status' => 'pending'
        ];

        $this->create($data);
        return $this->db->lastInsertId();
    }

    public function findByUsername(string $username): ?array {
        $sql = "SELECT * FROM {$this->table} WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['username' => $username]);
        
        return $stmt->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status): bool {
        return $this->update(['status' => $status], "id = {$id}");
    }

    public function validateCredentials(string $username, string $password): bool {
        $user = $this->findByUsername($username);
        if (!$user) return false;
        
        return password_verify($password, $user['password']);
    }
}
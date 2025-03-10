<?php

namespace src\Repositories;

require_once 'Repository.php';
require_once __DIR__ . '/../Models/User.php';

use src\Models\User;

class UserRepository extends Repository
{
    /**
     * @param string $id
     * @return User|false
     */
    public function getById(string $id): User|false
    {
        $sqlStatement = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $found = $sqlStatement->execute([$id]);
        if ($found) {
            return (new User())->fill($sqlStatement->fetch());
        }
        return false;
    }

    public function getByEmail(string $email): ?User
    {
        $sqlStatement = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $success = $sqlStatement->execute([$email]);
        if ($success && $sqlStatement->rowCount() !== 0) {
            return (new User())->fill($sqlStatement->fetch());
        }
        return null;
    }

    public function saveUser(string $name, string $email, string $passwordDigest): User|false
    {
        $sqlStatement = $this->pdo->prepare("INSERT INTO users (name, email, password_digest) VALUES (?, ?, ?);");
        $saved = $sqlStatement->execute([$name, $email, $passwordDigest]);
        if ($saved) {
            $id = $this->pdo->lastInsertId();
            $sqlStatement = "SELECT * FROM users where id = $id";
            $result = $this->pdo->query($sqlStatement);
            return (new User())->fill($result->fetch());
        }
        return false;
    }

    public function updateUser(int $id, string $name, ?string $profilePicture = null): bool
    {
        if ($profilePicture) {
            $sqlStatement = $this->pdo->prepare("UPDATE users SET name = ?, profile_picture = ? WHERE id = ?");
            $sqlStatement->execute([$name, $profilePicture, $id]);
        } else {
            $sqlStatement = $this->pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
            $sqlStatement->execute([$name, $id]);
        }
        return $sqlStatement->execute();
    }

    public function updatePassword(int $id, string $passwordDigest): bool {
        try {
            $sqlStatement = $this->pdo->prepare("UPDATE users SET password_digest = ? WHERE id = ?");
            return $sqlStatement->execute([$passwordDigest, $id]);
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    

}

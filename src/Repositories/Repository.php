<?php

namespace src\Repositories;

use PDO;
use PDOException;

class Repository {

    protected PDO $pdo;
    private string $hostname;
    private string $username;
    private string $databaseName;
    private string $databasePassword;
    private string $charset;

    public function __construct() {
        // Retrieve credentials from environment variables.
        $this->hostname = $_ENV['DB_HOST'] ?: 'localhost';
        $this->username = $_ENV['DB_USERNAME'] ?: 'root';
        $this->databaseName = $_ENV['DB_DATABASE'];
        $this->databasePassword = $_ENV['DB_PASSWORD'] ?: '';
        $this->charset = $_ENV['DB_CHARSET'] ?: 'utf8mb4';

        $dsn = "mysql:host=$this->hostname;dbname=$this->databaseName;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        try {
            $this->pdo = new PDO($dsn, $this->username, $this->databasePassword, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }
}

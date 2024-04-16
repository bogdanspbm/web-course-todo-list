<?php

class PostgresSQL
{

    /**
     * @var PDO
     */
    private $pdo;

    function  __construct($username = "postgres",
                         $password = "postgres",
                          $dbname = 'postgres',
                         $host = 'localhost',
                         $port = '5432')
    {
        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
            $this->pdo = new PDO($dsn, $username, $password);

            // Set PDO to throw exceptions on error
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch
        (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    function getConnection()
    {
        return $this->pdo;
    }
}

?>
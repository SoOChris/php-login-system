<?php

class Database
{
    private $host = "localhost";
    private $user = "root";
    private $pwd = "";
    private $db = "login-system-php";
    private $dbh;
    private $stmt;


    public function __construct()
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db}";
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pwd, $options);
        } catch (Exception $e) {
            exit($e->getMessage() . '<h2>Connection error</h2>');
        }
    }


    //Prepares statements with query
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    //Bind values
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute()
    {
        return $this->stmt->execute();
    }

    //Return multiple results
    public function results()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }


    //Return only one result
    public function singleResult()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    //row count
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
}

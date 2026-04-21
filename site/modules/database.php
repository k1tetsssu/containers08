<?php

class Database {
    private $pdo;

    public function __construct($path) {
        $this->pdo = new PDO('sqlite:' . $path);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function Execute($sql) {
        return $this->pdo->exec($sql);
    }
    
    public function Fetch($sql) {
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function Create($table, $data) {
        $columns = implode(',', array_keys($data));
        $values = implode(',', array_map(fn($v) => "'$v'", array_values($data)));
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        $this->Execute($sql);

        return $this->pdo->lastInsertId();
    }

    public function Read($table, $id) {
        $sql = "SELECT * FROM $table WHERE id = $id";
        $result = $this->Fetch($sql);
        return $result ? $result[0] : null;
    }

    public function Update($table, $id, $data) {
        $set = implode(',', array_map(fn($k, $v) => "$k = '$v'", array_keys($data), array_values($data)));
        $sql = "UPDATE $table SET $set WHERE id = $id";
        $this->Execute($sql);
    }

    public function Delete($table, $id) {
        $sql = "DELETE FROM $table WHERE id = $id";
        $this->Execute($sql);
    }

    public function Count($table) {
        $sql = "SELECT COUNT(*) as count FROM $table";
        $result = $this->Fetch($sql);
        return $result[0]['count'];
    }
}
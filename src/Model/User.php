<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Метод для создания пользователя
    public function create($full_name, $role, $efficiency) {
        $query = "INSERT INTO " . $this->table . " (full_name, role, efficiency) VALUES (:full_name, :role, :efficiency)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $full_name);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":efficiency", $efficiency);

        if ($stmt->execute()) {
            return $this->conn->lastInsertId(); // Возвращаем ID добавленного пользователя
        }
        return false;
    }

    // Метод для получения пользователей с сортировкой
    public function readAll($role = null) {
        $query = "SELECT * FROM " . $this->table;
        if ($role) {
            $query .= " WHERE role = :role";
        }

        $stmt = $this->conn->prepare($query);

        if ($role) {
            $stmt->bindParam(":role", $role);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Метод для обновления пользователя
    public function update($id, $data) {
        $setParts = [];
        $params = [];

        // Проверяем, какие поля есть в данных и добавляем их в массив
        if (isset($data['full_name'])) {
            $setParts[] = "full_name = :full_name";
            $params[':full_name'] = $data['full_name'];
        }
        if (isset($data['role'])) {
            $setParts[] = "role = :role";
            $params[':role'] = $data['role'];
        }
        if (isset($data['efficiency'])) {
            $setParts[] = "efficiency = :efficiency";
            $params[':efficiency'] = $data['efficiency'];
        }

        // Если нет полей для обновления, возвращаем false
        if (empty($setParts)) {
            return false;
        }

        // Собираем запрос для обновления
        $query = "UPDATE " . $this->table . " SET " . implode(", ", $setParts) . " WHERE id = :id";
        $params[':id'] = $id;

        $stmt = $this->conn->prepare($query);

        // Привязываем параметры и выполняем запрос
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        if ($stmt->execute()) {
            // Возвращаем обновленного пользователя
            return $this->readOne($id);
        }
        return false;
    }

    // Метод для удаления пользователя
    public function deleteOne($id) {
        $user = $this->readOne($id);
        if (!$user) {
            return false; // Пользователь не найден
        }

        // Выполняем удаление
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        if ($stmt->execute()) {
            return $user; // Возвращаем удаленного пользователя
        }
        return false;
    }

    public function deleteAll() {
        $query = "DELETE FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        return $stmt->execute();
    }
}

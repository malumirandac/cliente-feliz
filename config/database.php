<?php
class Database {
    private $host = "127.0.0.1:3307";
    private $db_name = "cliente_feliz";
    private $username = "cliente";
    private $password = "cliente1234";
    private $conn;

    public function getConnection() {
        $this->conn = null;
    
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                                  $this->username,
                                  $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Conexión exitosa"; // Mensaje de depuración
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
    
        return $this->conn;
    }
}

?>
